CREATE TABLE plano_comercial (
    idplano BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academia_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(120) NOT NULL,
    descricao TEXT NULL,
    valor DECIMAL(10,2) NOT NULL,
    duracao_dias INT UNSIGNED NOT NULL,
    recorrencia ENUM('unico','mensal','trimestral','semestral','anual') NOT NULL DEFAULT 'mensal',
    limite_acessos INT UNSIGNED NULL,
    beneficios TEXT NULL,
    status ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_plano_comercial_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
    UNIQUE KEY uk_plano_nome_academia (academia_id, nome)
);

CREATE TABLE matricula (
    idmatricula BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academia_id BIGINT UNSIGNED NOT NULL,
    unidade_id BIGINT UNSIGNED NULL,
    aluno_id INT NOT NULL,
    plano_id BIGINT UNSIGNED NOT NULL,
    valor_contratado DECIMAL(10,2) NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    proxima_cobranca DATE NULL,
    status ENUM('ativa','suspensa','congelada','cancelada','encerrada','inadimplente') NOT NULL DEFAULT 'ativa',
    motivo_status VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_matricula_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
    CONSTRAINT fk_matricula_unidade FOREIGN KEY (unidade_id) REFERENCES unidades(idunidade),
    CONSTRAINT fk_matricula_aluno FOREIGN KEY (aluno_id) REFERENCES aluno(idaluno),
    CONSTRAINT fk_matricula_plano FOREIGN KEY (plano_id) REFERENCES plano_comercial(idplano),
    INDEX idx_matricula_academia_aluno_status (academia_id, aluno_id, status)
);

CREATE TABLE matricula_historico (
    idhistorico BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    matricula_id BIGINT UNSIGNED NOT NULL,
    status_anterior VARCHAR(30) NULL,
    status_novo VARCHAR(30) NOT NULL,
    motivo VARCHAR(255) NULL,
    usuario_id INT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historico_matricula FOREIGN KEY (matricula_id) REFERENCES matricula(idmatricula) ON DELETE CASCADE,
    CONSTRAINT fk_matricula_historico_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario)
);
