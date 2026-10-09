<?php
$currentRoute = 'dashboard';
$pageTitle = 'Dashboard';
?>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <!-- Obras Ativas -->
    <div class="kpi-card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-400">Obras Ativas</p>
            <span class="w-9 h-9 rounded-lg bg-blue-900/40 flex items-center justify-center text-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </span>
        </div>
        <p class="text-3xl font-bold text-white mt-2"><?= $kpis['ativas'] ?></p>
        <p class="text-xs text-slate-500 mt-1">Em execução ou atrasadas</p>
    </div>

    <!-- Custo Previsto vs Realizado -->
    <div class="kpi-card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-400">Custo Previsto vs Realizado</p>
            <span class="w-9 h-9 rounded-lg bg-emerald-900/40 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>
        <p class="text-xl font-bold text-white mt-2">R$ <?= number_format($kpis['realizado'], 2, ',', '.') ?></p>
        <p class="text-xs text-slate-500 mt-1">de R$ <?= number_format($kpis['previsto'], 2, ',', '.') ?> previstos</p>
        <div class="mt-3 h-2 bg-slate-800 rounded-full overflow-hidden" role="progressbar" aria-valuenow="<?= $kpis['percentual'] ?>" aria-valuemin="0" aria-valuemax="100">
            <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: <?= $kpis['percentual'] ?>%"></div>
        </div>
    </div>

    <!-- OCs Pendentes -->
    <div class="kpi-card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-400">OCs Pendentes</p>
            <span class="w-9 h-9 rounded-lg bg-amber-900/40 flex items-center justify-center text-amber-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </span>
        </div>
        <?php if ($kpis['ocs_pendentes'] === null): ?>
            <p class="text-3xl font-bold text-slate-500 mt-2">—</p>
            <p class="text-xs text-slate-500 mt-1">Módulo Compras em desenvolvimento</p>
        <?php else: ?>
            <p class="text-3xl font-bold text-white mt-2"><?= $kpis['ocs_pendentes'] ?></p>
            <p class="text-xs text-slate-500 mt-1">Aguardando aprovação/entrega</p>
        <?php endif; ?>
    </div>

    <!-- Alocação Mão de Obra -->
    <div class="kpi-card">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-400">Alocação Mão de Obra</p>
            <span class="w-9 h-9 rounded-lg bg-violet-900/40 flex items-center justify-center text-violet-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </span>
        </div>
        <?php if ($kpis['alocacao'] === null): ?>
            <p class="text-3xl font-bold text-slate-500 mt-2">—</p>
            <p class="text-xs text-slate-500 mt-1">Módulo Mão de Obra em desenvolvimento</p>
        <?php else: ?>
            <p class="text-3xl font-bold text-white mt-2"><?= $kpis['alocacao'] ?></p>
            <p class="text-xs text-slate-500 mt-1">Profissionais alocados</p>
        <?php endif; ?>
    </div>
</div>

<!-- Gráfico + Atividades -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
    <!-- Gráfico físico x financeiro -->
    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-white">Progresso Físico x Financeiro</h3>
                <p class="text-xs text-slate-500">Últimas 5 obras (%)</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5 text-slate-400"><span class="w-3 h-3 rounded-sm bg-blue-500"></span> Físico</span>
                <span class="flex items-center gap-1.5 text-slate-400"><span class="w-3 h-3 rounded-sm bg-emerald-500"></span> Financeiro</span>
            </div>
        </div>
        <?php if (!$chart['temDados']): ?>
            <p class="text-sm text-slate-500 text-center py-10">Nenhuma obra cadastrada ainda.</p>
        <?php else: ?>
            <div class="h-64"><canvas id="chartFisicoFinanceiro" aria-label="Gráfico de progresso físico e financeiro" role="img"></canvas></div>
        <?php endif; ?>
    </div>

    <!-- Últimas atividades -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-white mb-4">Últimas Atividades</h3>
        <?php if (empty($atividades)): ?>
            <p class="text-sm text-slate-500 text-center py-10">Nenhuma atividade registrada.</p>
        <?php else: ?>
            <ul class="space-y-3">
                <?php foreach ($atividades as $a): ?>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full flex-shrink-0 <?= $a['tipo'] === 'Obra' ? 'bg-blue-500' : 'bg-emerald-500' ?>"></span>
                        <div class="min-w-0">
                            <p class="text-sm text-slate-300 truncate">
                                <?= htmlspecialchars($a['item']) ?>
                                <span class="text-slate-500">&middot;</span>
                                <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($a['codigo']) ?></span>
                            </p>
                            <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($a['obra']) ?> &middot; <?= htmlspecialchars($a['quando']) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- Diários de Obra -->
<div class="bg-slate-900 border border-slate-800 rounded-xl p-5 mt-4">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-white">Diários de Obra</h3>
            <p class="text-xs text-slate-500">Últimos registros</p>
        </div>
    </div>
    <?php if ($diarios === null): ?>
        <p class="text-sm text-slate-500 text-center py-6">Módulo de Diários de Obra em desenvolvimento.</p>
    <?php elseif (empty($diarios)): ?>
        <p class="text-sm text-slate-500 text-center py-6">Nenhum diário registrado ainda.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 border-b border-slate-800">
                        <th class="py-2 pr-4">Data</th>
                        <th class="py-2 pr-4">Obra</th>
                        <th class="py-2 pr-4">Título</th>
                        <th class="py-2">Registrado em</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php foreach ($diarios as $d): ?>
                        <tr class="text-slate-300">
                            <td class="py-2.5 pr-4 font-mono text-xs"><?= htmlspecialchars($d['data']) ?></td>
                            <td class="py-2.5 pr-4 truncate max-w-[180px]"><span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($d['codigo']) ?></span> <?= htmlspecialchars($d['obra_nome']) ?></td>
                            <td class="py-2.5 pr-4 truncate max-w-[240px]"><?= htmlspecialchars($d['titulo'] ?? '') ?></td>
                            <td class="py-2.5 text-xs text-slate-500"><?= htmlspecialchars($d['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php if ($chart['temDados']): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('chartFisicoFinanceiro');
    if (!el || typeof Chart === 'undefined') return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart['labels']) ?>,
            datasets: [
                { label: 'Físico', data: <?= json_encode($chart['fisico']) ?>, backgroundColor: 'rgba(59,130,246,0.7)', borderRadius: 4 },
                { label: 'Financeiro', data: <?= json_encode($chart['financeiro']) ?>, backgroundColor: 'rgba(16,185,129,0.7)', borderRadius: 4 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { color: '#94a3b8', callback: v => v + '%' }, grid: { color: 'rgba(51,65,85,0.5)' } },
                x: { ticks: { color: '#94a3b8' }, grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
<?php endif; ?>
