-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 23/01/2022 às 23:05
-- Versão do servidor: 5.6.41-84.1
-- Versão do PHP: 7.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `kellye90_financas_rodolfo`
--
CREATE DATABASE IF NOT EXISTS `kellye90_financas_rodolfo` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `kellye90_financas_rodolfo`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caixa`
--

CREATE TABLE `caixa` (
  `id` int(10) UNSIGNED NOT NULL,
  `descricao` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `obs` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `ativo` char(1) COLLATE latin1_general_ci DEFAULT NULL,
  `saldo` float NOT NULL,
  `data` date DEFAULT NULL,
  `codigo_saida_cabecalho` int(11) DEFAULT NULL,
  `codigo_entrada` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contas_receber`
--

CREATE TABLE `contas_receber` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `descricao` varchar(50) NOT NULL,
  `obs` varchar(50) DEFAULT NULL,
  `valor` double NOT NULL,
  `ativo` char(1) NOT NULL DEFAULT 'S',
  `lucro_real` char(1) NOT NULL DEFAULT 'S',
  `fixo` char(1) NOT NULL DEFAULT 'N',
  `data_compensacao` date NOT NULL,
  `creditado` char(1) DEFAULT 'N',
  `codigo_entrada` int(11) DEFAULT NULL COMMENT 'codigo da tabela entrada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='créditos para recebimento';

-- --------------------------------------------------------

--
-- Estrutura para tabela `encerramento`
--

