CREATE TABLE IF NOT EXISTS professor (
  idprofessor INT NOT NULL AUTO_INCREMENT,
  academia_id INT NOT NULL,
  usuario_id INT NOT NULL,
  nome VARCHAR(120) NOT NULL,
  cref VARCHAR(30) NOT NULL,
  telefone VARCHAR(25) NULL,
  bio TEXT NULL,
  status ENUM('ativo','inativo','suspenso') NOT NULL DEFAULT 'ativo',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (idprofessor),
  UNIQUE KEY uq_professor_academia_usuario (academia_id, usuario_id),
  UNIQUE KEY uq_professor_academia_cref (academia_id, cref),
  KEY idx_professor_academia_status (academia_id, status),
  CONSTRAINT fk_professor_academia FOREIGN KEY (academia_id) REFERENCES academias(idacademia),
  CONSTRAINT fk_professor_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(idusuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS professor_unidade (
  professor_id INT NOT NULL,
  unidade_id INT NOT NULL,
  PRIMARY KEY (professor_id, unidade_id),
  CONSTRAINT fk_professor_unidade_professor FOREIGN KEY (professor_id) REFERENCES professor(idprofessor) ON DELETE CASCADE,
  CONSTRAINT fk_professor_unidade_unidade FOREIGN KEY (unidade_id) REFERENCES unidades(idunidade) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS professor_especialidade (
  idespecialidade INT NOT NULL AUTO_INCREMENT,
  professor_id INT NOT NULL,
  nome VARCHAR(100) NOT NULL,
  PRIMARY KEY (idespecialidade),
  UNIQUE KEY uq_professor_especialidade (professor_id, nome),
  CONSTRAINT fk_professor_especialidade_professor FOREIGN KEY (professor_id) REFERENCES professor(idprofessor) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS professor_aluno (
  professor_id INT NOT NULL,
  aluno_id INT NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (professor_id, aluno_id),
  CONSTRAINT fk_professor_aluno_professor FOREIGN KEY (professor_id) REFERENCES professor(idprofessor) ON DELETE CASCADE,
  CONSTRAINT fk_professor_aluno_aluno FOREIGN KEY (aluno_id) REFERENCES aluno(idaluno) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;