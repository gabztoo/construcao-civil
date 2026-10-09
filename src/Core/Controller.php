<?php

abstract class Controller
{
    protected function view(string $path, array $data = []): void
    {
        extract($data);
        $file = __DIR__ . "/../Views/{$path}.php";
        
        if (!file_exists($file)) {
            App::abort(500, "View not found: {$path}");
        }

        ob_start();
        require $file;
        $content = ob_get_clean();

        $layout = str_starts_with($path, 'auth/') || str_starts_with($path, 'errors/')
            ? 'layouts/auth'
            : 'layouts/app';

        $layoutFile = __DIR__ . "/../Views/{$layout}.php";
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
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