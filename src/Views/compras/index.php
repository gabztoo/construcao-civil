<?php
$currentRoute = 'compras';
$pageTitle = 'Ordens de Compra';
$breadcrumb = [
    ['label' => 'Compras'],
    ['label' => 'Ordens de Compra'],
];
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Ordens de Compra</h1>
            <p class="text-slate-400 mt-1">Gerencie as ordens de compra das obras</p>
        </div>
        <a href="/compras/create" class="btn-primary w-full sm:w-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Ordem de Compra
        </a>
    </div>

    <!-- Search -->
    <div class="table-container">
        <div class="p-4 border-b border-slate-700">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por número, fornecedor ou obra..." class="input-field pl-10">
                </div>
                <select name="status" class="input-field sm:w-44">
                    <option value="">Todos os status</option>
                    <?php foreach ($statusList as $valor => $rotulo): ?>
                        <option value="<?= $valor ?>" <?= $statusFilter === $valor ? 'selected' : '' ?>><?= htmlspecialchars($rotulo) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-secondary w-full sm:w-auto">Buscar</button>
                <?php if ($search || $statusFilter): ?>
                    <a href="/compras" class="btn-secondary w-full sm:w-auto">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr class="text-left text-sm text-slate-400">
                        <th class="px-4 py-3 font-medium">Número</th>
                        <th class="px-4 py-3 font-medium">Obra</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Fornecedor</th>
                        <th class="px-4 py-3 font-medium">Valor</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($ocs)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <p class="text-lg font-medium text-slate-400">Nenhuma ordem de compra encontrada</p>
                                <a href="/compras/create" class="btn-primary inline-flex mt-4">Criar Ordem de Compra</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ocs as $oc): ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-4">
                                    <span class="font-mono text-sm text-white"><?= htmlspecialchars($oc->numero) ?></span>
                                    <?php if ($oc->data_pedido): ?>
                                        <p class="text-xs text-slate-500 mt-0.5"><?= date('d/m/Y', strtotime($oc->data_pedido)) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($oc->obra_codigo) ?></span>
                                    <p class="text-sm text-white truncate max-w-[180px]"><?= htmlspecialchars($oc->obra_nome) ?></p>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell text-sm text-slate-300 truncate max-w-[180px]">
                                    <?= htmlspecialchars($oc->fornecedor ?: '—') ?>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-white whitespace-nowrap">
                                    R$ <?= number_format((float) $oc->valor_total, 2, ',', '.') ?>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell">
                                    <span class="badge <?= OrdemCompra::badge($oc->status) ?>">
                                        <?= htmlspecialchars($statusList[$oc->status] ?? $oc->status) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="/compras/<?= $oc->id ?>/edit" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="/compras/<?= $oc->id ?>/delete" onsubmit="return confirm('Excluir a ordem de compra <?= htmlspecialchars(addslashes($oc->numero)) ?>? Esta ação não pode ser desfeita.')" class="inline">
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
        <?php if ($pagination['last_page'] > 1): ?>
            <div class="flex items-center justify-between px-4 py-3 border-t border-slate-700">
                <p class="text-sm text-slate-400">
                    Mostrando <span class="text-white"><?= $pagination['from'] ?></span>-<span class="text-white"><?= $pagination['to'] ?></span>
                    de <span class="text-white"><?= $pagination['total'] ?></span>
                </p>
                <div class="flex items-center gap-1">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">‹</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>"
                           class="px-3 py-1.5 rounded-lg text-sm transition-colors <?= $i === $pagination['current_page'] ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">›</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
