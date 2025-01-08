-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 28, 2024 at 02:19 PM
-- Server version: 8.4.3
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `etudiants`
--

-- --------------------------------------------------------

--
-- Table structure for table `b1`
--

DROP TABLE IF EXISTS `b1`;
CREATE TABLE IF NOT EXISTS `b1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Introduction_C` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `b1`
--

INSERT INTO `b1` (`id`, `matricule`, `nom`, `prenom`, `Introduction_C`) VALUES
(8, '2024B1001', 'Marinelle', 'Ebiane', '18');

-- --------------------------------------------------------

--
-- Table structure for table `b2`
--

DROP TABLE IF EXISTS `b2`;
CREATE TABLE IF NOT EXISTS `b2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `php_oriente_objet` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `b2`
--

INSERT INTO `b2` (`id`, `matricule`, `nom`, `prenom`, `php_oriente_objet`) VALUES
(5, '2024B2001', 'Mbala', 'Roxane', '19'),
(6, '2024B2002', 'Myriam', 'Deffo', '10');

-- --------------------------------------------------------

--
-- Table structure for table `b3`
--

DROP TABLE IF EXISTS `b3`;
CREATE TABLE IF NOT EXISTS `b3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Java_poo` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `b3`
--

INSERT INTO `b3` (`id`, `matricule`, `nom`, `prenom`, `Java_poo`) VALUES
(3, '2024B3001', 'BABA', 'Victor', '20');

-- --------------------------------------------------------

--
-- Table structure for table `connexion`
--

DROP TABLE IF EXISTS `connexion`;
CREATE TABLE IF NOT EXISTS `connexion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `etudiant_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `etudiant_id` (`etudiant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `connexion`
--

INSERT INTO `connexion` (`id`, `matricule`, `password`, `etudiant_id`) VALUES
(82, '2024B1001', '2024B1001', NULL),
(83, '2024B2001', '2024B2001', NULL),
(84, '2024B3001', '2024B3001', NULL),
(85, '2024B2002', '2024B2002', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `connexion_prof`
--

DROP TABLE IF EXISTS `connexion_prof`;
CREATE TABLE IF NOT EXISTS `connexion_prof` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `connexion_prof`
--

INSERT INTO `connexion_prof` (`id`, `matricule`, `password`) VALUES
(2, 'ENS-20241227233306', 'ENS-20241227233306'),
(3, 'ENS-20241227233348', 'ENS-20241227233348');

-- --------------------------------------------------------

--
-- Table structure for table `enseignants`
--

DROP TABLE IF EXISTS `enseignants`;
CREATE TABLE IF NOT EXISTS `enseignants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `fonction` varchar(50) DEFAULT NULL,
  `date_enregistrement` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enseignants`
--

INSERT INTO `enseignants` (`id`, `matricule`, `nom`, `prenom`, `photo`, `email`, `fonction`, `date_enregistrement`) VALUES
(23, 'ENS-20241227233306', 'Fomekong', 'Evariste', 'uploads/ENS-20241227233306_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'Docteur', '2024-12-28 00:33:06'),
(24, 'ENS-20241227233348', 'Batchato', 'Faguy', 'uploads/ENS-20241227233348_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'Docteur', '2024-12-28 00:33:48');

-- --------------------------------------------------------

--
-- Table structure for table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
CREATE TABLE IF NOT EXISTS `etudiants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `niveau` varchar(10) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `nom_parent` varchar(100) NOT NULL,
  `email_parent` varchar(100) NOT NULL,
  `age` int NOT NULL,
  `montant_paye` bigint DEFAULT '0',
  `reste` bigint DEFAULT NULL,
  `statut` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `matricule`, `photo`, `email`, `niveau`, `montant`, `nom_parent`, `email_parent`, `age`, `montant_paye`, `reste`, `statut`, `date_naissance`) VALUES
(82, 'Marinelle', 'Ebiane', '2024B1001', 'ENS-20241217032016_graduated (1).png', 'marinelleebiane1@gmail.com', 'B1', 1000000.00, 'Marinelle Marinelle', 'marinelleebiane1@gmail.com', 25, 0, 1000000, 'Insolvable', '2004-03-05'),
(83, 'Mbala', 'Roxane', '2024B2001', 'ENS-20241216174220_ENS-20241216174040_graduated (1).png', 'marinelleebiane1@gmail.com', 'B2', 2000000.00, 'Marinelle Marinelle', 'marinelleebiane1@gmail.com', 25, 0, 2000000, 'Insolvable', '2011-12-04'),
(84, 'BABA', 'Victor', '2024B3001', 'ENS-20241216174220_ENS-20241216174040_graduated (1).png', 'marinelleebiane1@gmail.com', 'B3', 3000000.00, 'Marinelle Marinelle', 'marinelleebiane1@gmail.com', 25, 0, 2000000, 'Insolvable', '2005-05-05'),
(85, 'Myriam', 'Deffo', '2024B2002', 'ENS-20241216174701_ENS-20241216173927_graduated (1).png', 'aymerickbaba05@gmail.com', 'B2', 2000000.00, 'victor baba', 'aymerickbaba05@gmail.com', 12, 0, 2000000, 'Insolvable', '2011-12-01');

-- --------------------------------------------------------

--
-- Table structure for table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
CREATE TABLE IF NOT EXISTS `matieres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule_prof` varchar(50) NOT NULL,
  `nom_prof` varchar(100) NOT NULL,
  `nom_matiere` varchar(100) NOT NULL,
  `niveau_matiere` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `matieres`
--

INSERT INTO `matieres` (`id`, `matricule_prof`, `nom_prof`, `nom_matiere`, `niveau_matiere`) VALUES
(16, 'ENS-20241227233306', 'Fomekong Evariste', 'php_oriente_objet', 'B2'),
(17, 'ENS-20241227233306', 'Fomekong Evariste', 'Introduction_C', 'B1'),
(18, 'ENS-20241227233348', 'Batchato Faguy', 'Java_poo', 'B3');

-- --------------------------------------------------------

--
-- Table structure for table `versements`
--

DROP TABLE IF EXISTS `versements`;
CREATE TABLE IF NOT EXISTS `versements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) DEFAULT NULL,
  `montant` bigint DEFAULT NULL,
  `date_versement` date DEFAULT NULL,
  `numero_versement` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `versements`
--

INSERT INTO `versements` (`id`, `matricule`, `montant`, `date_versement`, `numero_versement`) VALUES
(23, '2024B2001', 1000000, '2024-12-17', '241217V0001'),
(24, '2024B1001', 1000000, '2024-12-17', '241217V0002'),
(25, '2024B2001', 1000000, '2024-12-17', '241217V0003'),
(26, '2024B2001', 1000000, '2024-12-17', '241217V0004'),
(27, '2024B1001', 1000000, '2024-12-17', '241217V0005'),
(28, '2024B2002', 1000000, '2024-12-17', '241217V0006'),
(32, '2024B2001', 10000, '2024-12-27', '241227V0007');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `connexion`
--
ALTER TABLE `connexion`
  ADD CONSTRAINT `connexion_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
