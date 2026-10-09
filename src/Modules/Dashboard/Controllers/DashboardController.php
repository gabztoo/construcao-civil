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
        ]);
    }

    private function tableExists(string $table): bool
    {
        $table = preg_replace('/[^a-z0-9_]/i', '', $table);
        $row = Database::getInstance()->query("SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '{$table}'")->fetch();
        return (int) ($row['c'] ?? 0) > 0;
    }
}
