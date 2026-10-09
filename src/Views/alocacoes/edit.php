<?php
$currentRoute = 'alocacoes';
$pageTitle = 'Editar Alocação';
$breadcrumb = [
    ['label' => 'Alocações', 'url' => '/alocacoes'],
    ['label' => 'Editar Alocação'],
];
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Editar Alocação</h1>
            <p class="text-slate-400 mt-1">Ajuste os dados da alocação</p>
        </div>
        <a href="/alocacoes" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <form method="POST" action="/alocacoes/<?= $alocacao->id ?>" class="bg-slate-900 border border-slate-700 rounded-xl p-6 space-y-6" novalidate>
        <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="funcionario_id" class="label-field">Funcionário <span class="text-red-400">*</span></label>
                <select id="funcionario_id" name="funcionario_id" class="input-field" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($funcionarios as $f): ?>
                        <option value="<?= $f->id ?>" <?= (int) (App::old('funcionario_id') ?: $alocacao->funcionario_id) === (int) $f->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f->nome . ($f->cargo ? ' - ' . $f->cargo : '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($err = Session::getErrors()['funcionario_id'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="obra_id" class="label-field">Obra <span class="text-red-400">*</span></label>
                <select id="obra_id" name="obra_id" class="input-field" required>
                    <option value="">Selecione a obra...</option>
                    <?php foreach ($obras as $o): ?>
                        <option value="<?= $o->id ?>" <?= (int) (App::old('obra_id') ?: $alocacao->obra_id) === (int) $o->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($o->codigo . ' - ' . $o->nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($err = Session::getErrors()['obra_id'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="funcao" class="label-field">Função na Obra</label>
            <input type="text" id="funcao" name="funcao" class="input-field" placeholder="Ex: Alvenaria, Instalações elétricas" value="<?= htmlspecialchars(App::old('funcao') !== '' ? App::old('funcao') : (string) $alocacao->funcao) ?>" maxlength="120">
            <?php if ($err = Session::getErrors()['funcao'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="data_inicio" class="label-field">Início <span class="text-red-400">*</span></label>
                <input type="date" id="data_inicio" name="data_inicio" class="input-field" value="<?= htmlspecialchars(App::old('data_inicio') ?: $alocacao->data_inicio) ?>" required>
                <?php if ($err = Session::getErrors()['data_inicio'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="data_fim" class="label-field">Fim</label>
                <input type="date" id="data_fim" name="data_fim" class="input-field" value="<?= htmlspecialchars(App::old('data_fim') !== '' ? App::old('data_fim') : (string) $alocacao->data_fim) ?>">
                <p class="mt-1 text-xs text-slate-500">Deixe em branco para alocação em curso.</p>
                <?php if ($err = Session::getErrors()['data_fim'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-700">
            <button type="submit" class="btn-primary flex-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Salvar Alterações
            </button>
            <a href="/alocacoes" class="btn-secondary flex-1 justify-center">Cancelar</a>
        </div>
    </form>
</div>
