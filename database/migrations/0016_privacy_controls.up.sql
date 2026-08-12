CREATE TABLE consentimento_privacidade (
  idconsentimento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  academia_id INT NOT NULL,
  usuario_id INT NOT NULL,
  finalidade VARCHAR(120) NOT NULL,
  versao_termo VARCHAR(40) NOT NULL,
  aceito_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  revogado_em DATETIME NULL,
  origem VARCHAR(40) NOT NULL DEFAULT 'web',
  CONSTRAINT fk_consentimento_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
  CONSTRAINT fk_consentimento_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario),
  INDEX idx_consentimento_usuario_finalidade (usuario_id, finalidade, revogado_em),
  INDEX idx_consentimento_academia (academia_id)
);

CREATE TABLE solicitacao_titular (
  idsolicitacao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  academia_id INT NOT NULL,
  usuario_id INT NOT NULL,
  tipo ENUM('acesso','correcao','exportacao','eliminacao','anonimizacao','revogacao') NOT NULL,
  status ENUM('aberta','em_analise','concluida','negada') NOT NULL DEFAULT 'aberta',
  detalhes TEXT NULL,
  resposta TEXT NULL,
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  concluida_em DATETIME NULL,
  CONSTRAINT fk_solicitacao_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
  CONSTRAINT fk_solicitacao_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario),
  INDEX idx_solicitacao_academia_status (academia_id, status),
  INDEX idx_solicitacao_usuario (usuario_id, criada_em)
);

CREATE TABLE politica_retencao (
  idpolitica BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  academia_id INT NOT NULL,
  categoria VARCHAR(80) NOT NULL,
  dias_retencao INT UNSIGNED NOT NULL,
  acao ENUM('eliminar','anonimizar','revisar') NOT NULL DEFAULT 'revisar',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_retencao_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
  UNIQUE KEY uq_retencao_categoria (academia_id, categoria)
);
