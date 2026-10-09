<?php

class Fornecedor extends Model
{
    public static string $table = 'fornecedores';
    public static string $primaryKey = 'id';

    protected array $fillable = ['razao_social', 'cnpj_cpf', 'contato_nome', 'telefone', 'email', 'categoria'];

    public int $id = 0;
    public string $razao_social = '';
    public string $cnpj_cpf = '';
    public ?string $contato_nome = null;
    public ?string $telefone = null;
    public ?string $email = null;
    public ?string $categoria = null;
    public string $created_at = '';
    public string $updated_at = '';

    public const CATEGORIAS = [
        'materiais' => 'Materiais de Construção',
        'eletrica' => 'Elétrica',
        'hidraulica' => 'Hidráulica',
        'ferramentas' => 'Ferramentas',
        'servicos' => 'Serviços',
        'transporte' => 'Transporte',
        'diversos' => 'Diversos',
    ];
}
