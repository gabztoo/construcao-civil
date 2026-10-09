<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hermes - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: #333;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border: 1px solid #444;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #e0e0e0;
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .logo p {
            color: #888;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #bbb;
            font-size: 0.85rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: #222;
            border: 1px solid #444;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #666;
            box-shadow: 0 0 0 3px rgba(100, 100, 100, 0.2);
        }

        .form-group input::placeholder {
            color: #666;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #555;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #666;
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #666;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #444;
        }

        .divider span {
            padding: 0 15px;
        }

        .footer-links {
            text-align: center;
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
        }

        .footer-links a {
            color: #888;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #aaa;
        }

        .error-message {
            background: #3d1a1a;
            border: 1px solid #5c2a2a;
            color: #f88;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Hermes</h1>
            <p>Sistema de Gestão</p>
        </div>

        <?php
        session_start();
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Exemplo de validação simples (substitua pela sua lógica real)
            if ($username === 'admin' && $password === 'admin') {
                $_SESSION['user'] = $username;
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Usuário ou senha inválidos';
            }
        }
        ?>

        <?php if ($error): ?>
            <div class="error-message show"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" placeholder="Digite seu usuário" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div class="divider">
            <span>ou</span>
        </div>

        <div class="footer-links">
            <a href="#">Esqueci a senha</a>
            <a href="#">Criar conta</a>
        </div>
    </div>
</body>
</html>