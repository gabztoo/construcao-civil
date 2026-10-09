-- 015_create_lancamentos_financeiros_table.sql
CREATE TABLE IF NOT EXISTS lancamentos_financeiros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NULL,
    tipo ENUM('receita','despesa') NOT NULL,
    descricao VARCHAR(200) NOT NULL,
    valor DECIMAL(15,2) NOT NULL,
    data_vencimento DATE NOT NULL,
    data_pagamento DATE NULL,
    status ENUM('pendente','pago','atrasado') NOT NULL DEFAULT 'pendente',
    categoria_id INT NULL,
    comprovante_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias_financeiras(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_tipo (tipo),
    INDEX idx_vencimento (data_vencimento),
    INDEX idx_obra (obra_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
