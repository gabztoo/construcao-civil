-- 010_create_movimentacoes_estoque_table.sql
CREATE TABLE IF NOT EXISTS movimentacoes_estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    material_id INT NOT NULL,
    tipo ENUM('entrada','saida','transferencia') NOT NULL,
    quantidade DECIMAL(12,3) NOT NULL,
    observacao VARCHAR(500) NULL,
    usuario_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiais(id) ON DELETE CASCADE,
    INDEX idx_obra (obra_id),
    INDEX idx_material (material_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
