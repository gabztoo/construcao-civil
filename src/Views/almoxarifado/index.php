<?php
$currentRoute = 'almoxarifado';
$pageTitle = 'Almoxarifado';
$breadcrumb = [
    ['label' => 'Almoxarifado'],
];
$errors = Session::getErrors();
?>

<div x-data="{
    materialModal: false,
    movModal: false,
    editingId: 0,
    mat: { codigo_sku: '', nome: '', categoria: '', unidade_medida: '', estoque_minimo: '' },
    mov: { obra_id: '', material_id: '', tipo: 'entrada', quantidade: '', observacao: '' },
    openCreate() {
        this.editingId = 0;
        this.mat = { codigo_sku: '', nome: '', categoria: '', unidade_medida: '', estoque_minimo: '' };
        this.materialModal = true;
        this.$nextTick(() => this.$refs.matForm && this.$refs.matForm.reset());
    },
    openEdit(data) {
        this.editingId = data.id;
        this.mat = { codigo_sku: data.codigo_sku, nome: data.nome, categoria: data.categoria, unidade_medida: data.unidade_medida, estoque_minimo: data.estoque_minimo };
        this.materialModal = true;
    },
    openMov(materialId) {
        this.mov = { obra_id: '', material_id: materialId ? String(materialId) : '', tipo: 'entrada', quantidade: '', observacao: '' };
        this.movModal = true;
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Almoxarifado</h1>
            <p class="text-slate-400 mt-1">Insumos, ferramentas e estoque por obra</p>
        </div>
        <div class="flex gap-3">
            <button type="button" @click="openMov()" class="btn-secondary w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
                Movimentar Estoque
            </button>
            <button type="button" @click="openCreate()" class="btn-primary w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Material
            </button>
        </div>
    </div>

    <!-- Filters + Table -->
    <div class="table-container mb-6">
        <div class="p-4 border-b border-slate-700">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nome ou SKU..." class="input-field pl-10">
                </div>
                <select name="categoria" class="input-field sm:w-48">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categorias as $valor => $rotulo): ?>
                        <option value="<?= $valor ?>" <?= $categoriaFilter === $valor ? 'selected' : '' ?>><?= htmlspecialchars($rotulo) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-secondary w-full sm:w-auto">Filtrar</button>
                <?php if ($search || $categoriaFilter): ?>
                    <a href="/almoxarifado" class="btn-secondary w-full sm:w-auto">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr class="text-left text-sm text-slate-400">
                        <th class="px-4 py-3 font-medium">Material</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Categoria</th>
                        <th class="px-4 py-3 font-medium">Un.</th>
                        <th class="px-4 py-3 font-medium">Saldo Total</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Est. MÃ­nimo</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">AÃ§Ãµes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($materiais)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-lg font-medium text-slate-400">Nenhum material encontrado</p>
                                <button type="button" @click="openCreate()" class="btn-primary inline-flex mt-4">Cadastrar Material</button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($materiais as $m):
                            $saldo = (float) $m->saldo_total;
                            $min = (float) $m->estoque_minimo;
                            if ($saldo < $min) {
                                $badge = 'bg-red-900/50 text-red-300';
                                $label = 'Estoque baixo';
                            } elseif ($min > 0 && $saldo < $min * 1.5) {
                                $badge = 'bg-amber-900/50 text-amber-300';
                                $label = 'AtenÃ§Ã£o';
                            } else {
                                $badge = 'bg-emerald-900/50 text-emerald-300';
                                $label = 'Normal';
                            }
                        ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-4">
                                    <p class="font-medium text-white truncate max-w-[220px]"><?= htmlspecialchars($m->nome) ?></p>
                                    <p class="font-mono text-xs text-slate-500"><?= htmlspecialchars($m->codigo_sku) ?></p>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell">
                                    <span class="badge bg-slate-700/50 text-slate-300"><?= htmlspecialchars($categorias[$m->categoria] ?? $m->categoria) ?></span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-300"><?= htmlspecialchars($m->unidade_medida) ?></td>
                                <td class="px-4 py-4 text-sm font-medium <?= $saldo < $min ? 'text-red-400' : 'text-white' ?>">
                                    <?= rtrim(rtrim(number_format($saldo, 3, ',', '.'), '0'), ',') ?>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell text-sm text-slate-400">
                                    <?= rtrim(rtrim(number_format($min, 3, ',', '.'), '0'), ',') ?>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="badge <?= $badge ?>"><?= $label ?></span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click='openEdit(<?= htmlspecialchars(json_encode([
                                            'id' => (int) $m->id,
                                            'codigo_sku' => $m->codigo_sku,
                                            'nome' => $m->nome,
                                            'categoria' => $m->categoria,
                                            'unidade_medida' => $m->unidade_medida,
                                            'estoque_minimo' => $m->estoque_minimo,
                                        ]), ENT_QUOTES) ?>)' class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button type="button" @click="openMov(<?= (int) $m->id ?>)" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Movimentar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                            </svg>
                                        </button>
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
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($search) ?>&categoria=<?= urlencode($categoriaFilter) ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">â€¹</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&categoria=<?= urlencode($categoriaFilter) ?>"
                           class="px-3 py-1.5 rounded-lg text-sm transition-colors <?= $i === $pagination['current_page'] ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($search) ?>&categoria=<?= urlencode($categoriaFilter) ?>" class="p-2 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">â€º</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ãšltimas movimentaÃ§Ãµes -->
    <div class="table-container">
        <div class="p-4 border-b border-slate-700">
            <h2 class="text-sm font-semibold text-white">Ãšltimas MovimentaÃ§Ãµes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr class="text-left text-sm text-slate-400">
                        <th class="px-4 py-3 font-medium">Quando</th>
                        <th class="px-4 py-3 font-medium">Tipo</th>
                        <th class="px-4 py-3 font-medium">Material</th>
                        <th class="px-4 py-3 font-medium">Qtd.</th>
                        <th class="px-4 py-3 font-medium hidden md:table-cell">Obra</th>
                        <th class="px-4 py-3 font-medium hidden lg:table-cell">UsuÃ¡rio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($ultimasMovs)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Nenhuma movimentaÃ§Ã£o registrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ultimasMovs as $mv): ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($mv->created_at)) ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($mv->tipo === 'entrada'): ?>
                                        <span class="badge bg-emerald-900/50 text-emerald-300">Entrada</span>
                                    <?php elseif ($mv->tipo === 'saida'): ?>
                                        <span class="badge bg-red-900/50 text-red-300">SaÃ­da</span>
                                    <?php else: ?>
                                        <span class="badge bg-blue-900/50 text-blue-300">TransferÃªncia</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-white truncate max-w-[200px]">
                                    <?= htmlspecialchars($mv->material_nome) ?>
                                    <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($mv->codigo_sku) ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300 whitespace-nowrap">
                                    <?= rtrim(rtrim(number_format((float) $mv->quantidade, 3, ',', '.'), '0'), ',') ?>
                                    <?= htmlspecialchars($mv->unidade_medida) ?>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell text-sm text-slate-400 truncate max-w-[180px]">
                                    <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($mv->obra_codigo) ?></span> <?= htmlspecialchars($mv->obra_nome) ?>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell text-sm text-slate-400">
                                    <?= htmlspecialchars($mv->usuario_nome ?: 'â€”') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Material (cadastro/ediÃ§Ã£o) -->
    <div x-show="materialModal" x-transition.opacity.duration.150ms class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/60" @click="materialModal = false"></div>
        <div x-transition class="relative w-full max-w-lg bg-slate-900 border border-slate-700 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white" x-text="editingId ? 'Editar Material' : 'Novo Material'"></h3>
                <button type="button" @click="materialModal = false" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400" aria-label="Fechar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" x-ref="matForm" :action="editingId ? '/almoxarifado/' + editingId : '/almoxarifado'" class="p-6 space-y-5">
                <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="m_sku" class="label-field">SKU/CÃ³digo <span class="text-red-400">*</span></label>
                        <input type="text" id="m_sku" name="codigo_sku" x-model="mat.codigo_sku" class="input-field" placeholder="Ex: MAT-001" required maxlength="30">
                        <?php if ($err = $errors['codigo_sku'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="m_min" class="label-field">Estoque MÃ­nimo <span class="text-red-400">*</span></label>
                        <input type="number" id="m_min" name="estoque_minimo" x-model="mat.estoque_minimo" class="input-field" min="0" step="0.001" required>
                        <?php if ($err = $errors['estoque_minimo'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label for="m_nome" class="label-field">Nome <span class="text-red-400">*</span></label>
                    <input type="text" id="m_nome" name="nome" x-model="mat.nome" class="input-field" placeholder="Ex: Cabo flexÃ­vel 2,5mm" required maxlength="120">
                    <?php if ($err = $errors['nome'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="m_cat" class="label-field">Categoria <span class="text-red-400">*</span></label>
                        <select id="m_cat" name="categoria" x-model="mat.categoria" class="input-field" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($categorias as $valor => $rotulo): ?>
                                <option value="<?= $valor ?>"><?= htmlspecialchars($rotulo) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($err = $errors['categoria'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="m_un" class="label-field">Unidade de Medida <span class="text-red-400">*</span></label>
                        <select id="m_un" name="unidade_medida" x-model="mat.unidade_medida" class="input-field" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($unidades as $valor => $rotulo): ?>
                                <option value="<?= $valor ?>"><?= htmlspecialchars($rotulo) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($err = $errors['unidade_medida'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-700">
                    <button type="submit" class="btn-primary flex-1">Salvar</button>
                    <button type="button" @click="materialModal = false" class="btn-secondary flex-1 justify-center">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal MovimentaÃ§Ã£o -->
    <div x-show="movModal" x-transition.opacity.duration.150ms class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/60" @click="movModal = false"></div>
        <div x-transition class="relative w-full max-w-lg bg-slate-900 border border-slate-700 rounded-xl shadow-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Registrar MovimentaÃ§Ã£o</h3>
                <button type="button" @click="movModal = false" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400" aria-label="Fechar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="/almoxarifado/movimentacao" class="p-6 space-y-5">
                <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

                <div>
                    <label for="mv_material" class="label-field">Material <span class="text-red-400">*</span></label>
                    <select id="mv_material" name="material_id" x-model="mov.material_id" class="input-field" required>
                        <option value="">Selecione o material...</option>
                        <?php foreach ($selectMateriais as $sm): ?>
                            <option value="<?= $sm->id ?>"><?= htmlspecialchars($sm->codigo_sku . ' - ' . $sm->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($err = $errors['material_id'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="mv_obra" class="label-field">Obra <span class="text-red-400">*</span></label>
                    <select id="mv_obra" name="obra_id" x-model="mov.obra_id" class="input-field" required>
                        <option value="">Selecione a obra...</option>
                        <?php foreach ($obras as $o): ?>
                            <option value="<?= $o->id ?>"><?= htmlspecialchars($o->codigo . ' - ' . $o->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($err = $errors['obra_id'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="mv_tipo" class="label-field">Tipo <span class="text-red-400">*</span></label>
                        <select id="mv_tipo" name="tipo" x-model="mov.tipo" class="input-field" required>
                            <option value="entrada">Entrada</option>
                            <option value="saida">SaÃ­da</option>
                        </select>
                    </div>
                    <div>
                        <label for="mv_qtd" class="label-field">Quantidade <span class="text-red-400">*</span></label>
                        <input type="number" id="mv_qtd" name="quantidade" x-model="mov.quantidade" class="input-field" min="0.001" step="0.001" required placeholder="0">
                        <?php if ($err = $errors['quantidade'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label for="mv_obs" class="label-field">ObservaÃ§Ã£o</label>
                    <textarea id="mv_obs" name="observacao" x-model="mov.observacao" class="input-field" rows="3" maxlength="500" placeholder="Ex: NF 12345, consumo no serviÃ§o de alvenaria"></textarea>
                    <?php if ($err = $errors['observacao'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-700">
                    <button type="submit" class="btn-primary flex-1">Registrar</button>
                    <button type="button" @click="movModal = false" class="btn-secondary flex-1 justify-center">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
