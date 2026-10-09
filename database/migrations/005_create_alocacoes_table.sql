-- 005_create_alocacoes_table.sql
CREATE TABLE IF NOT EXISTS alocacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    obra_id INT NOT NULL,
    funcao VARCHAR(120),
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    INDEX idx_funcionario (funcionario_id),
    INDEX idx_obra (obra_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
