<?php

class Diario extends Model
{
    public static string $table = 'diarios_obra';
    public static string $primaryKey = 'id';

    protected array $fillable = ['obra_id', 'data', 'titulo', 'descricao'];

    public int $id = 0;
    public int $obra_id = 0;
    public string $data = '';
    public string $titulo = '';
    public ?string $descricao = null;
    public string $created_at = '';
    public string $updated_at = '';
}
