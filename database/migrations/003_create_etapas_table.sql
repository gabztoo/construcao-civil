-- 003_create_etapas_table.sql
CREATE TABLE IF NOT EXISTS etapas_obra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    nome VARCHAR(120) NOT NULL,
    descricao TEXT,
    ordem INT DEFAULT 0,
    percentual_conclusao DECIMAL(5,2) DEFAULT 0,
    data_inicio DATE NULL,
    data_fim_prevista DATE NULL,
    data_fim_real DATE NULL,
    status ENUM('pendente','em_andamento','concluida','atrasada') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    INDEX idx_obra_ordem (obra_id, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;