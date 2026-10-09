<?php
$currentRoute = 'obras';
$pageTitle = $obra->nome;
$breadcrumb = [
    ['label' => 'Obras', 'url' => '/obras'],
    ['label' => $obra->nome],
];
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <span class="badge <?= $obra->getStatusBadgeClass() ?> text-white text-sm px-3 py-1">
                    <?= $obra->getStatusLabel() ?>
                </span>
                <?php if ($obra->isAtrasada() && $obra->status !== 'atrasada'): ?>
                    <span class="badge bg-red-900/50 text-red-300">Atrasada</span>
                <?php endif; ?>
                <?php if ($obra->data_fim_real): ?>
                    <span class="badge bg-slate-700 text-slate-300">Finalizada</span>
                <?php endif; ?>
            </div>
            <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($obra->nome) ?></h1>
            <p class="text-slate-400 mt-1">Código: <span class="font-mono text-slate-300"><?= htmlspecialchars($obra->codigo) ?></span></p>
        </div>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <a href="/obras/<?= $obra->id ?>/edit" class="btn-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <a href="/obras" class="btn-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Progress Card -->
            <div class="bg-slate-900 border border-slate-700 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Progresso Geral</h2>
                    <span class="text-2xl font-bold text-blue-400"><?= $obra->progresso() ?>%</span>
                </div>
                <div class="h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all" style="width: <?= $obra->progresso() ?>%"></div>
                </div>
                <p class="text-sm text-slate-400 mt-2">Baseado na média das etapas cadastradas</p>
            </div>

            <!-- Info Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Orçamento -->
                <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                    <p class="text-slate-400 text-sm mb-1">Orçamento Total</p>
                    <p class="text-2xl font-bold text-white">R$ <?= number_format($obra->orcamento_total, 2, ',', '.') ?></p>
                </div>

                <!-- Responsável Técnico -->
                <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                    <p class="text-slate-400 text-sm mb-1">Responsável Técnico</p>
                    <p class="text-white font-medium"><?= htmlspecialchars($obra->responsavel_tecnico ?: 'Não informado') ?></p>
                    <?php if ($obra->crea_rrt): ?>
                        <p class="text-sm text-slate-500 mt-1">CREA/RRT: <?= htmlspecialchars($obra->crea_rrt) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Datas -->
                <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                    <p class="text-slate-400 text-sm mb-1">Início Previsto</p>
                    <p class="text-white font-medium"><?= $obra->data_inicio ? date('d/m/Y', strtotime($obra->data_inicio)) : '<span class="text-slate-500">Não definido</span>' ?></p>
                </div>

                <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                    <p class="text-slate-400 text-sm mb-1">Término Previsto</p>
                    <p class="text-white font-medium"><?= $obra->data_fim_prevista ? date('d/m/Y', strtotime($obra->data_fim_prevista)) : '<span class="text-slate-500">Não definido</span>' ?></p>
                </div>

                <?php if ($obra->data_fim_real): ?>
                <div class="bg-slate-900 border border-slate-700 rounded-xl p-5 sm:col-span-2">
                    <p class="text-slate-400 text-sm mb-1">Término Real</p>
                    <p class="text-white font-medium"><?= date('d/m/Y', strtotime($obra->data_fim_real)) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Localização -->
            <?php if ($obra->endereco): ?>
            <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                <h3 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Localização
                </h3>
                <p class="text-slate-300 whitespace-pre-line"><?= htmlspecialchars($obra->endereco) ?></p>
                <?php if ($obra->latitude && $obra->longitude): ?>
                    <a href="https://maps.google.com/?q=<?= $obra->latitude ?>,<?= $obra->longitude ?>" target="_blank" class="inline-flex items-center gap-2 mt-3 text-sm text-blue-400 hover:text-blue-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Ver no Google Maps
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Observações -->
            <?php if ($obra->observacoes): ?>
            <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                <h3 class="text-lg font-semibold text-white mb-3">Observações</h3>
                <p class="text-slate-300 whitespace-pre-line"><?= htmlspecialchars($obra->observacoes) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Etapas & Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
                <h3 class="text-lg font-semibold text-white mb-4">Ações Rápidas</h3>
                <div class="space-y-2">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300 transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Adicionar Etapa
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300 transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Novo RDO
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300 transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Ver Relatórios
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300 transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Documentos
                    </a>
                </div>
            </div>

            <!-- Etapas -->
            <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
                <div class="p-5 border-b border-slate-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Etapas da Obra</h3>
                    <span class="badge bg-slate-700 text-slate-300"><?= count($etapas) ?> etapas</span>
                </div>
                
                <?php if (empty($etapas)): ?>
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <p class="text-lg font-medium text-slate-400">Nenhuma etapa cadastrada</p>
                        <p class="text-sm text-slate-500 mt-1">Adicione etapas para acompanhar o progresso</p>
                        <button class="btn-primary mt-4 inline-flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Primeira Etapa
                        </button>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-800">
                        <?php foreach ($etapas as $index => $etapa): ?>
                            <div class="p-5 hover:bg-slate-800/50 transition-colors">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center">
                                        <span class="text-lg font-bold text-slate-300"><?= $index + 1 ?></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <h4 class="font-medium text-white truncate"><?= htmlspecialchars($etapa['nome']) ?></h4>
                                            <span class="badge bg-slate-700 text-slate-300 flex-shrink-0"><?= number_format($etapa['percentual_conclusao'], 1) ?>%</span>
                                        </div>
                                        <?php if ($etapa['descricao']): ?>
                                            <p class="text-sm text-slate-500 mt-1 truncate"><?= htmlspecialchars($etapa['descricao']) ?></p>
                                        <?php endif; ?>
                                        <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                                            <?php if ($etapa['data_inicio']): ?>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    Início: <?= date('d/m/Y', strtotime($etapa['data_inicio'])) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($etapa['data_fim_prevista']): ?>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7v4m0 0l-3-3m3 3l3-3m0 8V17a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2"/></svg>
                                                    Previsto: <?= date('d/m/Y', strtotime($etapa['data_fim_prevista'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="h-2 bg-slate-800 rounded-full overflow-hidden mt-3">
                                            <div class="h-full bg-blue-500 rounded-full transition-all" style="width: <?= $etapa['percentual_conclusao'] ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>