<?php

abstract class Controller
{
    protected function view(string $path, array $data = []): void
    {
        extract($data);
        $file = __DIR__ . "/../../Views/{$path}.php";
        
        if (!file_exists($file)) {
            $srcDir = __DIR__ . "/../../";
            error_log("SRC dir: {$srcDir}");
            $listing = is_dir($srcDir) ? implode(', ', scandir($srcDir)) : 'NAO EXISTE';
            error_log("Conteudo src/: {$listing}");

            $alt = __DIR__ . "/../../views/{$path}.php";
            error_log("Tentando lowercase views: {$alt} -> " . (file_exists($alt) ? 'EXISTE' : 'NAO'));

            App::abort(500, "View not found: {$path}\nFile: {$file}\nsrc/: {$listing}\nlowercase views: " . (file_exists($alt) ? 'EXISTE' : 'NAO'));
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