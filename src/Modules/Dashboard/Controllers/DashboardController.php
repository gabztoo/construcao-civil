<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->auth()->check() ?: App::redirect('/login');

        $pdo = Database::getInstance();

        $ativas = (int) $pdo->query("SELECT COUNT(*) c FROM obras WHERE status IN ('em_andamento','atrasada')")->fetch()['c'];
        $previsto = (float) $pdo->query("SELECT COALESCE(SUM(orcamento_total),0) t FROM obras")->fetch()['t'];

        $hasOc = $this->tableExists('ordens_compra');
        $realizado = 0.0;
        $ocsPendentes = null;
        if ($hasOc) {
            try {
                $realizado = (float) $pdo->query("SELECT COALESCE(SUM(valor_total),0) t FROM ordens_compra WHERE status IN ('aprovada','entregue','paga')")->fetch()['t'];
                $ocsPendentes = (int) $pdo->query("SELECT COUNT(*) c FROM ordens_compra WHERE status = 'pendente'")->fetch()['c'];
            } catch (Throwable $e) {
                $realizado = 0.0;
                $ocsPendentes = null;
            }
        }

        $alocacao = null;
        if ($this->tableExists('alocacoes')) {
            try {
                $alocacao = (int) $pdo->query("SELECT COUNT(DISTINCT funcionario_id) c FROM alocacoes")->fetch()['c'];
            } catch (Throwable $e) {
                $alocacao = null;
            }
        }

        $percentual = $previsto > 0 ? min(100, round($realizado / $previsto * 100)) : 0;

        $chart = $this->getChartData($pdo);
        $atividades = $this->getAtividades($pdo);
        $diarios = $this->getDiarios($pdo);

        $this->view('dashboard/index', [
            'title' => 'Dashboard - Hermes',
            'pageTitle' => 'Dashboard',
            'kpis' => [
                'ativas' => $ativas,
                'previsto' => $previsto,
                'realizado' => $realizado,
                'percentual' => $percentual,
                'ocs_pendentes' => $ocsPendentes,
                'alocacao' => $alocacao,
            ],
            'chart' => $chart,
            'atividades' => $atividades,
            'diarios' => $diarios,
        ]);
    }

    private function getChartData($pdo): array
    {
        $obras = $pdo->query(
            "SELECT o.id, o.codigo, o.nome, o.orcamento_total,
                    COALESCE((SELECT AVG(e.percentual_conclusao) FROM etapas_obra e WHERE e.obra_id = o.id), 0) AS fisico
             FROM obras o
             ORDER BY o.updated_at DESC
             LIMIT 5"
        )->fetchAll();

        $financeiro = array_fill(0, count($obras), 0);
        if ($obras && $this->tableExists('ordens_compra')) {
            try {
                $ids = implode(',', array_map('intval', array_column($obras, 'id')));
                $rows = $pdo->query(
                    "SELECT obra_id, COALESCE(SUM(CASE WHEN status IN ('aprovada','entregue','paga') THEN valor_total ELSE 0 END), 0) AS gasto
                     FROM ordens_compra WHERE obra_id IN ({$ids}) GROUP BY obra_id"
                )->fetchAll();
                $map = array_column($rows, 'gasto', 'obra_id');
                foreach ($obras as $i => $o) {
                    $orc = (float) $o['orcamento_total'];
                    $gasto = (float) ($map[$o['id']] ?? 0);
                    $financeiro[$i] = $orc > 0 ? min(100, round($gasto / $orc * 100)) : 0;
                }
            } catch (Throwable $e) {
                $financeiro = array_fill(0, count($obras), 0);
            }
        }

        return [
            'labels' => array_column($obras, 'codigo'),
            'fisico' => array_map(fn ($o) => round((float) $o['fisico'], 1), $obras),
            'financeiro' => $financeiro,
            'temDados' => (bool) $obras,
        ];
    }

    private function getAtividades($pdo): array
    {
        $lista = [];

        try {
            $etapas = $pdo->query(
                "SELECT e.nome AS item, e.status, e.updated_at, o.id AS obra_id, o.codigo, o.nome AS obra_nome
                 FROM etapas_obra e
                 INNER JOIN obras o ON o.id = e.obra_id
                 ORDER BY e.updated_at DESC
                 LIMIT 6"
            )->fetchAll();
            foreach ($etapas as $e) {
                $lista[] = ['tipo' => 'Etapa', 'item' => $e['item'], 'obra' => $e['obra_nome'], 'codigo' => $e['codigo'], 'obra_id' => $e['obra_id'], 'status' => $e['status'], 'quando' => $e['updated_at']];
            }
        } catch (Throwable $e) {
        }

        try {
            $obras = $pdo->query(
                "SELECT id, codigo, nome, status, created_at
                 FROM obras
                 ORDER BY created_at DESC
                 LIMIT 6"
            )->fetchAll();
            foreach ($obras as $o) {
                $lista[] = ['tipo' => 'Obra', 'item' => 'Obra criada', 'obra' => $o['nome'], 'codigo' => $o['codigo'], 'obra_id' => $o['id'], 'status' => $o['status'], 'quando' => $o['created_at']];
            }
        } catch (Throwable $e) {
        }

        usort($lista, fn ($a, $b) => strcmp($b['quando'], $a['quando']));
        return array_slice($lista, 0, 8);
    }

    private function getDiarios($pdo): ?array
    {
        if (!$this->tableExists('diarios_obra')) {
            return null;
        }
        try {
            return $pdo->query(
                "SELECT d.id, d.data, d.titulo, d.created_at, o.codigo, o.nome AS obra_nome
                 FROM diarios_obra d
                 INNER JOIN obras o ON o.id = d.obra_id
                 ORDER BY d.data DESC, d.id DESC
                 LIMIT 5"
            )->fetchAll();
        } catch (Throwable $e) {
            return null;
        }
    }

    private function tableExists(string $table): bool
    {
        $table = preg_replace('/[^a-z0-9_]/i', '', $table);
        $row = Database::getInstance()->query("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '{$table}'")->fetch();
        return (int) ($row['c'] ?? 0) > 0;
    }
}
