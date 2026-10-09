-- 013_create_oc_itens_table.sql
CREATE TABLE IF NOT EXISTS oc_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ordem_compra_id INT NOT NULL,
    material_id INT NULL,
    descricao VARCHAR(200) NOT NULL,
    quantidade DECIMAL(12,3) NOT NULL,
    valor_unitario DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ordem_compra_id) REFERENCES ordens_compra(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiais(id) ON DELETE SET NULL,
    INDEX idx_oc (ordem_compra_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
