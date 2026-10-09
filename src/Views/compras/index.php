<?php
$currentRoute = 'compras';
$pageTitle = 'Compras';
$breadcrumb = [
    ['label' => 'Compras'],
];
$errors = Session::getErrors();
$errosOc = ['codigo_oc', 'obra_id', 'fornecedor_id'];
$abrirModalOc = (bool) array_intersect($errosOc, array_keys($errors));
$errosForn = ['razao_social', 'cnpj_cpf', 'email'];
$abrirModalForn = (bool) array_intersect($errosForn, array_keys($errors));
$abrirAbaForn = $abrirModalForn || ($abaInicial === 'fornecedores');
?>

<div x-data="{
    aba: '<?= $abrirAbaForn ? 'fornecedores' : 'oc' ?>',
    ocModal: <?= $abrirModalOc ? 'true' : 'false' ?>,
    fornModal: <?= $abrirModalForn ? 'true' : 'false' ?>,
    fornEditId: 0,
    forn: { razao_social: '', cnpj_cpf: '', contato_nome: '', telefone: '', email: '', categoria: '' },
    itens: [{ material_id: '', descricao: '', quantidade: '', valor_unitario: '' }],
    openOc() { this.itens = [{ material_id: '', descricao: '', quantidade: '', valor_unitario: '' }]; this.ocModal = true; },
    openFornCreate() {
        this.fornEditId = 0;
        this.forn = { razao_social: '', cnpj_cpf: '', contato_nome: '', telefone: '', email: '', categoria: '' };
        this.fornModal = true;
    },
    openFornEdit(d) {
        this.fornEditId = d.id;
        this.forn = { razao_social: d.razao_social, cnpj_cpf: d.cnpj_cpf, contato_nome: d.contato_nome || '', telefone: d.telefone || '', email: d.email || '', categoria: d.categoria || '' };
        this.fornModal = true;
    },
    addItem() { this.itens.push({ material_id: '', descricao: '', quantidade: '', valor_unitario: '' }); },
    removeItem(i) { this.itens.splice(i, 1); },
    totalOc() {
        return this.itens.reduce((s, it) => s + (parseFloat(String(it.quantidade).replace(',', '.')) || 0) * (parseFloat(String(it.valor_unitario).replace(',', '.')) || 0), 0);
    },
    materialChanged(i) {
        const id = this.itens[i].material_id;
        if (!id) return;
        const sel = document.getElementById('itens_material_' + i);
        let nome = '';
        if (sel) { for (const opt of sel.options) { if (opt.value === id) { nome = opt.dataset.nome || ''; break; } } }
        if (nome && !this.itens[i].descricao) this.itens[i].descricao = nome;
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Compras</h1>
            <p class="text-slate-400 mt-1">Ordens de compra e cadastro de fornecedores</p>
        </div>
        <div class="flex gap-3">
            <button type="button" @click="aba = 'fornecedores'; openFornCreate()" class="btn-secondary w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Fornecedor
            </button>
            <button type="button" @click="aba = 'oc'; openOc()" class="btn-primary w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Ordem de Compra
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-6 bg-slate-900 border border-slate-800 rounded-xl p-1 w-fit">
        <button type="button" @click="aba = 'oc'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            :class="aba === 'oc' ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white'">
            Ordens de Compra
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-amber-900/50 text-amber-300"><?= (int) $pagination['total'] ?></span>
        </button>
        <button type="button" @click="aba = 'fornecedores'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            :class="aba === 'fornecedores' ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white'">
            Fornecedores
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-slate-700 text-slate-300"><?= count($fornecedoresList) ?></span>
        </button>
    </div>

    <!-- ABA: Ordens de Compra -->
    <div x-show="aba === 'oc'" x-cloak>
        <div class="table-container mb-6">
            <div class="p-4 border-b border-slate-700">
                <form method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por código, fornecedor ou obra..." class="input-field pl-10">
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

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr class="text-left text-sm text-slate-400">
                            <th class="px-4 py-3 font-medium">Código</th>
                            <th class="px-4 py-3 font-medium hidden md:table-cell">Obra</th>
                            <th class="px-4 py-3 font-medium">Fornecedor</th>
                            <th class="px-4 py-3 font-medium">Valor</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php if (empty($ocs)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <p class="text-lg font-medium text-slate-400">Nenhuma ordem de compra encontrada</p>
                                    <button type="button" @click="openOc()" class="btn-primary inline-flex mt-4">Criar Ordem de Compra</button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ocs as $oc): ?>
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-sm text-white"><?= htmlspecialchars($oc->codigo_oc ?: $oc->numero) ?></span>
                                        <?php if ($oc->data_pedido): ?>
                                            <p class="text-xs text-slate-500 mt-0.5"><?= date('d/m/Y', strtotime($oc->data_pedido)) ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 hidden md:table-cell">
                                        <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($oc->obra_codigo) ?></span>
                                        <p class="text-sm text-white truncate max-w-[160px]"><?= htmlspecialchars($oc->obra_nome) ?></p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-300 truncate max-w-[180px]">
                                        <?= htmlspecialchars($oc->fornecedor_nome ?: $oc->fornecedor ?: '—') ?>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-white whitespace-nowrap">
                                        R$ <?= number_format((float) $oc->valor_total, 2, ',', '.') ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="badge <?= OrdemCompra::badge($oc->status) ?>">
                                            <?= htmlspecialchars($statusList[$oc->status] ?? $oc->status) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <?php if ($oc->status === 'pendente'): ?>
                                                <form method="POST" action="/compras/<?= $oc->id ?>/status" class="inline">
                                                    <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">
                                                    <input type="hidden" name="status" value="aprovada">
                                                    <button type="submit" class="p-2 rounded-lg hover:bg-emerald-900/30 text-slate-400 hover:text-emerald-400 transition-colors" title="Aprovação rápida">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php elseif ($oc->status === 'aprovada'): ?>
                                                <form method="POST" action="/compras/<?= $oc->id ?>/status" class="inline">
                                                    <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">
                                                    <input type="hidden" name="status" value="entregue">
                                                    <button type="submit" class="p-2 rounded-lg hover:bg-blue-900/30 text-slate-400 hover:text-blue-400 transition-colors" title="Marcar como entregue">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="/compras/<?= $oc->id ?>/edit" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="/compras/<?= $oc->id ?>/delete" onsubmit="return confirm('Excluir a ordem de compra <?= htmlspecialchars(addslashes($oc->codigo_oc ?: $oc->numero)) ?>? Esta ação não pode ser desfeita.')" class="inline">
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

    <!-- ABA: Fornecedores -->
    <div x-show="aba === 'fornecedores'" x-cloak>
        <div class="table-container">
            <div class="p-4 border-b border-slate-700 flex items-center justify-between gap-4">
                <h2 class="text-sm font-semibold text-white">Cadastro de Fornecedores</h2>
                <button type="button" @click="openFornCreate()" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Fornecedor
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr class="text-left text-sm text-slate-400">
                            <th class="px-4 py-3 font-medium">Razão Social</th>
                            <th class="px-4 py-3 font-medium">CNPJ/CPF</th>
                            <th class="px-4 py-3 font-medium hidden md:table-cell">Contato</th>
                            <th class="px-4 py-3 font-medium hidden lg:table-cell">Telefone</th>
                            <th class="px-4 py-3 font-medium hidden lg:table-cell">Email</th>
                            <th class="px-4 py-3 font-medium hidden md:table-cell">Categoria</th>
                            <th class="px-4 py-3 font-medium text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php if (empty($fornecedoresList)): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <p class="text-lg font-medium text-slate-400">Nenhum fornecedor cadastrado</p>
                                    <button type="button" @click="openFornCreate()" class="btn-primary inline-flex mt-4">Cadastrar Fornecedor</button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($fornecedoresList as $f): ?>
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="px-4 py-4 text-sm font-medium text-white truncate max-w-[200px]"><?= htmlspecialchars($f->razao_social) ?></td>
                                    <td class="px-4 py-4 font-mono text-sm text-slate-300"><?= htmlspecialchars($f->cnpj_cpf) ?></td>
                                    <td class="px-4 py-4 hidden md:table-cell text-sm text-slate-400"><?= htmlspecialchars($f->contato_nome ?: '—') ?></td>
                                    <td class="px-4 py-4 hidden lg:table-cell text-sm text-slate-400"><?= htmlspecialchars($f->telefone ?: '—') ?></td>
                                    <td class="px-4 py-4 hidden lg:table-cell text-sm text-slate-400 truncate max-w-[180px]"><?= htmlspecialchars($f->email ?: '—') ?></td>
                                    <td class="px-4 py-4 hidden md:table-cell">
                                        <?php if ($f->categoria): ?>
                                            <span class="badge bg-slate-700/50 text-slate-300"><?= htmlspecialchars($categoriasFornecedor[$f->categoria] ?? $f->categoria) ?></span>
                                        <?php else: ?>
                                            <span class="text-slate-600">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" @click='openFornEdit(<?= htmlspecialchars(json_encode([
                                                'id' => (int) $f->id,
                                                'razao_social' => $f->razao_social,
                                                'cnpj_cpf' => $f->cnpj_cpf,
                                                'contato_nome' => $f->contato_nome,
                                                'telefone' => $f->telefone,
                                                'email' => $f->email,
                                                'categoria' => $f->categoria,
                                            ]), ENT_QUOTES) ?>)' class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <form method="POST" action="/fornecedores/<?= $f->id ?>/delete" onsubmit="return confirm('Excluir o fornecedor <?= htmlspecialchars(addslashes($f->razao_social)) ?>?')" class="inline">
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
        </div>
    </div>

    <!-- Modal Nova OC -->
    <div x-show="ocModal" x-transition.opacity.duration.150ms class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto" style="display: none;" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/60" @click="ocModal = false"></div>
        <div x-transition class="relative w-full max-w-3xl bg-slate-900 border border-slate-700 rounded-xl shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Nova Ordem de Compra</h3>
                <button type="button" @click="ocModal = false" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400" aria-label="Fechar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="/compras" class="p-6 space-y-5">
                <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="oc_codigo" class="label-field">Código da OC <span class="text-red-400">*</span></label>
                        <input type="text" id="oc_codigo" name="codigo_oc" class="input-field" placeholder="Ex: OC-2026-001" required maxlength="30">
                        <?php if ($err = $errors['codigo_oc'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="oc_data" class="label-field">Data do Pedido</label>
                        <input type="date" id="oc_data" name="data_pedido" class="input-field" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="oc_obra" class="label-field">Obra <span class="text-red-400">*</span></label>
                        <select id="oc_obra" name="obra_id" class="input-field" required>
                            <option value="">Selecione a obra...</option>
                            <?php foreach ($obras as $o): ?>
                                <option value="<?= $o->id ?>"><?= htmlspecialchars($o->codigo . ' - ' . $o->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($err = $errors['obra_id'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="oc_fornecedor" class="label-field">Fornecedor <span class="text-red-400">*</span></label>
                        <select id="oc_fornecedor" name="fornecedor_id" class="input-field" required>
                            <option value="">Selecione o fornecedor...</option>
                            <?php foreach ($fornecedores as $f): ?>
                                <option value="<?= $f->id ?>"><?= htmlspecialchars($f->razao_social) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($err = $errors['fornecedor_id'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Itens -->
                <div class="border-t border-slate-700 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <label class="label-field mb-0">Itens da OC <span class="text-red-400">*</span></label>
                        <button type="button" @click="addItem()" class="btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar item
                        </button>
                    </div>

                    <template x-for="(item, i) in itens" :key="i">
                        <div class="grid grid-cols-12 gap-2 mb-3 items-start bg-slate-800/40 border border-slate-700 rounded-lg p-3">
                            <div class="col-span-12 sm:col-span-4">
                                <select :id="'itens_material_' + i" :name="'itens[material_id][' + i + ']'" x-model="item.material_id" @change="materialChanged(i)" class="input-field !py-2">
                                    <option value="">Sem material (livre)</option>
                                    <?php foreach ($materiais as $m): ?>
                                        <option value="<?= $m->id ?>" data-nome="<?= htmlspecialchars($m->nome) ?>"><?= htmlspecialchars($m->codigo_sku . ' - ' . $m->nome) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <input type="text" :name="'itens[descricao][' + i + ']'" x-model="item.descricao" class="input-field !py-2" placeholder="Descrição" maxlength="200">
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <input type="number" :name="'itens[quantidade][' + i + ']'" x-model="item.quantidade" class="input-field !py-2" placeholder="Qtd." min="0.001" step="0.001" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2 relative">
                                <input type="number" :name="'itens[valor_unitario][' + i + ']'" x-model="item.valor_unitario" class="input-field !py-2 pr-8" placeholder="R$ unit." min="0" step="0.01">
                                <button type="button" @click="removeItem(i)" x-show="itens.length > 1" class="absolute right-1 top-1/2 -translate-y-1/2 p-1 text-slate-500 hover:text-red-400" title="Remover item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-end items-center gap-3 pt-3 border-t border-slate-700">
                        <span class="text-sm text-slate-400">Total da OC:</span>
                        <span class="text-lg font-bold text-white" x-text="'R$ ' + totalOc().toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                    </div>
                </div>

                <div>
                    <label for="oc_obs" class="label-field">Observações</label>
                    <textarea id="oc_obs" name="observacoes" class="input-field" rows="3" maxlength="2000" placeholder="Condições de pagamento, prazos..."></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-700">
                    <button type="submit" class="btn-primary flex-1">Criar Ordem de Compra</button>
                    <button type="button" @click="ocModal = false" class="btn-secondary flex-1 justify-center">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Fornecedor -->
    <div x-show="fornModal" x-transition.opacity.duration.150ms class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/60" @click="fornModal = false"></div>
        <div x-transition class="relative w-full max-w-lg bg-slate-900 border border-slate-700 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white" x-text="fornEditId ? 'Editar Fornecedor' : 'Novo Fornecedor'"></h3>
                <button type="button" @click="fornModal = false" class="p-2 rounded-lg hover:bg-slate-800 text-slate-400" aria-label="Fechar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" :action="fornEditId ? '/fornecedores/' + fornEditId : '/fornecedores'" class="p-6 space-y-5">
                <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

                <div>
                    <label for="f_razao" class="label-field">Razão Social <span class="text-red-400">*</span></label>
                    <input type="text" id="f_razao" name="razao_social" x-model="forn.razao_social" class="input-field" required maxlength="150">
                    <?php if ($err = $errors['razao_social'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="f_doc" class="label-field">CNPJ/CPF <span class="text-red-400">*</span></label>
                        <input type="text" id="f_doc" name="cnpj_cpf" x-model="forn.cnpj_cpf" class="input-field" required maxlength="20" placeholder="00.000.000/0000-00">
                        <?php if ($err = $errors['cnpj_cpf'][0] ?? null): ?>
                            <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="f_cat" class="label-field">Categoria</label>
                        <select id="f_cat" name="categoria" x-model="forn.categoria" class="input-field">
                            <option value="">Selecione...</option>
                            <?php foreach ($categoriasFornecedor as $valor => $rotulo): ?>
                                <option value="<?= $valor ?>"><?= htmlspecialchars($rotulo) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="f_contato" class="label-field">Contato</label>
                        <input type="text" id="f_contato" name="contato_nome" x-model="forn.contato_nome" class="input-field" maxlength="100" placeholder="Nome do responsável">
                    </div>
                    <div>
                        <label for="f_tel" class="label-field">Telefone</label>
                        <input type="text" id="f_tel" name="telefone" x-model="forn.telefone" class="input-field" maxlength="30" placeholder="(00) 00000-0000">
                    </div>
                </div>

                <div>
                    <label for="f_email" class="label-field">Email</label>
                    <input type="email" id="f_email" name="email" x-model="forn.email" class="input-field" maxlength="120" placeholder="contato@empresa.com">
                    <?php if ($err = $errors['email'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-700">
                    <button type="submit" class="btn-primary flex-1">Salvar</button>
                    <button type="button" @click="fornModal = false" class="btn-secondary flex-1 justify-center">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
