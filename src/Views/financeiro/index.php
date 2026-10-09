<?php
$currentRoute = 'financeiro';
$pageTitle = 'Financeiro';
$breadcrumb = [
    ['label' => 'Financeiro'],
];
$errors = Session::getErrors();
$abrirModal = (bool) array_intersect(['descricao', 'valor', 'data_vencimento'], array_keys($errors));
$meses = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
$anos = range((int) date('Y') - 2, (int) date('Y') + 1);
?>

<div x-data="{
    modal: <?= $abrirModal ? 'true' : 'false' ?>,
    openModal() { this.modal = true; }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Financeiro</h1>
            <p class="text-slate-400 mt-1">Contas a pagar e a receber por obra</p>
        </div>
        <div class="flex gap-3">
            <a href="/financeiro/relatorios?mes=<?= (int) $filtros['mes'] ?>&ano=<?= (int) $filtros['ano'] ?>" class="btn-secondary w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Relatórios
            </a>
            <button type="button" @click="openModal()" class="btn-primary w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Lançamento
            </button>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-400">Total a Pagar</p>
                <span class="w-9 h-9 rounded-lg bg-red-900/40 flex items-center justify-center text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-red-400 mt-2">R$ <?= number_format($kpis['a_pagar'], 2, ',', '.') ?></p>
            <p class="text-xs text-slate-500 mt-1">Despesas pendentes do período</p>
        </div>
        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-400">Total a Receber</p>
                <span class="w-9 h-9 rounded-lg bg-emerald-900/40 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-emerald-400 mt-2">R$ <?= number_format($kpis['a_receber'], 2, ',', '.') ?></p>
            <p class="text-xs text-slate-500 mt-1">Receitas pendentes do período</p>
        </div>
        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-400">Saldo do Período</p>
                <span class="w-9 h-9 rounded-lg bg-blue-900/40 flex items-center justify-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.012 0M10 6l3 1m0 0l-3 9a5.002 5.002 0 006.012 0M15 6l3 1m0 0l-3 9a5.002 5.002 0 006.012 0M4 18v-1a4 4 0 014-4h4a4 4 0 014 4v1"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold <?= $kpis['saldo'] >= 0 ? 'text-white' : 'text-red-400' ?> mt-2">R$ <?= number_format($kpis['saldo'], 2, ',', '.') ?></p>
            <p class="text-xs text-slate-500 mt-1">
                Pago: R$ <?= number_format($kpis['pago'], 2, ',', '.') ?> | Recebido: R$ <?= number_format($kpis['recebido'], 2, ',', '.') ?>
            </p>
        </div>
    </div>

    <!-- Filtros + Tabela -->
    <div class="table-container mb-6">
        <div class="p-4 border-b border-slate-700">
            <form method="GET" class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <select name="obra_id" class="input-field">
                    <option value="">Todas as obras</option>
                    <?php foreach ($obras as $o): ?>
                        <option value="<?= $o->id ?>" <?= $filtros['obra_id'] === (int) $o->id ? 'selected' : '' ?>><?= htmlspecialchars($o->codigo . ' - ' . $o->nome) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tipo" class="input-field">
                    <option value="">Receita e Despesa</option>
                    <option value="receita" <?= $filtros['tipo'] === 'receita' ? 'selected' : '' ?>>Receita</option>
                    <option value="despesa" <?= $filtros['tipo'] === 'despesa' ? 'selected' : '' ?>>Despesa</option>
                </select>
                <select name="status" class="input-field">
                    <option value="">Todos os status</option>
                    <?php foreach ($statusList as $valor => $rotulo): ?>
                        <option value="<?= $valor ?>" <?= $filtros['status'] === $valor ? 'selected' : '' ?>><?= htmlspecialchars($rotulo) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="mes" class="input-field">
                    <?php foreach ($meses as $num => $nome): ?>
                        <option value="<?= $num ?>" <?= (int) $filtros['mes'] === $num ? 'selected' : '' ?>><?= $nome ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="flex gap-3">
                    <select name="ano" class="input-field flex-1">
                        <?php foreach ($anos as $a): ?>
                            <option value="<?= $a ?>" <?= (int) $filtros['ano'] === $a ? 'selected' : '' ?>><?= $a ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-secondary">Filtrar</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr class="text-left text-sm text-slate-400">
                        <th class="px-4 py-3 font-medium">Vencimento</th>
                        <th class="px-4 py-3 font-medium">Descrição</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Obra</th>
                        <th class="px-4 py-3 font-medium hidden lg:table-cell">Categoria</th>
                        <th class="px-4 py-3 font-medium">Tipo</th>
                        <th class="px-4 py-3 font-medium">Valor</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($lancamentos)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <p class="text-lg font-medium text-slate-400">Nenhum lançamento no período</p>
                                <button type="button" @click="openModal()" class="btn-primary inline-flex mt-4">Novo Lançamento</button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lancamentos as $l): ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-4 text-sm text-slate-300 whitespace-nowrap">
                                    <?= date('d/m/Y', strtotime($l->data_vencimento)) ?>
                                    <?php if ($l->data_pagamento): ?>
                                        <p class="text-xs text-slate-500 mt-0.5">Pago em <?= date('d/m/Y', strtotime($l->data_pagamento)) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-white truncate max-w-[220px]"><?= htmlspecialchars($l->descricao) ?></p>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell text-sm text-slate-400 truncate max-w-[160px]">
                                    <?php if ($l->obra_id): ?>
                                        <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($l->obra_codigo) ?></span> <?= htmlspecialchars($l->obra_nome) ?>
                                    <?php else: ?>
                                        <span class="text-slate-600">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 hidden lg:table-cell text-sm text-slate-400">
                                    <?= htmlspecialchars($l->categoria_nome ?: '—') ?>
                                </td>
                                <td class="px-4 py-4">
                                    <?php if ($l->tipo === 'receita'): ?>
                                        <span class="badge bg-emerald-900/50 text-emerald-300">Receita</span>
                                    <?php else: ?>
                                        <span class="badge bg-red-900/50 text-red-300">Despesa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium whitespace-nowrap <?= $l->tipo === 'receita' ? 'text-emerald-400' : 'text-red-400' ?>">
                                    <?= $l->tipo === 'receita' ? '+' : '−' ?> R$ <?= number_format((float) $l->valor, 2, ',', '.') ?>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="badge <?= LancamentoFinanceiro::badge($l->status) ?>">
                                        <?= htmlspecialchars($statusList[$l->status] ?? $l->status) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <?php if ($l->status !== 'pago'): ?>
                                        <form method="POST" action="/financeiro/<?= $l->id ?>/baixar" class="inline" onsubmit="return confirm('Dar baixa no lançamento <?= htmlspecialchars(addslashes($l->descricao)) ?>?')">
                                            <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">
                                            <button type="submit" class="p-2 rounded-lg hover:bg-emerald-900/30 text-slate-400 hover:text-emerald-400 transition-colors" title="Dar baixa (pagar/receber)">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="p-2 inline-flex text-slate-600" title="Baixado">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </span>
                                    <?php endif; ?>
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
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>&obra_id=<?= $filtros['obra_id'] ?>&tipo=<?= urlencode($filtros['tipo']) ?>&status=<?= urlencode($filtros['status']) ?>&mes=<?= (int) $filtros['mes'] ?>&ano=<?= (int) $filtros['ano'] ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">‹</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                        <a href="?page=<?= $i ?>&obra_id=<?= $filtros['obra_id'] ?>&tipo=<?= urlencode($filtros['tipo']) ?>&status=<?= urlencode($filtros['status']) ?>&mes=<?= (int) $filtros['mes'] ?>&ano=<?= (int) $filtros['ano'] ?>"
                           class="px-3 py-1.5 rounded-lg text-sm transition-colors <?= $i === $pagination['current_page'] ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>&obra_id=<?= $filtros['obra_id'] ?>&tipo=<?= urlencode($filtros['tipo']) ?>&status=<?= urlencode($filtros['status']) ?>&mes=<?= (int) $filtros['mes'] ?>&ano=<?= (int) $filtros['ano'] ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">›</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Novo Lançamento -->
    <div x-show="modal" x-transition.opacity.duration.150ms class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/60" @click="modal = false"></div>
        <div x-transition class="relative w-full max-w-lg bg-slate-900 border border-slate-700 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Novo Lançamento</h3>
                <button type="button" @click="modal = false" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400" aria-label="Fechar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="/financeiro" class="p-6 space-y-5">
                <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="lf_tipo" class="label-field">Tipo <span class="text-red-400">*</span></label>
                        <select id="lf_tipo" name="tipo" class="input-field" required>
                            <option value="despesa">Despesa (a pagar)</option>
                            <option value="receita">Receita (a receber)</option>
                        </select>
                    </div>
                    <div>
                        <label for="lf_status" class="label-field">Status <span class="text-red-400">*</span></label>
                        <select id="lf_status" name="status" class="input-field" required>
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="lf_desc" class="label-field">Descrição <span class="text-red-400">*</span></label>
                    <input type="text" id="lf_desc" name="descricao" class="input-field" placeholder="Ex: Compra de cimento" required maxlength="200">
                    <?php if ($err = $errors['descricao'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="lf_valor" class="label-field">Valor (R$) <span class="text-red-400">*</span></label>
                        <input type="number" id="lf_valor" name="valor" class="input-field" min="0.01" step="0.01" required placeholder="0,00">
                        <?php if ($err = $errors['valor'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="lf_venc" class="label-field">Vencimento <span class="text-red-400">*</span></label>
                        <input type="date" id="lf_venc" name="data_vencimento" class="input-field" value="<?= date('Y-m-d') ?>" required>
                        <?php if ($err = $errors['data_vencimento'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="lf_obra" class="label-field">Obra</label>
                        <select id="lf_obra" name="obra_id" class="input-field">
                            <option value="">Sem obra (geral)</option>
                            <?php foreach ($obras as $o): ?>
                                <option value="<?= $o->id ?>"><?= htmlspecialchars($o->codigo . ' - ' . $o->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="lf_cat" class="label-field">Categoria</label>
                        <select id="lf_cat" name="categoria_id" class="input-field">
                            <option value="">Selecione...</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c->id ?>" data-tipo="<?= $c->tipo ?>"><?= htmlspecialchars($c->nome) ?> (<?= $c->tipo === 'receita' ? 'Receita' : 'Despesa' ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if ($err = $errors['obra_id'][0] ?? $errors['categoria_id'][0] ?? null): ?>
                    <p class="text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>

                <div class="flex gap-3 pt-4 border-t border-slate-700">
                    <button type="submit" class="btn-primary flex-1">Salvar Lançamento</button>
                    <button type="button" @click="modal = false" class="btn-secondary flex-1 justify-center">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
