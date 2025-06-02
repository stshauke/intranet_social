-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 26 mai 2025 à 12:22
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `intranet_social`
--

-- --------------------------------------------------------

--
-- Structure de la table `attachment`
--

DROP TABLE IF EXISTS `attachment`;
CREATE TABLE IF NOT EXISTS `attachment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_795FD9BB4B89032C` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `post_id` int NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_9474526C4B89032C` (`post_id`),
  KEY `IDX_9474526CF675F31B` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `author_id`, `post_id`, `content`, `created_at`) VALUES
(1, 23, 19, 'message de test', '2025-04-29 10:06:56'),
(2, 24, 21, 'Moi c\'est Joshua, j\'essaie tout simplement de tester les notifications', '2025-05-09 16:02:25'),
(3, 24, 21, 'test sur le post et le commentaire', '2025-05-10 19:35:06'),
(4, 17, 21, 'test pour l\'admin', '2025-05-10 19:39:15'),
(5, 24, 21, 'un autre test pour les préférences', '2025-05-10 19:47:34'),
(6, 23, 24, 'Gloire à Dieu', '2025-05-12 13:42:19'),
(7, 12, 87, 'commentaire pour Louane', '2025-05-24 11:27:11');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250405162347', '2025-04-08 11:59:59', 3675),
('DoctrineMigrations\\Version20250406131454', '2025-04-08 12:00:03', 376),
('DoctrineMigrations\\Version20250406142501', '2025-04-08 12:00:04', 327),
('DoctrineMigrations\\Version20250407212411', '2025-04-08 12:00:04', 120),
('DoctrineMigrations\\Version20250409145632', '2025-04-09 14:57:11', 1780),
('DoctrineMigrations\\Version20250409164432', '2025-04-09 17:47:34', 118),
('DoctrineMigrations\\Version20250409171504', '2025-04-09 18:15:40', 551),
('DoctrineMigrations\\Version20250430122119', '2025-04-30 12:32:59', 546),
('DoctrineMigrations\\Version20250430125037', '2025-04-30 13:30:32', 17),
('DoctrineMigrations\\Version20250503120000', '2025-05-03 16:04:40', 174),
('DoctrineMigrations\\Version20250503164056', '2025-05-03 16:41:18', 597),
('DoctrineMigrations\\Version20250508105151', '2025-05-08 10:52:14', 740),
('DoctrineMigrations\\Version20250508184501', '2025-05-08 18:45:35', 639),
('DoctrineMigrations\\Version20250509151841', '2025-05-09 15:19:18', 277),
('DoctrineMigrations\\Version20250510211836', '2025-05-10 21:18:58', 634),
('DoctrineMigrations\\Version20250512173339', '2025-05-12 17:34:09', 811),
('DoctrineMigrations\\Version20250512173920', '2025-05-12 17:39:39', 102),
('DoctrineMigrations\\Version20250513140751', '2025-05-13 14:08:12', 526),
('DoctrineMigrations\\Version20250513163219', '2025-05-14 09:08:37', 19),
('DoctrineMigrations\\Version20250514073034', '2025-05-14 09:13:22', 18);

-- --------------------------------------------------------

--
-- Structure de la table `favorite_group`
--

DROP TABLE IF EXISTS `favorite_group`;
CREATE TABLE IF NOT EXISTS `favorite_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `group_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DB486FE1A76ED395` (`user_id`),
  KEY `IDX_DB486FE1FE54D947` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `favorite_group`
--

INSERT INTO `favorite_group` (`id`, `user_id`, `group_id`) VALUES
(5, 23, 12),
(6, 23, 9);

-- --------------------------------------------------------

--
-- Structure de la table `group_invitation`
--

