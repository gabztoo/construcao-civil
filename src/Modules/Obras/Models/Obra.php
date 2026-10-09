<?php

class Obra extends Model
{
    public static string $table = 'obras';
    public static string $primaryKey = 'id';
    
    protected array $fillable = [
        'codigo', 'nome', 'cliente_id', 'endereco', 'latitude', 'longitude',
        'orcamento_total', 'data_inicio', 'data_fim_prevista', 'data_fim_real',
        'status', 'responsavel_tecnico', 'crea_rrt', 'observacoes'
    ];

    public int $id = 0;
    public string $codigo = '';
    public string $nome = '';
    public ?int $cliente_id = null;
    public ?string $endereco = null;
    public ?string $latitude = null;
    public ?string $longitude = null;
    public float $orcamento_total = 0;
    public ?string $data_inicio = null;
    public ?string $data_fim_prevista = null;
    public ?string $data_fim_real = null;
    public string $status = 'planejamento';
    public ?string $responsavel_tecnico = null;
    public ?string $crea_rrt = null;
    public ?string $observacoes = null;
    public string $created_at = '';
    public string $updated_at = '';

    public function etapas(): array
    {
        $sql = "SELECT * FROM etapas_obra WHERE obra_id = ? ORDER BY ordem";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function progresso(): float
    {
        $etapas = $this->etapas();
        if (empty($etapas)) return 0;
        
        $total = array_sum(array_column($etapas, 'percentual_conclusao'));
        return round($total / count($etapas), 2);
    }

    public function isAtrasada(): bool
    {
        if ($this->status === 'concluida') return false;
        if (!$this->data_fim_prevista) return false;
        return strtotime($this->data_fim_prevista) < strtotime('today');
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'planejamento' => 'bg-slate-500',
            'em_andamento' => 'bg-blue-500',
            'paralisada' => 'bg-amber-500',
            'concluida' => 'bg-emerald-500',
            'atrasada' => 'bg-red-500',
            default => 'bg-slate-500',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'planejamento' => 'Planejamento',
            'em_andamento' => 'Em Andamento',
            'paralisada' => 'Paralisada',
            'concluida' => 'Concluída',
            'atrasada' => 'Atrasada',
            default => 'Desconhecido',
        };
    }
}