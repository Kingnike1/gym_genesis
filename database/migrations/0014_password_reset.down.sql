DROP TABLE IF EXISTS password_reset_token;
ALTER TABLE usuario DROP COLUMN password_changed_at, DROP COLUMN session_version;
