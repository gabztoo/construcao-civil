-- 011_create_fornecedores_table.sql
CREATE TABLE IF NOT EXISTS fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razao_social VARCHAR(150) NOT NULL,
    cnpj_cpf VARCHAR(20) NOT NULL,
    contato_nome VARCHAR(100) NULL,
    telefone VARCHAR(30) NULL,
    email VARCHAR(120) NULL,
    categoria VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_razao_social (razao_social),
    INDEX idx_cnpj_cpf (cnpj_cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
