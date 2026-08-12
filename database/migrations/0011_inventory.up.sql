ALTER TABLE produto
    ADD COLUMN sku VARCHAR(80) NULL AFTER nome,
    ADD COLUMN custo DECIMAL(10,2) NULL AFTER preco,
    ADD COLUMN estoque_minimo INT UNSIGNED NOT NULL DEFAULT 0 AFTER estoque,
    ADD COLUMN status ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo' AFTER categoria;

CREATE UNIQUE INDEX uk_produto_sku_academia ON produto (academia_id, sku);

CREATE TABLE estoque_movimentacao (
    idmovimentacao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academia_id BIGINT UNSIGNED NOT NULL,
    produto_id INT NOT NULL,
    tipo ENUM('entrada','saida','ajuste') NOT NULL,
    quantidade INT NOT NULL,
    saldo_anterior INT NOT NULL,
    saldo_posterior INT NOT NULL,
    motivo VARCHAR(255) NULL,
    usuario_id INT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_estoque_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
    CONSTRAINT fk_estoque_produto FOREIGN KEY (produto_id) REFERENCES produto(idproduto),
    CONSTRAINT fk_estoque_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario),
    INDEX idx_estoque_produto_data (academia_id, produto_id, created_at)
);
