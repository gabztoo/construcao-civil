<?php
$currentUser = Auth::getInstance()->user();
$obrasList = Obra::query()->select(['id', 'codigo', 'nome'])->orderBy('nome', 'ASC')->get();
?>

<header class="sticky top-0 z-30 bg-slate-900/80 backdrop-blur-sm border-b border-slate-800">
    <div class="flex items-center gap-3 h-16 px-4 lg:px-6">
        <!-- Mobile menu -->
        <button
            @click="sidebarOpen = true"
            class="lg:hidden p-2 rounded-lg hover:bg-slate-800 text-slate-300 transition-colors flex-shrink-0"
            aria-label="Abrir menu"
            aria-expanded="false"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Page title / breadcrumb -->
        <div class="flex-1 min-w-0">
            <h2 class="text-lg font-semibold text-white truncate"><?= $pageTitle ?? 'Hermes' ?></h2>
            <?php if (isset($breadcrumb)): ?>
                <nav class="hidden sm:flex items-center gap-1 mt-0.5 text-sm" aria-label="Breadcrumb">
                    <a href="/dashboard" class="text-slate-400 hover:text-slate-200">Dashboard</a>
                    <?php foreach ($breadcrumb as $item): ?>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <?php if (isset($item['url'])): ?>
                            <a href="<?= $item['url'] ?>" class="text-slate-400 hover:text-slate-200"><?= htmlspecialchars($item['label']) ?></a>
                        <?php else: ?>
                            <span class="text-slate-300"><?= htmlspecialchars($item['label']) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-2 lg:gap-4 flex-shrink-0">
            <!-- Quick search -->
            <form method="GET" action="/obras" class="hidden md:block" role="search">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" name="search" placeholder="Busca rápida..." class="input-field !py-2 pl-9 w-40 lg:w-56 text-sm" aria-label="Busca rápida">
                </div>
            </form>

            <!-- Selected obra indicator -->
            <div class="hidden lg:block relative" x-data="{ obraOpen: false }" @click.outside="obraOpen = false">
                <button @click="obraOpen = !obraOpen" type="button" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-800/70 hover:bg-slate-800 border border-slate-700 text-sm text-slate-300 transition-colors max-w-[220px]" aria-label="Obra selecionada">
                    <svg class="w-4 h-4 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="truncate"><?= $obrasList ? 'Todas as obras (' . count($obrasList) . ')' : 'Nenhuma obra' ?></span>
                    <svg class="w-4 h-4 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="{ 'rotate-180': obraOpen }">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="obraOpen" x-transition.opacity.duration.150ms class="absolute right-0 mt-2 w-80 bg-slate-900 border border-slate-700 rounded-xl shadow-xl overflow-hidden z-40" style="display: none;">
                    <div class="max-h-72 overflow-y-auto divide-y divide-slate-800">
                        <?php if (empty($obrasList)): ?>
                            <p class="px-4 py-3 text-sm text-slate-500">Nenhuma obra cadastrada ainda.</p>
                        <?php else: ?>
                            <?php foreach ($obrasList as $o): ?>
                                <a href="/obras/<?= $o->id ?>" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 transition-colors">
                                    <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($o->codigo) ?></span>
                                    <span class="text-sm text-slate-300 truncate"><?= htmlspecialchars($o->nome) ?></span>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div x-data="{ notifOpen: false }" @click.outside="notifOpen = false" class="relative">
                <button @click="notifOpen = !notifOpen" type="button" class="relative p-2 rounded-lg hover:bg-slate-800 text-slate-300 transition-colors" aria-label="Notificações">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>
                <div x-show="notifOpen" x-transition.opacity.duration.150ms class="absolute right-0 mt-2 w-80 bg-slate-900 border border-slate-700 rounded-xl shadow-xl overflow-hidden z-40" style="display: none;">
                    <p class="px-4 py-3 text-sm font-medium text-white border-b border-slate-800">Notificações</p>
                    <p class="px-4 py-6 text-sm text-slate-500 text-center">Nenhuma notificação no momento.</p>
                </div>
            </div>

            <!-- Profile menu -->
            <div x-data="{ profileOpen: false }" @click.outside="profileOpen = false" class="relative">
                <button @click="profileOpen = !profileOpen" type="button" class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-800 transition-colors" aria-label="Menu do perfil">
                    <span class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-sm font-medium text-slate-200">
                        <?= strtoupper(mb_substr($currentUser['nome'] ?? 'U', 0, 1)) ?>
                    </span>
                    <svg class="w-4 h-4 text-slate-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="profileOpen" x-transition.opacity.duration.150ms class="absolute right-0 mt-2 w-64 bg-slate-900 border border-slate-700 rounded-xl shadow-xl overflow-hidden z-40" style="display: none;">
                    <div class="px-4 py-3 border-b border-slate-800">
                        <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($currentUser['nome'] ?? 'Usuário') ?></p>
                        <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($currentUser['email'] ?? '') ?></p>
                        <span class="badge bg-slate-700/50 text-slate-300 mt-2"><?= htmlspecialchars(ucfirst($currentUser['papel'] ?? '')) ?></span>
                    </div>
                    <a href="/logout" class="flex items-center gap-2 px-4 py-3 text-sm text-red-400 hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
