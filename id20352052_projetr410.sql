-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 31 mars 2023 à 09:27
-- Version du serveur :  10.5.16-MariaDB
-- Version de PHP : 7.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `id20352052_projetr410`
--

-- --------------------------------------------------------

--
-- Structure de la table `auteurs`
--

CREATE TABLE `auteurs` (
  `id_auteur` int(11) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `mdp` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `auteurs`
--

INSERT INTO `auteurs` (`id_auteur`, `nom`, `mdp`) VALUES
(1, 'Auxence', '$2y$10$tNGlYTglPDnIOJ1PxBqtAecB/7sNBDd/yUQ19QAwOg2WOwr7N1hMK'),
(2, 'Flavien', '$2y$10$WdfppytL2/QPT/kKJSsxvuf/kCA4irSW3/NOTubeXupbXBQ77Gmca'),
(7, 'userTest', '$2y$10$NtKMwEBjnoVAdnS6O31OBOcyaWnFO2Hc90KstORBJHoMwTjYVER7.'),
(8, 'sampleUser1', ''),
(9, 'sampleUser2', '');

-- --------------------------------------------------------

--
-- Structure de la table `chat`
--

CREATE TABLE `chat` (
  `id_message` int(11) NOT NULL,
  `horaire` timestamp NOT NULL DEFAULT current_timestamp(),
  `contenu` text NOT NULL,
  `id_auteur` int(11) NOT NULL,
  `id_receveur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `chat`
--

INSERT INTO `chat` (`id_message`, `horaire`, `contenu`, `id_auteur`, `id_receveur`) VALUES
(71, '2023-03-06 16:44:31', 'slihgksjglkjkgs', 1, 2),
(72, '2023-03-06 16:45:19', 'yo', 1, 2),
(73, '2023-03-06 17:10:10', 'ddlisj,spom', 2, 2),
(74, '2023-03-09 07:29:19', 'sgsikjis', 1, 2),
(75, '2023-03-09 07:29:30', 'gsqgqg', 1, 2),
(76, '2023-03-09 07:40:15', 'shgiqkjgq', 1, 2),
(77, '2023-03-09 07:40:28', 'gzèyghznguk', 1, 2),
(78, '2023-03-09 09:41:36', 'slslhjksbms', 2, 1),
(79, '2023-03-14 07:57:58', 'coucou', 2, 1),
(80, '2023-03-14 07:58:06', 'Salut', 1, 2),
(81, '2023-03-14 09:52:30', 'hdoskhkosb', 1, 2),
(87, '2023-03-14 10:11:38', 'qggqgqg', 1, 2),
(88, '2023-03-30 12:49:42', 'Salut', 1, 7),
(89, '2023-03-30 12:49:42', '', 1, 7),
(90, '2023-03-30 12:49:49', '', 1, 7),
(91, '2023-03-30 12:51:14', 'thdwhwdh', 1, 7),
(92, '2023-03-30 12:55:41', 'ryjfkfkf', 1, 7),
(93, '2023-03-30 13:00:00', 'Mec', 1, 2),
(94, '2023-03-30 13:00:56', 'kjghn,slk;k', 7, 1),
(95, '2023-03-30 13:07:50', 'ehehsh', 7, 1),
(96, '2023-03-30 20:56:42', 't la ?', 7, 2),
(97, '2023-03-30 20:56:57', 'ouep', 2, 7),
(98, '2023-03-30 20:57:12', 'okay', 2, 7),
(99, '2023-03-31 06:08:12', 'test', 7, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `auteurs`
--
ALTER TABLE `auteurs`
  ADD PRIMARY KEY (`id_auteur`);

--
-- Index pour la table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `fk_chat_auteur` (`id_auteur`),
  ADD KEY `fk_chat_raceveur` (`id_receveur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `auteurs`
--
ALTER TABLE `auteurs`
  MODIFY `id_auteur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `chat`
--
ALTER TABLE `chat`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `fk_chat_auteur` FOREIGN KEY (`id_auteur`) REFERENCES `auteurs` (`id_auteur`),
  ADD CONSTRAINT `fk_chat_raceveur` FOREIGN KEY (`id_receveur`) REFERENCES `auteurs` (`id_auteur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
