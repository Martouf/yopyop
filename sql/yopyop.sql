# Yopyop V 0h00
# http://martouf.ch
# Sequel Pro dump
# Version 2492
# http://code.google.com/p/sequel-pro
#
# Host: 127.0.0.1 (MySQL 5.1.45)
# Database: yopyop
# Generation Time: 2010-12-22 11:24:54 +0100
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table yop_calendrier
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_calendrier`;

CREATE TABLE `yop_calendrier` (
  `id_calendrier` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `couleur` char(255) DEFAULT NULL,
  `distant` tinyint(4) NOT NULL DEFAULT '0',
  `url` char(255) DEFAULT NULL,
  `tags` char(255) DEFAULT NULL,
  `date_last_importation` datetime DEFAULT NULL,
  `login` char(255) DEFAULT NULL,
  `paswd` char(255) DEFAULT NULL,
  `evaluation` tinyint(4) NOT NULL DEFAULT '0',
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_calendrier`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;



# Dump of table yop_commentaire
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_commentaire`;

CREATE TABLE `yop_commentaire` (
  `id_commentaire` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT '',
  `description` text,
  `id_auteur` int(32) unsigned NOT NULL DEFAULT '1',
  `mail` char(255) DEFAULT '',
  `url` char(255) DEFAULT '',
  `evaluation` tinyint(4) unsigned DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_commentaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_commentaire-element
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_commentaire-element`;

