-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 08:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medecine`
--

-- --------------------------------------------------------

--
-- Table structure for table `alertes`
--

CREATE TABLE `alertes` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `niveau` enum('info','alerte','critique') DEFAULT 'info',
  `date_alerte` datetime DEFAULT current_timestamp(),
  `statut` enum('non lu','lu') DEFAULT 'non lu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `constantes`
--

CREATE TABLE `constantes` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_nom` varchar(100) NOT NULL,
  `patient_prenom` varchar(100) DEFAULT NULL,
  `personnel_id` int(11) DEFAULT NULL,
  `temperature` decimal(4,1) NOT NULL,
  `tension_arterielle` varchar(10) NOT NULL,
  `frequence_cardiaque` int(11) NOT NULL,
  `frequence_respiratoire` int(11) NOT NULL,
  `saturation` int(11) NOT NULL,
  `glycemie` decimal(3,2) NOT NULL,
  `date_prise` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `constantes`
--

INSERT INTO `constantes` (`id`, `patient_id`, `patient_nom`, `patient_prenom`, `personnel_id`, `temperature`, `tension_arterielle`, `frequence_cardiaque`, `frequence_respiratoire`, `saturation`, `glycemie`, `date_prise`) VALUES
(1, 1, 'goly jean francois eric tresor', NULL, 2, 37.0, '120/80', 82, 18, 97, 1.05, '2025-05-29 16:02:37'),
(2, 2, 'goly', 'jean francois', 2, 38.0, '130/80', 82, 18, 97, 1.05, '2025-05-29 16:04:50');

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `date_consultation` datetime NOT NULL,
  `motif` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `utilisateur` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dossiers_medicaux`
--

CREATE TABLE `dossiers_medicaux` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `date_ouverture` datetime DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `etab_enreg`
--

CREATE TABLE `etab_enreg` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `etab_enreg`
--

INSERT INTO `etab_enreg` (`id`, `nom`, `type`, `adresse`, `telephone`, `email`, `responsable`, `date_creation`) VALUES
(1, 'hopital general de Koumassi', 'Hôpital', 'Abidjan,koumassi', '22566652', 'hopital@mail.com', 'Dr Tra bi', '2025-05-29 00:47:06'),
(2, 'Hôpital Général de Koumassi', 'Public', 'Koumassi quartier Mairie', '27 21 00 00 01', NULL, 'Dr Kouadio Jean', NOW()),
(3, 'CSU COM Divo', 'Public', 'Koumassi Divo', '27 21 00 00 02', NULL, 'Dr Koné Awa', NOW()),
(4, 'Clinique RIMCA', 'Privé', 'Koumassi Remblais', '27 21 00 00 03', 'contact@rimca.ci', 'Dr N’Guessan Alain', NOW()),
(5, 'Centre Médical Social El-Kabod', 'Privé', 'Koumassi Remblais', '27 21 00 00 04', 'elkabod@cms.ci', 'Dr Ouattara Mariam', NOW()),
(6, 'Centre Médical La Sagesse', 'Privé', 'Koumassi Sicogi', '27 21 00 00 05', 'lasagesse@cms.ci', 'Dr Bamba Fatou', NOW());

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `numero_dossier` varchar(50) DEFAULT NULL,
  `etablissement_id` int(11) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date NOT NULL,
  `sexe` enum('Homme','Femme','Autre') NOT NULL,
  `poids` decimal(5,2) DEFAULT NULL,
  `taille` decimal(5,2) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `groupe_sanguin` varchar(5) DEFAULT NULL,
  `date_enregistrement` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `numero_dossier`, `etablissement_id`, `nom`, `prenom`, `date_naissance`, `sexe`, `poids`, `taille`, `adresse`, `telephone`, `email`, `groupe_sanguin`, `date_enregistrement`) VALUES
(1, NULL, NULL, 'goly jean francois eric tresor', NULL, '2002-07-22', 'Homme', 82.00, 138.00, 'Rue H11', '0769393192', 'jean@mail.com', 'O-', '2025-05-28 23:47:07'),
(2, NULL, NULL, 'goly', 'jean francois', '2025-05-25', 'Homme', 80.00, 183.00, 'Rue H11', '0545226898', 'goly@mail.com', 'O-', '2025-05-29 02:03:33'),
(3, 'D2025050001', 1, 'Goly', 'Aya Yasmine Angeline', '2007-01-12', 'Femme', 75.00, 169.00, 'Rue H11', '0585805329', 'yasmin@mail.com', 'O-', '2025-05-29 16:18:14'),
(4, 'D2025050002', 1, 'Goly', 'Christ ebenezer', '2012-08-04', 'Homme', 30.00, 159.00, 'Rue H11', '0545006565', 'christ@mail.com', 'O-', '2025-05-29 16:34:32');

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

CREATE TABLE personnel (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  fonction VARCHAR(100) NOT NULL,
  telephone VARCHAR(30),
  email VARCHAR(100),
  mdp_defaut VARCHAR(255),
  date_embauche DATE,
  etablissement_id INT NOT NULL, -- rendre NOT NULL pour forcer le lien obligatoire
  actif TEXT,
  FOREIGN KEY (etablissement_id) REFERENCES etab_enreg(id)
);
--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`id`, `nom`, `prenom`, `fonction`, `telephone`, `email`, `mdp_defaut`, `date_embauche`, `etablissement_id`, `actif`) VALUES
(1, 'kone', 'Ahmed', 'Secretaire', '01556412', 'Ahmed@mail.com', NULL, '2025-05-29', NULL, NULL),
(2, 'Cha BI', 'Daniel', 'Secretaire', '010233654', 'chabidaniel@gmail.com', '$2y$10$6uqPY9Y/b4EGzpSDWFerJu/AW6Y0jMLgZL6ExOBSOGOTc/ALW7.au', '2025-05-23', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rapports`
--

