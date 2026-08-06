ALTER TABLE academia_usuario DROP INDEX idx_academia_usuario_papel, DROP COLUMN papel;
ALTER TABLE usuario DROP COLUMN updated_at, DROP COLUMN created_at, DROP COLUMN last_login_at, DROP COLUMN status;
