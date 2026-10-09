<?php

class OrdemCompraController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $search = Request::get('search', '');
        $status = Request::get('status', '');
        $aba = Request::get('fornecedores', '') !== '' ? 'fornecedores' : 'oc';
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search) {
            $where[] = '(oc.codigo_oc LIKE ? OR oc.numero LIKE ? OR oc.fornecedor LIKE ? OR f.razao_social LIKE ? OR o.nome LIKE ? OR o.codigo LIKE ?)';
            for ($i = 0; $i < 6; $i++) {
                $params[] = "%{$search}%";
            }
        }
        if ($status !== '' && isset(OrdemCompra::STATUS[$status])) {
            $where[] = 'oc.status = ?';
            $params[] = $status;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) c FROM ordens_compra oc
             INNER JOIN obras o ON o.id = oc.obra_id
             LEFT JOIN fornecedores f ON f.id = oc.fornecedor_id
             {$whereSql}"
        );
        $stmt->execute($params);
        $total = (int) $stmt->fetch()['c'];

        $offset = ($page - 1) * $perPage;
        $stmt = $pdo->prepare(
            "SELECT oc.*, o.nome AS obra_nome, o.codigo AS obra_codigo, f.razao_social AS fornecedor_nome,
                    (SELECT COUNT(*) FROM oc_itens oi WHERE oi.ordem_compra_id = oc.id) AS total_itens
             FROM ordens_compra oc
             INNER JOIN obras o ON o.id = oc.obra_id
             LEFT JOIN fornecedores f ON f.id = oc.fornecedor_id
             {$whereSql}
             ORDER BY oc.created_at DESC, oc.id DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        $ocs = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $pagination = [
            'data' => $ocs,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($page * $perPage, $total),
        ];

        $this->view('compras/index', [
            'title' => 'Compras - Hermes',
            'pageTitle' => 'Compras',
            'currentRoute' => 'compras',
            'ocs' => $ocs,
            'pagination' => $pagination,
            'search' => $search,
            'statusFilter' => $status,
            'statusList' => OrdemCompra::STATUS,
            'obras' => $this->obrasList(),
            'fornecedores' => Fornecedor::query()->select(['id', 'razao_social', 'cnpj_cpf', 'categoria'])->orderBy('razao_social', 'ASC')->get(),
            'materiais' => Material::query()->select(['id', 'codigo_sku', 'nome', 'unidade_medida'])->orderBy('nome', 'ASC')->get(),
            'abaInicial' => $aba,
            'fornecedoresList' => Fornecedor::query()->select(['id', 'razao_social', 'cnpj_cpf', 'contato_nome', 'telefone', 'email', 'categoria'])->orderBy('razao_social', 'ASC')->get(),
            'categoriasFornecedor' => Fornecedor::CATEGORIAS,
        ]);
    }

    public function create(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $this->view('compras/create', [
            'title' => 'Nova Ordem de Compra - Hermes',
            'pageTitle' => 'Nova Ordem de Compra',
            'currentRoute' => 'compras',
            'statusList' => OrdemCompra::STATUS,
            'obras' => $this->obrasList(),
        ]);
    }

    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validate(Request::all(), [
            'obra_id' => 'required|integer|exists:obras,id',
            'codigo_oc' => 'required|min:3|max:30|unique:ordens_compra,codigo_oc',
            'fornecedor_id' => 'required|integer|exists:fornecedores,id',
            'valor_total' => 'nullable|numeric|min:0',
            'data_pedido' => 'nullable|date',
            'observacoes' => 'nullable|max:2000',
        ]);

        $itens = $this->parseItens();

        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $oc = OrdemCompra::create([
                'codigo_oc' => $data['codigo_oc'],
                'numero' => $data['codigo_oc'],
                'obra_id' => (int) $data['obra_id'],
                'fornecedor_id' => (int) $data['fornecedor_id'],
                'fornecedor' => $this->fornecedorNome((int) $data['fornecedor_id']),
                'valor_total' => 0,
                'status' => 'pendente',
                'data_pedido' => $data['data_pedido'] ?? date('Y-m-d'),
                'observacoes' => $data['observacoes'] ?? null,
                'usuario_id' => Auth::getInstance()->id(),
            ]);

            $this->saveItens($oc->id, $itens);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Session::putFlash('error', 'Erro ao criar ordem de compra: ' . $e->getMessage());
            App::redirect('/compras');
        }

        Session::putFlash('success', 'Ordem de compra criada com sucesso!');
        App::redirect('/compras');
    }

    public function updateStatus(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $oc = OrdemCompra::find($id);
        if (!$oc) {
            App::abort(404, 'Ordem de compra não encontrada');
        }

        $data = $this->validate(Request::all(), [
            'status' => 'required|in:' . implode(',', array_keys(OrdemCompra::STATUS)),
        ]);

        $oc->status = $data['status'];
        $oc->save();

        Session::putFlash('success', 'Status da OC ' . ($oc->codigo_oc ?: $oc->numero) . ' atualizado para ' . OrdemCompra::STATUS[$data['status']] . '!');
        App::redirect('/compras');
    }

    public function edit(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $oc = OrdemCompra::find($id);
        if (!$oc) {
            App::abort(404, 'Ordem de compra não encontrada');
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT oi.*, m.nome AS material_nome, m.unidade_medida FROM oc_itens oi LEFT JOIN materiais m ON m.id = oi.material_id WHERE oi.ordem_compra_id = ?");
        $stmt->execute([$id]);

        $this->view('compras/edit', [
            'title' => 'Editar Ordem de Compra - Hermes',
            'pageTitle' => 'Editar Ordem de Compra',
            'currentRoute' => 'compras',
            'oc' => $oc,
            'itens' => $stmt->fetchAll(\PDO::FETCH_OBJ),
            'statusList' => OrdemCompra::STATUS,
            'obras' => $this->obrasList(),
            'fornecedores' => Fornecedor::query()->select(['id', 'razao_social'])->orderBy('razao_social', 'ASC')->get(),
        ]);
    }

    public function update(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $oc = OrdemCompra::find($id);
        if (!$oc) {
            App::abort(404, 'Ordem de compra não encontrada');
        }

        $data = $this->validate(Request::all(), [
            'obra_id' => 'required|integer|exists:obras,id',
            'numero' => 'required|min:1|max:30',
            'fornecedor' => 'nullable|max:200',
            'valor_total' => 'required|numeric|min:0',
            'status' => 'required|in:' . implode(',', array_keys(OrdemCompra::STATUS)),
            'data_pedido' => 'nullable|date',
            'observacoes' => 'nullable|max:2000',
        ]);

        $oc->fill([
            'obra_id' => (int) $data['obra_id'],
            'numero' => $data['numero'],
            'codigo_oc' => $oc->codigo_oc ?: $data['numero'],
            'fornecedor' => $data['fornecedor'] ?? null,
            'valor_total' => (float) $data['valor_total'],
            'status' => $data['status'],
            'data_pedido' => $data['data_pedido'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
        ]);
        $oc->save();

        Session::putFlash('success', 'Ordem de compra atualizada com sucesso!');
        App::redirect('/compras');
    }

    public function destroy(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $oc = OrdemCompra::find($id);
        if (!$oc) {
            App::abort(404, 'Ordem de compra não encontrada');
        }

        $oc->delete();

        Session::putFlash('success', 'Ordem de compra excluída com sucesso!');
        App::redirect('/compras');
    }

    private function parseItens(): array
    {
        $itens = [];
        $materialIds = $_POST['itens']['material_id'] ?? [];
        $descricoes = $_POST['itens']['descricao'] ?? [];
        $quantidades = $_POST['itens']['quantidade'] ?? [];
        $valores = $_POST['itens']['valor_unitario'] ?? [];

        if (!is_array($materialIds)) {
            return [];
        }

        foreach (array_keys($materialIds) as $i) {
            $materialId = (int) ($materialIds[$i] ?? 0);
            $descricao = trim((string) ($descricoes[$i] ?? ''));
            $quantidade = (float) str_replace(',', '.', (string) ($quantidades[$i] ?? 0));
            $valor = (float) str_replace(',', '.', (string) ($valores[$i] ?? 0));

            if ($quantidade <= 0) {
                continue;
            }

            if (!$descricao && $materialId > 0) {
                $material = Material::find($materialId);
                $descricao = $material ? $material->nome : '';
            }
            if (!$descricao) {
                $descricao = 'Item';
            }

            $itens[] = [
                'material_id' => $materialId > 0 ? $materialId : null,
                'descricao' => mb_substr($descricao, 0, 200),
                'quantidade' => $quantidade,
                'valor_unitario' => $valor,
            ];
        }

        return $itens;
    }

    private function saveItens(int $ocId, array $itens): void
    {
        $pdo = Database::getInstance();
        $pdo->prepare("DELETE FROM oc_itens WHERE ordem_compra_id = ?")->execute([$ocId]);

        $stmt = $pdo->prepare("INSERT INTO oc_itens (ordem_compra_id, material_id, descricao, quantidade, valor_unitario) VALUES (?, ?, ?, ?, ?)");
        $total = 0.0;

        foreach ($itens as $item) {
            $stmt->execute([$ocId, $item['material_id'], $item['descricao'], $item['quantidade'], $item['valor_unitario']]);
            $total += $item['quantidade'] * $item['valor_unitario'];
        }

        $pdo->prepare("UPDATE ordens_compra SET valor_total = ? WHERE id = ?")->execute([$total, $ocId]);
    }

    private function fornecedorNome(int $id): ?string
    {
        $fornecedor = Fornecedor::find($id);
        return $fornecedor?->razao_social;
    }

    private function verifyCsrf(): void
    {
        $token = Request::post('_token', '');
        if (!App::verifyCsrf((string) $token)) {
            App::abort(403, 'Token de segurança inválido.');
        }
    }

    private function obrasList(): array
    {
        return Obra::query()->select(['id', 'codigo', 'nome'])->orderBy('nome', 'ASC')->get();
    }
}
