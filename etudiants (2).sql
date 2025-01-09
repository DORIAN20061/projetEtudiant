-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 09 jan. 2025 à 11:08
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `etudiants`
--

-- --------------------------------------------------------

--
-- Structure de la table `b1`
--

DROP TABLE IF EXISTS `b1`;
CREATE TABLE IF NOT EXISTS `b1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `philosophie` varchar(60) DEFAULT '0',
  `philosophie_CC` varchar(60) DEFAULT '0',
  `Tournage` varchar(60) DEFAULT '0',
  `Tournage_CC` varchar(60) DEFAULT '0',
  `POO_PHP` varchar(60) DEFAULT '0',
  `POO_PHP_CC` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `b1`
--

INSERT INTO `b1` (`id`, `matricule`, `nom`, `prenom`, `philosophie`, `philosophie_CC`, `Tournage`, `Tournage_CC`, `POO_PHP`, `POO_PHP_CC`) VALUES
(9, '2024B1001', 'ABDOURAMANE', 'GAZA', '0', '0', '0', '13', '0', '0'),
(10, '2024B1002', 'ABDOURAMANE', 'GAZA', '0', '0', '12', '19', '0', '0'),
(11, '2025B1003', 'YOPA', 'DORIAN', '0', '0', '0', '0', '0', '0'),
(12, '2025B1004', 'NExu', 'anas', '0', '0', '0', '0', '0', '0');

-- --------------------------------------------------------

--
-- Structure de la table `b2`
--

DROP TABLE IF EXISTS `b2`;
CREATE TABLE IF NOT EXISTS `b2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `b2`
--

INSERT INTO `b2` (`id`, `matricule`, `nom`, `prenom`) VALUES
(7, '2024B2003', 'BATOSINE', 'DORIAN'),
(8, '2024B2002', 'SANAMA', 'KING'),
(9, '2024B2002', 'ABDOURAMANE', 'GAZA'),
(10, '2024B2002', 'MATANGA', 'BROOKLYN'),
(11, '2025B2002', 'brooklyn', 'frances');

-- --------------------------------------------------------

--
-- Structure de la table `b3`
--

DROP TABLE IF EXISTS `b3`;
CREATE TABLE IF NOT EXISTS `b3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Ui_design` varchar(60) DEFAULT '0',
  `Ui_design_CC` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `b3`
--

INSERT INTO `b3` (`id`, `matricule`, `nom`, `prenom`, `Ui_design`, `Ui_design_CC`) VALUES
(3, '2024B3001', 'BABA', 'Victor', '0', '0'),
(4, '2024B3001', 'tamar', 'ondoa', '0', '0'),
(5, '2024B3001', 'MATANGA', 'BROOKLYN', '0', '0'),
(6, '2024B3001', 'MATANGA', 'BROOKLYN', '0', '0'),
(7, '2025B3002', 'SESINE', 'MAXIME', '0', '0'),
(8, '2025B3002', 'NExu', 'anas', '0', '0');

-- --------------------------------------------------------

--
-- Structure de la table `connexion`
--

DROP TABLE IF EXISTS `connexion`;
CREATE TABLE IF NOT EXISTS `connexion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `etudiant_id` int DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `etudiant_id` (`etudiant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `connexion`
--

