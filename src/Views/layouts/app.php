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
        <?php require __DIR__ . '/partials/header.php'; ?>



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