-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 17, 2023 at 03:09 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `registro_sumario`
--

-- --------------------------------------------------------

--
-- Table structure for table `administradortbl`
--

DROP TABLE IF EXISTS `administradortbl`;
CREATE TABLE IF NOT EXISTS `administradortbl` (
  `ID_admin` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `senha` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `administradortbl`
--

INSERT INTO `administradortbl` (`ID_admin`, `nome`, `email`, `senha`) VALUES
(2, 'Tiago Victor Banze', 'teste@gmail.com', '0000');

-- --------------------------------------------------------

--
-- Table structure for table `cadeiratbl`
--

DROP TABLE IF EXISTS `cadeiratbl`;
CREATE TABLE IF NOT EXISTS `cadeiratbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_cadeira` varchar(10) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `credito` int NOT NULL,
  `curso` varchar(10) NOT NULL,
  `hora_semestral` int NOT NULL,
  PRIMARY KEY (`ID_cadeira`),
  UNIQUE KEY `nr` (`nr`),
  KEY `curso` (`curso`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cadeiratbl`
--

INSERT INTO `cadeiratbl` (`nr`, `ID_cadeira`, `nome`, `credito`, `curso`, `hora_semestral`) VALUES
(1, 'EEI-05', 'Algortmos e estruturas de dados 1', 6, 'EIT', 60),
(3, 'EEI-09', 'Base de dados', 6, 'EIT', 60),
(2, 'EEI-11', 'Estruturas de Dados e Algoritmos 2', 6, 'EIT', 60),
(4, 'EEI-12', 'Laboratório 1', 7, 'EIT', 105),
(5, 'EEI-15', 'Laboratório 2', 7, 'EIT', 105),
(6, 'TIA', 'estatistica', 3, 'CAG', 60);

-- --------------------------------------------------------

--
-- Table structure for table `cursotbl`
--

DROP TABLE IF EXISTS `cursotbl`;
CREATE TABLE IF NOT EXISTS `cursotbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_curso` varchar(10) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `diretor` varchar(50) NOT NULL,
  `ID_faculdade` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_curso`),
  UNIQUE KEY `nr` (`nr`),
  KEY `ID_faculdade` (`ID_faculdade`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cursotbl`
--

INSERT INTO `cursotbl` (`nr`, `ID_curso`, `nome`, `diretor`, `ID_faculdade`) VALUES
(3, 'CAG', 'Ciencias Administração, Gestão', 'Fernando Muzime', 'FAGEC'),
(1, 'EIT', 'Engenharia Informática e Tecnologia', 'Jean Marie', 'FET'),
(2, 'TEO', 'Teologia', 'Filipe huguane', 'FAHCS');

-- --------------------------------------------------------

--
-- Table structure for table `curso_uctbl`
--

DROP TABLE IF EXISTS `curso_uctbl`;
CREATE TABLE IF NOT EXISTS `curso_uctbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_curso` varchar(10) NOT NULL,
  `ID_cadeira` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_curso`,`ID_cadeira`),
  UNIQUE KEY `nr` (`nr`),
  KEY `ID_cadeira` (`ID_cadeira`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `curso_uctbl`
--

INSERT INTO `curso_uctbl` (`nr`, `ID_curso`, `ID_cadeira`) VALUES
(1, 'EIT', 'EEI-05'),
(2, 'EIT', 'EEI-11'),
(3, 'EIT', 'EEI-09'),
(4, 'EIT', 'EEI-12'),
(5, 'EIT', 'EEI-15'),
(6, 'CAG', 'TIA');

-- --------------------------------------------------------

--
-- Table structure for table `docentetbl`
--

DROP TABLE IF EXISTS `docentetbl`;
CREATE TABLE IF NOT EXISTS `docentetbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_docente` varchar(10) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `contacto` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `grau_academico` varchar(50) NOT NULL,
  `formacao` varchar(50) NOT NULL,
  `nacionalidade` varchar(50) NOT NULL DEFAULT 'Mocambique',
  `genero` enum('M','F') DEFAULT NULL,
  `ID_faculdade` varchar(10) NOT NULL,
  `senha` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_docente`),
  UNIQUE KEY `nr` (`nr`),
  KEY `ID_faculdade` (`ID_faculdade`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `docentetbl`
--

INSERT INTO `docentetbl` (`nr`, `ID_docente`, `nome`, `contacto`, `email`, `grau_academico`, `formacao`, `nacionalidade`, `genero`, `ID_faculdade`, `senha`) VALUES
(1, '222', 'Tiago Victor Banze', 847388489, 'teste1@gmail.com', 'licenciado', 'licenciatura em engenharia informatica e tecnologi', 'Mocambique', 'M', 'FEIT', '0000');

-- --------------------------------------------------------

--
-- Table structure for table `docente_uctbl`
--

DROP TABLE IF EXISTS `docente_uctbl`;
CREATE TABLE IF NOT EXISTS `docente_uctbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_docente` varchar(10) NOT NULL,
  `ID_cadeira` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_docente`,`ID_cadeira`),
  UNIQUE KEY `nr` (`nr`),
  KEY `ID_cadeira` (`ID_cadeira`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estudantetbl`
--

DROP TABLE IF EXISTS `estudantetbl`;
CREATE TABLE IF NOT EXISTS `estudantetbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_estudante` int NOT NULL,
  `nome` varchar(50) NOT NULL,
  `contacto` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `nacionalidade` varchar(50) NOT NULL DEFAULT 'Mocambique',
  `provincia` varchar(50) NOT NULL,
  `destrito` varchar(50) NOT NULL,
  `genero` enum('M','F') DEFAULT NULL,
  `curso` varchar(10) NOT NULL,
  `turma` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_estudante`),
  UNIQUE KEY `nr` (`nr`),
  KEY `curso` (`curso`),
  KEY `fk_turma_estudante` (`turma`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `estudantetbl`
--

INSERT INTO `estudantetbl` (`nr`, `ID_estudante`, `nome`, `contacto`, `email`, `nacionalidade`, `provincia`, `destrito`, `genero`, `curso`, `turma`) VALUES
(1, 1301, 'tiago Banze', 847388489, 'tiagovbanze@gmail.com', 'moçambique', 'Maxixe', 'Kamubukwane', 'M', 'EIT', 'EIT22'),
(2, 225026, 'dercia jose', 841111111, 'dercia@gmail.com', 'moçambique', 'inhambane', 'Massinga', 'F', 'EIT', 'EIT22');

-- --------------------------------------------------------

--
-- Table structure for table `estudante_uctbl`
--

DROP TABLE IF EXISTS `estudante_uctbl`;
CREATE TABLE IF NOT EXISTS `estudante_uctbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_estudante` int NOT NULL,
  `ID_cadeira` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_estudante`,`ID_cadeira`),
  UNIQUE KEY `nr` (`nr`),
  KEY `ID_cadeira` (`ID_cadeira`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculdadetbl`
--

DROP TABLE IF EXISTS `faculdadetbl`;
CREATE TABLE IF NOT EXISTS `faculdadetbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_faculdade` varchar(10) NOT NULL,
  `nome` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_faculdade`),
  UNIQUE KEY `nr` (`nr`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `faculdadetbl`
--

INSERT INTO `faculdadetbl` (`nr`, `ID_faculdade`, `nome`) VALUES
(2, 'FACED', 'Faculdade de ciencias de educacao'),
(4, 'FAGEC', 'Faculdade de administracao gestao e contabilidade'),
(3, 'FAHCS', 'Faculdade de humanidade e ciencias sociais'),
(1, 'FET', 'faculdade de engenharia e tecnologia');

-- --------------------------------------------------------

--
-- Table structure for table `presencatbl`
--

DROP TABLE IF EXISTS `presencatbl`;
CREATE TABLE IF NOT EXISTS `presencatbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `docente` varchar(10) NOT NULL,
  `curso` varchar(10) NOT NULL,
  `turma` varchar(10) NOT NULL,
  `data_presenca` date NOT NULL,
  `cadeira` varchar(10) NOT NULL,
  PRIMARY KEY (`nr`),
  KEY `curso` (`curso`),
  KEY `docente` (`docente`),
  KEY `turma` (`turma`),
  KEY `fk_cadeira` (`cadeira`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `presencatbl`
--

INSERT INTO `presencatbl` (`nr`, `docente`, `curso`, `turma`, `data_presenca`, `cadeira`) VALUES
(1, '222', 'EIT', 'EIT22', '2023-10-17', 'EEI-09');

--
-- Triggers `presencatbl`
--
DROP TRIGGER IF EXISTS `TG_presenca_presencaTBL`;
DELIMITER $$
CREATE TRIGGER `TG_presenca_presencaTBL` BEFORE INSERT ON `presencatbl` FOR EACH ROW set new.data_presenca=current_date()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `presenca_estudantetbl`
--

DROP TABLE IF EXISTS `presenca_estudantetbl`;
CREATE TABLE IF NOT EXISTS `presenca_estudantetbl` (
  `ID_presenca_estudante` int NOT NULL AUTO_INCREMENT,
  `ID_estudante` int NOT NULL,
  `ID_cadeira` varchar(10) NOT NULL,
  `data_presenca` date NOT NULL,
  `presenca` enum('Presente','Ausente') NOT NULL,
  PRIMARY KEY (`ID_presenca_estudante`),
  KEY `ID_estudante` (`ID_estudante`),
  KEY `ID_cadeira` (`ID_cadeira`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `presenca_estudantetbl`
--

INSERT INTO `presenca_estudantetbl` (`ID_presenca_estudante`, `ID_estudante`, `ID_cadeira`, `data_presenca`, `presenca`) VALUES
(8, 225026, 'EEI-09', '2023-10-17', 'Ausente'),
(7, 1301, 'EEI-09', '2023-10-17', 'Presente');

--
-- Triggers `presenca_estudantetbl`
--
DROP TRIGGER IF EXISTS `TG_presenca_presenca_estudanteTBL`;
DELIMITER $$
CREATE TRIGGER `TG_presenca_presenca_estudanteTBL` BEFORE INSERT ON `presenca_estudantetbl` FOR EACH ROW set new.data_presenca=current_date()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sumariotbl`
--

DROP TABLE IF EXISTS `sumariotbl`;
CREATE TABLE IF NOT EXISTS `sumariotbl` (
  `ID_sumario` int NOT NULL AUTO_INCREMENT,
  `docente` varchar(10) NOT NULL,
  `curso` varchar(10) NOT NULL,
  `cadeira` varchar(10) NOT NULL,
  `data_registro` date NOT NULL,
  `sumario` varchar(225) NOT NULL,
  `duracao` varchar(3) NOT NULL,
  `turma` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_sumario`),
  KEY `curso` (`curso`),
  KEY `cadeira` (`cadeira`),
  KEY `docente` (`docente`),
  KEY `turma` (`turma`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sumariotbl`
--

INSERT INTO `sumariotbl` (`ID_sumario`, `docente`, `curso`, `cadeira`, `data_registro`, `sumario`, `duracao`, `turma`) VALUES
(5, '222', 'EIT', 'EEI-09', '2023-10-17', 'sistemas de gerenciamento de base de dados', '2 h', 'EIT22');

--
-- Triggers `sumariotbl`
--
DROP TRIGGER IF EXISTS `TG_inserir_sumarioTBL`;
DELIMITER $$
CREATE TRIGGER `TG_inserir_sumarioTBL` BEFORE INSERT ON `sumariotbl` FOR EACH ROW set new.data_registro=current_date()
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `turmatbl`
--

DROP TABLE IF EXISTS `turmatbl`;
CREATE TABLE IF NOT EXISTS `turmatbl` (
  `nr` int NOT NULL AUTO_INCREMENT,
  `ID_turma` varchar(10) NOT NULL,
  `curso` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_turma`),
  UNIQUE KEY `nr` (`nr`),
  KEY `curso` (`curso`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `turmatbl`
--

INSERT INTO `turmatbl` (`nr`, `ID_turma`, `curso`) VALUES
(3, 'CAG22', 'CAG'),
(4, 'CAG23', 'CAG'),
(1, 'EIT22', 'EIT'),
(2, 'EIT23', 'EIT');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `estudantetbl`
--
ALTER TABLE `estudantetbl`
  ADD CONSTRAINT `fk_turma_estudante` FOREIGN KEY (`turma`) REFERENCES `turmatbl` (`ID_turma`);

--
-- Constraints for table `presencatbl`
--
ALTER TABLE `presencatbl`
  ADD CONSTRAINT `fk_cadeira` FOREIGN KEY (`cadeira`) REFERENCES `cadeiratbl` (`ID_cadeira`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
