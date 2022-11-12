-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 08-Nov-2022 às 22:58
-- Versão do servidor: 8.0.0-dmr-log
-- versão do PHP: 7.4.26

--CREATE SCHEMA `financas` DEFAULT CHARACTER SET utf8 ;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `financas`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo` varchar(45) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `sobreNome` varchar(45) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `idade` varchar(2) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `recebe_email` char(1) DEFAULT 'N',
  `desenvolvedor` char(1) DEFAULT 'N',
  `data_cadastro` date NOT NULL COMMENT 'data de cadastro no sistema',
  `ativo` char(1) NOT NULL DEFAULT 'S',
  `data_nascimento` date NOT NULL COMMENT 'data de nascimento do usuario',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_UNIQUE` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
