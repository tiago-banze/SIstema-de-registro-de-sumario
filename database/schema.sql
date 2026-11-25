-- Sistema de Registo de Sumário: esquema novo (MySQL 5.7+ / MariaDB 10.3+)
CREATE DATABASE IF NOT EXISTS registro_sumario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE registro_sumario;

CREATE TABLE administradores (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nome       VARCHAR(80)  NOT NULL,
  email      VARCHAR(120) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL
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
  id                VARCHAR(10)  NOT NULL PRIMARY KEY,
  nome              VARCHAR(120) NOT NULL,
  creditos          TINYINT UNSIGNED  NOT NULL,
  horas_semestrais  SMALLINT UNSIGNED NOT NULL
) ENGINE=InnoDB;

CREATE TABLE docentes (
  id              VARCHAR(10)  NOT NULL PRIMARY KEY,
  nome            VARCHAR(80)  NOT NULL,
  contacto        VARCHAR(20)  NOT NULL,
  email           VARCHAR(120) NOT NULL UNIQUE,
  grau_academico  VARCHAR(50)  NOT NULL,
  formacao        VARCHAR(120) NOT NULL,
  nacionalidade   VARCHAR(50)  NOT NULL DEFAULT 'Moçambique',
  genero          ENUM('M','F') NOT NULL,
  faculdade_id    VARCHAR(10)  NOT NULL,
  senha_hash      VARCHAR(255) NOT NULL,
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
  FOREIGN KEY (curso_id) REFERENCES cursos (id),
  FOREIGN KEY (turma_id) REFERENCES turmas (id)
) ENGINE=InnoDB;

CREATE TABLE curso_uc (
  curso_id VARCHAR(10) NOT NULL,
  uc_id    VARCHAR(10) NOT NULL,
  PRIMARY KEY (curso_id, uc_id),
  FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE CASCADE,
  FOREIGN KEY (uc_id)    REFERENCES ucs (id)    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE docente_uc (
  docente_id VARCHAR(10) NOT NULL,
  uc_id      VARCHAR(10) NOT NULL,
  PRIMARY KEY (docente_id, uc_id),
  FOREIGN KEY (docente_id) REFERENCES docentes (id) ON DELETE CASCADE,
  FOREIGN KEY (uc_id)      REFERENCES ucs (id)      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE estudante_uc (
  estudante_id INT UNSIGNED NOT NULL,
  uc_id        VARCHAR(10)  NOT NULL,
  PRIMARY KEY (estudante_id, uc_id),
  FOREIGN KEY (estudante_id) REFERENCES estudantes (id) ON DELETE CASCADE,
  FOREIGN KEY (uc_id)        REFERENCES ucs (id)        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sumarios (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  docente_id     VARCHAR(10)  NOT NULL,
  turma_id       VARCHAR(10)  NOT NULL,
  uc_id          VARCHAR(10)  NOT NULL,
  data_registro  DATE         NOT NULL,
  sumario        VARCHAR(500) NOT NULL,
  duracao_horas  TINYINT UNSIGNED NOT NULL,
  INDEX (data_registro),
  FOREIGN KEY (docente_id) REFERENCES docentes (id),
  FOREIGN KEY (turma_id)   REFERENCES turmas (id),
  FOREIGN KEY (uc_id)      REFERENCES ucs (id)
) ENGINE=InnoDB;

CREATE TABLE presencas_estudante (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  sumario_id   INT UNSIGNED NOT NULL,
  estudante_id INT UNSIGNED NOT NULL,
  presente     TINYINT(1)   NOT NULL,
  UNIQUE KEY (sumario_id, estudante_id),
  FOREIGN KEY (sumario_id)   REFERENCES sumarios (id)   ON DELETE CASCADE,
  FOREIGN KEY (estudante_id) REFERENCES estudantes (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Dados iniciais (vindos do projeto antigo). O administrador cria-se em /instalar.php
INSERT INTO faculdades (id, nome) VALUES
 ('FACED','Faculdade de Ciências de Educação'),
 ('FAGEC','Faculdade de Administração, Gestão e Contabilidade'),
 ('FAHCS','Faculdade de Humanidades e Ciências Sociais'),
 ('FET','Faculdade de Engenharia e Tecnologia');

INSERT INTO cursos (id, nome, diretor, faculdade_id) VALUES
 ('CAG','Ciências de Administração e Gestão','Fernando Muzime','FAGEC'),
 ('EIT','Engenharia Informática e Tecnologia','Jean Marie','FET'),
 ('TEO','Teologia','Filipe Huguane','FAHCS');

INSERT INTO turmas (id, curso_id) VALUES
 ('CAG22','CAG'),('CAG23','CAG'),('EIT22','EIT'),('EIT23','EIT');

INSERT INTO ucs (id, nome, creditos, horas_semestrais) VALUES
 ('EEI-05','Algoritmos e Estruturas de Dados 1',6,60),
 ('EEI-09','Base de Dados',6,60),
 ('EEI-11','Algoritmos e Estruturas de Dados 2',6,60),
 ('EEI-12','Laboratório 1',7,105),
 ('EEI-15','Laboratório 2',7,105),
 ('TIA','Estatística',3,60);

INSERT INTO curso_uc (curso_id, uc_id) VALUES
 ('EIT','EEI-05'),('EIT','EEI-09'),('EIT','EEI-11'),('EIT','EEI-12'),('EIT','EEI-15'),('CAG','TIA');
