<?php
$currentRoute = str_starts_with(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', '/dashboard') ? 'dashboard' : 'obras';
$pageTitle = 'Obras';
$breadcrumb = [
    ['label' => 'Obras'],
];
?>

<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Obras</h1>
            <p class="text-slate-400 mt-1">Gerencie suas obras e projetos</p>
        </div>
        <a href="/obras/create" class="btn-primary w-full sm:w-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Obra
        </a>
    </div>

    <!-- KPIs Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Total de Obras</p>
                    <p class="text-3xl font-bold text-white mt-1"><?= $kpis['total'] ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Em Andamento</p>
                    <p class="text-3xl font-bold text-blue-400 mt-1"><?= $kpis['em_andamento'] ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Concluídas</p>
                    <p class="text-3xl font-bold text-emerald-400 mt-1"><?= $kpis['concluidas'] ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Atrasadas</p>
                    <p class="text-3xl font-bold text-red-400 mt-1"><?= $kpis['atrasadas'] ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Orçado Total</p>
                    <p class="text-3xl font-bold text-white mt-1">R$ <?= number_format($kpis['orcado_total'], 2, ',', '.') ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="table-container">
        <div class="p-4 border-b border-slate-700">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nome ou código..." class="input-field pl-10">
                </div>
                <select name="status" class="input-field w-auto sm:w-48">
                    <option value="">Todos os status</option>
                    <option value="planejamento" <?= $status === 'planejamento' ? 'selected' : '' ?>>Planejamento</option>
                    <option value="em_andamento" <?= $status === 'em_andamento' ? 'selected' : '' ?>>Em Andamento</option>
                    <option value="paralisada" <?= $status === 'paralisada' ? 'selected' : '' ?>>Paralisada</option>
                    <option value="concluida" <?= $status === 'concluida' ? 'selected' : '' ?>>Concluída</option>
                    <option value="atrasada" <?= $status === 'atrasada' ? 'selected' : '' ?>>Atrasada</option>
                </select>
                <button type="submit" class="btn-secondary w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrar
                </button>
                <?php if ($search || $status): ?>
                    <a href="/obras" class="btn-secondary w-full sm:w-auto">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full" role="grid">
                <thead class="bg-slate-800/50">
                    <tr class="text-left text-sm text-slate-400">
                        <th class="px-4 py-3 font-medium">Código</th>
                        <th class="px-4 py-3 font-medium">Obra</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Status</th>
                        <th class="px-4 py-3 font-medium hidden lg:table-cell">Período</th>
                        <th class="px-4 py-3 font-medium hidden xl:table-cell">Orçado</th>
                        <th class="px-4 py-3 font-medium">Progresso</th>
                        <th class="px-4 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($obras)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p class="text-lg font-medium text-slate-400">Nenhuma obra encontrada</p>
                                <p class="text-sm mt-1">Comece criando sua primeira obra</p>
                                <a href="/obras/create" class="btn-primary inline-flex mt-4">Criar Obra</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($obras as $obra): ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-4 font-mono text-sm text-slate-300"><?= htmlspecialchars($obra->codigo) ?></td>
                                <td class="px-4 py-4">
                                    <a href="/obras/<?= $obra->id ?>" class="font-medium text-white hover:text-blue-400 transition-colors">
                                        <?= htmlspecialchars($obra->nome) ?>
                                    </a>
                                    <?php if ($obra->endereco): ?>
                                        <p class="text-xs text-slate-500 truncate max-w-xs mt-0.5"><?= htmlspecialchars($obra->endereco) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell">
                                    <span class="badge <?= $obra->getStatusBadgeClass() ?> text-white">
                                        <?= $obra->getStatusLabel() ?>
                                    </span>
                                    <?php if ($obra->isAtrasada() && $obra->status !== 'atrasada'): ?>
                                        <span class="badge bg-red-900/50 text-red-300 ml-1">Atrasada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 hidden lg:table-cell text-sm text-slate-400">
                                    <?php if ($obra->data_inicio && $obra->data_fim_prevista): ?>
                                        <?= date('d/m/Y', strtotime($obra->data_inicio)) ?> - <?= date('d/m/Y', strtotime($obra->data_fim_prevista)) ?>
                                    <?php else: ?>
                                        <span class="text-slate-600">Não definido</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 hidden xl:table-cell text-sm text-slate-300 font-mono">
                                    R$ <?= number_format($obra->orcamento_total, 2, ',', '.') ?>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="w-full max-w-xs">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="text-slate-400">Progresso</span>
                                            <span class="text-white font-medium"><?= $obra->progresso() ?>%</span>
                                        </div>
                                        <div class="h-2 bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full transition-all" style="width: <?= $obra->progresso() ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="/obras/<?= $obra->id ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Ver detalhes">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="/obras/<?= $obra->id ?>/edit" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="/obras/<?= $obra->id ?>/delete" onsubmit="return confirm('Excluir esta obra? Esta ação não pode ser desfeita.')" class="inline">
                                            <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">
                                            <button type="submit" class="p-2 rounded-lg hover:bg-red-900/20 text-slate-400 hover:text-red-400 transition-colors" title="Excluir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (($pagination['last_page'] ?? 1) > 1): ?>
            <div class="p-4 border-t border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-400">
                    Mostrando <?= $pagination['from'] ?> a <?= $pagination['to'] ?> de <?= $pagination['total'] ?> resultados
                </p>
                <nav class="flex items-center gap-1" aria-label="Paginação">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" 
                           class="px-3 py-1.5 rounded-lg text-sm <?= $i === $pagination['current_page'] ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?> transition-colors">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>