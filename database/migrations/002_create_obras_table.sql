-- 002_create_obras_table.sql
CREATE TABLE IF NOT EXISTS obras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nome VARCHAR(200) NOT NULL,
    cliente_id INT NULL,
    endereco TEXT,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    orcamento_total DECIMAL(15,2) DEFAULT 0,
    data_inicio DATE NULL,
    data_fim_prevista DATE NULL,
    data_fim_real DATE NULL,
    status ENUM('planejamento','em_andamento','paralisada','concluida','atrasada') DEFAULT 'planejamento',
    responsavel_tecnico VARCHAR(120) NULL,
    crea_rrt VARCHAR(50) NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_datas (data_inicio, data_fim_prevista)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;