DROP TABLE IF EXISTS `group_invitation`;
CREATE TABLE IF NOT EXISTS `group_invitation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `invited_user_id` int NOT NULL,
  `invited_by_id` int NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_26D00010FE54D947` (`group_id`),
  KEY `IDX_26D00010C58DAD6E` (`invited_user_id`),
  KEY `IDX_26D00010A7B4A7E3` (`invited_by_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `group_invitation`
--

INSERT INTO `group_invitation` (`id`, `group_id`, `invited_user_id`, `invited_by_id`, `created_at`, `status`) VALUES
(3, 11, 23, 24, '2025-05-03 18:50:09', 'declined'),
(4, 5, 24, 23, '2025-05-03 19:40:35', 'accepted'),
(5, 4, 17, 23, '2025-05-06 11:47:26', 'accepted');

-- --------------------------------------------------------

--
-- Structure de la table `group_message`
--

DROP TABLE IF EXISTS `group_message`;
CREATE TABLE IF NOT EXISTS `group_message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `work_group_id` int NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_30BD6473F675F31B` (`author_id`),
  KEY `IDX_30BD64732BE1531B` (`work_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `group_message`
--

INSERT INTO `group_message` (`id`, `author_id`, `work_group_id`, `content`, `created_at`) VALUES
(1, 23, 5, 'c\'est mon exemple de forum pour tester que tout fonctionne correctement. ne me tenez pas rigueur', '2025-04-09 18:53:33'),
(2, 23, 5, 'c\'est mon exemple de forum pour tester que tout fonctionne correctement. de la patience s\'il vous plait', '2025-04-09 19:03:52'),
(3, 17, 6, 'J\'essaie de tester cette fonctionnalité', '2025-04-28 18:12:30'),
(4, 17, 6, 'J\'essaie de tester cette fonctionnalité', '2025-04-28 18:30:45'),
(5, 23, 5, 'Un autre test pour tester si cette partie fonctionne correctement', '2025-04-28 20:17:45');

-- --------------------------------------------------------

--
-- Structure de la table `like_comment`
--

DROP TABLE IF EXISTS `like_comment`;
CREATE TABLE IF NOT EXISTS `like_comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `comment_id` int NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_C7F9184FA76ED395` (`user_id`),
  KEY `IDX_C7F9184FF8697D13` (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `like_comment`
--

INSERT INTO `like_comment` (`id`, `user_id`, `comment_id`, `created_at`) VALUES
(26, 23, 1, '2025-04-29 10:44:31');

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `recipient_id` int NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `is_read` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B6BD307FF624B39D` (`sender_id`),
  KEY `IDX_B6BD307FE92F8F78` (`recipient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `sender_id`, `recipient_id`, `content`, `created_at`, `is_read`) VALUES
(1, 23, 17, 'Bonjour Monsieur l\'Admin, \r\nvoici le message de test, j\'espère que vous allez bien', '2025-04-08 15:40:23', 0),
(2, 17, 23, 'Salut Louane, \r\n\r\nMessage, bien reçu', '2025-04-28 18:07:04', 0),
(4, 23, 24, 'Message pour le prof Riadh', '2025-05-09 20:06:48', 0),
(5, 24, 23, 'Message à Louane Emera pour tester la nofication', '2025-05-10 20:23:26', 0),
(7, 23, 24, 'Je suis entrain de tester la messagerie et la notification', '2025-05-10 20:36:59', 0),
(8, 24, 23, 'encore moi Louane', '2025-05-10 20:42:38', 0),
(9, 23, 24, 'Joshua', '2025-05-10 20:46:24', 0),
(10, 24, 23, 'Encore moi, Lou', '2025-05-10 20:54:37', 0),
(11, 23, 17, 'Admin, bonjour', '2025-05-10 20:56:20', 0),
(12, 23, 24, 'jjjhhgg', '2025-05-10 21:07:27', 0),
(13, 24, 23, 'salut', '2025-05-10 21:26:29', 0),
(14, 23, 24, 'salut', '2025-05-10 21:34:50', 0),
(15, 24, 23, 'Allo', '2025-05-10 21:49:26', 0),
(16, 23, 24, 'salut', '2025-05-10 21:50:34', 0),
(17, 24, 23, 'haaaiiiiiii', '2025-05-10 21:55:30', 0),
(18, 23, 24, 'salut beauté', '2025-05-11 12:47:06', 0),
(19, 24, 23, 'coucou', '2025-05-11 12:59:03', 0),
(20, 23, 24, 'Est-ce que tout va bien ?', '2025-05-11 13:12:57', 0),
(21, 23, 24, 'j\'espère maintenant que grâce à Dieu, tout y ira bien', '2025-05-11 13:24:22', 0),
(22, 24, 23, 'essaie', '2025-05-11 14:43:28', 0),
(23, 23, 24, 'rien ne marche toujours', '2025-05-11 15:14:55', 0),
(24, 24, 23, 'oh mon Dieu, aide moi s\'il te plait', '2025-05-11 15:31:41', 0),
(25, 23, 24, 'Mon Dieu localise moi s\'il te plait', '2025-05-11 16:38:54', 0),
(26, 24, 23, 'ffffffff', '2025-05-11 17:20:41', 0),
(27, 23, 24, 'Merci, Seigneur Jésus-Christ pour ta réponse', '2025-05-11 17:37:17', 0),
(28, 24, 23, 'Merci, Seigneur', '2025-05-11 17:46:36', 0),
(29, 23, 24, 'Est-ce que vous êtes là ?', '2025-05-11 18:25:50', 0),
(30, 23, 24, 'Coucou Joshua, salut', '2025-05-12 13:11:24', 0),
(31, 23, 24, 'voici encore içi', '2025-05-12 13:39:06', 0),
(32, 12, 23, 'Salut Loulou, \r\nc\'est Sara', '2025-05-24 11:27:57', 0);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `related_post_id` int DEFAULT NULL,
  `related_comment_id` int DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `related_message_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BF5476CAA76ED395` (`user_id`),
  KEY `IDX_BF5476CA7490C989` (`related_post_id`),
  KEY `IDX_BF5476CA72A475A3` (`related_comment_id`),
  KEY `IDX_BF5476CAD9B71B99` (`related_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `related_post_id`, `related_comment_id`, `type`, `message`, `is_read`, `created_at`, `related_message_id`) VALUES
(1, 17, NULL, NULL, 'group_message', 'Louane Emera a posté un nouveau message dans le groupe \"Les amis de Louane Emera\".', 1, '2025-04-09 19:03:52', NULL),
(2, 23, NULL, NULL, 'group_message', 'Administrateur a posté un nouveau message dans le groupe \"Groupe Symfony\".', 1, '2025-04-28 18:30:45', NULL),
(3, 17, NULL, NULL, 'group_message', 'Louane Emera a posté un nouveau message dans le groupe \"Les amis de Louane Emera\".', 1, '2025-04-28 20:17:45', NULL),
(4, 15, 19, 1, 'new_comment', 'Un utilisateur a commenté votre publication.', 0, '2025-04-29 10:06:56', NULL),
(5, 23, 21, NULL, 'new_like', 'Joshua NTAMBWE a aimé votre publication : \"Publication test\"', 0, '2025-05-10 19:34:40', NULL),
(6, 23, 21, 3, 'new_comment', 'Joshua NTAMBWE a commenté votre publication : \"Publication test\"', 0, '2025-05-10 19:35:06', NULL),
(7, 23, 21, NULL, 'new_like', 'Administrateur a aimé votre publication : \"Publication test\"', 0, '2025-05-10 19:39:00', NULL),
(8, 23, 21, 4, 'new_comment', 'Administrateur a commenté votre publication : \"Publication test\"', 0, '2025-05-10 19:39:15', NULL),
(9, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-10 20:34:30', NULL),
(10, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-10 20:36:59', NULL),
(11, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-10 20:42:38', NULL),
(12, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-10 20:46:24', NULL),
(13, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-10 20:54:38', NULL),
(14, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-10 20:56:20', NULL),
(15, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-10 21:07:27', NULL),
(16, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-10 21:26:29', NULL),
(17, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-10 21:34:50', NULL),
(18, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-10 21:49:26', 15),
(19, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-10 21:50:34', 16),
(20, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-10 21:55:30', 17),
(21, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-11 12:47:07', 18),
(22, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-11 12:59:03', 19),
(23, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-11 13:12:57', 20),
(24, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-11 13:24:22', 21),
(25, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-11 14:43:28', 22),
(26, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-11 15:14:55', 23),
(27, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-11 15:31:41', 24),
(28, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-11 16:38:54', 25),
(29, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-11 17:20:41', 26),
(30, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-11 17:37:17', 27),
(31, 12, NULL, NULL, 'message', 'Joshua NTAMBWE vous a envoyé un message.', 0, '2025-05-11 17:46:37', 28),
(32, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 1, '2025-05-11 18:25:50', 29),
(33, 12, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-12 13:11:24', 30),
(34, 24, NULL, NULL, 'message', 'Louane Emera vous a envoyé un message.', 0, '2025-05-12 13:39:06', 31),
(35, 24, 24, NULL, 'new_like', 'Louane Emera a aimé votre publication : \"Merci mon Dieu\"', 0, '2025-05-12 13:41:18', NULL),
(36, 24, 24, NULL, 'new_like', 'Louane Emera a aimé votre publication : \"Merci mon Dieu\"', 0, '2025-05-12 13:42:04', NULL),
(37, 24, 24, 6, 'new_comment', 'Louane Emera a commenté votre publication : \"Merci mon Dieu\"', 0, '2025-05-12 13:42:19', NULL),
(38, 12, 80, NULL, 'mention', 'Joshua NTAMBWE vous a mentionné dans une publication : \"1er test avec @arobase\"', 0, '2025-05-14 16:40:57', NULL),
(39, 12, 81, NULL, 'mention', 'Louane Emera vous a mentionné dans une publication : \"1r test avec @arobase\"', 0, '2025-05-14 16:49:38', NULL),
(40, 12, 84, NULL, 'mention', 'Sara SBITT vous a mentionné dans une publication : \"2ème test de Sara\"', 0, '2025-05-24 11:03:07', NULL),
(41, 13, 84, NULL, 'mention', 'Sara SBITT vous a mentionné dans une publication : \"2ème test de Sara\"', 0, '2025-05-24 11:03:07', NULL),
(42, 23, 85, NULL, 'mention', 'Sara SBITT vous a mentionné dans une publication : \"3ème test de Sara\"', 0, '2025-05-24 11:07:42', NULL),
(43, 24, 85, NULL, 'mention', 'Sara SBITT vous a mentionné dans une publication : \"3ème test de Sara\"', 0, '2025-05-24 11:07:42', NULL),
(44, 23, 87, NULL, 'new_like', 'Sara SBITT a aimé votre publication : \"Notification de Louane Emera\"', 0, '2025-05-24 11:26:52', NULL),
(45, 23, NULL, NULL, 'message', 'Sara SBITT vous a envoyé un message.', 0, '2025-05-24 11:27:57', 32);

-- --------------------------------------------------------

--
-- Structure de la table `notification_preference`
--

DROP TABLE IF EXISTS `notification_preference`;
CREATE TABLE IF NOT EXISTS `notification_preference` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_type_unique` (`user_id`,`type`),
  KEY `IDX_A61B1571A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notification_preference`
--

INSERT INTO `notification_preference` (`id`, `user_id`, `type`, `enabled`) VALUES
(1, 23, 'new_post', 0),
(2, 23, 'new_comment', 0),
(3, 23, 'new_like', 1),
(4, 23, 'mention', 1),
(5, 24, 'new_post', 1),
(6, 24, 'new_comment', 1),
(7, 24, 'new_like', 1),
(8, 24, 'mention', 1),
(9, 23, 'message', 1),
(10, 24, 'message', 1);

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `work_group_id` int DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_draft` tinyint(1) NOT NULL,
  `tags` json NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A8A6C8D2BE1531B` (`work_group_id`),
  KEY `IDX_5A8A6C8DF675F31B` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `author_id`, `title`, `content`, `created_at`, `work_group_id`, `type`, `is_draft`, `tags`) VALUES
(14, 15, 'Mollitia ut maiores voluptates ducimus in.', 'Veritatis accusantium nostrum quaerat veritatis repellat dignissimos. Doloremque corporis nesciunt ut ipsum. Rerum aspernatur quod quas commodi. Repellat exercitationem aspernatur ratione ut quos et amet. Et qui eum consequuntur quos.', '2025-04-08 14:35:54', 4, '', 0, 'null'),
(17, 13, 'Labore iusto nesciunt atque aut quis sunt.', 'Fuga eveniet quidem praesentium dolorem consequatur ipsa modi. Asperiores possimus omnis officiis. Laudantium et eius nam quia enim non adipisci nemo.', '2025-04-08 14:35:54', 4, '', 0, 'null'),
(19, 15, 'Sed blanditiis officiis quo eum.', 'Est sint reiciendis ut quia. Aut laboriosam cupiditate voluptas voluptatum. Doloribus culpa cum consequatur eaque accusantium aut. Repellat nisi itaque sed aliquid error provident occaecati.', '2025-04-08 14:35:54', 4, '', 0, 'null'),
(21, 23, 'Publication test', 'je suis entrain de tester la publication', '2025-05-08 18:29:09', NULL, '', 0, 'null'),
(24, 24, 'Merci mon Dieu', 'je remercie le Seigneur parce qu\'il m\'a permit de finaliser cette partie', '2025-05-12 13:40:57', NULL, '', 0, 'null'),
(25, 12, 'Publication test utilisateur 12', 'Contenu publication généré pour Sara SBITT', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(26, 12, 'Annonce test utilisateur 12', 'Contenu annonce généré pour Sara SBITT', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(27, 13, 'Publication test utilisateur 13', 'Contenu publication généré pour Suzanne Maurice', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(28, 13, 'Annonce test utilisateur 13', 'Contenu annonce généré pour Suzanne Maurice', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(29, 14, 'Publication test utilisateur 14', 'Contenu publication généré pour Martin Vincent', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(30, 14, 'Annonce test utilisateur 14', 'Contenu annonce généré pour Martin Vincent', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(31, 15, 'Publication test utilisateur 15', 'Contenu publication généré pour Olivier DUBOIS', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(32, 15, 'Annonce test utilisateur 15', 'Contenu annonce généré pour Olivier DUBOIS', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(33, 16, 'Publication test utilisateur 16', 'Contenu publication généré pour Sebastien CAUET', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(34, 16, 'Annonce test utilisateur 16', 'Contenu annonce généré pour Sebastien CAUET', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(35, 17, 'Publication test utilisateur 17', 'Contenu publication généré pour Administrateur', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(36, 17, 'Annonce test utilisateur 17', 'Contenu annonce généré pour Administrateur', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(37, 18, 'Publication test utilisateur 18', 'Contenu publication généré pour Snekha HARIKRISHNAN', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(38, 18, 'Annonce test utilisateur 18', 'Contenu annonce généré pour Snekha HARIKRISHNAN', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(39, 19, 'Publication test utilisateur 19', 'Contenu publication généré pour Emmanuel KWEDI', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(40, 19, 'Annonce test utilisateur 19', 'Contenu annonce généré pour Emmanuel KWEDI', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(41, 20, 'Publication test utilisateur 20', 'Contenu publication généré pour Anne Fleur', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(42, 20, 'Annonce test utilisateur 20', 'Contenu annonce généré pour Anne Fleur', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(43, 21, 'Publication test utilisateur 21', 'Contenu publication généré pour Lee-Seing-Darino HERINJANAHARY', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(44, 21, 'Annonce test utilisateur 21', 'Contenu annonce généré pour Lee-Seing-Darino HERINJANAHARY', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(45, 22, 'Publication test utilisateur 22', 'Contenu publication généré pour Kirusaan SIVANAND', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(46, 22, 'Annonce test utilisateur 22', 'Contenu annonce généré pour Kirusaan SIVANAND', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(47, 23, 'Publication test utilisateur 23', 'Contenu publication généré pour Louane Emera', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(48, 23, 'Annonce test utilisateur 23', 'Contenu annonce généré pour Louane Emera', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(49, 24, 'Publication test utilisateur 24', 'Contenu publication généré pour Joshua NTAMBWE', '2025-05-12 18:07:42', NULL, 'publication', 0, 'null'),
(50, 24, 'Annonce test utilisateur 24', 'Contenu annonce généré pour Joshua NTAMBWE', '2025-05-12 18:07:42', NULL, 'annonce', 0, 'null'),
(51, 12, 'Publication test utilisateur #12', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(52, 12, 'Annonce test utilisateur #12', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(53, 13, 'Publication test utilisateur #13', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(54, 13, 'Annonce test utilisateur #13', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(55, 14, 'Publication test utilisateur #14', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(56, 14, 'Annonce test utilisateur #14', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(57, 15, 'Publication test utilisateur #15', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(58, 15, 'Annonce test utilisateur #15', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(59, 16, 'Publication test utilisateur #16', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(60, 16, 'Annonce test utilisateur #16', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(61, 17, 'Publication test utilisateur #17', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(62, 17, 'Annonce test utilisateur #17', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(63, 18, 'Publication test utilisateur #18', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(64, 18, 'Annonce test utilisateur #18', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(65, 19, 'Publication test utilisateur #19', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(66, 19, 'Annonce test utilisateur #19', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(67, 20, 'Publication test utilisateur #20', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(68, 20, 'Annonce test utilisateur #20', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(69, 21, 'Publication test utilisateur #21', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(70, 21, 'Annonce test utilisateur #21', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(71, 22, 'Publication test utilisateur #22', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(72, 22, 'Annonce test utilisateur #22', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(73, 23, 'Publication test utilisateur #23', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(74, 23, 'Annonce test utilisateur #23', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(75, 24, 'Publication test utilisateur #24', 'Contenu généré automatiquement pour le type : publication.', '2025-05-12 18:11:17', NULL, 'publication', 0, 'null'),
(76, 24, 'Annonce test utilisateur #24', 'Contenu généré automatiquement pour le type : annonce.', '2025-05-12 18:11:17', NULL, 'annonce', 0, 'null'),
(77, 23, 'test de #', 'Voici un test de #Symfony et #PHP8', '2025-05-13 14:56:24', NULL, 'publication', 0, '[\"Symfony\", \"PHP8\"]'),
(78, 23, '2ème test sur le #tag', 'Ce post teste les #Tags et #Symfony avec du contenu utile.', '2025-05-13 15:38:18', NULL, 'publication', 0, '[\"Tags\", \"Symfony\"]'),
(79, 24, '1er test avec @arobase', '1er test avec @arobase', '2025-05-14 07:24:41', NULL, 'publication', 0, '[]'),
(80, 24, '1er test avec @arobase', '@user_12', '2025-05-14 16:40:57', NULL, 'publication', 0, '[]'),
(81, 23, '1r test avec @arobase', '@@user_12', '2025-05-14 16:49:38', NULL, 'publication', 0, '[]'),
(82, 23, 'test de brouillon', 'ceci est un test de brouillon', '2025-05-15 10:42:16', NULL, 'publication', 1, '[]'),
(83, 12, 'Test de Sara', '#JS et #jQuery', '2025-05-24 10:59:45', NULL, 'publication', 0, '[\"JS\", \"jQuery\"]'),
(84, 12, '2ème test de Sara', '@user_12 \r\n@@user_13', '2025-05-24 11:03:07', NULL, 'publication', 0, '[]'),
(85, 12, '3ème test de Sara', '@user_23\r\n@user_24', '2025-05-24 11:07:42', NULL, 'publication', 0, '[]'),
(87, 23, 'Notification de Louane Emera', 'Ceci est une notification de Louane Emera', '2025-05-24 11:25:54', NULL, 'annonce', 0, '[]'),
(88, 23, 'Filtrage du fil d’actualité', '« publications », « annonces »', '2025-05-24 11:32:32', NULL, 'annonce', 0, '[]');

-- --------------------------------------------------------

--
-- Structure de la table `post_like`
--

DROP TABLE IF EXISTS `post_like`;
CREATE TABLE IF NOT EXISTS `post_like` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_653627B8A76ED395` (`user_id`),
  KEY `IDX_653627B84B89032C` (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `post_like`
--

INSERT INTO `post_like` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(49, 17, 17, '2025-05-08 11:51:25'),
(51, 17, 14, '2025-05-08 16:24:06'),
(58, 17, 21, '2025-05-10 19:39:00'),
(59, 24, 21, '2025-05-10 19:47:11'),
(61, 23, 24, '2025-05-12 13:42:04'),
(62, 12, 87, '2025-05-24 11:26:52');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `full_name`, `profile_image`, `bio`, `created_at`, `updated_at`, `username`) VALUES
(12, 'sara.sbitt@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$riqcm5Xf.Z0jjq9HFAlJeuOmQx1LbOAN28OnJnB6SIZlHlv18rI.K', 'Sara SBITT', 'Sara-SBITT-67f53608e9d62.jpg', 'Iure laudantium dignissimos officiis commodi ea tenetur magni.', '2025-04-08 14:35:49', '2025-04-08 14:43:20', 'user_12'),
(13, 'suzanne.maurice@yahoo.com', '[\"ROLE_USER\"]', '$2y$13$8tCIBeMe5w6pPt5whFRJ0OoQtp0biNj.W.Uo981JrRm6w4ov5TrWC', 'Suzanne Maurice', 'GARCON-FRANCAIS-67f5366b0f403.jpg', 'Facere impedit sint ullam aut placeat.', '2025-04-08 14:35:50', '2025-04-08 14:44:59', 'user_13'),
(14, 'martin.vincent@hebert.fr', '[\"ROLE_USER\"]', '$2y$13$n4t.PJX9kbjkZ4882pMllOXY3DwnW.aJ3iPGv3PS7t/GVQmv4c2yS', 'Martin Vincent', 'Martin-Vincent-67f536c416ce2.jpg', 'Nisi nostrum cupiditate repudiandae distinctio aut.', '2025-04-08 14:35:52', '2025-04-08 14:46:28', 'user_14'),
(15, 'olivier.dubois@free.fr', '[\"ROLE_USER\"]', '$2y$13$0odOn2c/p2Xs8i7sL1u2e.9ukqsx1uqWQGUprRoI/HWTBKiOitzS6', 'Olivier DUBOIS', 'Olivier-Dubois-67f5375639e8f.jpg', 'Temporibus quia pariatur quo.', '2025-04-08 14:35:53', '2025-04-08 14:48:54', 'user_15'),
(16, 'sebastien.cauet@gmail.com', '[\"ROLE_USER\"]', '$2y$13$Sn46l7NKzyDACQK.P0HS6ujmgpTzoaYyZ/6.K4EtiImd3t6Qnp/pO', 'Sebastien CAUET', 'Sebastien-Cauet-67f537bf4a5e4.jpg', 'Repudiandae nihil est veniam ea et ut.', '2025-04-08 14:35:54', '2025-04-08 14:50:39', 'user_16'),
(17, 'admin@intranet.com', '[\"ROLE_ADMIN\"]', '$2y$13$riqcm5Xf.Z0jjq9HFAlJeuOmQx1LbOAN28OnJnB6SIZlHlv18rI.K', 'Administrateur', 'Salomon-cropped-2-67f5356254a80.jpg', 'Passionné par l’innovation technologique et le développement d’applications performantes, je suis un développeur full-stack avec une expertise en développement web, mobile (Flutter), Java et Big Data. Grâce à une approche axée sur la performance et l’optimisation, je conçois des solutions digitales modernes et évolutives adaptées aux besoins des entreprises.', '2025-04-08 14:35:55', '2025-04-08 14:40:34', 'user_17'),
(18, 'snekha.h@example.com', '[\"ROLE_USER\"]', '$2y$13$BCGrURfEj0cU39dljdP1leS94QdMrsDFRoJKB0yLYuyvt8QMS4/yK', 'Snekha HARIKRISHNAN', 'default.png', 'Coucou, \r\nmoi c\'est Snekha, je suis product owner et j\'adore également la programmation', '2025-04-08 14:35:56', '2025-04-08 15:08:36', 'user_18'),
(19, 'emmanuel.kwedi@example.com', '[\"ROLE_USER\"]', '$2y$13$q4B/3jxr5BSB6Z4LdvDtTOXf.RLEWGISiBZdijOr5I6ThKBIqXFLe', 'Emmanuel KWEDI', 'testimonial-2-67f53b4328ddd.jpg', 'Moi c\'est Emmanuel, je n\'ai jamais eu la chance d\'être dans une relation amoureuse. prier pour moi, enfin que Dieu m\'aide', '2025-04-08 14:35:57', '2025-04-08 15:05:39', 'user_19'),
(20, 'anne.fleur@example.com', '[\"ROLE_USER\"]', '$2y$13$0YmRud/uBS9fIt3PYLr.eOOdFw1lUrh5jO0.ODU7v7d.tj7/cMJ.W', 'Anne Fleur', 'default.png', 'Je suis avocate encours de formation, j\'ai 22 ans et je suis célibataire', '2025-04-08 14:35:58', '2025-04-08 15:02:01', 'user_20'),
(21, 'lee-seing-darino@example.com', '[\"ROLE_USER\"]', '$2y$13$NSNvKoRf9arnPCMYPVt1UuN88IscFZ5EYFvZOFz9prG8y/11slLhW', 'Lee-Seing-Darino HERINJANAHARY', 'Jean-Dupont-67f53d1533ce2.jpg', 'J\'adore compter des billets', '2025-04-08 14:35:59', '2025-04-08 15:13:25', 'user_21'),
(22, 'kirusaan.s@example.com', '[\"ROLE_USER\"]', '$2y$13$5YD3q4JubhPb4owi2JBkjuc77naa3xTN62AO5XIdqEDJWPc1Ehbji', 'Kirusaan SIVANAND', 'testimonial-2-67f53e84e9b63.jpg', 'Moi, c\'est Kirusaan\r\nje fais la formation de Master en développement web et big data', '2025-04-08 14:36:00', '2025-04-08 15:19:32', 'user_22'),
(23, 'louaneemera@yahoo.com', '[\"ROLE_USER\"]', '$2y$13$nHAQkFd5tJFrdqWeXLZYe.9bIhKL8e1TtIDD.gfVbO0slCyrXfFi6', 'Louane Emera', 'Louane-Emera-67f540be678d4.jpg', 'Coucou,\r\nmoi c\'est Louane, j\'ai 28 ans et j\'habite dans la region lilloise. Je suis célibataire et mère de deux enfants', '2025-04-08 15:29:02', '2025-04-08 15:29:02', 'user_23'),
(24, 'joshua.ntambwe@yahoo.com', '[\"ROLE_USER\"]', '$2y$13$aj/I9KN4ONEYFUrvVslO0.Dzw0wnjS7xyIWlF3XAwOh.KA5A3Vl9.', 'Joshua NTAMBWE', 'joshua-ntambwe-68165278255f1.jpg', NULL, '2025-05-03 17:29:28', '2025-05-03 17:29:28', 'user_24');

-- --------------------------------------------------------

--
-- Structure de la table `user_work_group`
--

DROP TABLE IF EXISTS `user_work_group`;
CREATE TABLE IF NOT EXISTS `user_work_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `work_group_id` int NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E25CF906A76ED395` (`user_id`),
  KEY `IDX_E25CF9062BE1531B` (`work_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_work_group`
--

INSERT INTO `user_work_group` (`id`, `user_id`, `work_group_id`, `is_admin`) VALUES
(16, 12, 4, 1),
(17, 13, 4, 1),
(18, 14, 4, 1),
(19, 15, 4, 1),
(20, 16, 4, 1),
(21, 20, 9, 0),
(22, 23, 9, 0),
(23, 24, 11, 1),
(24, 23, 11, 0),
(25, 24, 5, 0),
(26, 17, 4, 0),
(28, 23, 12, 1),
(29, 24, 12, 0),
(30, 17, 13, 1),
(31, 17, 13, 0),
(32, 24, 13, 0),
(33, 24, 14, 1),
(34, 24, 14, 0),
(35, 17, 14, 0),
(36, 12, 14, 0);

-- --------------------------------------------------------

--
-- Structure de la table `work_group`
--

DROP TABLE IF EXISTS `work_group`;
CREATE TABLE IF NOT EXISTS `work_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator_id` int NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_453B3FEA61220EA6` (`creator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `work_group`
--

INSERT INTO `work_group` (`id`, `name`, `description`, `creator_id`, `type`, `created_at`, `updated_at`) VALUES
(4, 'Groupe 2', 'Dicta saepe iure laborum dolores. C\'est le groupe 2', 17, 'public', NULL, NULL),
(5, 'Les amis de Louane Emera', 'c\'est un groupe de test', 23, '', NULL, NULL),
(6, 'Groupe Symfony', 'C\'est un groupe qui permet à toute personne qui est dans le projet Symfony', 17, '', NULL, NULL),
(9, 'Groupe de teste pour invitation', NULL, 23, 'public', '2025-05-03 16:45:23', '2025-05-03 16:45:24'),
(11, 'Les amis de Joshua', NULL, 24, 'public', '2025-05-03 17:37:36', '2025-05-03 17:37:36'),
(12, 'Scrappynetwork', NULL, 23, 'private', '2025-05-07 16:52:54', '2025-05-07 17:43:33'),
(13, 'Premier Groupe Secret', 'C\'est un groupe secret car uniquement les membres pourront le voir', 17, 'secret', '2025-05-09 12:49:52', '2025-05-09 12:49:52'),
(14, 'Groupe de Joshua NTAMBWE MULUMBA MULUMBA', 'c\'est un groupe de test', 24, 'secret', '2025-05-24 11:18:00', '2025-05-24 11:18:00');

-- --------------------------------------------------------

--
-- Structure de la table `work_group_user`
--

DROP TABLE IF EXISTS `work_group_user`;
CREATE TABLE IF NOT EXISTS `work_group_user` (
  `work_group_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`work_group_id`,`user_id`),
  KEY `IDX_BFAA20EA2BE1531B` (`work_group_id`),
  KEY `IDX_BFAA20EAA76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `work_group_user`
--

INSERT INTO `work_group_user` (`work_group_id`, `user_id`) VALUES
(9, 23),
(11, 24),
(12, 24),
(13, 17),
(14, 24);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `attachment`
--
ALTER TABLE `attachment`
  ADD CONSTRAINT `FK_795FD9BB4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`);

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_9474526CF675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `favorite_group`
--
ALTER TABLE `favorite_group`
  ADD CONSTRAINT `FK_DB486FE1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_DB486FE1FE54D947` FOREIGN KEY (`group_id`) REFERENCES `work_group` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `group_invitation`
--
ALTER TABLE `group_invitation`
  ADD CONSTRAINT `FK_26D00010A7B4A7E3` FOREIGN KEY (`invited_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_26D00010C58DAD6E` FOREIGN KEY (`invited_user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_26D00010FE54D947` FOREIGN KEY (`group_id`) REFERENCES `work_group` (`id`);

--
-- Contraintes pour la table `group_message`
--
ALTER TABLE `group_message`
  ADD CONSTRAINT `FK_30BD64732BE1531B` FOREIGN KEY (`work_group_id`) REFERENCES `work_group` (`id`),
  ADD CONSTRAINT `FK_30BD6473F675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `like_comment`
--
ALTER TABLE `like_comment`
  ADD CONSTRAINT `FK_C7F9184FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_C7F9184FF8697D13` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_B6BD307FE92F8F78` FOREIGN KEY (`recipient_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_B6BD307FF624B39D` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_BF5476CA72A475A3` FOREIGN KEY (`related_comment_id`) REFERENCES `comment` (`id`),
  ADD CONSTRAINT `FK_BF5476CA7490C989` FOREIGN KEY (`related_post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_BF5476CAD9B71B99` FOREIGN KEY (`related_message_id`) REFERENCES `message` (`id`);

--
-- Contraintes pour la table `notification_preference`
--
ALTER TABLE `notification_preference`
  ADD CONSTRAINT `FK_A61B1571A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8D2BE1531B` FOREIGN KEY (`work_group_id`) REFERENCES `work_group` (`id`),
  ADD CONSTRAINT `FK_5A8A6C8DF675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `post_like`
--
ALTER TABLE `post_like`
  ADD CONSTRAINT `FK_653627B84B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_653627B8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user_work_group`
--
ALTER TABLE `user_work_group`
  ADD CONSTRAINT `FK_E25CF9062BE1531B` FOREIGN KEY (`work_group_id`) REFERENCES `work_group` (`id`),
  ADD CONSTRAINT `FK_E25CF906A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `work_group`
--
ALTER TABLE `work_group`
  ADD CONSTRAINT `FK_453B3FEA61220EA6` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `work_group_user`
--
ALTER TABLE `work_group_user`
  ADD CONSTRAINT `FK_BFAA20EA2BE1531B` FOREIGN KEY (`work_group_id`) REFERENCES `work_group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_BFAA20EAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
