<!DOCTYPE html>
<html lang="pt-BR" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Hermes' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: {
                            950: '#020617',
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155',
                            600: '#475569',
                            500: '#64748b',
                            400: '#94a3b8',
                            300: '#cbd5e1',
                            200: '#e2e8f0',
                            100: '#f1f5f9',
                            50: '#f8fafc',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        :root { color-scheme: dark; }
        select option { background-color: #0f172a; color: #fff; }
    </style>
    <style type="text/tailwindcss">
        @layer components {
            .sidebar-link {
                @apply flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition-colors;
            }
            .sidebar-link.active {
                @apply bg-slate-800 text-white;
            }
            .kpi-card {
                @apply bg-slate-900 border border-slate-700 rounded-xl p-5 shadow-sm hover:border-slate-600 transition-colors;
            }
            .table-container {
                @apply bg-slate-900 border border-slate-700 rounded-xl overflow-hidden;
            }
            .btn-primary {
                @apply inline-flex items-center gap-2 px-4 py-2.5 bg-slate-600 hover:bg-slate-500 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-slate-500/50 focus:ring-offset-2 focus:ring-offset-slate-900;
            }
            .btn-secondary {
                @apply inline-flex items-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors;
            }
            .btn-danger {
                @apply inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white font-medium rounded-lg transition-colors;
            }
            .input-field {
                @apply w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500
                       focus:outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 transition-all;
            }
            .label-field {
                @apply block text-sm font-medium text-slate-300 mb-1.5;
            }
            .badge {
                @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
            }
            .modal-overlay {
                @apply fixed inset-0 bg-black/50 backdrop-blur-sm z-50;
            }
            .modal-content {
                @apply fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-slate-900 border border-slate-700 rounded-xl shadow-xl z-50 w-full max-w-lg max-h-[90vh] overflow-y-auto;
            }
            .sidebar-transition {
                @apply transition-transform duration-300 ease-in-out;
            }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex" x-cloak>

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
                    <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
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

                <!-- Placeholders para módulos futuros -->
                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="px-3 text-xs text-slate-500 uppercase tracking-wider mb-3">Em Desenvolvimento</p>
                    <a href="#" class="sidebar-link opacity-50 cursor-not-allowed">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Suprimentos
                    </a>
                    <a href="#" class="sidebar-link opacity-50 cursor-not-allowed">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Compras
                    </a>
                    <a href="#" class="sidebar-link opacity-50 cursor-not-allowed">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Financeiro
                    </a>
                    <a href="#" class="sidebar-link opacity-50 cursor-not-allowed">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Pessoas
                    </a>
                </div>
            </nav>

            <!-- User & Logout -->
            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-sm font-medium text-slate-200">
                        <?= strtoupper(substr($_SESSION['user']['nome'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($_SESSION['user']['nome'] ?? 'Usuário') ?></p>
                        <p class="text-xs text-slate-500 capitalize"><?= htmlspecialchars($_SESSION['user']['papel'] ?? 'engenheiro') ?></p>
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

    <!-- Main Content -->
    <main class="flex-1 w-full lg:ml-0 min-h-screen flex flex-col">
        <!-- Topbar -->
        <header class="sticky top-0 z-30 bg-slate-900/80 backdrop-blur-sm border-b border-slate-800">
            <div class="flex items-center justify-between h-16 px-4 lg:px-6">
                <button 
                    @click="sidebarOpen = true" 
                    class="lg:hidden p-2 rounded-lg hover:bg-slate-800 text-slate-300 transition-colors"
                    aria-label="Abrir menu"
                    aria-expanded="false"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1 lg:flex-none">
                    <h2 class="text-lg font-semibold text-white"><?= $pageTitle ?? 'Hermes' ?></h2>
                    <?php if (isset($breadcrumb)): ?>
                        <nav class="flex items-center gap-1 mt-1 text-sm" aria-label="Breadcrumb">
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

                <div class="flex items-center gap-4">
                    <!-- Notifications (placeholder) -->
                    <button class="relative p-2 rounded-lg hover:bg-slate-800 text-slate-300 transition-colors" aria-label="Notificações">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if ($success = Session::getFlash('success')): ?>
            <div class="fixed top-4 right-4 z-50 px-4 py-3 bg-emerald-900/90 border border-emerald-700 rounded-lg text-emerald-200 shadow-lg flex items-center gap-2 animate-slide-in" role="alert">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error = Session::getFlash('error')): ?>
            <div class="fixed top-4 right-4 z-50 px-4 py-3 bg-red-900/90 border border-red-700 rounded-lg text-red-200 shadow-lg flex items-center gap-2 animate-slide-in" role="alert">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="flex-1 p-4 lg:p-6">
            <?= $content ?? '' ?>
        </div>

        <!-- Footer -->
        <footer class="border-t border-slate-800 px-4 lg:px-6 py-3">
            <p class="text-xs text-slate-500 text-center">
                Hermes v1.0 &copy; 2026 - Sistema de Gestão para Construção Civil
            </p>
        </footer>
    </main>

    <script>
        // Auto-hide flash messages
        setTimeout(() => {
            document.querySelectorAll('[role="alert"]').forEach(el => {
                el.style.transition = 'opacity 0.3s, transform 0.3s';
                el.style.opacity = '0';
                el.style.transform = 'translateX(100%)';
                setTimeout(() => el.remove(), 300);
            });
        }, 5000);

        // Close mobile sidebar on link click
        document.addEventListener('click', (e) => {
            if (e.target.closest('.sidebar-link') && window.innerWidth < 1024) {
                document.querySelector('[x-data]')._x_dataStack[0].sidebarOpen = false;
            }
        });
    </script>

    <style>
        @keyframes slide-in {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in { animation: slide-in 0.3s ease-out; }
    </style>
</body>
</html>