CREATE TABLE `yop_commentaire-element` (
  `id_commentaire` int(32) unsigned NOT NULL DEFAULT '0',
  `id_element` int(32) unsigned NOT NULL DEFAULT '0',
  `table_element` tinyint(8) unsigned NOT NULL DEFAULT '0',
  `evaluation` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  PRIMARY KEY (`id_commentaire`,`id_element`,`table_element`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_document
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_document`;

CREATE TABLE `yop_document` (
  `id_document` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) NOT NULL,
  `description` text,
  `evaluation` tinyint(8) unsigned DEFAULT '0',
  `access` tinyint(8) unsigned DEFAULT '0',
  `groupe_autorise` char(255) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `date_publication` datetime DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_document`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `yop_document`
-- 

INSERT INTO `yop_document` (`id_document`, `nom`, `description`, `evaluation`, `date_creation`, `date_modification`) VALUES 
(1, 'Menu', 'c\\''est la page qui sert de menu', NULL, '2008-12-08 11:31:13', '2008-12-21 22:49:39'),
(2, 'Page d\\''accueil', 'Ce document est intégré dans la page d\\''accueil afin de décrire l\\''identité du site web.', NULL, '2008-12-08 11:31:56', '2008-12-19 20:19:22');



# Dump of table yop_evenement
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_evenement`;

CREATE TABLE `yop_evenement` (
  `id_evenement` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `jour_entier` char(255) DEFAULT NULL,
  `lieu` char(255) DEFAULT NULL,
  `id_calendrier` int(32) DEFAULT NULL,
  `periodicite` char(50) DEFAULT NULL,
  `evaluation` tinyint(8) unsigned DEFAULT '0',
  `uid` char(255) DEFAULT NULL,
  `delai_inscription` datetime DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `info` char(255) DEFAULT NULL,
  `state` tinyint(8) unsigned DEFAULT '0',
  `auteur` char(255) DEFAULT NULL,
  `auteur_modif` char(255) DEFAULT NULL,
  `remarque` text,
  `date_creation` datetime DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL,
  PRIMARY KEY (`id_evenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_fichier
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_fichier`;

CREATE TABLE `yop_fichier` (
  `id_fichier` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `lien` char(255) DEFAULT NULL,
  `evaluation` tinyint(4) DEFAULT '0',
  `externe` tinyint(4) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_fichier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_groupe
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_groupe`;

CREATE TABLE `yop_groupe` (
  `id_groupe` int(32) NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `type` tinyint(8) DEFAULT NULL,
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL,
  PRIMARY KEY (`id_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_groupe-element
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_groupe-element`;

CREATE TABLE `yop_groupe-element` (
  `id_groupe` int(32) unsigned NOT NULL DEFAULT '0',
  `id_element` int(32) unsigned NOT NULL DEFAULT '0',
  `table_element` tinyint(8) unsigned NOT NULL,
  `type` int(8) NOT NULL DEFAULT '1',
  `evaluation` tinyint(8) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `date_creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id_groupe`,`id_element`,`table_element`)
) ENGINE=InnoDB CHARSET=utf8;



# Dump of table yop_historique
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_historique`;

CREATE TABLE `yop_historique` (
  `id_historique` int(32) NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `url` char(255) DEFAULT NULL,
  `ip` char(255) DEFAULT NULL,
  `user_agent` char(255) DEFAULT NULL,
  `evaluation` tinyint(4) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL,
  PRIMARY KEY (`id_historique`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_lieu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_lieu`;

CREATE TABLE `yop_lieu` (
  `id_lieu` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) NOT NULL DEFAULT 'lieu',
  `description` text,
  `categorie` char(255) DEFAULT NULL,
  `rue` char(255) DEFAULT NULL,
  `npa` char(255) DEFAULT NULL,
  `commune` char(255) DEFAULT NULL,
  `pays` char(255) DEFAULT NULL,
  `latitude` char(50) DEFAULT NULL,
  `longitude` char(50) DEFAULT NULL,
  `altitude` char(50) DEFAULT NULL,
  `evaluation` tinyint(4) NOT NULL DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_lieu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_meta`;

CREATE TABLE `yop_meta` (
  `id_meta` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `id_element` int(32) unsigned DEFAULT '0',
  `table_element` tinyint(8) unsigned DEFAULT NULL,
  `evaluation` tinyint(4) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_meta`)
) ENGINE=InnoDB CHARSET=utf8;



# Dump of table yop_objet
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_objet`;

CREATE TABLE `yop_objet` (
  `id_objet` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `url` char(255) DEFAULT NULL,
  `id_proprietaire` int(32) DEFAULT NULL,
  `id_image` int(32) DEFAULT NULL,
  `id_calendrier` int(32) DEFAULT NULL,
  `prix` int(32) DEFAULT NULL,
  `caution` int(32) DEFAULT NULL,
  `latitude` char(50) DEFAULT NULL,
  `longitude` char(50) DEFAULT NULL,
  `lieu` char(255) DEFAULT NULL,
  `etat` tinyint(4) DEFAULT '0',
  `duree_max` int(32) DEFAULT NULL,
  `duree_min` int(32) DEFAULT NULL,
  `evaluation` tinyint(4) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_objet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_personne
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_personne`;

CREATE TABLE `yop_personne` (
  `id_personne` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `prenom` char(255) DEFAULT NULL,
  `nom` char(255) DEFAULT NULL,
  `surnom` char(255) DEFAULT NULL,
  `description` text,
  `date_naissance` datetime DEFAULT NULL,
  `mot_de_passe` char(255) DEFAULT NULL,
  `photo` char(255) DEFAULT NULL,
  `rue` char(255) DEFAULT NULL,
  `npa` char(255) DEFAULT NULL,
  `lieu` char(255) DEFAULT NULL,
  `pays` char(255) DEFAULT NULL,
  `tel` char(255) DEFAULT NULL,
  `email` char(255) DEFAULT NULL,
  `rang` char(255) DEFAULT NULL,
  `url` char(255) DEFAULT NULL,
  `url_fb_avatar` char(255) DEFAULT NULL,
  `id_fb` int(32) DEFAULT NULL,
  `evaluation` tinyint(4) unsigned DEFAULT '0',
  `fortune` int(32) DEFAULT NULL,
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL,
  PRIMARY KEY (`id_personne`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `yop_personne` (`id_personne`, `prenom`, `nom`, `surnom`, `description`, `date_naissance`, `mot_de_passe`, `photo`, `rue`, `npa`, `lieu`, `pays`, `tel`, `email`, `rang`, `url`, `evaluation`, `date_creation`, `date_modification`) VALUES 
(1, 'John', 'Doe', 'inconnu', 'Inconnu', NULL, NULL, NULL, '', '', '', NULL, '', '', NULL, NULL, 0, '2008-11-06 16:59:26', '2008-11-11 12:07:36'),
(2, 'Toto', 'bob', 'Admin', 'oui', '1981-07-12 00:00:00', '23206deb7eba65b3fbc80a2ffbc53c28', NULL, 'rue', '2017', 'Boudry', NULL, '', 'toto@bob.ch', '1', NULL, 0, '2008-11-11 12:04:15', '2008-11-11 12:04:15');


# Dump of table yop_photo
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_photo`;

CREATE TABLE `yop_photo` (
  `id_photo` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `lien` char(255) DEFAULT NULL,
  `compteur` int(32) DEFAULT NULL,
  `orientation` char(50) DEFAULT NULL,
  `date_prise_de_vue` datetime DEFAULT NULL,
  `latitude` char(50) DEFAULT NULL,
  `longitude` char(50) DEFAULT NULL,
  `evaluation` tinyint(4) DEFAULT '0',
  `externe` tinyint(4) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_photo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_reservation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_reservation`;

CREATE TABLE `yop_reservation` (
  `id_reservation` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `id_locataire` int(32) DEFAULT NULL,
  `id_objet` int(32) DEFAULT NULL,
  `id_evenement` int(32) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '0',
  `etat` tinyint(4) DEFAULT '0',
  `evaluation` tinyint(4) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_reservation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_restriction
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_restriction`;

CREATE TABLE `yop_restriction` (
  `id_restriction` int(32) NOT NULL AUTO_INCREMENT,
  `id_groupe_utilisateur` int(32) NOT NULL,
  `id_groupe_element` int(32) NOT NULL,
  `type` int(32) NOT NULL,
  `nom` char(255) DEFAULT NULL,
  `evaluation` tinyint(4) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL,
  PRIMARY KEY (`id_restriction`,`id_groupe_utilisateur`,`id_groupe_element`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_statut
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_statut`;

CREATE TABLE `yop_statut` (
  `id_statut` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` text,
  `id_auteur` int(32) unsigned NOT NULL DEFAULT '1',
  `description` text,
  `guid` char(255) DEFAULT NULL,
  `evaluation` tinyint(4) DEFAULT '0',
  `modificateur` int(32) DEFAULT NULL,
  `date_publication` datetime DEFAULT NULL,
  `auteur_texte` char(255) DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_ticket
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_ticket`;

CREATE TABLE `yop_ticket` (
  `ticket_id` char(32) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remote_ip` varchar(15) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_transaction
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_transaction`;

CREATE TABLE `yop_transaction` (
  `id_transaction` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `id_source` int(32) DEFAULT NULL,
  `id_destinataire` int(32) DEFAULT NULL,
  `montant` int(32) DEFAULT NULL,
  `ip` char(255) DEFAULT NULL,
  `user_agent` char(255) DEFAULT NULL,
  `evaluation` tinyint(4) DEFAULT '0',
  `createur` int(32) DEFAULT NULL,
  `modificateur` int(32) DEFAULT NULL,
  `groupe_autorise_lecture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_ecriture` char(255) NOT NULL DEFAULT '0',
  `groupe_autorise_commentaire` char(255) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_transaction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table yop_version
# ------------------------------------------------------------

DROP TABLE IF EXISTS `yop_version`;

CREATE TABLE `yop_version` (
  `id_version` int(32) NOT NULL AUTO_INCREMENT,
  `id_document` int(32) NOT NULL DEFAULT '0',
  `nom` char(255) DEFAULT NULL,
  `description` text,
  `contenu` text,
  `auteur` char(255) DEFAULT NULL,
  `langue` char(50) NOT NULL DEFAULT 'fr',
  `evaluation` tinyint(4) DEFAULT '0',
  `date_creation` datetime DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL,
  PRIMARY KEY (`id_version`,`id_document`),
  KEY `date_modification` (`date_modification`),
  KEY `langue` (`langue`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- 
-- Contenu de la table `yop_version`
-- 

INSERT INTO `yop_version` (`id_version`, `id_document`, `nom`, `description`, `contenu`, `auteur`, `langue`, `evaluation`, `date_creation`, `date_modification`) VALUES 
(1, 2, 'Page d\\''accueil', 'base du document', '<h1>Yop!</h1>\n<p>\nBienvenue sur mon site web... \n</p>', '5', 'fr', 0, '2008-12-19 20:19:22', '2008-12-19 20:19:22'),
(2, 1, 'Menu', NULL, '<h1> Menu</h1>\n<ul>\n	<li><a href=\\"http://yopyop.ch/document/document.html?new\\" title=\\"Créer un nouveau document\\"><img src=\\"/utile/images/theme_yop/add_document.png\\" alt=\\"add document\\" /></a></li>\n	<li><a href=\\"http://yopyop.ch/document/document.html\\" title=\\"voir la liste de tous les documents\\">Tous les documents</a></li>\n	<li>rechercher</li>\n	<li><a href=\\"http://yopyop.ch\\">page d\\''accueil</a></li>\n</ul>', '5', 'fr', 0, '2008-12-21 22:49:39', '2008-12-21 22:49:39');




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
