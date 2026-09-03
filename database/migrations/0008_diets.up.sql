CREATE TABLE IF NOT EXISTS plano_alimentar (
    idplano_alimentar BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academia_id BIGINT UNSIGNED NOT NULL,
    aluno_id INT NOT NULL,
    responsavel_usuario_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    objetivo VARCHAR(255) NULL,
    observacoes TEXT NULL,
    qualificacao_responsavel VARCHAR(120) NOT NULL,
    registro_profissional VARCHAR(80) NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    status ENUM('rascunho','ativo','encerrado') NOT NULL DEFAULT 'rascunho',
    versao INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_plano_alimentar_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
    CONSTRAINT fk_plano_alimentar_aluno FOREIGN KEY (aluno_id) REFERENCES aluno(idaluno),
    CONSTRAINT fk_plano_alimentar_responsavel FOREIGN KEY (responsavel_usuario_id) REFERENCES usuario(idusuario),
    INDEX idx_plano_alimentar_academia_aluno (academia_id, aluno_id),
    INDEX idx_plano_alimentar_responsavel (academia_id, responsavel_usuario_id),
    INDEX idx_plano_alimentar_status (academia_id, status)
);

CREATE TABLE IF NOT EXISTS plano_alimentar_refeicao (
    idrefeicao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plano_alimentar_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    horario TIME NULL,
    ordem SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    observacoes VARCHAR(500) NULL,
    CONSTRAINT fk_refeicao_plano FOREIGN KEY (plano_alimentar_id) REFERENCES plano_alimentar(idplano_alimentar) ON DELETE CASCADE,
    INDEX idx_refeicao_plano_ordem (plano_alimentar_id, ordem)
);

CREATE TABLE IF NOT EXISTS plano_alimentar_item (
    iditem BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    refeicao_id BIGINT UNSIGNED NOT NULL,
    alimento_id INT NULL,
    descricao VARCHAR(180) NOT NULL,
    quantidade DECIMAL(10,2) NULL,
    unidade VARCHAR(30) NULL,
    substituicoes TEXT NULL,
    ordem SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    CONSTRAINT fk_item_refeicao FOREIGN KEY (refeicao_id) REFERENCES plano_alimentar_refeicao(idrefeicao) ON DELETE CASCADE,
    INDEX idx_item_refeicao_ordem (refeicao_id, ordem)
);

CREATE TABLE IF NOT EXISTS plano_alimentar_historico (
    idhistorico BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plano_alimentar_id BIGINT UNSIGNED NOT NULL,
    usuario_id INT NOT NULL,
    versao INT UNSIGNED NOT NULL,
    evento VARCHAR(40) NOT NULL,
    snapshot_json JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historico_plano FOREIGN KEY (plano_alimentar_id) REFERENCES plano_alimentar(idplano_alimentar) ON DELETE CASCADE,
    CONSTRAINT fk_historico_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario),
    INDEX idx_historico_plano (plano_alimentar_id, created_at)
);