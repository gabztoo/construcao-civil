<?php

abstract class Controller
{
    protected function view(string $path, array $data = []): void
    {
        extract($data);
        $file = __DIR__ . "/../../Views/{$path}.php";
        
        if (!file_exists($file)) {
            error_log("VIEW NAO ENCONTRADA: {$file}");
            error_log("__DIR__: " . __DIR__);
            error_log("cwd: " . getcwd());
            $viewsDir = __DIR__ . "/../../Views/";
            error_log("Views dir existe: " . (is_dir($viewsDir) ? 'SIM' : 'NAO'));
            if (is_dir($viewsDir)) {
                error_log("Conteudo: " . implode(', ', scandir($viewsDir)));
            }
            App::abort(500, "View not found: {$path}\nFile: {$file}\nDir exists: " . (is_dir($viewsDir) ? 'yes' : 'no'));
        }

        require $file;
    }

    protected function json(array $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function validate(array $data, array $rules): array
    {
        $validator = new Validator($data, $rules);
        if (!$validator->passes()) {
            $_SESSION['_errors'] = $validator->errors();
            $_SESSION['_old'] = $data;
            App::back();
        }
        return $validator->validated();
    }

    protected function auth(): Auth
    {
        return Auth::getInstance();
    }

    protected function user(): ?array
    {
        return $this->auth()->user();
    }
}