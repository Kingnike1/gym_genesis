CREATE TABLE IF NOT EXISTS ficha_treino (
  idtreino INT NOT NULL AUTO_INCREMENT,
  academia_id BIGINT UNSIGNED NOT NULL,
  aluno_id INT NOT NULL,
  professor_id INT NOT NULL,
  nome VARCHAR(120) NOT NULL,
  descricao TEXT NULL,
  data_inicio DATE NOT NULL,
  data_fim DATE NULL,
  status ENUM('rascunho','ativo','encerrado') NOT NULL DEFAULT 'ativo',
  versao INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (idtreino),
  KEY idx_ficha_academia_aluno (academia_id, aluno_id, status),
  KEY idx_ficha_academia_professor (academia_id, professor_id, status),
  CONSTRAINT fk_ficha_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
  CONSTRAINT fk_ficha_aluno FOREIGN KEY (aluno_id) REFERENCES aluno(idaluno),
  CONSTRAINT fk_ficha_professor FOREIGN KEY (professor_id) REFERENCES professor(idprofessor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ficha_treino_exercicio (
  iditem INT NOT NULL AUTO_INCREMENT,
  treino_id INT NOT NULL,
  exercicio_id INT NOT NULL,
  ordem INT NOT NULL,
  series INT NOT NULL,
  repeticoes VARCHAR(30) NOT NULL,
  carga DECIMAL(8,2) NULL,
  intervalo_segundos INT NULL,
  observacoes VARCHAR(255) NULL,
  PRIMARY KEY (iditem),
  UNIQUE KEY uq_ficha_ordem (treino_id, ordem),
  KEY idx_ficha_exercicio (exercicio_id),
  CONSTRAINT fk_ficha_item_treino FOREIGN KEY (treino_id) REFERENCES ficha_treino(idtreino) ON DELETE CASCADE,
  CONSTRAINT fk_ficha_item_exercicio FOREIGN KEY (exercicio_id) REFERENCES exercicio(idexercicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS execucao_treino (
  idexecucao INT NOT NULL AUTO_INCREMENT,
  academia_id BIGINT UNSIGNED NOT NULL,
  treino_id INT NOT NULL,
  aluno_id INT NOT NULL,
  iniciado_em DATETIME NOT NULL,
  concluido_em DATETIME NULL,
  observacoes TEXT NULL,
  PRIMARY KEY (idexecucao),
  KEY idx_execucao_aluno_data (academia_id, aluno_id, iniciado_em),
  CONSTRAINT fk_execucao_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
  CONSTRAINT fk_execucao_treino FOREIGN KEY (treino_id) REFERENCES ficha_treino(idtreino),
  CONSTRAINT fk_execucao_aluno FOREIGN KEY (aluno_id) REFERENCES aluno(idaluno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;