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
