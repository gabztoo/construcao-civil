<?php
/**
 * Migration Runner - Execute no terminal: php database/migrate.php
 * Ou acesse via browser: /database/migrate.php (apenas em desenvolvimento)
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../src/Config/Database.php';

$pdo = Database::getInstance();

$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

echo "=== Hermes Migration Runner ===\n\n";

if (empty($files)) {
    echo "Nenhum arquivo de migration encontrado em {$migrationsDir}\n";
    exit(1);
}

$executed = 0;
$errors = 0;

foreach ($files as $file) {
    $filename = basename($file);
    echo "Executando: {$filename} ... ";
    
    try {
        $sql = file_get_contents($file);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        echo "✓ OK\n";
        $executed++;
    } catch (PDOException $e) {
        echo "✗ ERRO\n";
        echo "  {$e->getMessage()}\n";
        $errors++;
    }
}

echo "\n=== Resumo ===\n";
echo "Executadas: {$executed}\n";
echo "Erros: {$errors}\n";

if ($errors === 0) {
    echo "\n✓ Todas as migrations executadas com sucesso!\n";
    
    // Criar admin se não existir
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM usuarios WHERE email = ?");
        $stmt->execute(['admin@hermes.local']);
        if ($stmt->fetch()['c'] == 0) {
            $hash = password_hash('admin123', PASSWORD_ARGON2ID);
            $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES (?, ?, ?, ?)")
                ->execute(['Admin Hermes', 'admin@hermes.local', $hash, 'admin']);
            echo "✓ Usuário admin criado (email: admin@hermes.local, senha: admin123)\n";
        }
    } catch (PDOException $e) {
        echo "Aviso: não foi possível verificar/criar admin: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n✗ Algumas migrations falharam. Verifique os erros acima.\n";
    exit(1);
}