CREATE TABLE `rapports` (
  `id` int(11) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `date_rapport` datetime DEFAULT current_timestamp(),
  `contenu` text DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `date_rdv` datetime NOT NULL,
  `motif` varchar(255) DEFAULT NULL,
  `statut` enum('Planifié','Réalisé','Annulé') DEFAULT 'Planifié',
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','medecin','secretaire') DEFAULT 'medecin',
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `actif`, `date_creation`) VALUES
(1, 'kouadou yaya', 'kyaya@mail.com', '$2y$10$6uqPY9Y/b4EGzpSDWFerJu/AW6Y0jMLgZL6ExOBSOGOTc/ALW7.au', 'medecin', 1, '2025-05-29 00:15:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alertes`
--
ALTER TABLE `alertes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `constantes`
--
ALTER TABLE `constantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `personnel_id` (`personnel_id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_id` (`patient_id`);

--
-- Indexes for table `dossiers_medicaux`
--
ALTER TABLE `dossiers_medicaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dm_patient_id` (`patient_id`);

--
-- Indexes for table `etab_enreg`
--
ALTER TABLE `etab_enreg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_etablissement` (`etablissement_id`);

--
-- Indexes for table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etablissement_id` (`etablissement_id`);

--
-- Indexes for table `rapports`
--
ALTER TABLE `rapports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rdv_patient_id` (`patient_id`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alertes`
--
ALTER TABLE `alertes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `constantes`
--
ALTER TABLE `constantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dossiers_medicaux`
--
ALTER TABLE `dossiers_medicaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `etab_enreg`
--
ALTER TABLE `etab_enreg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rapports`
--
ALTER TABLE `rapports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `constantes`
--
ALTER TABLE `constantes`
  ADD CONSTRAINT `constantes_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `constantes_ibfk_2` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dossiers_medicaux`
--
ALTER TABLE `dossiers_medicaux`
  ADD CONSTRAINT `dossiers_medicaux_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_etablissement` FOREIGN KEY (`etablissement_id`) REFERENCES `etab_enreg` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `personnel`
--
ALTER TABLE `personnel`
  ADD CONSTRAINT `personnel_ibfk_1` FOREIGN KEY (`etablissement_id`) REFERENCES `etab_enreg` (`id`);

--
-- Constraints for table `rapports`
--
ALTER TABLE `rapports`
  ADD CONSTRAINT `rapports_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `rendez_vous_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;