CREATE TABLE `encerramento` (
  `id` int(11) NOT NULL,
  `mes` int(11) NOT NULL DEFAULT '0',
  `ano` int(11) NOT NULL DEFAULT '0',
  `vr_final` double DEFAULT '0',
  `vr_inicial` double DEFAULT '0',
  `vr_maior` double DEFAULT '0',
  `vr_menor` double DEFAULT '0',
  `data_encerramento` date DEFAULT NULL,
  `encerrado` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='tabela para encerrar determinadas competências';

-- --------------------------------------------------------

--
-- Estrutura para tabela `entradas`
--

CREATE TABLE `entradas` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `descricao` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `obs` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `valor` float NOT NULL DEFAULT '0',
  `ativo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `fixo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N',
  `data` date NOT NULL,
  `lucro_real` char(1) CHARACTER SET utf8 NOT NULL DEFAULT 'N' COMMENT 'saber quando essa entrada é um lucro da conta(salario, serviços feitos, ganhos etc). E nao um pagamento que saiu da conta(emprestimo etc)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estabelecimentos`
--

CREATE TABLE `estabelecimentos` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `nome` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `cnpj` varchar(14) COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_comercio` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `cidade` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `ativo` char(1) COLLATE latin1_general_ci DEFAULT 'S'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `formas_pagamento`
--

CREATE TABLE `formas_pagamento` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` int(10) UNSIGNED NOT NULL,
  `descricao` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ativo` char(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `dia_fechamento` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '''''dia vencimento, se for cartão de crédito''''',
  `dia_vencimento` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos_futuros`
--

CREATE TABLE `lancamentos_futuros` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `data_compra` date NOT NULL,
  `data_debito` date NOT NULL,
  `valor_total` float NOT NULL DEFAULT '0',
  `estabelecimento` int(11) NOT NULL,
  `forma_pagamento` int(10) UNSIGNED NOT NULL,
  `qtd_parcelas` int(11) NOT NULL,
  `ativo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'S',
  `debitado` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N',
  `obs` varchar(75) COLLATE latin1_general_ci DEFAULT NULL,
  `codigo_cabecalho` int(11) DEFAULT NULL,
  `codigo_debito` int(11) DEFAULT NULL,
  `numero_parcela` int(11) DEFAULT NULL,
  `juros` float DEFAULT '0',
  `total_geral` float NOT NULL DEFAULT '0',
  `desconto` float NOT NULL DEFAULT '0' COMMENT 'possiveis descontos'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos_futuros_itens`
--

CREATE TABLE `lancamentos_futuros_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` int(10) UNSIGNED NOT NULL,
  `codigo_cabecalho` int(11) NOT NULL,
  `produto` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `qtd_produto` float DEFAULT NULL,
  `valor_produto` float DEFAULT NULL,
  `ativo` char(1) COLLATE latin1_general_ci DEFAULT NULL,
  `unidade_medida` varchar(6) CHARACTER SET latin1 COLLATE latin1_danish_ci DEFAULT 'UND'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `saidas_itens`
--

CREATE TABLE `saidas_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` int(10) UNSIGNED NOT NULL,
  `codigo_cabecalho` int(11) NOT NULL,
  `produto` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `qtd_produto` float DEFAULT NULL,
  `valor_produto` float DEFAULT NULL,
  `ativo` char(1) COLLATE latin1_general_ci DEFAULT NULL,
  `unidade_medida` varchar(6) CHARACTER SET latin1 COLLATE latin1_danish_ci DEFAULT 'UND'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `saida_cabecalho`
--

CREATE TABLE `saida_cabecalho` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `data_compra` date NOT NULL,
  `data_debito` date NOT NULL,
  `valor_total` float NOT NULL DEFAULT '0',
  `estabelecimento` int(11) NOT NULL,
  `forma_pagamento` int(10) UNSIGNED NOT NULL,
  `qtd_parcelas` int(11) NOT NULL,
  `ativo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'S',
  `fixo` int(11) NOT NULL DEFAULT '0' COMMENT 'qtd de meses que o debito ira se repetir. max24',
  `obs` varchar(75) COLLATE latin1_general_ci DEFAULT NULL,
  `juros` float DEFAULT '0',
  `total_geral` float NOT NULL DEFAULT '0',
  `desconto` float UNSIGNED NOT NULL DEFAULT '0',
  `atipico` char(1) CHARACTER SET utf8 DEFAULT 'N' COMMENT 'Para gastos atipicos ou não'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `caixa`
--
ALTER TABLE `caixa`
  ADD UNIQUE KEY `id` (`id`);

--
-- Índices de tabela `contas_receber`
--
ALTER TABLE `contas_receber`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Índices de tabela `encerramento`
--
ALTER TABLE `encerramento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index2` (`mes`,`ano`);

--
-- Índices de tabela `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Índices de tabela `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Índices de tabela `formas_pagamento`
--
ALTER TABLE `formas_pagamento`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Índices de tabela `lancamentos_futuros`
--
ALTER TABLE `lancamentos_futuros`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `FK1_ESTABELECIMENTO` (`estabelecimento`),
  ADD KEY `FK2_FORMA_PAGAMENTO` (`forma_pagamento`),
  ADD KEY `codigo_cabecalho_idx` (`codigo_cabecalho`);

--
-- Índices de tabela `lancamentos_futuros_itens`
--
ALTER TABLE `lancamentos_futuros_itens`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `FK1_codigo_cabecalho` (`codigo_cabecalho`);

--
-- Índices de tabela `saidas_itens`
--
ALTER TABLE `saidas_itens`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `FK1_codigo_cabecalho` (`codigo_cabecalho`);

--
-- Índices de tabela `saida_cabecalho`
--
ALTER TABLE `saida_cabecalho`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `FK1_ESTABELECIMENTO` (`estabelecimento`),
  ADD KEY `FK2_FORMA_PAGAMENTO` (`forma_pagamento`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `caixa`
--
ALTER TABLE `caixa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contas_receber`
--
ALTER TABLE `contas_receber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `encerramento`
--
ALTER TABLE `encerramento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `entradas`
--
ALTER TABLE `entradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `formas_pagamento`
--
ALTER TABLE `formas_pagamento`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `lancamentos_futuros`
--
ALTER TABLE `lancamentos_futuros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `lancamentos_futuros_itens`
--
ALTER TABLE `lancamentos_futuros_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `saidas_itens`
--
ALTER TABLE `saidas_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `saida_cabecalho`
--
ALTER TABLE `saida_cabecalho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
