-- Sistema de Registo de Sumário: estrutura da base de dados (MySQL 5.7+ ou MariaDB 10.3+)
-- ATENCAO: este ficheiro apaga e recria a base registro_sumario. Use-o apenas numa instalacao nova.
DROP DATABASE IF EXISTS registro_sumario;
CREATE DATABASE registro_sumario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE registro_sumario;

CREATE TABLE administradores (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nome         VARCHAR(80)  NOT NULL,
  email        VARCHAR(120) NOT NULL UNIQUE,
  senha_hash   VARCHAR(255) NOT NULL,
  trocar_senha TINYINT(1)   NOT NULL DEFAULT 0,
  ativo        TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE faculdades (
  id   VARCHAR(10)  NOT NULL PRIMARY KEY,
  nome VARCHAR(120) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE cursos (
  id           VARCHAR(10)  NOT NULL PRIMARY KEY,
  nome         VARCHAR(120) NOT NULL,
  diretor      VARCHAR(80)  NOT NULL,
  faculdade_id VARCHAR(10)  NOT NULL,
  FOREIGN KEY (faculdade_id) REFERENCES faculdades (id)
) ENGINE=InnoDB;

CREATE TABLE turmas (
  id       VARCHAR(10) NOT NULL PRIMARY KEY,
  curso_id VARCHAR(10) NOT NULL,
  FOREIGN KEY (curso_id) REFERENCES cursos (id)
) ENGINE=InnoDB;

CREATE TABLE ucs (
  id               VARCHAR(10)  NOT NULL PRIMARY KEY,
  nome             VARCHAR(120) NOT NULL,
  creditos         TINYINT UNSIGNED  NOT NULL,
  horas_semestrais SMALLINT UNSIGNED NOT NULL
) ENGINE=InnoDB;

CREATE TABLE periodos (
  id     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nome   VARCHAR(40) NOT NULL,
  inicio DATE NOT NULL,
  fim    DATE NOT NULL,
  ativo  TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE curso_uc (
  curso_id VARCHAR(10) NOT NULL,
  uc_id    VARCHAR(10) NOT NULL,
  ano      TINYINT UNSIGNED NOT NULL,
  semestre TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (curso_id, uc_id),
  FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE CASCADE,
  FOREIGN KEY (uc_id)    REFERENCES ucs (id)    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE docentes (
  id             VARCHAR(10)  NOT NULL PRIMARY KEY,
  nome           VARCHAR(80)  NOT NULL,
  contacto       VARCHAR(20)  NOT NULL,
  email          VARCHAR(120) NOT NULL UNIQUE,
  genero         ENUM('M','F') NOT NULL,
  grau_academico VARCHAR(50)  NOT NULL,
  formacao       VARCHAR(120) NOT NULL,
  nacionalidade  VARCHAR(50)  NOT NULL DEFAULT 'Moçambique',
  faculdade_id   VARCHAR(10)  NOT NULL,
  senha_hash     VARCHAR(255) NOT NULL,
  trocar_senha   TINYINT(1)   NOT NULL DEFAULT 0,
  ativo          TINYINT(1)   NOT NULL DEFAULT 1,
  FOREIGN KEY (faculdade_id) REFERENCES faculdades (id)
) ENGINE=InnoDB;

CREATE TABLE estudantes (
  id            INT UNSIGNED NOT NULL PRIMARY KEY,
  nome          VARCHAR(80)  NOT NULL,
  contacto      VARCHAR(20)  NOT NULL,
  email         VARCHAR(120) NOT NULL UNIQUE,
  genero        ENUM('M','F') NOT NULL,
  nacionalidade VARCHAR(50)  NOT NULL DEFAULT 'Moçambique',
  provincia     VARCHAR(50)  NOT NULL,
  distrito      VARCHAR(50)  NOT NULL,
  curso_id      VARCHAR(10)  NOT NULL,
  turma_id      VARCHAR(10)  NOT NULL,
  senha_hash    VARCHAR(255) NOT NULL,
  trocar_senha  TINYINT(1)   NOT NULL DEFAULT 0,
  ativo         TINYINT(1)   NOT NULL DEFAULT 1,
  FOREIGN KEY (curso_id) REFERENCES cursos (id),
  FOREIGN KEY (turma_id) REFERENCES turmas (id)
) ENGINE=InnoDB;

-- Quem leciona o quê, a que turma, em que período. Todos os registos de aulas partem daqui.
CREATE TABLE atribuicoes (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  periodo_id INT UNSIGNED NOT NULL,
  turma_id   VARCHAR(10)  NOT NULL,
  uc_id      VARCHAR(10)  NOT NULL,
  docente_id VARCHAR(10)  NOT NULL,
  UNIQUE KEY uq_atribuicao (periodo_id, turma_id, uc_id),
  FOREIGN KEY (periodo_id) REFERENCES periodos (id),
  FOREIGN KEY (turma_id)   REFERENCES turmas (id),
  FOREIGN KEY (uc_id)      REFERENCES ucs (id),
  FOREIGN KEY (docente_id) REFERENCES docentes (id)
) ENGINE=InnoDB;

CREATE TABLE horarios (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  atribuicao_id INT UNSIGNED NOT NULL,
  dia_semana    TINYINT UNSIGNED NOT NULL,
  hora_inicio   TIME NOT NULL,
  hora_fim      TIME NOT NULL,
  sala          VARCHAR(30) NULL,
  FOREIGN KEY (atribuicao_id) REFERENCES atribuicoes (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE feriados (
  data      DATE NOT NULL PRIMARY KEY,
  descricao VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE sumarios (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  atribuicao_id INT UNSIGNED NOT NULL,
  horario_id    INT UNSIGNED NULL,
  data_aula     DATE NOT NULL,
  hora_registo  TIME NOT NULL,
  sumario       VARCHAR(500) NOT NULL,
  duracao_horas TINYINT UNSIGNED NOT NULL,
  estado        ENUM('registado','validado','rejeitado') NOT NULL DEFAULT 'registado',
  nota          VARCHAR(255) NULL,
  validado_por  INT UNSIGNED NULL,
  validado_em   DATETIME NULL,
  criado_em     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_aula (atribuicao_id, data_aula, horario_id),
  INDEX idx_data (data_aula),
  INDEX idx_estado (estado),
  FOREIGN KEY (atribuicao_id) REFERENCES atribuicoes (id),
  FOREIGN KEY (horario_id)    REFERENCES horarios (id) ON DELETE SET NULL,
  FOREIGN KEY (validado_por)  REFERENCES administradores (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE presencas (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  sumario_id   INT UNSIGNED NOT NULL,
  estudante_id INT UNSIGNED NOT NULL,
  estado       ENUM('presente','falta','justificada') NOT NULL DEFAULT 'presente',
  UNIQUE KEY uq_presenca (sumario_id, estudante_id),
  INDEX idx_estudante (estudante_id),
  FOREIGN KEY (sumario_id)   REFERENCES sumarios (id)   ON DELETE CASCADE,
  FOREIGN KEY (estudante_id) REFERENCES estudantes (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tentativas_login (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  identificador VARCHAR(120) NOT NULL,
  ip            VARCHAR(45)  NOT NULL,
  criado_em     DATETIME     NOT NULL,
  INDEX idx_ident (identificador, criado_em),
  INDEX idx_ip (ip, criado_em)
) ENGINE=InnoDB;

CREATE TABLE auditoria (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  quando       DATETIME     NOT NULL,
  usuario_tipo VARCHAR(10)  NULL,
  usuario_id   VARCHAR(20)  NULL,
  acao         VARCHAR(40)  NOT NULL,
  entidade     VARCHAR(40)  NOT NULL DEFAULT '',
  detalhe      VARCHAR(255) NOT NULL DEFAULT '',
  ip           VARCHAR(45)  NOT NULL DEFAULT '',
  INDEX idx_quando (quando)
) ENGINE=InnoDB;
