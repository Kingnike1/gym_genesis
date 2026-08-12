ALTER TABLE usuario
    ADD COLUMN session_version INT UNSIGNED NOT NULL DEFAULT 1,
    ADD COLUMN password_changed_at TIMESTAMP NULL;

CREATE TABLE password_reset_token (
    idtoken BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_password_reset_user FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario) ON DELETE CASCADE,
    UNIQUE KEY uk_password_reset_hash (token_hash),
    INDEX idx_password_reset_user_expiry (usuario_id, expires_at)
);
