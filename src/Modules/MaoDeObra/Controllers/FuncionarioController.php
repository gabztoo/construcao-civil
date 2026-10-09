<?php

class FuncionarioController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $search = Request::get('search', '');
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 10;

        $query = Funcionario::query()->orderBy('nome', 'ASC');

        if ($search) {
            $query->where('nome', 'LIKE', "%{$search}%");
        }

        $pagination = $query->paginate($perPage, $page);

        $this->view('funcionarios/index', [
            'title' => 'Mão de Obra - Hermes',
            'pageTitle' => 'Mão de Obra',
            'currentRoute' => 'funcionarios',
            'funcionarios' => $pagination['data'],
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $this->view('funcionarios/create', [
            'title' => 'Novo Funcionário - Hermes',
            'pageTitle' => 'Novo Funcionário',
            'currentRoute' => 'funcionarios',
        ]);
    }

    public function store(): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $data = $this->validate(Request::all(), [
            'nome' => 'required|min:3|max:120',
            'cargo' => 'nullable|max:120',
            'cpf' => 'nullable|cpf_cnpj|max:14',
            'telefone' => 'nullable|max:20',
        ]);

        Funcionario::create([
            'nome' => $data['nome'],
            'cargo' => $data['cargo'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'ativo' => 1,
        ]);

        Session::putFlash('success', 'Funcionário criado com sucesso!');
        App::redirect('/funcionarios');
    }

    public function edit(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $funcionario = Funcionario::find($id);
        if (!$funcionario) {
            App::abort(404, 'Funcionário não encontrado');
        }

        $this->view('funcionarios/edit', [
            'title' => 'Editar Funcionário - Hermes',
            'pageTitle' => 'Editar Funcionário',
            'currentRoute' => 'funcionarios',
            'funcionario' => $funcionario,
        ]);
    }

    public function update(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $funcionario = Funcionario::find($id);
        if (!$funcionario) {
            App::abort(404, 'Funcionário não encontrado');
        }

        $data = $this->validate(Request::all(), [
            'nome' => 'required|min:3|max:120',
            'cargo' => 'nullable|max:120',
            'cpf' => 'nullable|cpf_cnpj|max:14',
            'telefone' => 'nullable|max:20',
            'ativo' => 'required|boolean',
        ]);

        $funcionario->fill([
            'nome' => $data['nome'],
            'cargo' => $data['cargo'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'ativo' => (int) $data['ativo'],
        ]);
        $funcionario->save();

        Session::putFlash('success', 'Funcionário atualizado com sucesso!');
        App::redirect('/funcionarios');
    }

    public function destroy(int $id): void
    {
        $this->auth()->check() ?: App::redirect('/login');
        $this->verifyCsrf();

        $funcionario = Funcionario::find($id);
        if (!$funcionario) {
            App::abort(404, 'Funcionário não encontrado');
        }

        $funcionario->delete();

        Session::putFlash('success', 'Funcionário excluído com sucesso!');
        App::redirect('/funcionarios');
    }

    private function verifyCsrf(): void
    {
        $token = Request::post('_token', '');
        if (!App::verifyCsrf((string) $token)) {
            App::abort(403, 'Token de segurança inválido.');
        }
    }
}
