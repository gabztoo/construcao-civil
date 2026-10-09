<?php
$currentRoute = 'usuarios';
$pageTitle = 'Novo Usuário';
$breadcrumb = [
    ['label' => 'Configurações'],
    ['label' => 'Usuários', 'url' => '/usuarios'],
    ['label' => 'Novo Usuário'],
];
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Novo Usuário</h1>
            <p class="text-slate-400 mt-1">Crie um novo acesso ao sistema</p>
        </div>
        <a href="/usuarios" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <form method="POST" action="/usuarios" class="bg-slate-900 border border-slate-700 rounded-xl p-6 space-y-6" novalidate>
        <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

        <div>
            <label for="nome" class="label-field">Nome <span class="text-red-400">*</span></label>
            <input type="text" id="nome" name="nome" class="input-field" placeholder="Nome completo" value="<?= htmlspecialchars(App::old('nome')) ?>" required maxlength="120">
            <?php if ($err = Session::getErrors()['nome'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="email" class="label-field">E-mail <span class="text-red-400">*</span></label>
            <input type="email" id="email" name="email" class="input-field" placeholder="usuario@hermes.local" value="<?= htmlspecialchars(App::old('email')) ?>" required maxlength="180">
            <?php if ($err = Session::getErrors()['email'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="papel" class="label-field">Cargo <span class="text-red-400">*</span></label>
            <select id="papel" name="papel" class="input-field" required>
                <option value="">Selecione o cargo...</option>
                <?php foreach ($papeis as $valor => $rotulo): ?>
                    <option value="<?= $valor ?>" <?= App::old('papel') === $valor ? 'selected' : '' ?>><?= htmlspecialchars($rotulo) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="mt-1 text-xs text-slate-500">O cargo define o que o usuário pode ver e fazer no sistema.</p>
            <?php if ($err = Session::getErrors()['papel'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="senha" class="label-field">Senha <span class="text-red-400">*</span></label>
            <input type="password" id="senha" name="senha" class="input-field" placeholder="Mínimo 6 caracteres" required minlength="6" maxlength="100" autocomplete="new-password">
            <?php if ($err = Session::getErrors()['senha'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-700">
            <button type="submit" class="btn-primary flex-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Criar Usuário
            </button>
            <a href="/usuarios" class="btn-secondary flex-1 justify-center">Cancelar</a>
        </div>
    </form>
</div>