INSERT INTO `connexion` (`id`, `matricule`, `password`, `etudiant_id`, `statut`) VALUES
(91, '2024B1002', 'forever', NULL, NULL),
(95, '2025B3002', 'ADMIN', NULL, NULL),
(96, '2025B1003', '2025B1003', NULL, NULL),
(97, '2025B2002', '2025B2002', NULL, NULL),
(99, '2025B1004', '2025B1004', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `connexion_prof`
--

DROP TABLE IF EXISTS `connexion_prof`;
CREATE TABLE IF NOT EXISTS `connexion_prof` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `connexion_prof`
--

INSERT INTO `connexion_prof` (`id`, `matricule`, `password`) VALUES
(2, 'ENS-20241227233306', 'ENS-20241227233306'),
(3, 'ENS-20241227233348', 'ENS-20241227233348'),
(4, 'ENS-20241230145720', 'ENS-20241230145720'),
(5, 'ENS-20241230145837', 'ENS-20241230145837'),
(6, 'ENS-20241231130512', 'ENS-20241231130512'),
(7, 'ENS-20241231131058', 'ENS-20241231131058'),
(8, 'ENS-20241231131324', 'ENS-20241231131324'),
(9, 'ENS-20250103033931', 'ENS-20250103033931');

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `matricule`, `nom`, `prenom`, `photo`, `email`, `fonction`, `date_enregistrement`) VALUES
(26, 'ENS-20241230145837', 'tamar', 'ondoa', 'uploads/ENS-20241230145837_ENS-20241223092132_converted_k_logo.png', 'dorianbat8@gmail.com', 'stagière', '2024-12-30 15:58:37'),
(27, 'ENS-20241231130512', 'TIK NDENGUE', 'TENE', 'uploads/ENS-20241231130512_2436828d-23a0-4601-8427-0b0c753cb310.jpg', 'tenetik@gmail.com', 'Développeur web', '2024-12-31 14:05:12'),
(29, 'ENS-20241231131324', 'NZENGUEM', 'TORSADE', 'uploads/ENS-20241231131324_29a28982-66e6-41de-84c1-dd81fa002af4.jpg', 'dorianbat8@gmail.com', 'TIK TOKEUR', '2024-12-31 14:13:24'),
(30, 'ENS-20250103033931', 'FOMEKONG', 'Paya', 'uploads/ENS-20250103033931_Hacker хакер 駭客 هکر हैकर 해커 (1).jpg', 'dorianbat8@gmail.com', 'Développeur mobile', '2025-01-03 04:39:31');

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
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
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `matricule`, `photo`, `email`, `niveau`, `montant`, `nom_parent`, `email_parent`, `age`, `montant_paye`, `reste`, `statut`, `date_naissance`) VALUES
(91, 'ABDOURAMANE', 'GAZA', '2024B1002', 'ENS-20241217032703_ENS-20241216174220_ENS-20241216174040_graduated (1).png', 'dorianbat8@gmail.com', 'B1', 1000000.00, '0', 'dorianbat8@gmail.com', 2005, 0, 1000000, 'Insolvable', '0000-00-00'),
(95, 'SESINE', 'MAXIME', '2025B3002', 'Anonymous Silver🔥.jpg', 'dorianbat8@gmail.com', 'B3', 2600000.00, '0', 'dorianbat8@gmail.com', 2003, 400000, 1600000, 'Insolvable', '2003-06-30'),
(96, 'YOPA', 'DORIAN', '2025B1003', 'Anonymous.jpg', 'dorianbat8@gmail.com', 'B1', 300000.00, 'PAPADORIAN', 'dorianbat8@gmail.com', 21, 700000, 300000, 'En cours', '2003-11-01'),
(97, 'brooklyn', 'frances', '2025B2002', 'ENS-20241223091827_converted_k_logo.png', 'matangabrooklyn@gmail.com', 'B2', -446900.00, 'yiyi', 'matangabrooklyn@gmail.com', 19, 3670350, -1670350, 'Solvable', '0000-00-00'),
(98, 'NExu', 'anas', '2025B3002', 'ENS-20241216174701_ENS-20241216173927_graduated (1).png', 'batodorian3@gmail.com', 'B3', 3000000.00, 'papnexus', 'papanexus@gmail.com', 23, 0, 2000000, 'Insolvable', '2001-12-24'),
(99, 'NExu', 'anas', '2025B1004', 'ENS-20241216174701_ENS-20241216173927_graduated (1).png', 'batodorian3@gmail.com', 'B1', 1000000.00, 'papnexus', 'papanexus@gmail.com', 0, 0, 1000000, 'Insolvable', '2001-12-24');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
CREATE TABLE IF NOT EXISTS `matieres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule_prof` varchar(50) NOT NULL,
  `nom_prof` varchar(100) NOT NULL,
  `nom_matiere` varchar(100) NOT NULL,
  `niveau_matiere` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `matricule_prof`, `nom_prof`, `nom_matiere`, `niveau_matiere`) VALUES
(19, 'ENS-20241227233306', 'Fomekong Evariste', 'philosophie', 'B1'),
(20, 'ENS-20241231130512', 'TIK NDENGUE TENE', 'UX/UI_Design', 'B3'),
(21, 'ENS-20241231130512', 'TIK NDENGUE TENE', 'Ui_design', 'B3'),
(22, 'ENS-20241231131324', 'NZENGUEM TORSADE', 'Tournage', 'B1');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Etudiant','Admin') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `matricule`, `password`, `role`) VALUES
(1, '2025B1003', '2025B1003', 'Etudiant');

-- --------------------------------------------------------

--
-- Structure de la table `versements`
--

DROP TABLE IF EXISTS `versements`;
CREATE TABLE IF NOT EXISTS `versements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) DEFAULT NULL,
  `montant` bigint DEFAULT NULL,
  `date_versement` date DEFAULT NULL,
  `numero_versement` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `versements`
--

INSERT INTO `versements` (`id`, `matricule`, `montant`, `date_versement`, `numero_versement`) VALUES
(40, '2024B2003', 1700000, '2024-12-30', '241230V0015'),
(41, '2024B3001', 2357345678, '2025-01-02', '250102V0002'),
(42, '2025B3002', 400000, '2025-01-02', '250102V0003'),
(43, '2025B1003', 700000, '2025-01-03', '250103V0004'),
(44, '2025B2002', 1223450, '2025-01-03', '250103V0005'),
(45, '2025B2002', 1223450, '2025-01-03', '250103V0005'),
(46, '2025B2002', 1223450, '2025-01-03', '250103V0005');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `connexion`
--
ALTER TABLE `connexion`
  ADD CONSTRAINT `connexion_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
