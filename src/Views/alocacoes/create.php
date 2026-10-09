<?php
$currentRoute = 'alocacoes';
$pageTitle = 'Nova Alocação';
$breadcrumb = [
    ['label' => 'Alocações', 'url' => '/alocacoes'],
    ['label' => 'Nova Alocação'],
];
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Nova Alocação</h1>
            <p class="text-slate-400 mt-1">Aloque um funcionário a uma obra</p>
        </div>
        <a href="/alocacoes" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <?php if (empty($funcionarios)): ?>
        <div class="bg-slate-900 border border-amber-700/50 rounded-xl p-6 text-center">
            <p class="text-slate-300">Nenhum funcionário ativo cadastrado.</p>
            <a href="/funcionarios/create" class="btn-primary inline-flex mt-4">Criar Funcionário</a>
        </div>
    <?php else: ?>
        <form method="POST" action="/alocacoes" class="bg-slate-900 border border-slate-700 rounded-xl p-6 space-y-6" novalidate>
            <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="funcionario_id" class="label-field">Funcionário <span class="text-red-400">*</span></label>
                    <select id="funcionario_id" name="funcionario_id" class="input-field" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($funcionarios as $f): ?>
                            <option value="<?= $f->id ?>" <?= App::old('funcionario_id') == $f->id ? 'selected' : '' ?>>
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
                            <option value="<?= $o->id ?>" <?= App::old('obra_id') == $o->id ? 'selected' : '' ?>>
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
                <input type="text" id="funcao" name="funcao" class="input-field" placeholder="Ex: Alvenaria, Instalações elétricas" value="<?= htmlspecialchars(App::old('funcao')) ?>" maxlength="120">
                <?php if ($err = Session::getErrors()['funcao'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="data_inicio" class="label-field">Início <span class="text-red-400">*</span></label>
                    <input type="date" id="data_inicio" name="data_inicio" class="input-field" value="<?= htmlspecialchars(App::old('data_inicio') ?: date('Y-m-d')) ?>" required>
                    <?php if ($err = Session::getErrors()['data_inicio'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="data_fim" class="label-field">Fim</label>
                    <input type="date" id="data_fim" name="data_fim" class="input-field" value="<?= htmlspecialchars(App::old('data_fim')) ?>">
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
                    Criar Alocação
                </button>
                <a href="/alocacoes" class="btn-secondary flex-1 justify-center">Cancelar</a>
            </div>
        </form>
    <?php endif; ?>
</div>
