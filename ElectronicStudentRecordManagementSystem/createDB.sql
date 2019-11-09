-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Nov 09, 2019 alle 18:32
-- Versione del server: 10.4.8-MariaDB
-- Versione PHP: 7.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school`
--
DROP DATABASE IF EXISTS `school`;
CREATE DATABASE `school` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `school`;

-- --------------------------------------------------------

--
-- Struttura della tabella `Admins`
--

CREATE TABLE IF NOT EXISTS `Admins` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(50) NOT NULL,
  PRIMARY KEY (`codFisc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Assignments`
--

CREATE TABLE IF NOT EXISTS `Assignments` (
  `subject` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `classID` varchar(50) NOT NULL,
  `textAssignment` varchar(50) NOT NULL,
  PRIMARY KEY (`subject`,`date`,`classID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Lectures`
--

CREATE TABLE IF NOT EXISTS `Lectures` (
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `classID` varchar(5) NOT NULL,
  `codFiscTeacher` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `topic` varchar(50) NOT NULL,
  PRIMARY KEY (`date`,`hour`,`classID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Marks`
--

CREATE TABLE IF NOT EXISTS `Marks` (
  `codFisc` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `mark` varchar(50) NOT NULL,
  PRIMARY KEY (`codFisc`,`subject`,`date`,`hour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Parents`
--

CREATE TABLE IF NOT EXISTS `Parents` (
  `email` varchar(50) NOT NULL,
  `hashedPassword` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `codFisc` varchar(50) NOT NULL,
  `firstLogin` tinyint(1) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Principals`
--

CREATE TABLE IF NOT EXISTS `Principals` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  PRIMARY KEY (`codFisc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `ProposedClasses`
--

CREATE TABLE IF NOT EXISTS `ProposedClasses` (
  `classID` varchar(5) NOT NULL,
  `codFisc` varchar(50) NOT NULL,
  KEY `studentIDForeignKey` (`codFisc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `ProposedClasses`
--

INSERT INTO `ProposedClasses` (`classID`, `codFisc`) VALUES
('1A', 'FRCWTR'),
('1B', 'MRC');

-- --------------------------------------------------------

--
-- Struttura della tabella `Students`
--

CREATE TABLE IF NOT EXISTS `Students` (
  `codFisc` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `emailP1` varchar(50) NOT NULL,
  `emailP2` varchar(50) NOT NULL,
  `classID` varchar(50) NOT NULL,
  PRIMARY KEY (`codFisc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Students`
--

INSERT INTO `Students` (`codFisc`, `name`, `surname`, `emailP1`, `emailP2`, `classID`) VALUES
('FRCWTR', 'Walter', 'Forcignanò', 'wlt@gmail.it', '', '1A'),
('MRC', 'Marco', 'Cipriano', 'mrc@gmail.it', '', '1B');

-- --------------------------------------------------------

--
-- Struttura della tabella `Subjects`
--

CREATE TABLE IF NOT EXISTS `Subjects` (
  `name` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `hours` int(11) NOT NULL,
  PRIMARY KEY (`name`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Teachers`
--

CREATE TABLE IF NOT EXISTS `Teachers` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  PRIMARY KEY (`codFisc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `Marks`
--
ALTER TABLE `Marks`
  ADD CONSTRAINT `codFiscForeignKey` FOREIGN KEY (`codFisc`) REFERENCES `Students` (`codFisc`);

--
-- Limiti per la tabella `ProposedClasses`
--
ALTER TABLE `ProposedClasses`
  ADD CONSTRAINT `studentIDForeignKey` FOREIGN KEY (`codFisc`) REFERENCES `Students` (`codFisc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
