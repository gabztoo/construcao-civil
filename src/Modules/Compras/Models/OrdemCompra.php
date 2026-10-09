<?php

class OrdemCompra extends Model
{
    public static string $table = 'ordens_compra';
    public static string $primaryKey = 'id';

    protected array $fillable = ['obra_id', 'numero', 'fornecedor', 'valor_total', 'status', 'data_pedido', 'observacoes'];

    public int $id = 0;
    public int $obra_id = 0;
    public string $numero = '';
    public ?string $fornecedor = null;
    public float $valor_total = 0;
    public string $status = 'pendente';
    public ?string $data_pedido = null;
    public ?string $observacoes = null;
    public string $created_at = '';
    public string $updated_at = '';

    public const STATUS = [
        'pendente' => 'Pendente',
        'aprovada' => 'Aprovada',
        'entregue' => 'Entregue',
        'paga' => 'Paga',
        'cancelada' => 'Cancelada',
    ];

    public static function badge(string $status): string
    {
        return match ($status) {
            'pendente' => 'bg-amber-900/50 text-amber-300',
            'aprovada' => 'bg-blue-900/50 text-blue-300',
            'entregue' => 'bg-violet-900/50 text-violet-300',
            'paga' => 'bg-emerald-900/50 text-emerald-300',
            'cancelada' => 'bg-red-900/50 text-red-300',
            default => 'bg-slate-700/50 text-slate-300',
        };
    }
}
