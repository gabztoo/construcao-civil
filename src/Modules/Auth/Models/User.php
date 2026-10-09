<?php

class User extends Model
{
    protected static string $table = 'usuarios';
    protected static string $primaryKey = 'id';
    
    protected array $fillable = [
        'nome', 'email', 'senha_hash', 'papel', 'ativo'
    ];
    
    protected array $hidden = ['senha_hash'];
    
    public int $id = 0;
    public string $nome = '';
    public string $email = '';
    public string $senha_hash = '';
    public string $papel = 'engenheiro';
    public bool $ativo = true;
    public ?string $ultimo_login = null;
    public string $created_at = '';
    public string $updated_at = '';

    public function setSenha(string $senha): void
    {
        $this->senha_hash = password_hash($senha, PASSWORD_ARGON2ID);
    }

    public function checkSenha(string $senha): bool
    {
        return password_verify($senha, $this->senha_hash);
    }

    public static function findByEmail(string $email): ?self
    {
        return static::query()->where('email', $email)->first();
    }

    public function isAdmin(): bool
    {
        return $this->papel === 'admin';
    }

    public function can(string $permission): bool
    {
        return Auth::getInstance()->can($permission);
    }
}