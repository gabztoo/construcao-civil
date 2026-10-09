<?php

class CategoriaFinanceira extends Model
{
    public static string $table = 'categorias_financeiras';
    public static string $primaryKey = 'id';

    protected array $fillable = ['nome', 'tipo'];

    public int $id = 0;
    public string $nome = '';
    public string $tipo = 'despesa';
    public string $created_at = '';
    public string $updated_at = '';
}
