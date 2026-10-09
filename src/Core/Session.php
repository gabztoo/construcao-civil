<?php

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $lifetime = (int) (App::env('SESSION_LIFETIME', 7200));
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => App::isProduction(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flush(): void
    {
        self::start();
        $_SESSION = [];
    }

    public static function regenerate(bool $deleteOld = true): void
    {
        self::start();
        session_regenerate_id($deleteOld);
    }

    public static function id(): string
    {
        self::start();
        return session_id();
    }

    public static function putFlash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key): mixed
    {
        self::start();
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function setOld(array $data): void
    {
        self::start();
        $_SESSION['_old'] = $data;
    }

    public static function getOld(string $key, mixed $default = ''): mixed
    {
        self::start();
        return $_SESSION['_old'][$key] ?? $default;
    }

    public static function clearOld(): void
    {
        self::start();
        unset($_SESSION['_old']);
    }

    public static function setErrors(array $errors): void
    {
        self::start();
        $_SESSION['_errors'] = $errors;
    }

    public static function getErrors(): array
    {
        self::start();
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
        return $errors;
    }

    public static function hasErrors(): bool
    {
        self::start();
        return !empty($_SESSION['_errors']);
    }
}