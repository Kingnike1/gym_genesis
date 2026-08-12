CREATE TABLE avaliacao_fisica_registro (
    idavaliacao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academia_id BIGINT UNSIGNED NOT NULL,
    aluno_id INT NOT NULL,
    responsavel_usuario_id INT NULL,
    data_avaliacao DATE NOT NULL,
    peso DECIMAL(6,2) NOT NULL,
    altura DECIMAL(5,2) NOT NULL,
    imc DECIMAL(5,2) NOT NULL,
    percentual_gordura DECIMAL(5,2) NULL,
    pressao_arterial VARCHAR(20) NULL,
    observacoes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_avaliacao_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
    CONSTRAINT fk_avaliacao_aluno FOREIGN KEY (aluno_id) REFERENCES aluno(idaluno),
    CONSTRAINT fk_avaliacao_responsavel FOREIGN KEY (responsavel_usuario_id) REFERENCES usuario(idusuario),
    INDEX idx_avaliacao_academia_aluno_data (academia_id, aluno_id, data_avaliacao)
);

CREATE TABLE avaliacao_fisica_medida (
    idmedida BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    avaliacao_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(80) NOT NULL,
    valor DECIMAL(8,2) NOT NULL,
    unidade VARCHAR(20) NOT NULL DEFAULT 'cm',
    CONSTRAINT fk_medida_avaliacao FOREIGN KEY (avaliacao_id) REFERENCES avaliacao_fisica_registro(idavaliacao) ON DELETE CASCADE,
    UNIQUE KEY uk_avaliacao_medida_nome (avaliacao_id, nome)
);
