<?php
$currentRoute = 'usuarios';
$pageTitle = 'Editar Usuário';
$breadcrumb = [
    ['label' => 'Configurações'],
    ['label' => 'Usuários', 'url' => '/usuarios'],
    ['label' => 'Editar Usuário'],
];
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Editar Usuário</h1>
            <p class="text-slate-400 mt-1"><?= htmlspecialchars($usuario->nome) ?></p>
        </div>
        <a href="/usuarios" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <form method="POST" action="/usuarios/<?= $usuario->id ?>" class="bg-slate-900 border border-slate-700 rounded-xl p-6 space-y-6" novalidate>
        <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

        <div>
            <label for="nome" class="label-field">Nome <span class="text-red-400">*</span></label>
            <input type="text" id="nome" name="nome" class="input-field" placeholder="Nome completo" value="<?= htmlspecialchars(App::old('nome', $usuario->nome)) ?>" required maxlength="120">
            <?php if ($err = Session::getErrors()['nome'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="email" class="label-field">E-mail <span class="text-red-400">*</span></label>
            <input type="email" id="email" name="email" class="input-field" placeholder="usuario@hermes.local" value="<?= htmlspecialchars(App::old('email', $usuario->email)) ?>" required maxlength="180">
            <?php if ($err = Session::getErrors()['email'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="papel" class="label-field">Cargo <span class="text-red-400">*</span></label>
            <select id="papel" name="papel" class="input-field" required>
                <?php foreach ($papeis as $valor => $rotulo): ?>
                    <option value="<?= $valor ?>" <?= App::old('papel', $usuario->papel) === $valor ? 'selected' : '' ?>><?= htmlspecialchars($rotulo) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="mt-1 text-xs text-slate-500">O cargo define o que o usuário pode ver e fazer no sistema.</p>
            <?php if ($err = Session::getErrors()['papel'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="senha" class="label-field">Nova Senha</label>
            <input type="password" id="senha" name="senha" class="input-field" placeholder="Deixe em branco para manter a atual" minlength="6" maxlength="100" autocomplete="new-password">
            <p class="mt-1 text-xs text-slate-500">Preencha apenas se quiser alterar a senha.</p>
            <?php if ($err = Session::getErrors()['senha'][0] ?? null): ?>
                <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-3 p-4 bg-slate-800/50 border border-slate-700 rounded-lg">
            <input type="hidden" name="ativo" value="0">
            <input type="checkbox" id="ativo" name="ativo" value="1" <?= App::old('ativo', $usuario->ativo ? '1' : '0') === '1' ? 'checked' : '' ?> class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-slate-500 focus:ring-slate-500">
            <label for="ativo" class="text-sm text-slate-300 cursor-pointer">Usuário ativo (pode fazer login)</label>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-700">
            <button type="submit" class="btn-primary flex-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Salvar Alterações
            </button>
            <a href="/usuarios" class="btn-secondary flex-1 justify-center">Cancelar</a>
        </div>
    </form>
</div>
