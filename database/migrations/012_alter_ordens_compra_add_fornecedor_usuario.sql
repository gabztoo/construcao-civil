-- 012_alter_ordens_compra_add_fornecedor_usuario.sql
ALTER TABLE ordens_compra
    ADD COLUMN codigo_oc VARCHAR(30) NULL AFTER id,
    ADD COLUMN fornecedor_id INT NULL AFTER fornecedor,
    ADD COLUMN usuario_id INT NULL AFTER fornecedor_id;

UPDATE ordens_compra SET codigo_oc = numero WHERE codigo_oc IS NULL OR codigo_oc = '';

ALTER TABLE ordens_compra ADD INDEX idx_codigo_oc (codigo_oc);
ALTER TABLE ordens_compra ADD CONSTRAINT fk_oc_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE SET NULL;
ALTER TABLE ordens_compra ADD CONSTRAINT fk_oc_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL;
