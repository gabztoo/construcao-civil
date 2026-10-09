<?php
$currentRoute = 'funcionarios';
$pageTitle = 'Mão de Obra';
$breadcrumb = [
    ['label' => 'Mão de Obra'],
];
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Mão de Obra</h1>
            <p class="text-slate-400 mt-1">Equipe de funcionários disponíveis para alocação</p>
        </div>
        <div class="flex gap-3">
            <a href="/alocacoes" class="btn-secondary w-full sm:w-auto">Ver Alocações</a>
            <a href="/funcionarios/create" class="btn-primary w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Funcionário
            </a>
        </div>
    </div>

    <div class="table-container">
        <div class="p-4 border-b border-slate-700">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nome..." class="input-field pl-10">
                </div>
                <button type="submit" class="btn-secondary w-full sm:w-auto">Buscar</button>
                <?php if ($search): ?>
                    <a href="/funcionarios" class="btn-secondary w-full sm:w-auto">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr class="text-left text-sm text-slate-400">
                        <th class="px-4 py-3 font-medium">Funcionário</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">CPF</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Telefone</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($funcionarios)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-lg font-medium text-slate-400">Nenhum funcionário encontrado</p>
                                <a href="/funcionarios/create" class="btn-primary inline-flex mt-4">Criar Funcionário</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($funcionarios as $f): ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-sm font-medium text-slate-200 flex-shrink-0">
                                            <?= strtoupper(mb_substr($f->nome, 0, 1)) ?>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-white truncate"><?= htmlspecialchars($f->nome) ?></p>
                                            <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($f->cargo ?: 'Sem cargo') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell text-sm text-slate-400 font-mono"><?= htmlspecialchars($f->cpf ?: '—') ?></td>
                                <td class="px-4 py-4 hidden md:table-cell text-sm text-slate-400"><?= htmlspecialchars($f->telefone ?: '—') ?></td>
                                <td class="px-4 py-4">
                                    <?php if ($f->ativo): ?>
                                        <span class="badge bg-emerald-900/50 text-emerald-300">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-red-900/50 text-red-300">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="/funcionarios/<?= $f->id ?>/edit" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="/funcionarios/<?= $f->id ?>/delete" onsubmit="return confirm('Excluir o funcionário <?= htmlspecialchars(addslashes($f->nome)) ?>? Esta ação não pode ser desfeita.')" class="inline">
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

        <?php if ($pagination['last_page'] > 1): ?>
            <div class="flex items-center justify-between px-4 py-3 border-t border-slate-700">
                <p class="text-sm text-slate-400">
                    Mostrando <span class="text-white"><?= $pagination['from'] ?></span>-<span class="text-white"><?= $pagination['to'] ?></span>
                    de <span class="text-white"><?= $pagination['total'] ?></span>
                </p>
                <div class="flex items-center gap-1">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($search) ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">‹</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                           class="px-3 py-1.5 rounded-lg text-sm transition-colors <?= $i === $pagination['current_page'] ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($search) ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">›</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
