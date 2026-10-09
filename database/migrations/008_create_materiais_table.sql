-- 008_create_materiais_table.sql
CREATE TABLE IF NOT EXISTS materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_sku VARCHAR(30) NOT NULL UNIQUE,
    nome VARCHAR(120) NOT NULL,
    categoria VARCHAR(30) NOT NULL DEFAULT 'diversos',
    unidade_medida VARCHAR(10) NOT NULL DEFAULT 'UN',
    estoque_minimo DECIMAL(12,3) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 009_create_estoque_obra_table.sql
CREATE TABLE IF NOT EXISTS estoque_obra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    material_id INT NOT NULL,
    quantidade_atual DECIMAL(12,3) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_obra_material (obra_id, material_id),
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiais(id) ON DELETE CASCADE,
    INDEX idx_material (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
