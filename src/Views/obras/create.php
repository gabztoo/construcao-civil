<?php
$currentRoute = 'obras';
$pageTitle = 'Nova Obra';
$breadcrumb = [
    ['label' => 'Obras', 'url' => '/obras'],
    ['label' => 'Nova Obra'],
];
?>

<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Nova Obra</h1>
            <p class="text-slate-400 mt-1">Preencha os dados da nova obra</p>
        </div>
        <a href="/obras" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>

    <form method="POST" action="/obras" class="bg-slate-900 border border-slate-700 rounded-xl p-6 space-y-6" novalidate>
        <input type="hidden" name="_token" value="<?= App::csrfToken() ?>">

        <!-- Identificação -->
        <fieldset>
            <legend class="text-lg font-medium text-white mb-4 pb-2 border-b border-slate-700">Identificação</legend>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="codigo" class="label-field">Código <span class="text-red-400">*</span></label>
                    <input type="text" id="codigo" name="codigo" class="input-field" placeholder="Ex: OBRA-001" value="<?= htmlspecialchars(App::old('codigo')) ?>" required maxlength="20">
                    <?php if ($err = Session::getErrors()['codigo'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="status" class="label-field">Status <span class="text-red-400">*</span></label>
                    <select id="status" name="status" class="input-field" required>
                        <option value="planejamento" <?= App::old('status') === 'planejamento' ? 'selected' : '' ?>>Planejamento</option>
                        <option value="em_andamento" <?= App::old('status') === 'em_andamento' ? 'selected' : '' ?>>Em Andamento</option>
                        <option value="paralisada" <?= App::old('status') === 'paralisada' ? 'selected' : '' ?>>Paralisada</option>
                        <option value="concluida" <?= App::old('status') === 'concluida' ? 'selected' : '' ?>>Concluída</option>
                        <option value="atrasada" <?= App::old('status') === 'atrasada' ? 'selected' : '' ?>>Atrasada</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label for="nome" class="label-field">Nome da Obra <span class="text-red-400">*</span></label>
                <input type="text" id="nome" name="nome" class="input-field" placeholder="Ex: Condomínio Residencial Alpha" value="<?= htmlspecialchars(App::old('nome')) ?>" required maxlength="200">
                <?php if ($err = Session::getErrors()['nome'][0] ?? null): ?>
                    <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                <?php endif; ?>
            </div>
        </fieldset>

        <!-- Localização -->
        <fieldset>
            <legend class="text-lg font-medium text-white mb-4 pb-2 border-b border-slate-700">Localização</legend>
            <div class="space-y-4">
                <div>
                    <label for="endereco" class="label-field">Endereço Completo</label>
                    <textarea id="endereco" name="endereco" rows="2" class="input-field" placeholder="Rua, número, bairro, cidade, estado" maxlength="500"><?= htmlspecialchars(App::old('endereco')) ?></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="label-field">Latitude</label>
                        <input type="text" id="latitude" name="latitude" class="input-field" placeholder="-23.5505" value="<?= htmlspecialchars(App::old('latitude')) ?>">
                    </div>
                    <div>
                        <label for="longitude" class="label-field">Longitude</label>
                        <input type="text" id="longitude" name="longitude" class="input-field" placeholder="-46.6333" value="<?= htmlspecialchars(App::old('longitude')) ?>">
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Dados Técnicos -->
        <fieldset>
            <legend class="text-lg font-medium text-white mb-4 pb-2 border-b border-slate-700">Dados Técnicos</legend>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="orcamento_total" class="label-field">Orçamento Total (R$)</label>
                    <input type="text" id="orcamento_total" name="orcamento_total" class="input-field" placeholder="0,00" value="<?= htmlspecialchars(App::old('orcamento_total')) ?>" inputmode="decimal">
                    <?php if ($err = Session::getErrors()['orcamento_total'][0] ?? null): ?>
                        <p class="mt-1 text-sm text-red-400"><?= htmlspecialchars($err) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="responsavel_tecnico" class="label-field">Responsável Técnico</label>
                    <input type="text" id="responsavel_tecnico" name="responsavel_tecnico" class="input-field" placeholder="Nome do engenheiro/arquiteto responsável" value="<?= htmlspecialchars(App::old('responsavel_tecnico')) ?>" maxlength="120">
                </div>
                <div>
                    <label for="crea_rrt" class="label-field">CREA / RRT</label>
                    <input type="text" id="crea_rrt" name="crea_rrt" class="input-field" placeholder="Número do registro" value="<?= htmlspecialchars(App::old('crea_rrt')) ?>" maxlength="50">
                </div>
            </div>
        </fieldset>

        <!-- Datas -->
        <fieldset>
            <legend class="text-lg font-medium text-white mb-4 pb-2 border-b border-slate-700">Cronograma</legend>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="data_inicio" class="label-field">Início Previsto</label>
                    <input type="date" id="data_inicio" name="data_inicio" class="input-field" value="<?= htmlspecialchars(App::old('data_inicio')) ?>">
                </div>
                <div>
                    <label for="data_fim_prevista" class="label-field">Término Previsto</label>
                    <input type="date" id="data_fim_prevista" name="data_fim_prevista" class="input-field" value="<?= htmlspecialchars(App::old('data_fim_prevista')) ?>">
                </div>
                <div>
                    <label for="data_fim_real" class="label-field">Término Real</label>
                    <input type="date" id="data_fim_real" name="data_fim_real" class="input-field" value="<?= htmlspecialchars(App::old('data_fim_real')) ?>">
                </div>
            </div>
        </fieldset>

        <!-- Observações -->
        <fieldset>
            <legend class="text-lg font-medium text-white mb-4 pb-2 border-b border-slate-700">Observações</legend>
            <div>
                <label for="observacoes" class="label-field">Observações Gerais</label>
                <textarea id="observacoes" name="observacoes" rows="4" class="input-field" placeholder="Informações adicionais sobre a obra..." maxlength="2000"><?= htmlspecialchars(App::old('observacoes')) ?></textarea>
            </div>
        </fieldset>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-700">
            <button type="submit" class="btn-primary flex-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Salvar Obra
            </button>
            <a href="/obras" class="btn-secondary flex-1 justify-center">Cancelar</a>
        </div>
    </form>
</div>

<script>
// Format money input
document.getElementById('orcamento_total')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value) {
        value = (parseInt(value) / 100).toFixed(2).replace('.', ',');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    e.target.value = value;
});

// Auto-generate codigo from nome if empty
document.getElementById('nome')?.addEventListener('blur', function(e) {
    const codigoField = document.getElementById('codigo');
    if (!codigoField.value && e.target.value) {
        const prefix = 'OBRA';
        const timestamp = Date.now().toString(36).toUpperCase().slice(-4);
        codigoField.value = `${prefix}-${timestamp}`;
    }
});
</script>