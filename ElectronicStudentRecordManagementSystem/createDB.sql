-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Dic 10, 2019 alle 11:02
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
  `hashedPassword` varchar(512) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `sysAdmin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Admins`
--

INSERT INTO `Admins` (`codFisc`, `hashedPassword`, `name`, `surname`, `sysAdmin`) VALUES
('ADM', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'Adamo', 'Eva', 0),
('FLC', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'Nino', 'Frassica', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `Announcements`
--

CREATE TABLE `Announcements` (
  `ID` int(11) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Title` varchar(100) NOT NULL,
  `Text` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Announcements`
--

INSERT INTO `Announcements` (`ID`, `Timestamp`, `Title`, `Text`) VALUES
(1, '2019-11-21 17:07:25', 'First Announcement', 'This is the first announcement of the poliTO school. For the real slim shady please stand up!'),
(2, '2019-11-21 17:09:18', 'Second official announcement', 'Just gonna stand there and watch me burn\r\nBut that\'s alright, because I like the way it hurts\r\nJust gonna stand there and hear me cry\r\nBut that\'s alright, because I love the way you lie\r\nI love the way you lie');

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

--
-- Dump dei dati per la tabella `Assignments`
--

INSERT INTO `Assignments` (`subject`, `date`, `classID`, `textAssignment`) VALUES
('History', '2019-12-03', '1A', 'WWII'),
('Maths', '2019-11-28', '1A', 'Equations'),
('Physics', '2019-11-27', '1A', 'Vectors');

-- --------------------------------------------------------

--
-- Struttura della tabella `Attendance`
--

CREATE TABLE `Attendance` (
  `date` date NOT NULL,
  `codFisc` varchar(50) NOT NULL,
  `absence` tinyint(1) NOT NULL,
  `lateEntry` int(2) NOT NULL,
  `earlyExit` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Attendance`
--

INSERT INTO `Attendance` (`date`, `codFisc`, `absence`, `lateEntry`, `earlyExit`) VALUES
('2019-09-20', 'FRCWTR', 1, 1, 5),
('2019-10-03', 'FRCWTR', 1, 0, 2),
('2019-10-08', 'FRCWTR', 1, 0, 0),
('2019-10-09', 'FRCWTR', 1, 0, 0),
('2019-10-10', 'FRCWTR', 1, 0, 0),
('2019-10-11', 'FRCWTR', 1, 0, 0),
('2019-11-22', 'FRCWTR', 1, 0, 0),
('2019-11-25', 'ILA', 1, 2, 4),
('2019-11-26', 'ILA', 1, 0, 0),
('2019-11-27', 'FRCWTR', 0, 2, 0),
('2019-11-27', 'ILA', 1, 0, 4),
('2019-11-28', 'ILA', 0, 3, 0),
('2019-12-01', 'FRCWTR', 1, 0, 0),
('2019-12-02', 'BOB', 1, 0, 0),
('2019-12-02', 'CHARED', 1, 1, 1),
('2019-12-02', 'DANBROWN', 0, 1, 0),
('2019-12-02', 'FRCWTR', 1, 1, 3),
('2019-12-02', 'ILA', 1, 2, 4),
('2019-12-03', 'FRCWTR', 0, 3, 0),
('2020-01-12', 'FRCWTR', 1, 2, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `Classes`
--

CREATE TABLE `Classes` (
  `classID` varchar(32) NOT NULL,
  `coordinatorSSN` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Classes`
--

INSERT INTO `Classes` (`classID`, `coordinatorSSN`) VALUES
('1A', 'TEA'),
('1B', 'TEA'),
('1D', 'GNV');

-- --------------------------------------------------------

--
-- Struttura della tabella `FinalGrades`
--

CREATE TABLE `FinalGrades` (
  `codFisc` varchar(64) NOT NULL,
  `subject` varchar(32) NOT NULL,
  `finalTerm` date NOT NULL,
  `finalGrade` int(2) NOT NULL
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

--
-- Dump dei dati per la tabella `Lectures`
--

INSERT INTO `Lectures` (`date`, `hour`, `classID`, `codFiscTeacher`, `subject`, `topic`) VALUES
('2019-11-05', 1, '1A', 'TEA', 'History', 'arg0'),
('2019-11-11', 1, '1A', 'TEA', 'History', 'arg1');

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

--
-- Dump dei dati per la tabella `Marks`
--

INSERT INTO `Marks` (`codFisc`, `subject`, `date`, `hour`, `mark`) VALUES
('FRCWTR', 'Italian', '2019-10-10', 1, '7+'),
('FRCWTR', 'History', '2019-10-10', 2, '6'),
('FRCWTR', 'Philosophy', '2019-10-10', 3, '5/6'),
('FRCWTR', 'Maths', '2019-10-11', 1, '9-'),
('FRCWTR', 'Physics', '2019-10-12', 1, '3+'),
('FRCWTR', 'Italian', '2019-10-14', 2, '9/10'),
('FRCWTR', 'Italian', '2019-10-15', 3, '9');

-- --------------------------------------------------------

--
-- Struttura della tabella `Parents`
--

CREATE TABLE `Parents` (
  `email` varchar(50) NOT NULL,
  `hashedPassword` varchar(512) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `codFisc` varchar(50) NOT NULL,
  `firstLogin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Parents`
--

INSERT INTO `Parents` (`email`, `hashedPassword`, `name`, `surname`, `codFisc`, `firstLogin`) VALUES
('cla_9_6@hotmail.it', '$2y$10$aC4SPkQR3pFQjw1rrIcsT.Xn0AFtcdSc2BXsBYwh.Bor9FUAp0NeK', 'Claudio', 'Filocamo', 'CLDFLCM', 1),
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
-- Struttura della tabella `Principals`
--

CREATE TABLE `Principals` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(512) DEFAULT NULL,
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
('1B', 'MRC'),
('1C', 'ANDR'),
('1C', 'SMN');

-- --------------------------------------------------------

--
-- Struttura della tabella `StudentNotes`
--

CREATE TABLE `StudentNotes` (
  `codFiscStudent` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `hour` int(1) NOT NULL,
  `subject` varchar(32) NOT NULL,
  `Note` varchar(4096) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
('ANDR', 'Andrew', 'Cristen', 'pippo@gmail.it', '', ''),
('BOB', 'Bob', 'Silver', 'cla_9_6@hotmail.it', '', '1A'),
('CHAPIN', 'Charlotte', 'Pink', 'miiiimmo_9_6@hotmail.it', '', '1B'),
('CHARED', 'Charlie', 'Red', 'cla_9_6@hotmail.it', '', '1A'),
('CLDFLCM', 'Claudio', 'Filocamo', 'cld@gmail.com', '', ''),
('CRS', 'Cristian', 'Mins', 'cris@gmail.it', 'parent@parent.it', '1D'),
('DANBROWN', 'Daniel', 'Brown', 'parent@parent.it', '', '1A'),
('EMILI', 'Emily', 'Lime', 'volley.champions@hotmail.it', '', '1B'),
('FRCWTR', 'Walter', 'Forcignano', 'wlt@gmail.it', 'parent@parent.it', '1A'),
('HRRWHI', 'Harry', 'White', 'gigimarzullo@genitore1.it', '', '1D'),
('ILA', 'Ilaria', 'Gioda', 'ila@gmail.it', 'cla_9_6@hotmail.it', '1A'),
('ISAORA', 'Isabella', 'Orange', 'padre@hotmail.it', '', '1D'),
('JAMBCK', 'James', 'Black', 'padre@hotmail.it', '', '1D'),
('JEPPL', 'Jessica', 'Purple', 'gigimarzullo@genitore1.it', '', '1D'),
('LILYCO', 'Lily', 'Coral', 'parent@parent.it', '', '1A'),
('MARYG', 'Mary', 'Golden', 'volley.champions@hotmail.it', '', '1B'),
('MRC', 'Marco', 'Cipriano', 'mrc@gmail.it', '', ''),
('OSCGRA', 'Oscar', 'Gray', 'volley.champions@hotmail.it', '', '1B'),
('SAHYEW', 'Sarah', 'Yellow', 'volley.champions@hotmail.it', '', '1D'),
('SMN', 'Simona', 'Genovese', 'smn@gmail.it', 'parent@parent.it', ''),
('SUSBLU', 'Susan', 'Blue', 'miiiimmo_9_6@hotmail.it', '', '1B'),
('WILGEE', 'William', 'Green', 'padre@hotmail.it', '', '1A');

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
-- Struttura della tabella `TeacherClassSubjectTable`
--

CREATE TABLE `TeacherClassSubjectTable` (
  `codFisc` varchar(50) NOT NULL,
  `classID` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `TeacherClassSubjectTable`
--

INSERT INTO `TeacherClassSubjectTable` (`codFisc`, `classID`, `subject`) VALUES
('FLCM', '1A', 'Philosophy'),
('GNV', '1A', 'Physics'),
('GNV', '1D', 'Geography'),
('TEA', '1A', 'History'),
('TEA', '1A', 'Maths'),
('TEA', '1B', 'Italian');

-- --------------------------------------------------------

--
-- Struttura della tabella `Teachers`
--

CREATE TABLE `Teachers` (
  `codFisc` varchar(50) NOT NULL,
  `hashedPassword` varchar(512) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `principal` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `Teachers`
--

INSERT INTO `Teachers` (`codFisc`, `hashedPassword`, `name`, `surname`, `principal`) VALUES
('FLCM', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'Filocamo', 'Claudio', 1),
('GNV', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'simona', 'genovese', 0),
('TEA', '$2y$10$GyIznxAh8Wdk01oelidrQOm.XBSxZNnyDxclIiG9cqdkgoGjQTc.m', 'TeacherName', 'TeacherSurname', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `Timetable`
--

CREATE TABLE `Timetable` (
  `classID` varchar(32) NOT NULL,
  `day` varchar(8) NOT NULL,
  `hour` int(1) NOT NULL,
  `subject` varchar(32) NOT NULL
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
-- Indici per le tabelle `Attendance`
--
ALTER TABLE `Attendance`
  ADD PRIMARY KEY (`date`,`codFisc`);

--
-- Indici per le tabelle `Lectures`
--
ALTER TABLE `Lectures`
  ADD PRIMARY KEY (`date`,`hour`,`classID`);

--
-- Indici per le tabelle `Marks`
--
ALTER TABLE `Marks`
  ADD PRIMARY KEY (`codFisc`,`date`,`hour`),
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
  ADD PRIMARY KEY (`classID`,`codFisc`),
  ADD KEY `studentIDForeignKey` (`codFisc`);

--
-- Indici per le tabelle `StudentNotes`
--
ALTER TABLE `StudentNotes`
  ADD PRIMARY KEY (`codFiscStudent`,`date`,`hour`,`subject`);

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
-- Indici per le tabelle `TeacherClassSubjectTable`
--
ALTER TABLE `TeacherClassSubjectTable`
  ADD PRIMARY KEY (`codFisc`,`classID`,`subject`),
  ADD KEY `subjectTeacherClassSubjectForeignKey` (`subject`);

--
-- Indici per le tabelle `Teachers`
--
ALTER TABLE `Teachers`
  ADD PRIMARY KEY (`codFisc`);

--
-- Indici per le tabelle `Timetable`
--
ALTER TABLE `Timetable`
  ADD PRIMARY KEY (`classID`,`day`,`hour`,`subject`);

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

--
-- Limiti per la tabella `TeacherClassSubjectTable`
--
ALTER TABLE `TeacherClassSubjectTable`
  ADD CONSTRAINT `codFiscTeacherForeignKey` FOREIGN KEY (`codFisc`) REFERENCES `Teachers` (`codFisc`),
  ADD CONSTRAINT `subjectTeacherClassSubjectForeignKey` FOREIGN KEY (`subject`) REFERENCES `Subjects` (`name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
