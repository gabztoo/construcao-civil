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

    <?php require __DIR__ . '/partials/sidebar.php'; ?>



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