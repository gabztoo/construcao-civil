<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Config/App.php';
require_once __DIR__ . '/../src/Core/Controller.php';
require_once __DIR__ . '/../src/Core/Model.php';
require_once __DIR__ . '/../src/Core/Request.php';
require_once __DIR__ . '/../src/Core/Response.php';
require_once __DIR__ . '/../src/Core/Session.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/Validator.php';
require_once __DIR__ . '/../src/Modules/Auth/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Modules/Auth/Controllers/UserController.php';
require_once __DIR__ . '/../src/Modules/Obras/Models/Obra.php';
require_once __DIR__ . '/../src/Modules/Obras/Controllers/ObraController.php';
require_once __DIR__ . '/../src/Modules/Obras/Models/Diario.php';
require_once __DIR__ . '/../src/Modules/Obras/Controllers/DiarioController.php';
require_once __DIR__ . '/../src/Modules/Dashboard/Controllers/DashboardController.php';
require_once __DIR__ . '/../src/Modules/Compras/Models/OrdemCompra.php';
require_once __DIR__ . '/../src/Modules/Compras/Models/Fornecedor.php';
require_once __DIR__ . '/../src/Modules/Compras/Controllers/OrdemCompraController.php';
require_once __DIR__ . '/../src/Modules/Compras/Controllers/FornecedorController.php';
require_once __DIR__ . '/../src/Modules/MaoDeObra/Models/Funcionario.php';
require_once __DIR__ . '/../src/Modules/MaoDeObra/Models/Alocacao.php';
require_once __DIR__ . '/../src/Modules/MaoDeObra/Controllers/FuncionarioController.php';
require_once __DIR__ . '/../src/Modules/MaoDeObra/Controllers/AlocacaoController.php';
require_once __DIR__ . '/../src/Modules/Almoxarifado/Models/Material.php';
require_once __DIR__ . '/../src/Modules/Almoxarifado/Controllers/AlmoxarifadoController.php';

Session::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/ibconstrucoes/public', '', $uri);
$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET' => [
        '/' => [AuthController::class, 'showLogin'],
        '/login' => [AuthController::class, 'showLogin'],
        '/logout' => [AuthController::class, 'logout'],
        '/dashboard' => [DashboardController::class, 'index'],
        '/obras' => [ObraController::class, 'index'],
        '/obras/create' => [ObraController::class, 'create'],
        '/obras/{id:\d+}' => [ObraController::class, 'show'],
        '/obras/{id:\d+}/edit' => [ObraController::class, 'edit'],
        '/usuarios' => [UserController::class, 'index'],
        '/diarios' => [DiarioController::class, 'index'],
        '/diarios/create' => [DiarioController::class, 'create'],
        '/diarios/{id:\d+}/edit' => [DiarioController::class, 'edit'],
        '/compras' => [OrdemCompraController::class, 'index'],
        '/compras/create' => [OrdemCompraController::class, 'create'],
        '/compras/{id:\d+}/edit' => [OrdemCompraController::class, 'edit'],
        '/funcionarios' => [FuncionarioController::class, 'index'],
        '/funcionarios/create' => [FuncionarioController::class, 'create'],
        '/funcionarios/{id:\d+}/edit' => [FuncionarioController::class, 'edit'],
        '/alocacoes' => [AlocacaoController::class, 'index'],
        '/alocacoes/create' => [AlocacaoController::class, 'create'],
        '/alocacoes/{id:\d+}/edit' => [AlocacaoController::class, 'edit'],
        '/almoxarifado' => [AlmoxarifadoController::class, 'index'],
        '/usuarios/create' => [UserController::class, 'create'],
        '/usuarios/{id:\d+}/edit' => [UserController::class, 'edit'],
    ],
    'POST' => [
        '/login' => [AuthController::class, 'login'],
        '/obras' => [ObraController::class, 'store'],
        '/obras/{id:\d+}' => [ObraController::class, 'update'],
        '/obras/{id:\d+}/delete' => [ObraController::class, 'destroy'],
        '/usuarios' => [UserController::class, 'store'],
        '/diarios' => [DiarioController::class, 'store'],
        '/diarios/{id:\d+}' => [DiarioController::class, 'update'],
        '/diarios/{id:\d+}/delete' => [DiarioController::class, 'destroy'],
        '/compras' => [OrdemCompraController::class, 'store'],
        '/compras/{id:\d+}' => [OrdemCompraController::class, 'update'],
        '/compras/{id:\d+}/delete' => [OrdemCompraController::class, 'destroy'],
        '/compras/{id:\d+}/status' => [OrdemCompraController::class, 'updateStatus'],
        '/fornecedores' => [FornecedorController::class, 'store'],
        '/fornecedores/{id:\d+}' => [FornecedorController::class, 'update'],
        '/fornecedores/{id:\d+}/delete' => [FornecedorController::class, 'destroy'],
        '/funcionarios' => [FuncionarioController::class, 'store'],
        '/funcionarios/{id:\d+}' => [FuncionarioController::class, 'update'],
        '/funcionarios/{id:\d+}/delete' => [FuncionarioController::class, 'destroy'],
        '/alocacoes' => [AlocacaoController::class, 'store'],
        '/alocacoes/{id:\d+}' => [AlocacaoController::class, 'update'],
        '/alocacoes/{id:\d+}/delete' => [AlocacaoController::class, 'destroy'],
        '/almoxarifado' => [AlmoxarifadoController::class, 'store'],
        '/almoxarifado/movimentacao' => [AlmoxarifadoController::class, 'movimentacao'],
        '/almoxarifado/{id:\d+}' => [AlmoxarifadoController::class, 'update'],
        '/usuarios/{id:\d+}' => [UserController::class, 'update'],
        '/usuarios/{id:\d+}/toggle' => [UserController::class, 'toggle'],
        '/usuarios/{id:\d+}/delete' => [UserController::class, 'destroy'],
    ],
];

function matchRoute(string $method, string $uri, array $routes): ?array
{
    $methodRoutes = $routes[$method] ?? [];
    
    foreach ($methodRoutes as $pattern => $handler) {
        $regex = '@^' . preg_replace('/\{(\w+)(?::([^}]+))?\}/', '(?P<$1>$2)', $pattern) . '$@';
        
        if (preg_match($regex, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return [$handler, $params];
        }
    }
    
    return null;
}

$matched = matchRoute($method, $uri, $routes);

if ($matched) {
    [$handler, $params] = $matched;
    [$controllerClass, $methodName] = $handler;
    try {
        $controller = new $controllerClass();
        $controller->$methodName(...$params);
    } catch (\Throwable $e) {
        error_log("ERRO FATAL: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
        error_log($e->getTraceAsString());
        http_response_code(500);
        echo "<h1>Error 500</h1><pre>" . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</pre>";
    }
    exit;
}

http_response_code(404);
require __DIR__ . '/../src/Views/errors/404.php';