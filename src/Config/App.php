<?php

class App
{
    public static function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }

    public static function isProduction(): bool
    {
        return (self::env('APP_ENV') ?? 'development') === 'production';
    }

    public static function isDebug(): bool
    {
        return self::env('APP_DEBUG', false) === true;
    }

    public static function url(string $path = ''): string
    {
        $base = rtrim(self::env('APP_URL', ''), '/');
        return $base . '/' . ltrim($path, '/');
    }

    public static function asset(string $path): string
    {
        return self::url('assets/' . ltrim($path, '/'));
    }

    public static function csrfToken(): string
    {
        $name = self::env('CSRF_TOKEN_NAME', 'hermes_csrf');
        if (empty($_SESSION[$name])) {
            $_SESSION[$name] = bin2hex(random_bytes(32));
        }
        return $_SESSION[$name];
    }

    public static function verifyCsrf(string $token): bool
    {
        $name = self::env('CSRF_TOKEN_NAME', 'hermes_csrf');
        return isset($_SESSION[$name]) && hash_equals($_SESSION[$name], $token);
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['_old'][$key] ?? $default;
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function redirect(string $path, int $code = 302): never
    {
        header('Location: ' . self::url($path), true, $code);
        exit;
    }

    public static function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? self::url('/');
        header('Location: ' . $referer);
        exit;
    }

    public static function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        if (self::isDebug()) {
            echo "<h1>Error {$code}</h1><pre>{$message}</pre>";
        } else {
            echo "<h1>Error {$code}</h1><p>Ocorreu um erro. Tente novamente.</p>";
        }
        exit;
    }
}