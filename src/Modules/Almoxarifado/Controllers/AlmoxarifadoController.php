<?php

class AlmoxarifadoController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $search = Request::get('search', '');
        $categoria = Request::get('categoria', '');
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search) {
            $where[] = '(m.nome LIKE ? OR m.codigo_sku LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($categoria !== '' && isset(Material::CATEGORIAS[$categoria])) {
            $where[] = 'm.categoria = ?';
            $params[] = $categoria;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT COUNT(*) c FROM materiais m {$whereSql}");
        $stmt->execute($params);
        $total = (int) $stmt->fetch()['c'];

        $offset = ($page - 1) * $perPage;
        $stmt = $pdo->prepare(
            "SELECT m.*,
                    COALESCE((SELECT SUM(e.quantidade_atual) FROM estoque_obra e WHERE e.material_id = m.id), 0) AS saldo_total
             FROM materiais m
             {$whereSql}
             ORDER BY m.nome ASC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        $materiais = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $pagination = [
            'data' => $materiais,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($page * $perPage, $total),
        ];

        $selectMateriais = $pdo->query("SELECT id, codigo_sku, nome, unidade_medida FROM materiais ORDER BY nome ASC")->fetchAll(\PDO::FETCH_OBJ);
        $ultimasMovs = $pdo->query(
            "SELECT mv.*, m.nome AS material_nome, m.codigo_sku, m.unidade_medida, o.codigo AS obra_codigo, o.nome AS obra_nome, u.nome AS usuario_nome
             FROM movimentacoes_estoque mv
             INNER JOIN materiais m ON m.id = mv.material_id
             INNER JOIN obras o ON o.id = mv.obra_id
             LEFT JOIN usuarios u ON u.id = mv.usuario_id
             ORDER BY mv.id DESC
             LIMIT 10"
        )->fetchAll(\PDO::FETCH_OBJ);

        $this->view('almoxarifado/index', [
            'title' => 'Almoxarifado - Hermes',
            'pageTitle' => 'Almoxarifado',
            'currentRoute' => 'almoxarifado',
            'materiais' => $materiais,
            'pagination' => $pagination,
            'search' => $search,
            'categoriaFilter' => $categoria,
            'categorias' => Material::CATEGORIAS,
            'unidades' => Material::UNIDADES,
            'selectMateriais' => $selectMateriais,
            'obras' => Obra::query()->select(['id', 'codigo', 'nome'])->orderBy('nome', 'ASC')->get(),
            'ultimasMovs' => $ultimasMovs,
        ]);
    }

    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validateMaterial();

        Material::create($data);

        Session::putFlash('success', 'Material cadastrado com sucesso!');
        App::redirect('/almoxarifado');
    }

    public function update(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $material = Material::find($id);
        if (!$material) {
            App::abort(404, 'Material não encontrado');
        }

        $data = $this->validateMaterial($id);
        $material->fill($data);
        $material->save();

        Session::putFlash('success', 'Material atualizado com sucesso!');
        App::redirect('/almoxarifado');
    }

    public function movimentacao(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validate(Request::all(), [
            'obra_id' => 'required|integer|exists:obras,id',
            'material_id' => 'required|integer|exists:materiais,id',
            'tipo' => 'required|in:entrada,saida',
            'quantidade' => 'required|numeric|min:0.001',
            'observacao' => 'nullable|max:500',
        ]);

        $obraId = (int) $data['obra_id'];
        $materialId = (int) $data['material_id'];
        $quantidade = (float) $data['quantidade'];
        $tipo = $data['tipo'];

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT id, quantidade_atual FROM estoque_obra WHERE obra_id = ? AND material_id = ?");
        $stmt->execute([$obraId, $materialId]);
        $row = $stmt->fetch();

        $atual = $row ? (float) $row['quantidade_atual'] : 0.0;
        $novoSaldo = $tipo === 'entrada' ? $atual + $quantidade : $atual - $quantidade;

        if ($novoSaldo < 0) {
            Session::putFlash('error', 'Saldo insuficiente para saída. Saldo atual: ' . $atual . '.');
            App::redirect('/almoxarifado');
        }

        if ($row) {
            $pdo->prepare("UPDATE estoque_obra SET quantidade_atual = ? WHERE id = ?")->execute([$novoSaldo, $row['id']]);
        } else {
            $pdo->prepare("INSERT INTO estoque_obra (obra_id, material_id, quantidade_atual) VALUES (?, ?, ?)")
                ->execute([$obraId, $materialId, $novoSaldo]);
        }

        $pdo->prepare("INSERT INTO movimentacoes_estoque (obra_id, material_id, tipo, quantidade, observacao, usuario_id) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$obraId, $materialId, $tipo, $quantidade, $data['observacao'] ?? null, Auth::getInstance()->id()]);

        Session::putFlash('success', 'Movimentação registrada com sucesso!');
        App::redirect('/almoxarifado');
    }

    private function validateMaterial(?int $excludeId = null): array
    {
        $uniqueRule = $excludeId
            ? "required|min:3|max:30|unique:materiais,codigo_sku,{$excludeId},id"
            : 'required|min:3|max:30|unique:materiais,codigo_sku';

        return $this->validate(Request::all(), [
            'codigo_sku' => $uniqueRule,
            'nome' => 'required|min:2|max:120',
            'categoria' => 'required|in:' . implode(',', array_keys(Material::CATEGORIAS)),
            'unidade_medida' => 'required|in:' . implode(',', array_keys(Material::UNIDADES)),
            'estoque_minimo' => 'required|numeric|min:0',
        ]);
    }

    private function verifyCsrf(): void
    {
        $token = Request::post('_token', '');
        if (!App::verifyCsrf((string) $token)) {
            App::abort(403, 'Token de segurança inválido.');
        }
    }
}
