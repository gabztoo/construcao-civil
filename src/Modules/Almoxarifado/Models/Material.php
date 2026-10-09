<?php

class Material extends Model
{
    public static string $table = 'materiais';
    public static string $primaryKey = 'id';

    protected array $fillable = ['codigo_sku', 'nome', 'categoria', 'unidade_medida', 'estoque_minimo'];

    public int $id = 0;
    public string $codigo_sku = '';
    public string $nome = '';
    public string $categoria = 'diversos';
    public string $unidade_medida = 'UN';
    public float $estoque_minimo = 0;
    public string $created_at = '';
    public string $updated_at = '';

    public const CATEGORIAS = [
        'eletrica' => 'Elétrica',
        'hidraulica' => 'Hidráulica',
        'alvenaria' => 'Alvenaria',
        'ferramentas' => 'Ferramentas',
        'estrutural' => 'Estrutural',
        'pintura' => 'Pintura',
        'diversos' => 'Diversos',
    ];

    public const UNIDADES = [
        'KG' => 'KG',
        'UN' => 'UN',
        'M2' => 'M2',
        'M3' => 'M3',
        'Saco' => 'Saco',
        'L' => 'L',
        'CX' => 'CX',
    ];
}
