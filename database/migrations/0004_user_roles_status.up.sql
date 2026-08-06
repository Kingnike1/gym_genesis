ALTER TABLE usuario
    ADD COLUMN status ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo' AFTER tipo_usuario,
    ADD COLUMN last_login_at DATETIME NULL AFTER status,
    ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER last_login_at,
    ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE academia_usuario
    ADD COLUMN papel TINYINT UNSIGNED NOT NULL DEFAULT 3 AFTER unidade_id,
    ADD INDEX idx_academia_usuario_papel (academia_id, papel, ativo);

UPDATE academia_usuario au
INNER JOIN usuario u ON u.idusuario = au.usuario_id
SET au.papel = u.tipo_usuario;
