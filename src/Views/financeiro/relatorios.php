<?php
$currentRoute = 'financeiro';
$pageTitle = 'Relatórios Financeiros';
$breadcrumb = [
    ['label' => 'Financeiro', 'url' => '/financeiro'],
    ['label' => 'Relatórios'],
];
$meses = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
$anos = range((int) date('Y') - 2, (int) date('Y') + 1);

$receitaTotal = $totais['receita_pendente'] + $totais['receita_pago'];
$despesaTotal = $totais['despesa_pendente'] + $totais['despesa_pago'];
$saldo = $receitaTotal - $despesaTotal;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Relatórios Financeiros</h1>
            <p class="text-slate-400 mt-1">Resumo do período, categorias, obras e vencidos</p>
        </div>
        <a href="/financeiro" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <!-- Filtro período -->
    <div class="table-container">
        <form method="GET" class="p-4 flex flex-col sm:flex-row gap-4">
            <select name="mes" class="input-field sm:w-56">
                <?php foreach ($meses as $num => $nome): ?>
                    <option value="<?= $num ?>" <?= (int) $filtros['mes'] === $num ? 'selected' : '' ?>><?= $nome ?></option>
                <?php endforeach; ?>
            </select>
            <select name="ano" class="input-field sm:w-32">
                <?php foreach ($anos as $a): ?>
                    <option value="<?= $a ?>" <?= (int) $filtros['ano'] === $a ? 'selected' : '' ?>><?= $a ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-secondary">Filtrar</button>
        </form>
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="kpi-card">
            <p class="text-sm text-slate-400">Receitas do Período</p>
            <p class="text-2xl font-bold text-emerald-400 mt-2">R$ <?= number_format($receitaTotal, 2, ',', '.') ?></p>
            <p class="text-xs text-slate-500 mt-1">Pago: R$ <?= number_format($totais['receita_pago'], 2, ',', '.') ?> | Pendente: R$ <?= number_format($totais['receita_pendente'], 2, ',', '.') ?></p>
        </div>
        <div class="kpi-card">
            <p class="text-sm text-slate-400">Despesas do Período</p>
            <p class="text-2xl font-bold text-red-400 mt-2">R$ <?= number_format($despesaTotal, 2, ',', '.') ?></p>
            <p class="text-xs text-slate-500 mt-1">Pago: R$ <?= number_format($totais['despesa_pago'], 2, ',', '.') ?> | Pendente: R$ <?= number_format($totais['despesa_pendente'], 2, ',', '.') ?></p>
        </div>
        <div class="kpi-card">
            <p class="text-sm text-slate-400">Saldo do Período</p>
            <p class="text-2xl font-bold <?= $saldo >= 0 ? 'text-white' : 'text-red-400' ?> mt-2">R$ <?= number_format($saldo, 2, ',', '.') ?></p>
            <p class="text-xs text-slate-500 mt-1">Receitas − Despesas</p>
        </div>
        <div class="kpi-card">
            <p class="text-sm text-slate-400">Títulos Vencidos</p>
            <p class="text-2xl font-bold <?= $vencidos ? 'text-red-400' : 'text-white' ?> mt-2"><?= count($vencidos) ?></p>
            <p class="text-xs text-slate-500 mt-1">Não pagos com vencimento até hoje</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Por Categoria -->
        <div class="table-container">
            <div class="p-4 border-b border-slate-700">
                <h2 class="text-sm font-semibold text-white">Por Categoria</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr class="text-left text-sm text-slate-400">
                            <th class="px-4 py-3 font-medium">Categoria</th>
                            <th class="px-4 py-3 font-medium">Tipo</th>
                            <th class="px-4 py-3 font-medium text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php if (empty($porCategoria)): ?>
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">Sem dados no período.</td></tr>
                        <?php else: ?>
                            <?php foreach ($porCategoria as $c): ?>
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-white"><?= htmlspecialchars($c->nome) ?></td>
                                    <td class="px-4 py-3">
                                        <span class="badge <?= $c->tipo === 'receita' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-red-900/50 text-red-300' ?>">
                                            <?= $c->tipo === 'receita' ? 'Receita' : 'Despesa' ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-right <?= $c->tipo === 'receita' ? 'text-emerald-400' : 'text-red-400' ?>">
                                        R$ <?= number_format((float) $c->total, 2, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Por Obra -->
        <div class="table-container">
            <div class="p-4 border-b border-slate-700">
                <h2 class="text-sm font-semibold text-white">Por Obra</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr class="text-left text-sm text-slate-400">
                            <th class="px-4 py-3 font-medium">Obra</th>
                            <th class="px-4 py-3 font-medium">Tipo</th>
                            <th class="px-4 py-3 font-medium text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php if (empty($porObra)): ?>
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">Sem dados no período.</td></tr>
                        <?php else: ?>
                            <?php foreach ($porObra as $o): ?>
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-white">
                                        <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($o->codigo) ?></span>
                                        <?= htmlspecialchars($o->nome) ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge <?= $o->tipo === 'receita' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-red-900/50 text-red-300' ?>">
                                            <?= $o->tipo === 'receita' ? 'Receita' : 'Despesa' ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-right <?= $o->tipo === 'receita' ? 'text-emerald-400' : 'text-red-400' ?>">
                                        R$ <?= number_format((float) $o->total, 2, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Títulos vencidos -->
    <div class="table-container">
        <div class="p-4 border-b border-slate-700">
            <h2 class="text-sm font-semibold text-white">Títulos Vencidos (não pagos)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr class="text-left text-sm text-slate-400">
                        <th class="px-4 py-3 font-medium">Vencimento</th>
                        <th class="px-4 py-3 font-medium">Descrição</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Categoria</th>
                        <th class="px-4 py-3 font-medium">Tipo</th>
                        <th class="px-4 py-3 font-medium text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($vencidos)): ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum título vencido. Tudo em dia!</td></tr>
                    <?php else: ?>
                        <?php foreach ($vencidos as $v): ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-red-400 whitespace-nowrap"><?= date('d/m/Y', strtotime($v->data_vencimento)) ?></td>
                                <td class="px-4 py-3 text-sm text-white"><?= htmlspecialchars($v->descricao) ?></td>
                                <td class="px-4 py-3 hidden md:table-cell text-sm text-slate-400"><?= htmlspecialchars($v->categoria_nome ?: '—') ?></td>
                                <td class="px-4 py-3">
                                    <span class="badge <?= $v->tipo === 'receita' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-red-900/50 text-red-300' ?>">
                                        <?= $v->tipo === 'receita' ? 'Receita' : 'Despesa' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-right <?= $v->tipo === 'receita' ? 'text-emerald-400' : 'text-red-400' ?>">
                                    R$ <?= number_format((float) $v->valor, 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
