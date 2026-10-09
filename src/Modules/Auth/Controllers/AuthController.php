<?php

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::getInstance()->check()) {
            App::redirect('/dashboard');
        }
        
        $this->view('auth/login', [
            'title' => 'Login - Hermes',
        ]);
    }

    public function login(): void
    {
        $data = $this->validate(Request::all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $auth = Auth::getInstance();

        error_log("=== LOGIN DEBUG ===");
        error_log("Email recebido: " . $data['email']);
        error_log("DB Name: " . ($_ENV['DB_NAME'] ?? 'NAO DEFINIDO'));
        error_log("DB Host: " . ($_ENV['DB_HOST'] ?? 'NAO DEFINIDO'));

        try {
            $user = \App\Modules\Auth\Models\User::query()->where('email', $data['email'])->first();
            error_log("User encontrado: " . ($user ? 'SIM (id=' . $user->id . ')' : 'NAO'));

            if ($user) {
                error_log("Hash no banco: " . $user->senha_hash);
                error_log("Ativo: " . ($user->ativo ? '1' : '0'));
                error_log("password_verify: " . (password_verify($data['password'], $user->senha_hash) ? 'TRUE' : 'FALSE'));
                error_log("PHP argon2id suportado: " . (in_array('argon2id', password_algos()) ? 'SIM' : 'NAO'));
            }
        } catch (\Throwable $e) {
            error_log("ERRO na query: " . $e->getMessage());
        }

        $result = $auth->attempt($data['email'], $data['password']);
        error_log("Attempt resultado: " . ($result ? 'TRUE (login ok)' : 'FALSE (falhou)'));
        error_log("===================");

        if ($result) {
            App::redirect('/dashboard');
        }

        Session::setFlash('error', 'E-mail ou senha inválidos');
        App::redirect('/login');
    }

    public function logout(): void
    {
        Auth::getInstance()->logout();
        App::redirect('/login');
    }

    public function register(): void
    {
        // Apenas admin pode criar usuários - implementar depois
        App::abort(403);
    }
}