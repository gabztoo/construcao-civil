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
require_once __DIR__ . '/../src/Modules/Auth/Models/User.php';
require_once __DIR__ . '/../src/Modules/Auth/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Modules/Obras/Models/Obra.php';
require_once __DIR__ . '/../src/Modules/Obras/Controllers/ObraController.php';

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
        '/dashboard' => [ObraController::class, 'index'],
        '/obras' => [ObraController::class, 'index'],
        '/obras/create' => [ObraController::class, 'create'],
        '/obras/{id:\d+}' => [ObraController::class, 'show'],
        '/obras/{id:\d+}/edit' => [ObraController::class, 'edit'],
    ],
    'POST' => [
        '/login' => [AuthController::class, 'login'],
        '/obras' => [ObraController::class, 'store'],
        '/obras/{id:\d+}' => [ObraController::class, 'update'],
        '/obras/{id:\d+}/delete' => [ObraController::class, 'destroy'],
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