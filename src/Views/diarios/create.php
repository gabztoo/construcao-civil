<?php
$currentRoute = 'diarios';
$pageTitle = 'Novo Diário de Obra';
$breadcrumb = [
    ['label' => 'Diários de Obra', 'url' => '/diarios'],
    ['label' => 'Novo Diário'],
];
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Novo Diário de Obra</h1>
            <p class="text-slate-400 mt-1">Registre o andamento do dia</p>
        </div>
        <a href="/diarios" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <form method="POST" action="/diarios" class="bg-slate-900 border border-slate-700 rounded-xl p-6 space-y-6" novalidate>
        <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="data" class="label-field">Data <span class="text-red-400">*</span></label>
                <input type="date" id="data" name="data" class="input-field" value="<?= htmlspecialchars(App::old('data') ?: date('Y-m-d')) ?>" required>
                <?php if ($err = Session::getErrors()['data'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="titulo" class="label-field">Título <span class="text-red-400">*</span></label>
                <input type="text" id="titulo" name="titulo" class="input-field" placeholder="Ex: Fundação concluída" value="<?= htmlspecialchars(App::old('titulo')) ?>" required maxlength="200">
                <?php if ($err = Session::getErrors()['titulo'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="descricao" class="label-field">Descrição <span class="text-red-400">*</span></label>
            <textarea id="descricao" name="descricao" class="input-field" rows="6" placeholder="Atividades realizadas, ocorrências, clima, mão de obra alocada..." required maxlength="5000"><?= htmlspecialchars(App::old('descricao')) ?></textarea>
            <?php if ($err = Session::getErrors()['descricao'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-700">
            <button type="submit" class="btn-primary flex-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Criar Diário
            </button>
            <a href="/diarios" class="btn-secondary flex-1 justify-center">Cancelar</a>
        </div>
    </form>
</div>
