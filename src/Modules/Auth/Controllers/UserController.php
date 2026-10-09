<?php

class UserController extends Controller
{
    private const PAPEIS = [
        'admin' => 'Administrador',
        'engenheiro' => 'Engenheiro',
        'mestre' => 'Mestre de Obras',
        'comprador' => 'Comprador',
        'financeiro' => 'Financeiro',
    ];

    public function index(): void
    {
        $this->adminOnly();

        $search = Request::get('search', '');
        $page = max(1, (int) Request::get('page', 1));
        $perPage = 10;

        $query = User::query()->orderBy('nome', 'ASC');

        if ($search) {
            $query->where('nome', 'LIKE', "%{$search}%");
        }

        $pagination = $query->paginate($perPage, $page);

        $this->view('usuarios/index', [
            'title' => 'Usuários - Hermes',
            'pageTitle' => 'Usuários',
            'currentRoute' => 'usuarios',
            'usuarios' => $pagination['data'],
            'pagination' => $pagination,
            'search' => $search,
            'papeis' => self::PAPEIS,
        ]);
    }

    public function create(): void
    {
        $this->adminOnly();

        $this->view('usuarios/create', [
            'title' => 'Novo Usuário - Hermes',
            'pageTitle' => 'Novo Usuário',
            'currentRoute' => 'usuarios',
            'papeis' => self::PAPEIS,
        ]);
    }

    public function store(): void
    {
        $this->adminOnly();
        $this->verifyCsrf();

        $data = $this->validate(Request::all(), [
            'nome' => 'required|min:3|max:120',
            'email' => 'required|email|max:180|unique:usuarios,email',
            'papel' => 'required|in:admin,engenheiro,mestre,comprador,financeiro',
            'senha' => 'required|min:6|max:100',
        ]);

        $user = User::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'papel' => $data['papel'],
            'senha_hash' => password_hash($data['senha'], PASSWORD_ARGON2ID),
            'ativo' => 1,
        ]);

        Session::putFlash('success', 'Usuário criado com sucesso!');
        App::redirect('/usuarios');
    }

    public function edit(int $id): void
    {
        $this->adminOnly();

        $user = User::find($id);
        if (!$user) {
            App::abort(404, 'Usuário não encontrado');
        }

        $this->view('usuarios/edit', [
            'title' => 'Editar Usuário - Hermes',
            'pageTitle' => 'Editar Usuário',
            'currentRoute' => 'usuarios',
            'usuario' => $user,
            'papeis' => self::PAPEIS,
        ]);
    }

    public function update(int $id): void
    {
        $this->adminOnly();
        $this->verifyCsrf();

        $user = User::find($id);
        if (!$user) {
            App::abort(404, 'Usuário não encontrado');
        }

        $data = $this->validate(Request::all(), [
            'nome' => 'required|min:3|max:120',
            'email' => "required|email|max:180|unique:usuarios,email,{$id},id",
            'papel' => 'required|in:admin,engenheiro,mestre,comprador,financeiro',
            'senha' => 'min:6|max:100',
            'ativo' => 'boolean',
        ]);

        $novoPapel = $data['papel'];
        $novoAtivo = filter_var($data['ativo'] ?? '1', FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        if ($this->isLastAdminProtecting($user, $novoPapel, $novoAtivo)) {
            Session::putFlash('error', 'Não é possível remover o acesso do último administrador.');
            App::redirect('/usuarios/' . $id . '/edit');
        }

        $user->nome = $data['nome'];
        $user->email = $data['email'];
        $user->papel = $novoPapel;
        $user->ativo = $novoAtivo;

        if (!empty($data['senha'])) {
            $user->setSenha($data['senha']);
        }

        $user->save();

        Session::putFlash('success', 'Usuário atualizado com sucesso!');
        App::redirect('/usuarios');
    }

    public function toggle(int $id): void
    {
        $this->adminOnly();
        $this->verifyCsrf();

        $user = User::find($id);
        if (!$user) {
            App::abort(404, 'Usuário não encontrado');
        }

        if ($user->ativo && $this->isLastAdmin($user)) {
            Session::putFlash('error', 'Não é possível desativar o último administrador.');
            App::redirect('/usuarios');
        }

        $user->ativo = ((int) $user->ativo) === 1 ? 0 : 1;
        $user->save();

        Session::putFlash('success', $user->ativo ? 'Usuário ativado.' : 'Usuário desativado.');
        App::redirect('/usuarios');
    }

    public function destroy(int $id): void
    {
        $this->adminOnly();
        $this->verifyCsrf();

        $user = User::find($id);
        if (!$user) {
            App::abort(404, 'Usuário não encontrado');
        }

        if ($user->id === Auth::getInstance()->id()) {
            Session::putFlash('error', 'Você não pode excluir a própria conta.');
            App::redirect('/usuarios');
        }

        if ($this->isLastAdmin($user)) {
            Session::putFlash('error', 'Não é possível excluir o último administrador.');
            App::redirect('/usuarios');
        }

        $user->delete();

        Session::putFlash('success', 'Usuário excluído com sucesso!');
        App::redirect('/usuarios');
    }

    private function adminOnly(): void
    {
        Auth::getInstance()->check() ?: App::redirect('/login');
        Auth::getInstance()->hasRole('admin') ?: App::abort(403, 'Acesso restrito a administradores.');
    }

    private function verifyCsrf(): void
    {
        $token = Request::post('_token', '');
        if (!App::verifyCsrf((string) $token)) {
            App::abort(403, 'Token de segurança inválido.');
        }
    }

    private function isLastAdmin(User $user): bool
    {
        if ($user->papel !== 'admin') {
            return false;
        }

        return User::query()->where('papel', 'admin')->count() <= 1;
    }

    private function isLastAdminProtecting(User $user, string $novoPapel, int $novoAtivo): bool
    {
        if ($user->papel !== 'admin') {
            return false;
        }

        $perdeAdmin = $novoPapel !== 'admin' || $novoAtivo === 0;
        if (!$perdeAdmin) {
            return false;
        }

        return User::query()->where('papel', 'admin')->where('ativo', 1)->count() <= 1;
    }
}
