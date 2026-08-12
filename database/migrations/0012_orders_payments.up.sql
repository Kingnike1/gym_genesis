CREATE TABLE pedido_comercial (
    idpedido BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academia_id BIGINT UNSIGNED NOT NULL,
    usuario_id INT NOT NULL,
    idempotency_key VARCHAR(100) NOT NULL,
    status ENUM('pendente','aguardando_pagamento','pago','cancelado','reembolsado') NOT NULL DEFAULT 'pendente',
    subtotal DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) NOT NULL DEFAULT 0,
    frete DECIMAL(10,2) NOT NULL DEFAULT 0,
    valor_total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedido_comercial_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
    CONSTRAINT fk_pedido_comercial_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario),
    UNIQUE KEY uk_pedido_idempotency (academia_id, idempotency_key)
);

CREATE TABLE pedido_item_registro (
    iditem BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    produto_id INT NOT NULL,
    nome_produto VARCHAR(255) NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    quantidade INT UNSIGNED NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_pedido_comercial FOREIGN KEY (pedido_id) REFERENCES pedido_comercial(idpedido) ON DELETE CASCADE,
    CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produto(idproduto)
);

CREATE TABLE pagamento (
    idpagamento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academia_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    gateway VARCHAR(80) NOT NULL,
    external_id VARCHAR(190) NULL,
    idempotency_key VARCHAR(100) NOT NULL,
    metodo VARCHAR(50) NOT NULL,
    status ENUM('pendente','processando','aprovado','recusado','cancelado','reembolsado') NOT NULL DEFAULT 'pendente',
    valor DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pagamento_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
    CONSTRAINT fk_pagamento_pedido FOREIGN KEY (pedido_id) REFERENCES pedido_comercial(idpedido),
    UNIQUE KEY uk_pagamento_idempotency (academia_id, idempotency_key),
    UNIQUE KEY uk_pagamento_external (gateway, external_id)
);
