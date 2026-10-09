-- 004_create_ordens_compra_table.sql
CREATE TABLE IF NOT EXISTS ordens_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    numero VARCHAR(30) NOT NULL,
    fornecedor VARCHAR(200),
    valor_total DECIMAL(15,2) DEFAULT 0,
    status ENUM('pendente','aprovada','entregue','paga','cancelada') DEFAULT 'pendente',
    data_pedido DATE NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_obra (obra_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
