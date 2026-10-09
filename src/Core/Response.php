<?php

class Response
{
    public static function json(array $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success(array $data = [], string $message = 'Sucesso', int $code = 200): never
    {
        self::json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }

    public static function error(string $message = 'Erro', array $errors = [], int $code = 400): never
    {
        self::json(['success' => false, 'message' => $message, 'errors' => $errors], $code);
    }

    public static function notFound(string $message = 'Recurso não encontrado'): never
    {
        self::error($message, [], 404);
    }

    public static function unauthorized(string $message = 'Não autorizado'): never
    {
        self::error($message, [], 401);
    }

    public static function forbidden(string $message = 'Acesso negado'): never
    {
        self::error($message, [], 403);
    }

    public static function validationError(array $errors): never
    {
        self::json(['success' => false, 'message' => 'Dados inválidos', 'errors' => $errors], 422);
    }
}