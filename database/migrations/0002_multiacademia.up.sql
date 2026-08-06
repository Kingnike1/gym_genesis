CREATE TABLE academias (
    idacademia BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    nome_fantasia VARCHAR(150) NULL,
    cnpj VARCHAR(18) NULL,
    telefone VARCHAR(30) NULL,
    email VARCHAR(150) NULL,
    logo VARCHAR(255) NULL,
    status ENUM('ativa','suspensa','cancelada') NOT NULL DEFAULT 'ativa',
    configuracoes JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idacademia),
    UNIQUE KEY uq_academias_cnpj (cnpj)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE unidades (
    idunidade BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    academia_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(150) NOT NULL,
    cep VARCHAR(10) NULL,
    rua VARCHAR(150) NULL,
    numero VARCHAR(20) NULL,
    complemento VARCHAR(80) NULL,
    bairro VARCHAR(80) NULL,
    cidade VARCHAR(100) NULL,
    estado CHAR(2) NULL,
    telefone VARCHAR(30) NULL,
    status ENUM('ativa','inativa') NOT NULL DEFAULT 'ativa',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idunidade),
    KEY idx_unidades_academia (academia_id),
    CONSTRAINT fk_unidades_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE academia_usuario (
    academia_id BIGINT UNSIGNED NOT NULL,
    usuario_id INT(11) NOT NULL,
    unidade_id BIGINT UNSIGNED NULL,
    is_principal TINYINT(1) NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (academia_id, usuario_id),
    KEY idx_academia_usuario_usuario (usuario_id),
    KEY idx_academia_usuario_unidade (unidade_id),
    CONSTRAINT fk_academia_usuario_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_academia_usuario_usuario FOREIGN KEY (usuario_id) REFERENCES usuario (idusuario) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_academia_usuario_unidade FOREIGN KEY (unidade_id) REFERENCES unidades (idunidade) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO academias (nome, nome_fantasia, status) VALUES ('Gym Genesis Legado', 'Gym Genesis', 'ativa');
SET @legacy_academia_id = LAST_INSERT_ID();
INSERT INTO unidades (academia_id, nome, status) VALUES (@legacy_academia_id, 'Unidade Principal', 'ativa');
SET @legacy_unidade_id = LAST_INSERT_ID();
INSERT INTO academia_usuario (academia_id, usuario_id, unidade_id, is_principal, ativo)
SELECT @legacy_academia_id, idusuario, @legacy_unidade_id, 1, 1 FROM usuario;

ALTER TABLE plano ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_plano_academia (academia_id);
ALTER TABLE assinatura ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_assinatura_academia (academia_id);
ALTER TABLE funcionario ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_funcionario_academia (academia_id);
ALTER TABLE treino ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_treino_academia (academia_id);
ALTER TABLE aula_agendada ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_aula_agendada_academia (academia_id);
ALTER TABLE avaliacao_fisica ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_avaliacao_academia (academia_id);
ALTER TABLE dieta ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_dieta_academia (academia_id);
ALTER TABLE endereco ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_endereco_academia (academia_id);
ALTER TABLE forum ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_forum_academia (academia_id);
ALTER TABLE historico_peso ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_historico_peso_academia (academia_id);
ALTER TABLE historico_treino ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_historico_treino_academia (academia_id);
ALTER TABLE pedido ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_pedido_academia (academia_id);
ALTER TABLE produto ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_produto_academia (academia_id);
ALTER TABLE cupom_desconto ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_cupom_academia (academia_id);
ALTER TABLE meta_usuario ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_meta_academia (academia_id);
ALTER TABLE perfil_professor ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_perfil_professor_academia (academia_id);
ALTER TABLE perfil_usuario ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_perfil_usuario_academia (academia_id);
ALTER TABLE resposta_forum ADD COLUMN academia_id BIGINT UNSIGNED NULL, ADD KEY idx_resposta_forum_academia (academia_id);

UPDATE plano SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE assinatura SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE funcionario SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE treino SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE aula_agendada SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE avaliacao_fisica SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE dieta SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE endereco SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE forum SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE historico_peso SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE historico_treino SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE pedido SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE produto SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE cupom_desconto SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE meta_usuario SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE perfil_professor SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE perfil_usuario SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;
UPDATE resposta_forum SET academia_id=@legacy_academia_id WHERE academia_id IS NULL;

ALTER TABLE plano MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_plano_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE assinatura MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_assinatura_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE funcionario MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_funcionario_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE treino MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_treino_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE aula_agendada MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_aula_agendada_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE avaliacao_fisica MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_avaliacao_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE dieta MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_dieta_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE endereco MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_endereco_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE forum MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_forum_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE historico_peso MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_historico_peso_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE historico_treino MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_historico_treino_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE pedido MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_pedido_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE produto MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_produto_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE cupom_desconto MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_cupom_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE meta_usuario MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_meta_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE perfil_professor MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_perfil_professor_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE perfil_usuario MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_perfil_usuario_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE resposta_forum MODIFY academia_id BIGINT UNSIGNED NOT NULL, ADD CONSTRAINT fk_resposta_forum_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE;