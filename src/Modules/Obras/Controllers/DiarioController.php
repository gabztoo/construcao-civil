<?php

class DiarioController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $search = Request::get('search', '');
        $obraId = (int) Request::get('obra_id', 0);
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search) {
            $where[] = '(d.titulo LIKE ? OR o.nome LIKE ? OR o.codigo LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($obraId > 0) {
            $where[] = 'd.obra_id = ?';
            $params[] = $obraId;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT COUNT(*) c FROM diarios_obra d INNER JOIN obras o ON o.id = d.obra_id {$whereSql}");
        $stmt->execute($params);
        $total = (int) $stmt->fetch()['c'];

        $offset = ($page - 1) * $perPage;
        $stmt = $pdo->prepare(
            "SELECT d.*, o.nome AS obra_nome, o.codigo AS obra_codigo
             FROM diarios_obra d
             INNER JOIN obras o ON o.id = d.obra_id
             {$whereSql}
             ORDER BY d.data DESC, d.id DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        $diarios = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $pagination = [
            'data' => $diarios,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($page * $perPage, $total),
        ];

        $this->view('diarios/index', [
            'title' => 'Diários de Obra - Hermes',
            'pageTitle' => 'Diários de Obra',
            'currentRoute' => 'diarios',
            'diarios' => $diarios,
            'pagination' => $pagination,
            'search' => $search,
            'obraId' => $obraId,
            'obras' => $this->obrasList(),
        ]);
    }

    public function create(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $this->view('diarios/create', [
            'title' => 'Novo Diário - Hermes',
            'pageTitle' => 'Novo Diário de Obra',
            'currentRoute' => 'diarios',
            'obras' => $this->obrasList(),
        ]);
    }

    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validate(Request::all(), [
            'obra_id' => 'required|integer|exists:obras,id',
            'data' => 'required|date',
            'titulo' => 'required|min:3|max:200',
            'descricao' => 'required|max:5000',
        ]);

        Diario::create([
            'obra_id' => (int) $data['obra_id'],
            'data' => $data['data'],
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'],
        ]);

        Session::putFlash('success', 'Diário criado com sucesso!');
        App::redirect('/diarios');
    }

    public function edit(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $diario = Diario::find($id);
        if (!$diario) {
            App::abort(404, 'Diário não encontrado');
        }

        $this->view('diarios/edit', [
            'title' => 'Editar Diário - Hermes',
            'pageTitle' => 'Editar Diário de Obra',
            'currentRoute' => 'diarios',
            'diario' => $diario,
            'obras' => $this->obrasList(),
        ]);
    }

    public function update(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $diario = Diario::find($id);
        if (!$diario) {
            App::abort(404, 'Diário não encontrado');
        }

        $data = $this->validate(Request::all(), [
            'obra_id' => 'required|integer|exists:obras,id',
            'data' => 'required|date',
            'titulo' => 'required|min:3|max:200',
            'descricao' => 'required|max:5000',
        ]);

        $diario->fill([
            'obra_id' => (int) $data['obra_id'],
            'data' => $data['data'],
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'],
        ]);
        $diario->save();

        Session::putFlash('success', 'Diário atualizado com sucesso!');
        App::redirect('/diarios');
    }

    public function destroy(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $diario = Diario::find($id);
        if (!$diario) {
            App::abort(404, 'Diário não encontrado');
        }

        $diario->delete();

        Session::putFlash('success', 'Diário excluído com sucesso!');
        App::redirect('/diarios');
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
