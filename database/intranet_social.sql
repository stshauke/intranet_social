-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 10 avr. 2025 à 09:57
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
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_9474526CA76ED395` (`user_id`),
  KEY `IDX_9474526C4B89032C` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('DoctrineMigrations\\Version20250409171504', '2025-04-09 18:15:40', 551);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `group_message`
--

INSERT INTO `group_message` (`id`, `author_id`, `work_group_id`, `content`, `created_at`) VALUES
(1, 23, 5, 'c\'est mon exemple de forum pour tester que tout fonctionne correctement. ne me tenez pas rigueur', '2025-04-09 18:53:33'),
(2, 23, 5, 'c\'est mon exemple de forum pour tester que tout fonctionne correctement. de la patience s\'il vous plait', '2025-04-09 19:03:52');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `sender_id`, `recipient_id`, `content`, `created_at`, `is_read`) VALUES
(1, 23, 17, 'Bonjour Monsieur l\'Admin, \r\nvoici le message de test, j\'espère que vous allez bien', '2025-04-08 15:40:23', 0);

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
  PRIMARY KEY (`id`),
  KEY `IDX_BF5476CAA76ED395` (`user_id`),
  KEY `IDX_BF5476CA7490C989` (`related_post_id`),
  KEY `IDX_BF5476CA72A475A3` (`related_comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `related_post_id`, `related_comment_id`, `type`, `message`, `is_read`, `created_at`) VALUES
