CREATE TABLE api_token (
  idtoken BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  academia_id INT NOT NULL,
  usuario_id INT NOT NULL,
  nome VARCHAR(120) NOT NULL,
  token_hash CHAR(64) NOT NULL,
  scopes JSON NOT NULL,
  expira_em DATETIME NULL,
  ultimo_uso_em DATETIME NULL,
  revogado_em DATETIME NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_api_token_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
  CONSTRAINT fk_api_token_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario),
  UNIQUE KEY uq_api_token_hash (token_hash),
  INDEX idx_api_token_owner (academia_id, usuario_id, revogado_em)
);

CREATE TABLE webhook_evento (
  idevento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  academia_id INT NOT NULL,
  provedor VARCHAR(80) NOT NULL,
  evento_externo_id VARCHAR(190) NOT NULL,
  tipo VARCHAR(120) NOT NULL,
  payload_hash CHAR(64) NOT NULL,
  status ENUM('recebido','processado','falhou','ignorado') NOT NULL DEFAULT 'recebido',
  tentativas SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  recebido_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processado_em DATETIME NULL,
  CONSTRAINT fk_webhook_evento_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
  UNIQUE KEY uq_webhook_provider_event (academia_id, provedor, evento_externo_id),
  INDEX idx_webhook_status (academia_id, status, recebido_em)
);
