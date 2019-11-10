-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Nov 09, 2019 alle 23:52
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
CREATE DATABASE IF NOT EXISTS `school` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `school`;

-- --------------------------------------------------------

--
-- Struttura della tabella `Admins`
--

CREATE TABLE `Admins` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Assignments`
--

CREATE TABLE `Assignments` (
  `subject` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `classID` varchar(50) NOT NULL,
  `textAssignment` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Lectures`
--

CREATE TABLE `Lectures` (
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `classID` varchar(5) NOT NULL,
  `codFiscTeacher` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `topic` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Marks`
--

CREATE TABLE `Marks` (
  `codFisc` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `mark` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Parents`
--

CREATE TABLE `Parents` (
  `email` varchar(50) NOT NULL,
  `hashedPassword` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `codFisc` varchar(50) NOT NULL,
  `firstLogin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `Principals`
--

CREATE TABLE `Principals` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `ProposedClasses`
--

CREATE TABLE `ProposedClasses` (
  `classID` varchar(5) NOT NULL,
  `codFisc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `ProposedClasses`
--

INSERT INTO `ProposedClasses` (`classID`, `codFisc`) VALUES
('1A', 'CLDFLCM'),
('1A', 'FRCWTR'),
('1B', 'MRC');

-- --------------------------------------------------------

--
-- Struttura della tabella `Students`
--

CREATE TABLE `Students` (
  `codFisc` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `emailP1` varchar(50) NOT NULL,
  `emailP2` varchar(50) NOT NULL,
  `classID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Students`
--

INSERT INTO `Students` (`codFisc`, `name`, `surname`, `emailP1`, `emailP2`, `classID`) VALUES
('CLDFLCM', 'Claudio', 'Filocamo', 'cld@gmail.com', '', '1A'),
('FRCWTR', 'Walter', 'Forcignanò', 'wlt@gmail.it', '', '1A'),
('MRC', 'Marco', 'Cipriano', 'mrc@gmail.it', '', '1B');

-- --------------------------------------------------------

--
-- Struttura della tabella `Subjects`
--

CREATE TABLE `Subjects` (
  `name` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `hours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Subjects`
--

INSERT INTO `Subjects` (`name`, `year`, `hours`) VALUES
('Biology and Chemistry', 1, 4),
('English', 1, 3),
('Geography', 1, 2),
('History', 1, 3),
('Italian', 1, 3),
('Latin', 1, 4),
('Maths', 1, 5),
('Philosophy', 1, 3),
('Physics', 1, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `Teachers`
--

CREATE TABLE `Teachers` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Admins`
--
ALTER TABLE `Admins`
  ADD PRIMARY KEY (`codFisc`);

--
-- Indici per le tabelle `Assignments`
--
ALTER TABLE `Assignments`
  ADD PRIMARY KEY (`subject`,`date`,`classID`);

--
-- Indici per le tabelle `Lectures`
--
ALTER TABLE `Lectures`
  ADD PRIMARY KEY (`date`,`hour`,`classID`);

--
-- Indici per le tabelle `Marks`
--
ALTER TABLE `Marks`
  ADD PRIMARY KEY (`codFisc`,`subject`,`date`,`hour`),
  ADD KEY `subjectForeignKey` (`subject`);

--
-- Indici per le tabelle `Parents`
--
ALTER TABLE `Parents`
  ADD PRIMARY KEY (`email`);

--
-- Indici per le tabelle `Principals`
--
ALTER TABLE `Principals`
  ADD PRIMARY KEY (`codFisc`);

--
-- Indici per le tabelle `ProposedClasses`
--
ALTER TABLE `ProposedClasses`
  ADD KEY `studentIDForeignKey` (`codFisc`);

--
-- Indici per le tabelle `Students`
--
ALTER TABLE `Students`
  ADD PRIMARY KEY (`codFisc`);

--
-- Indici per le tabelle `Subjects`
--
ALTER TABLE `Subjects`
  ADD PRIMARY KEY (`name`,`year`);

--
-- Indici per le tabelle `Teachers`
--
ALTER TABLE `Teachers`
  ADD PRIMARY KEY (`codFisc`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `Marks`
--
ALTER TABLE `Marks`
  ADD CONSTRAINT `codFiscForeignKey` FOREIGN KEY (`codFisc`) REFERENCES `Students` (`codFisc`),
  ADD CONSTRAINT `subjectForeignKey` FOREIGN KEY (`subject`) REFERENCES `Subjects` (`name`);

--
-- Limiti per la tabella `ProposedClasses`
--
ALTER TABLE `ProposedClasses`
  ADD CONSTRAINT `studentIDForeignKey` FOREIGN KEY (`codFisc`) REFERENCES `Students` (`codFisc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
