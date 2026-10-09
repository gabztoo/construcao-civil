<?php

class AlocacaoController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $search = Request::get('search', '');
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search) {
            $where[] = '(f.nome LIKE ? OR o.nome LIKE ? OR o.codigo LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT COUNT(*) c FROM alocacoes a INNER JOIN funcionarios f ON f.id = a.funcionario_id INNER JOIN obras o ON o.id = a.obra_id {$whereSql}");
        $stmt->execute($params);
        $total = (int) $stmt->fetch()['c'];

        $offset = ($page - 1) * $perPage;
        $stmt = $pdo->prepare(
            "SELECT a.*, f.nome AS func_nome, f.cargo AS func_cargo, o.nome AS obra_nome, o.codigo AS obra_codigo
             FROM alocacoes a
             INNER JOIN funcionarios f ON f.id = a.funcionario_id
             INNER JOIN obras o ON o.id = a.obra_id
             {$whereSql}
             ORDER BY a.data_inicio DESC, a.id DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        $alocacoes = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $pagination = [
            'data' => $alocacoes,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($page * $perPage, $total),
        ];

        $this->view('alocacoes/index', [
            'title' => 'Alocações de Mão de Obra - Hermes',
            'pageTitle' => 'Alocações de Mão de Obra',
            'currentRoute' => 'alocacoes',
            'alocacoes' => $alocacoes,
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $this->view('alocacoes/create', [
            'title' => 'Nova Alocação - Hermes',
            'pageTitle' => 'Nova Alocação',
            'currentRoute' => 'alocacoes',
            'funcionarios' => $this->funcionariosAtivos(),
            'obras' => $this->obrasList(),
        ]);
    }

    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validate(Request::all(), [
            'funcionario_id' => 'required|integer|exists:funcionarios,id',
            'obra_id' => 'required|integer|exists:obras,id',
            'funcao' => 'nullable|max:120',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after:data_inicio',
        ]);

        Alocacao::create([
            'funcionario_id' => (int) $data['funcionario_id'],
            'obra_id' => (int) $data['obra_id'],
            'funcao' => $data['funcao'] ?? null,
            'data_inicio' => $data['data_inicio'],
            'data_fim' => $data['data_fim'] ?? null,
        ]);

        Session::putFlash('success', 'Alocação criada com sucesso!');
        App::redirect('/alocacoes');
    }

    public function edit(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $alocacao = Alocacao::find($id);
        if (!$alocacao) {
            App::abort(404, 'Alocação não encontrada');
        }

        $this->view('alocacoes/edit', [
            'title' => 'Editar Alocação - Hermes',
            'pageTitle' => 'Editar Alocação',
            'currentRoute' => 'alocacoes',
            'alocacao' => $alocacao,
            'funcionarios' => $this->funcionariosAtivos(),
            'obras' => $this->obrasList(),
        ]);
    }

    public function update(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $alocacao = Alocacao::find($id);
        if (!$alocacao) {
            App::abort(404, 'Alocação não encontrada');
        }

        $data = $this->validate(Request::all(), [
            'funcionario_id' => 'required|integer|exists:funcionarios,id',
            'obra_id' => 'required|integer|exists:obras,id',
            'funcao' => 'nullable|max:120',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after:data_inicio',
        ]);

        $alocacao->fill([
            'funcionario_id' => (int) $data['funcionario_id'],
            'obra_id' => (int) $data['obra_id'],
            'funcao' => $data['funcao'] ?? null,
            'data_inicio' => $data['data_inicio'],
            'data_fim' => $data['data_fim'] ?? null,
        ]);
        $alocacao->save();

        Session::putFlash('success', 'Alocação atualizada com sucesso!');
        App::redirect('/alocacoes');
    }

    public function destroy(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $alocacao = Alocacao::find($id);
        if (!$alocacao) {
            App::abort(404, 'Alocação não encontrada');
        }

        $alocacao->delete();

        Session::putFlash('success', 'Alocação excluída com sucesso!');
        App::redirect('/alocacoes');
    }

    private function verifyCsrf(): void
    {
        $token = Request::post('_token', '');
        if (!App::verifyCsrf((string) $token)) {
            App::abort(403, 'Token de segurança inválido.');
        }
    }

    private function funcionariosAtivos(): array
    {
        return Funcionario::query()->where('ativo', 1)->orderBy('nome', 'ASC')->get();
    }

    private function obrasList(): array
    {
        return Obra::query()->select(['id', 'codigo', 'nome'])->orderBy('nome', 'ASC')->get();
    }
}
