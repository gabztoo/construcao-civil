<!DOCTYPE html>
<html lang="pt-BR">
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
    <style>
        :root { color-scheme: dark; }
        select option { background-color: #0f172a; color: #fff; }
    </style>
    <style type="text/tailwindcss">
        @layer components {
            .input-field {
                @apply w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500
                       focus:outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 transition-all;
            }
            .btn-primary {
                @apply w-full py-3 px-4 bg-slate-600 hover:bg-slate-500 text-white font-semibold rounded-lg
                       transition-colors focus:outline-none focus:ring-2 focus:ring-slate-500/50 focus:ring-offset-2 focus:ring-offset-slate-950;
            }
        }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4">
    <?= $content ?? '' ?>
</body>
</html>