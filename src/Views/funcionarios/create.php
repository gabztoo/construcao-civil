<?php
$currentRoute = 'funcionarios';
$pageTitle = 'Novo Funcionário';
$breadcrumb = [
    ['label' => 'Mão de Obra', 'url' => '/funcionarios'],
    ['label' => 'Novo Funcionário'],
];
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Novo Funcionário</h1>
            <p class="text-slate-400 mt-1">Adicione um membro da equipe</p>
        </div>
        <a href="/funcionarios" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <form method="POST" action="/funcionarios" class="bg-slate-900 border border-slate-700 rounded-xl p-6 space-y-6" novalidate>
        <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

        <div>
            <label for="nome" class="label-field">Nome <span class="text-red-400">*</span></label>
            <input type="text" id="nome" name="nome" class="input-field" placeholder="Nome completo" value="<?= htmlspecialchars(App::old('nome')) ?>" required maxlength="120">
            <?php if ($err = Session::getErrors()['nome'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="cargo" class="label-field">Cargo</label>
                <input type="text" id="cargo" name="cargo" class="input-field" placeholder="Ex: Pedreiro, Eletricista" value="<?= htmlspecialchars(App::old('cargo')) ?>" maxlength="120">
                <?php if ($err = Session::getErrors()['cargo'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="cpf" class="label-field">CPF</label>
                <input type="text" id="cpf" name="cpf" class="input-field" placeholder="000.000.000-00" value="<?= htmlspecialchars(App::old('cpf')) ?>" maxlength="14">
                <?php if ($err = Session::getErrors()['cpf'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="telefone" class="label-field">Telefone</label>
            <input type="text" id="telefone" name="telefone" class="input-field" placeholder="(00) 00000-0000" value="<?= htmlspecialchars(App::old('telefone')) ?>" maxlength="20">
            <?php if ($err = Session::getErrors()['telefone'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-700">
            <button type="submit" class="btn-primary flex-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Criar Funcionário
            </button>
            <a href="/funcionarios" class="btn-secondary flex-1 justify-center">Cancelar</a>
        </div>
    </form>
</div>