(1, 17, NULL, NULL, 'group_message', 'Louane Emera a posté un nouveau message dans le groupe \"Les amis de Louane Emera\".', 0, '2025-04-09 19:03:52');

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL,
  `work_group_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A8A6C8DA76ED395` (`user_id`),
  KEY `IDX_5A8A6C8D2BE1531B` (`work_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `user_id`, `title`, `content`, `created_at`, `updated_at`, `work_group_id`) VALUES
(14, 15, 'Mollitia ut maiores voluptates ducimus in.', 'Veritatis accusantium nostrum quaerat veritatis repellat dignissimos. Doloremque corporis nesciunt ut ipsum. Rerum aspernatur quod quas commodi. Repellat exercitationem aspernatur ratione ut quos et amet. Et qui eum consequuntur quos.', '2025-04-08 14:35:54', '2025-04-08 14:35:54', 4),
(17, 13, 'Labore iusto nesciunt atque aut quis sunt.', 'Fuga eveniet quidem praesentium dolorem consequatur ipsa modi. Asperiores possimus omnis officiis. Laudantium et eius nam quia enim non adipisci nemo.', '2025-04-08 14:35:54', '2025-04-08 14:35:54', 4),
(19, 15, 'Sed blanditiis officiis quo eum.', 'Est sint reiciendis ut quia. Aut laboriosam cupiditate voluptas voluptatum. Doloribus culpa cum consequatur eaque accusantium aut. Repellat nisi itaque sed aliquid error provident occaecati.', '2025-04-08 14:35:54', '2025-04-08 14:35:54', 4);

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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `post_like`
--

INSERT INTO `post_like` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(14, 23, 14, '2025-04-08 15:34:08');

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `full_name`, `profile_image`, `bio`, `created_at`, `updated_at`) VALUES
(12, 'sara.sbitt@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$letahON5Vz/4i0FRvoxNIuU2ujyAGES6Q1KNL9UM5WqJ7xDbUS1PG', 'Sara SBITT', 'Sara-SBITT-67f53608e9d62.jpg', 'Iure laudantium dignissimos officiis commodi ea tenetur magni.', '2025-04-08 14:35:49', '2025-04-08 14:43:20'),
(13, 'suzanne.maurice@yahoo.com', '[\"ROLE_USER\"]', '$2y$13$8tCIBeMe5w6pPt5whFRJ0OoQtp0biNj.W.Uo981JrRm6w4ov5TrWC', 'Suzanne Maurice', 'GARCON-FRANCAIS-67f5366b0f403.jpg', 'Facere impedit sint ullam aut placeat.', '2025-04-08 14:35:50', '2025-04-08 14:44:59'),
(14, 'martin.vincent@hebert.fr', '[\"ROLE_USER\"]', '$2y$13$n4t.PJX9kbjkZ4882pMllOXY3DwnW.aJ3iPGv3PS7t/GVQmv4c2yS', 'Martin Vincent', 'Martin-Vincent-67f536c416ce2.jpg', 'Nisi nostrum cupiditate repudiandae distinctio aut.', '2025-04-08 14:35:52', '2025-04-08 14:46:28'),
(15, 'olivier.dubois@free.fr', '[\"ROLE_USER\"]', '$2y$13$0odOn2c/p2Xs8i7sL1u2e.9ukqsx1uqWQGUprRoI/HWTBKiOitzS6', 'Olivier DUBOIS', 'Olivier-Dubois-67f5375639e8f.jpg', 'Temporibus quia pariatur quo.', '2025-04-08 14:35:53', '2025-04-08 14:48:54'),
(16, 'sebastien.cauet@gmail.com', '[\"ROLE_USER\"]', '$2y$13$Sn46l7NKzyDACQK.P0HS6ujmgpTzoaYyZ/6.K4EtiImd3t6Qnp/pO', 'Sebastien CAUET', 'Sebastien-Cauet-67f537bf4a5e4.jpg', 'Repudiandae nihil est veniam ea et ut.', '2025-04-08 14:35:54', '2025-04-08 14:50:39'),
(17, 'admin@intranet.com', '[\"ROLE_ADMIN\"]', '$2y$13$riqcm5Xf.Z0jjq9HFAlJeuOmQx1LbOAN28OnJnB6SIZlHlv18rI.K', 'Administrateur', 'Salomon-cropped-2-67f5356254a80.jpg', 'Passionné par l’innovation technologique et le développement d’applications performantes, je suis un développeur full-stack avec une expertise en développement web, mobile (Flutter), Java et Big Data. Grâce à une approche axée sur la performance et l’optimisation, je conçois des solutions digitales modernes et évolutives adaptées aux besoins des entreprises.', '2025-04-08 14:35:55', '2025-04-08 14:40:34'),
(18, 'snekha.h@example.com', '[\"ROLE_USER\"]', '$2y$13$BCGrURfEj0cU39dljdP1leS94QdMrsDFRoJKB0yLYuyvt8QMS4/yK', 'Snekha HARIKRISHNAN', 'default.png', 'Coucou, \r\nmoi c\'est Snekha, je suis product owner et j\'adore également la programmation', '2025-04-08 14:35:56', '2025-04-08 15:08:36'),
(19, 'emmanuel.kwedi@example.com', '[\"ROLE_USER\"]', '$2y$13$q4B/3jxr5BSB6Z4LdvDtTOXf.RLEWGISiBZdijOr5I6ThKBIqXFLe', 'Emmanuel KWEDI', 'testimonial-2-67f53b4328ddd.jpg', 'Moi c\'est Emmanuel, je n\'ai jamais eu la chance d\'être dans une relation amoureuse. prier pour moi, enfin que Dieu m\'aide', '2025-04-08 14:35:57', '2025-04-08 15:05:39'),
(20, 'anne.fleur@example.com', '[\"ROLE_USER\"]', '$2y$13$0YmRud/uBS9fIt3PYLr.eOOdFw1lUrh5jO0.ODU7v7d.tj7/cMJ.W', 'Anne Fleur', 'default.png', 'Je suis avocate encours de formation, j\'ai 22 ans et je suis célibataire', '2025-04-08 14:35:58', '2025-04-08 15:02:01'),
(21, 'lee-seing-darino@example.com', '[\"ROLE_USER\"]', '$2y$13$NSNvKoRf9arnPCMYPVt1UuN88IscFZ5EYFvZOFz9prG8y/11slLhW', 'Lee-Seing-Darino HERINJANAHARY', 'Jean-Dupont-67f53d1533ce2.jpg', 'J\'adore compter des billets', '2025-04-08 14:35:59', '2025-04-08 15:13:25'),
(22, 'kirusaan.s@example.com', '[\"ROLE_USER\"]', '$2y$13$5YD3q4JubhPb4owi2JBkjuc77naa3xTN62AO5XIdqEDJWPc1Ehbji', 'Kirusaan SIVANAND', 'testimonial-2-67f53e84e9b63.jpg', 'Moi, c\'est Kirusaan\r\nje fais la formation de Master en développement web et big data', '2025-04-08 14:36:00', '2025-04-08 15:19:32'),
(23, 'louaneemera@yahoo.com', '[\"ROLE_USER\"]', '$2y$13$nHAQkFd5tJFrdqWeXLZYe.9bIhKL8e1TtIDD.gfVbO0slCyrXfFi6', 'Louane Emera', 'Louane-Emera-67f540be678d4.jpg', 'Coucou,\r\nmoi c\'est Louane, j\'ai 28 ans et j\'habite dans la region lilloise. Je suis célibataire et mère de deux enfants', '2025-04-08 15:29:02', '2025-04-08 15:29:02');

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
  `joined_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_E25CF906A76ED395` (`user_id`),
  KEY `IDX_E25CF9062BE1531B` (`work_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_work_group`
--

INSERT INTO `user_work_group` (`id`, `user_id`, `work_group_id`, `is_admin`, `joined_at`) VALUES
(16, 12, 4, 1, '2025-04-08 14:35:54'),
(17, 13, 4, 1, '2025-04-08 14:35:54'),
(18, 14, 4, 1, '2025-04-08 14:35:54'),
(19, 15, 4, 1, '2025-04-08 14:35:54'),
(20, 16, 4, 1, '2025-04-08 14:35:54');

-- --------------------------------------------------------

--
-- Structure de la table `work_group`
--

DROP TABLE IF EXISTS `work_group`;
CREATE TABLE IF NOT EXISTS `work_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_private` tinyint(1) NOT NULL,
  `creator_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_453B3FEA61220EA6` (`creator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `work_group`
--

INSERT INTO `work_group` (`id`, `name`, `description`, `is_private`, `creator_id`, `created_at`, `updated_at`) VALUES
(4, 'Groupe 2', 'Dicta saepe iure laborum dolores. C\'est le groupe 2', 0, 17, NULL, '2025-04-09 20:41:26'),
(5, 'Les amis de Louane Emera', 'c\'est un groupe de test', 0, 23, NULL, NULL),
(6, 'Groupe Symfony', 'C\'est un groupe qui permet toute personne qui est dans le projet Symfony', 0, 17, '2025-04-09 21:08:23', '2025-04-09 21:08:23');

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
(4, 23),
(5, 17),
(6, 17),
(6, 18),
(6, 19),
(6, 21),
(6, 22),
(6, 23);

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
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

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
  ADD CONSTRAINT `FK_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8D2BE1531B` FOREIGN KEY (`work_group_id`) REFERENCES `work_group` (`id`),
  ADD CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

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
