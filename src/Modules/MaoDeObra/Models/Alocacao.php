<?php

class Alocacao extends Model
{
    public static string $table = 'alocacoes';
    public static string $primaryKey = 'id';

    protected array $fillable = ['funcionario_id', 'obra_id', 'funcao', 'data_inicio', 'data_fim'];

    public int $id = 0;
    public int $funcionario_id = 0;
    public int $obra_id = 0;
    public ?string $funcao = null;
    public string $data_inicio = '';
    public ?string $data_fim = null;
    public string $created_at = '';
    public string $updated_at = '';
}
