<?php

class LancamentoFinanceiro extends Model
{
    public static string $table = 'lancamentos_financeiros';
    public static string $primaryKey = 'id';

    protected array $fillable = ['obra_id', 'tipo', 'descricao', 'valor', 'data_vencimento', 'data_pagamento', 'status', 'categoria_id', 'comprovante_path'];

    public int $id = 0;
    public ?int $obra_id = null;
    public string $tipo = 'despesa';
    public string $descricao = '';
    public float $valor = 0;
    public string $data_vencimento = '';
    public ?string $data_pagamento = null;
    public string $status = 'pendente';
    public ?int $categoria_id = null;
    public ?string $comprovante_path = null;
    public string $created_at = '';
    public string $updated_at = '';

    public const STATUS = [
        'pendente' => 'Pendente',
        'pago' => 'Pago',
        'atrasado' => 'Atrasado',
    ];

    public static function badge(string $status): string
    {
        return match ($status) {
            'pendente' => 'bg-amber-900/50 text-amber-300',
            'pago' => 'bg-emerald-900/50 text-emerald-300',
            'atrasado' => 'bg-red-900/50 text-red-300',
            default => 'bg-slate-700/50 text-slate-300',
        };
    }

    public static function atualizarAtrasados(): void
    {
        Database::getInstance()->exec(
            "UPDATE lancamentos_financeiros SET status = 'atrasado'
             WHERE status = 'pendente' AND data_vencimento < CURDATE()"
        );
    }
}
