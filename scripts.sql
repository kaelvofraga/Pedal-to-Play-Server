-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Tempo de geração: 20/09/2015 às 19:32
-- Versão do servidor: 5.6.21
-- Versão do PHP: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de dados: `pedal2play`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `activitylog`
--
CREATE TABLE IF NOT EXISTS `activitylog` (
  `id_activitylog` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `description` varchar(101) NOT NULL,
  `timer` int(15) NOT NULL,
  `calories` DOUBLE NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avatar`
--
CREATE TABLE IF NOT EXISTS `avatar` (
  `id_avatar` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_user` int(11) NOT NULL,
  `gender` char(1) NOT NULL,
  `skin_color` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avatar_image`
--
CREATE TABLE IF NOT EXISTS `avatar_image` (
  `id_image` int(11) NOT NULL,
  `id_avatar` int(11) NOT NULL,
  `color` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `image`
--
CREATE TABLE IF NOT EXISTS `image` (
  `id_image` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_typeimage` int(11) NOT NULL,
  `description` varchar(30) NOT NULL,
  `reference` INT(11) NOT NULL,
  `required_level` INT(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ranking`
--
CREATE TABLE IF NOT EXISTS `ranking` (
  `id_ranking` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `score` DOUBLE NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `type_image`
--
CREATE TABLE IF NOT EXISTS `type_image` (
 `id_typeimage` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `description` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `user`
--
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `token` varchar(100) NOT NULL,
  `subscription_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `weight` DOUBLE DEFAULT NULL,
  `password` varchar(33) NOT NULL,
  `address` varchar(150) DEFAULT NULL,
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `position`
--
CREATE TABLE IF NOT EXISTS `position` (
	`id_position` INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`id_activitylog` INT (11) NOT NULL,
	`date_time` TIMESTAMP NOT NULL,	
	`latitude` DOUBLE NOT NULL,
	`longitude` DOUBLE NOT NULL,
	`speed` DOUBLE NOT NULL
)ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Restrições para tabelas `activitylog`
--
ALTER TABLE `position` ADD FOREIGN KEY ( id_activitylog ) REFERENCES `activitylog` ( id_activitylog )

--
-- Restrições para tabelas `activitylog`
--
ALTER TABLE `activitylog`
ADD CONSTRAINT `activitylog_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Restrições para tabelas `avatar`
--
ALTER TABLE `avatar`
ADD CONSTRAINT `avatar_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE  `avatar` ADD UNIQUE (`id_user`);

--
-- Restrições para tabelas `avatar_image`
--
ALTER TABLE `avatar_image`
ADD CONSTRAINT `avatar_image_ibfk_1` FOREIGN KEY (`id_image`) REFERENCES `image` (`id_image`),
ADD CONSTRAINT `avatar_image_ibfk_2` FOREIGN KEY (`id_avatar`) REFERENCES `avatar` (`id_avatar`);

--
-- Restrições para tabelas `image`
--
ALTER TABLE `image`
ADD CONSTRAINT `image_ibfk_1` FOREIGN KEY (`id_typeimage`) REFERENCES `type_image` (`id_typeimage`);

ALTER TABLE  `image` ADD UNIQUE (`id_typeimage` ,`reference`);

--
-- Restrições para tabelas `ranking`
--
ALTER TABLE `ranking`
ADD CONSTRAINT `ranking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

-- --------------------------------------------------------

--
-- Inserções pré-estabelecidas em `type_image`
--
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (1, 'faces'  );
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (2, 'hairs'  );
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (3, 'helmets');
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (4, 'shorts' );
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (5, 'jerseys');
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (6, 'gloves' );
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (7, 'shoes'  );
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (8, 'glasses');
INSERT INTO `type_image` (`id_typeimage`, `description`) VALUES (9, 'bikes'  );

-- --------------------------------------------------------

--
-- Inserções pré-estabelecidas em `image`
--

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (1, 1, 1, 'face1', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (2, 2, 1, 'face2', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (3, 3, 1, 'face3', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (4, 4, 1, 'face4', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (5, 5, 1, 'face5', 1);

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (6, 1, 2, 'none', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (7, 2, 2, 'hair1', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (8, 3, 2, 'hair2', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (9, 4, 2, 'hair3', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (10, 5, 2, 'hair4', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (11, 6, 2, 'hair5', 1);

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (12, 1, 3, 'helmet', 1);

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (13, 1, 4, 'base-shorts', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (14, 2, 4, 'gear-shorts', 5);

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (15, 1, 5, 'base-jersey', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (16, 2, 5, 'gear-jersey', 5);

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (17, 1, 6, 'none', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (18, 2, 6, 'gloves', 2);

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (19, 1, 7, 'base-shoes', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (20, 2, 7, 'gear-shoes', 3);

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (21, 1, 8, 'none', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (22, 2, 8, 'glasses', 4);

INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (23, 1, 9, 'cruiser', 1);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (24, 2, 9, 'bmx', 2);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (25, 3, 9, 'mountain', 3);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (26, 4, 9, 'road', 4);
INSERT INTO `image`(`id_image`, `reference`, `id_typeimage`, `description`, `required_level`) VALUES (27, 5, 9, 'triathlon', 5);

-- --------------------------------------------------------

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
