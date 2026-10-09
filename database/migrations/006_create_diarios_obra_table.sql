-- 006_create_diarios_obra_table.sql
CREATE TABLE IF NOT EXISTS diarios_obra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    data DATE NOT NULL,
    titulo VARCHAR(200),
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    INDEX idx_obra_data (obra_id, data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
