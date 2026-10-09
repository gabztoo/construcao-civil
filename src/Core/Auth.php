<?php

require_once __DIR__ . '/../Modules/Auth/Models/User.php';

class Auth
{
    private static ?self $instance = null;
    private ?array $user = null;
    private bool $checked = false;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function check(): bool
    {
        if ($this->checked) {
            return $this->user !== null;
        }

        $this->checked = true;
        Session::start();

        if (isset($_SESSION['user_id'])) {
            $user = User::find($_SESSION['user_id']);
            if ($user && $user->ativo) {
                $this->user = $user->toArray();
                return true;
            }
            $this->logout();
        }

        return false;
    }

    public function user(): ?array
    {
        $this->check();
        return $this->user;
    }

    public function id(): ?int
    {
        return $this->user()['id'] ?? null;
    }

    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        $user = User::query()->where('email', $email)->first();

        if ($user && password_verify($password, $user->senha_hash)) {
            if (!$user->ativo) {
                return false;
            }

            Session::regenerate();
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_nome'] = $user->nome;
            $_SESSION['user_papel'] = $user->papel;

            $user->ultimo_login = date('Y-m-d H:i:s');
            $user->save();

            $this->user = $user->toArray();
            $this->checked = true;

            return true;
        }

        return false;
    }

    public function login(array $user): void
    {
        Session::regenerate();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_papel'] = $user['papel'];
        $this->user = $user;
        $this->checked = true;
    }

    public function logout(): void
    {
        Session::flush();
        Session::regenerate();
        $this->user = null;
        $this->checked = false;
    }

    public function hasRole(string|array $roles): bool
    {
        $user = $this->user();
        if (!$user) return false;

        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($user['papel'], $roles);
    }

    public function can(string $permission): bool
    {
        $rolePermissions = [
            'admin' => ['*'],
            'engenheiro' => ['obras.ver', 'obras.criar', 'obras.editar', 'obras.excluir', 'etapas.gerenciar', 'rdo.criar', 'relatorios.ver'],
            'mestre' => ['obras.ver', 'etapas.ver', 'rdo.criar', 'rdo.ver'],
            'comprador' => ['obras.ver', 'fornecedores.ver', 'cotacoes.gerenciar', 'ordens.gerenciar'],
            'financeiro' => ['obras.ver', 'financeiro.ver', 'contas.pagar', 'contas.receber', 'medicoes.gerenciar'],
        ];

        $user = $this->user();
        if (!$user) return false;

        $permissions = $rolePermissions[$user['papel']] ?? [];
        
        if (in_array('*', $permissions)) return true;
        
        return in_array($permission, $permissions);
    }

    public function guest(): bool
    {
        return !$this->check();
    }
}