<?php
$currentUser = Auth::getInstance()->user();
$isAdmin = Auth::getInstance()->hasRole('admin');
$papeisUser = [
    'admin' => 'Administrador',
    'engenheiro' => 'Engenheiro',
    'mestre' => 'Mestre de Obras',
    'comprador' => 'Comprador',
    'financeiro' => 'Financeiro',
];
?>

<!-- Sidebar Overlay (Mobile) -->
<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-black/50 lg:hidden"
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    aria-hidden="true"
></div>

<!-- Sidebar -->
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 sidebar-transition lg:translate-x-0 lg:static lg:z-auto flex flex-col"
    aria-label="Menu principal"
>
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="p-5 border-b border-slate-800">
            <a href="/dashboard" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 bg-slate-800">
                    <img src="/images/hermes.jpg" alt="Hermes" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-wider text-white">HERMES</h1>
                    <p class="text-xs text-slate-500">Gestão de Obras</p>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto" aria-label="Navegação principal">
            <a href="/dashboard" class="sidebar-link" :class="{ active: currentRoute === 'dashboard' }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            <a href="/obras" class="sidebar-link" :class="{ active: currentRoute === 'obras' }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Obras
            </a>

            <a href="/diarios" class="sidebar-link" :class="{ active: currentRoute === 'diarios' }">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Diários de Obra
            </a>

            <a href="#" class="sidebar-link opacity-50 cursor-not-allowed" onclick="return false" title="Em desenvolvimento">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Almoxarifado
            </a>

            <a href="#" class="sidebar-link opacity-50 cursor-not-allowed" onclick="return false" title="Em desenvolvimento">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Compras
            </a>

            <a href="#" class="sidebar-link opacity-50 cursor-not-allowed" onclick="return false" title="Em desenvolvimento">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Financeiro
            </a>

            <a href="#" class="sidebar-link opacity-50 cursor-not-allowed" onclick="return false" title="Em desenvolvimento">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Mão de Obra
            </a>

            <!-- Configurações (admin) -->
            <?php if ($isAdmin): ?>
            <div class="pt-4 mt-4 border-t border-slate-800">
                <p class="px-3 text-xs text-slate-500 uppercase tracking-wider mb-3">Configurações</p>
                <a href="/usuarios" class="sidebar-link" :class="{ active: currentRoute === 'usuarios' }">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Usuários
                </a>
            </div>
            <?php endif; ?>
        </nav>

        <!-- User & Logout -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-sm font-medium text-slate-200 flex-shrink-0">
                    <?= strtoupper(mb_substr($currentUser['nome'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($currentUser['nome'] ?? 'Usuário') ?></p>
                    <p class="text-xs text-slate-500 capitalize"><?= htmlspecialchars($papeisUser[$currentUser['papel'] ?? ''] ?? ucfirst($currentUser['papel'] ?? 'engenheiro')) ?></p>
                </div>
            </div>
            <a href="/logout" class="sidebar-link text-red-400 hover:bg-red-900/20 hover:text-red-300">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sair
            </a>
        </div>
    </div>
</aside>
