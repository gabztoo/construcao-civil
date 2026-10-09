<?php
$currentRoute = 'compras';
$pageTitle = 'Nova Ordem de Compra';
$breadcrumb = [
    ['label' => 'Compras', 'url' => '/compras'],
    ['label' => 'Ordens de Compra', 'url' => '/compras'],
    ['label' => 'Nova Ordem de Compra'],
];
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Nova Ordem de Compra</h1>
            <p class="text-slate-400 mt-1">Registre uma nova ordem de compra</p>
        </div>
        <a href="/compras" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <form method="POST" action="/compras" class="bg-slate-900 border border-slate-700 rounded-xl p-6 space-y-6" novalidate>
        <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="obra_id" class="label-field">Obra <span class="text-red-400">*</span></label>
                <select id="obra_id" name="obra_id" class="input-field" required>
                    <option value="">Selecione a obra...</option>
                    <?php foreach ($obras as $o): ?>
                        <option value="<?= $o->id ?>" <?= App::old('obra_id') == $o->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($o->codigo . ' - ' . $o->nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($err = Session::getErrors()['obra_id'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="numero" class="label-field">Número <span class="text-red-400">*</span></label>
                <input type="text" id="numero" name="numero" class="input-field" placeholder="Ex: OC-2026-001" value="<?= htmlspecialchars(App::old('numero')) ?>" required maxlength="30">
                <?php if ($err = Session::getErrors()['numero'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="fornecedor" class="label-field">Fornecedor</label>
                <input type="text" id="fornecedor" name="fornecedor" class="input-field" placeholder="Nome do fornecedor" value="<?= htmlspecialchars(App::old('fornecedor')) ?>" maxlength="200">
                <?php if ($err = Session::getErrors()['fornecedor'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="valor_total" class="label-field">Valor Total (R$) <span class="text-red-400">*</span></label>
                <input type="number" id="valor_total" name="valor_total" class="input-field" placeholder="0,00" value="<?= htmlspecialchars(App::old('valor_total')) ?>" min="0" step="0.01" required>
                <?php if ($err = Session::getErrors()['valor_total'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="status" class="label-field">Status <span class="text-red-400">*</span></label>
                <select id="status" name="status" class="input-field" required>
                    <?php foreach ($statusList as $valor => $rotulo): ?>
                        <option value="<?= $valor ?>" <?= (App::old('status') ?: 'pendente') === $valor ? 'selected' : '' ?>><?= htmlspecialchars($rotulo) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($err = Session::getErrors()['status'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="data_pedido" class="label-field">Data do Pedido</label>
                <input type="date" id="data_pedido" name="data_pedido" class="input-field" value="<?= htmlspecialchars(App::old('data_pedido') ?: date('Y-m-d')) ?>">
                <?php if ($err = Session::getErrors()['data_pedido'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="observacoes" class="label-field">Observações</label>
            <textarea id="observacoes" name="observacoes" class="input-field" rows="4" placeholder="Itens, condições de pagamento, prazos..." maxlength="2000"><?= htmlspecialchars(App::old('observacoes')) ?></textarea>
            <?php if ($err = Session::getErrors()['observacoes'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-700">
            <button type="submit" class="btn-primary flex-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Criar Ordem de Compra
            </button>
            <a href="/compras" class="btn-secondary flex-1 justify-center">Cancelar</a>
        </div>
    </form>
</div>
