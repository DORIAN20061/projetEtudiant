-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 17 jan. 2025 à 01:54
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
  `SQL_1` varchar(60) DEFAULT '0',
  `SQL_1_CC` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `b1`
--

INSERT INTO `b1` (`id`, `matricule`, `nom`, `prenom`, `SQL_1`, `SQL_1_CC`) VALUES
(13, '2025B1002', 'BATOSINE TCHOUHO', 'DORIAN', '16', '12');

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
  `JAVA_POO` varchar(60) DEFAULT '0',
  `JAVA_POO_CC` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `b2`
--

INSERT INTO `b2` (`id`, `matricule`, `nom`, `prenom`, `JAVA_POO`, `JAVA_POO_CC`) VALUES
(12, '2025B2002', 'sfdqfsvbds', 'sfdds', '18', '20');

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
  `Linux` varchar(60) DEFAULT '0',
  `Linux_CC` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `b3`
--

INSERT INTO `b3` (`id`, `matricule`, `nom`, `prenom`, `Linux`, `Linux_CC`) VALUES
(9, '2025B3003', 'hgjilhjklm', 'ghjkgoij', '0', '0');

-- --------------------------------------------------------

--
-- Structure de la table `connexion`
--

DROP TABLE IF EXISTS `connexion`;
CREATE TABLE IF NOT EXISTS `connexion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `statut` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `connexion`
--

INSERT INTO `connexion` (`id`, `matricule`, `password`, `statut`) VALUES
(1, 'KIA-20250116', 'keyce', 'Professeur'),
(2, '2025B3003', 'keyce', 'Etudiant'),
(3, 'Admin', 'admin', 'Admin'),
(4, '2025B1002', '2025B1002', 'Etudiant'),
(5, '2025B2002', '2025B2002', 'Etudiant'),
(6, 'KIA-20250117', 'admin', 'Professeur');

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(9, 'ENS-20250103033931', 'ENS-20250103033931'),
(10, 'KIA-20250116', 'KIA-20250116'),
(11, 'KIA-20250117', 'KIA-20250117');

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `matricule`, `nom`, `prenom`, `photo`, `email`, `fonction`, `date_enregistrement`) VALUES
(31, 'KIA-20250116', 'dszfqkjewewwe', 'wwewewwew', 'uploads/KIA-20250116_#pinterest #wallpaper #colors #aestetic #brightwallpaper #followme #likeme #fashionable #wonderfull (1).jpg', 'dorianbat8@gmail.com', 'qewgfew', '2025-01-16 14:36:21'),
(32, 'KIA-20250117', 'BATCHATO', 'FAGUY', 'uploads/KIA-20250117_th.jpg', 'dorianbat8@gmail.com', 'Developpeur WEB', '2025-01-17 02:34:29');

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
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `matricule`, `photo`, `email`, `niveau`, `montant`, `nom_parent`, `email_parent`, `age`, `montant_paye`, `reste`, `statut`, `date_naissance`) VALUES
(100, 'hgjilhjklm', 'ghjkgoij', '2025B3003', 'Ninja skull illustration for t shirt design or game logo.jpg', 'dorianbat8@gmail.com', 'B3', 3000000.00, '', '', 24, 0, 2000000, 'Insolvable', '2000-08-07'),
(101, 'BATOSINE TCHOUHO', 'DORIAN', '2025B1002', 'Anonymous.png', 'dorianbat8@gmail.com', 'B1', 1000000.00, '', '', 24, 0, 1000000, 'Insolvable', '2000-09-01'),
(102, 'sfdqfsvbds', 'sfdds', '2025B2002', 'pngtree-businessman-avatar-icon-vector-download-vector-user-icon-avatar-silhouette-social-png-image_1991050.jpg', 'dorianbat8@gmail.com', 'B2', 0.00, 'DORIAN BATOSINE TCHOUHO', '', 13, 2000000, 0, 'Solvable', '2011-12-12');

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `matricule_prof`, `nom_prof`, `nom_matiere`, `niveau_matiere`) VALUES
(24, 'KIA-20250116', 'dszfqkjewewwe wwewewwew', 'php_orienter_objet', 'B3'),
(26, 'KIA-20250117', 'BATCHATO FAGUY', 'JAVA_POO', 'B2'),
(27, 'KIA-20250117', 'BATCHATO FAGUY', 'SQL_1', 'B1'),
(28, 'KIA-20250116', 'dszfqkjewewwe wwewewwew', 'Linux', 'B3');

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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(46, '2025B2002', 1223450, '2025-01-03', '250103V0005'),
(47, '2025B1004', 1234567, '2025-01-15', '250115V0008'),
(48, '2025B1004', 1234567, '2025-01-15', '250115V0009'),
(49, '2025B2002', 2000000, '2025-01-17', '250117V0010');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
