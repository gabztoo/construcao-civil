<?php

class Funcionario extends Model
{
    public static string $table = 'funcionarios';
    public static string $primaryKey = 'id';

    protected array $fillable = ['nome', 'cargo', 'cpf', 'telefone', 'ativo'];

    public int $id = 0;
    public string $nome = '';
    public ?string $cargo = null;
    public ?string $cpf = null;
    public ?string $telefone = null;
    public int $ativo = 1;
    public string $created_at = '';
    public string $updated_at = '';
}
