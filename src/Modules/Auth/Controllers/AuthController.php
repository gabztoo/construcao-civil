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
        
        if ($auth->attempt($data['email'], $data['password'])) {
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