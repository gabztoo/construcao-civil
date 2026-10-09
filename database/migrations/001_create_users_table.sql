-- 001_create_users_table.sql
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    papel ENUM('admin','engenheiro','mestre','comprador','financeiro') DEFAULT 'engenheiro',
    ativo BOOLEAN DEFAULT TRUE,
    ultimo_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir admin padrão (senha: admin123)
INSERT IGNORE INTO usuarios (nome, email, senha_hash, papel) VALUES 
('Admin Hermes', 'admin@hermes.local', '$argon2id$v=19$m=65536,t=4,p=1$WXZ4NElTLmdjb01peEt0Mg$JqKlojYO1nMgMz02FOZ9Md/WnLz0uuP8QogWbJTmdaM', 'admin');