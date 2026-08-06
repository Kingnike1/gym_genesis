CREATE TABLE auditoria_academia (
    idaudit BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    academia_id BIGINT UNSIGNED NOT NULL,
    usuario_id INT(11) NULL,
    acao VARCHAR(100) NOT NULL,
    recurso VARCHAR(100) NOT NULL,
    recurso_id VARCHAR(100) NULL,
    contexto JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idaudit),
    KEY idx_auditoria_academia_created (academia_id, created_at),
    KEY idx_auditoria_usuario (usuario_id),
    CONSTRAINT fk_auditoria_academia FOREIGN KEY (academia_id) REFERENCES academias (idacademia) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (usuario_id) REFERENCES usuario (idusuario) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;