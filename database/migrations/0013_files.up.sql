CREATE TABLE arquivo (
    idarquivo BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academia_id BIGINT UNSIGNED NOT NULL,
    usuario_id INT NULL,
    storage_disk VARCHAR(40) NOT NULL DEFAULT 'local',
    storage_path VARCHAR(500) NOT NULL,
    original_name VARCHAR(255) NULL,
    mime_type VARCHAR(120) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL,
    visibility ENUM('private','public') NOT NULL DEFAULT 'private',
    purpose VARCHAR(80) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_arquivo_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
    CONSTRAINT fk_arquivo_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario),
    INDEX idx_arquivo_tenant_owner (academia_id, usuario_id, purpose)
);
