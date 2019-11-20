-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Nov 20, 2019 alle 14:01
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
-- Struttura della tabella `admins`
--

CREATE TABLE `admins` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `admins`
--

INSERT INTO `admins` (`codFisc`, `hashedPassword`) VALUES
('ADM', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m'),
('FLC', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m');

-- --------------------------------------------------------

--
-- Struttura della tabella `assignments`
--

CREATE TABLE `assignments` (
  `subject` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `classID` varchar(50) NOT NULL,
  `textAssignment` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `lectures`
--

CREATE TABLE `lectures` (
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `classID` varchar(5) NOT NULL,
  `codFiscTeacher` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `topic` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `lectures`
--

INSERT INTO `lectures` (`date`, `hour`, `classID`, `codFiscTeacher`, `subject`, `topic`) VALUES
('2019-11-05', 1, '1A', 'GNV', 'Maths', 'arg0'),
('2019-11-11', 1, '1A', 'GNV', 'Maths', 'arg1');

-- --------------------------------------------------------

--
-- Struttura della tabella `marks`
--

CREATE TABLE `marks` (
  `codFisc` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `mark` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `marks`
--

INSERT INTO `marks` (`codFisc`, `subject`, `date`, `hour`, `mark`) VALUES
('FRCWTR', 'Italian', '2019-10-10', 1, '7+'),
('FRCWTR', 'History', '2019-10-10', 2, '6'),
('FRCWTR', 'Philosophy', '2019-10-10', 3, '5/6'),
('FRCWTR', 'Maths', '2019-10-11', 1, '5-'),
('FRCWTR', 'Physics', '2019-10-12', 1, '4+'),
('FRCWTR', 'Italian', '2019-10-14', 2, '9/10'),
('FRCWTR', 'Italian', '2019-10-15', 3, '9');

-- --------------------------------------------------------

--
-- Struttura della tabella `parents`
--

CREATE TABLE `parents` (
  `email` varchar(50) NOT NULL,
  `hashedPassword` varchar(512) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `codFisc` varchar(50) NOT NULL,
  `firstLogin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `parents`
--

INSERT INTO `parents` (`email`, `hashedPassword`, `name`, `surname`, `codFisc`, `firstLogin`) VALUES
('cla_9_6@hotmail.it', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'Claudio', 'Filocamo', 'CLDFLCM', 1),
('fant@hotmail.it', '', 'Ugo', 'Fantozzi', 'GFNTZZ', 0),
('gian_9_6@hotmail.it', '', 'Gian', 'Giacomo', 'GNGCM', 0),
('gigimarzullo@genitore1.it', '', 'Gigi', 'Marzullo', 'MZRGG65', 1),
('hot_9_6@hotmail.it', '', 'Pippo', 'Franco', 'PPPFRNC', 1),
('miiiimmo_9_6@hotmail.it', 'provaProvina', 'Mimmo', 'Secondino', 'MMMSCNDN', 1),
('padre@hotmail.it', '', 'Pio', 'Amadeo', 'PMD', 0),
('parent@parent.it', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'ParentName', 'ParentSurname', 'CCCC', 0),
('silv_9_6@hotmail.it', '', 'Silvia', 'Gertrude', 'SLVGRTD', 1),
('toro_9_6@hotmail.it', '', 'Tor', 'Ino', 'TRN', 1),
('volley.champions@hotmail.it', '', 'Mila', 'Shiro', 'MLSHR', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `principals`
--

CREATE TABLE `principals` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(512) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `proposedclasses`
--

CREATE TABLE `proposedclasses` (
  `classID` varchar(5) NOT NULL,
  `codFisc` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `proposedclasses`
--

INSERT INTO `proposedclasses` (`classID`, `codFisc`) VALUES
('1A', 'CLDFLCM'),
('1B', 'MRC'),
('1C', 'ANDR'),
('1C', 'SMN');

-- --------------------------------------------------------

--
-- Struttura della tabella `students`
--

CREATE TABLE `students` (
  `codFisc` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `emailP1` varchar(50) NOT NULL,
  `emailP2` varchar(50) NOT NULL,
  `classID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `students`
--

INSERT INTO `students` (`codFisc`, `name`, `surname`, `emailP1`, `emailP2`, `classID`) VALUES
('ANDR', 'Andrew', 'Cristen', 'pippo@gmail.it', '', ''),
('CLDFLCM', 'Claudio', 'Filocamo', 'cld@gmail.com', '', ''),
('CRS', 'Cristian', 'Mins', 'cris@gmail.it', 'parent@parent.it', '1D'),
('FRCWTR', 'Walter', 'Forcignano', 'wlt@gmail.it', 'parent@parent.it', '1A'),
('ILA', 'Ilaria', 'Gioda', 'ila@gmail.it', 'wlt@gmail.it', '1C'),
('MRC', 'Marco', 'Cipriano', 'mrc@gmail.it', '', ''),
('SMN', 'Simona', 'Genovese', 'smn@gmail.it', 'parent@parent.it', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `subjects`
--

CREATE TABLE `subjects` (
  `name` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `hours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `subjects`
--

INSERT INTO `subjects` (`name`, `year`, `hours`) VALUES
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
-- Struttura della tabella `teacherclasssubjecttable`
--

CREATE TABLE `teacherclasssubjecttable` (
  `codFisc` varchar(50) NOT NULL,
  `classID` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `teacherclasssubjecttable`
--

INSERT INTO `teacherclasssubjecttable` (`codFisc`, `classID`, `subject`) VALUES
('FLCM', '1A', 'Philosophy'),
('GNV', '1A', 'Maths'),
('GNV', '1A', 'Physics'),
('GNV', '1B', 'Geography'),
('TEA', '1A', 'History'),
('TEA', '1B', 'Italian');

-- --------------------------------------------------------

--
-- Struttura della tabella `teachers`
--

CREATE TABLE `teachers` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(512) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `teachers`
--

INSERT INTO `teachers` (`codFisc`, `hashedPassword`, `name`, `surname`) VALUES
('FLCM', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'Filocamo', 'Claudio'),
('GNV', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'simona', 'genovese'),
('TEA', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'TeacherName', 'TeacherSurname');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`codFisc`);

--
-- Indici per le tabelle `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`subject`,`date`,`classID`);

--
-- Indici per le tabelle `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`date`,`hour`,`classID`);

--
-- Indici per le tabelle `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`codFisc`,`date`,`hour`),
  ADD KEY `subjectForeignKey` (`subject`);

--
-- Indici per le tabelle `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`email`);

--
-- Indici per le tabelle `principals`
--
ALTER TABLE `principals`
  ADD PRIMARY KEY (`codFisc`);

--
-- Indici per le tabelle `proposedclasses`
--
ALTER TABLE `proposedclasses`
  ADD PRIMARY KEY (`classID`,`codFisc`),
  ADD KEY `studentIDForeignKey` (`codFisc`);

--
-- Indici per le tabelle `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`codFisc`);

--
-- Indici per le tabelle `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`name`,`year`);

--
-- Indici per le tabelle `teacherclasssubjecttable`
--
ALTER TABLE `teacherclasssubjecttable`
  ADD PRIMARY KEY (`codFisc`,`classID`,`subject`),
  ADD KEY `subjectTeacherClassSubjectForeignKey` (`subject`);

--
-- Indici per le tabelle `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`codFisc`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `codFiscForeignKey` FOREIGN KEY (`codFisc`) REFERENCES `students` (`codFisc`),
  ADD CONSTRAINT `subjectForeignKey` FOREIGN KEY (`subject`) REFERENCES `subjects` (`name`);

--
-- Limiti per la tabella `proposedclasses`
--
ALTER TABLE `proposedclasses`
  ADD CONSTRAINT `studentIDForeignKey` FOREIGN KEY (`codFisc`) REFERENCES `students` (`codFisc`);

--
-- Limiti per la tabella `teacherclasssubjecttable`
--
ALTER TABLE `teacherclasssubjecttable`
  ADD CONSTRAINT `codFiscTeacherForeignKey` FOREIGN KEY (`codFisc`) REFERENCES `teachers` (`codFisc`),
  ADD CONSTRAINT `subjectTeacherClassSubjectForeignKey` FOREIGN KEY (`subject`) REFERENCES `subjects` (`name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
