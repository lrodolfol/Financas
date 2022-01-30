-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 23/01/2022 às 23:02
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
-- Banco de dados: `kellye90_financas`
--
CREATE DATABASE IF NOT EXISTS `financas` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `financas`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatosErro`
--

CREATE TABLE `relatosErro` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `usuario` int(11) DEFAULT NULL,
  `texto` varchar(255) NOT NULL,
  `titulo` varchar(25) NOT NULL,
  `data` date NOT NULL,
  `status` varchar(25) DEFAULT 'clinica',
  `resposta` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `relatosErro`
--

INSERT INTO `relatosErro` (`id`, `codigo`, `usuario`, `texto`, `titulo`, `data`, `status`, `resposta`) VALUES
(12, 1, 1, 'Ao importar a  conta por XML, a tabela forma de pagamento não é importada', 'Erro importa XML', '2020-07-30', 'concluido', NULL),
(13, 2, 1, 'Tratar aspas simples ao realizar consultas no BD ', 'Consultas no BD', '2020-07-30', 'concluido', NULL),
(14, 3, 1, 'Ocorre erro toda vez que for salvar debito no estabelecimento: acai da vila ', 'Erro ao salvar debito', '2020-07-31', 'concluido', NULL),
(15, 4, 1, 'Erro ao editar lanç futuros', 'Edita lanc futuros', '2020-07-01', 'concluido', NULL),
(16, 5, 1, 'Erro ao cadastrar novo relato de erro(esse foi via BD)', 'Cadastro relato erro', '2020-07-03', 'concluido', NULL),
(18, 6, 1, 'Não funciona tela para importar e exportar conta ', 'Importação e Exportação', '2020-08-11', 'concluido', NULL),
(19, 7, 1, 'Ao deletar um cabeçalho de debito, não esta deletando os itens ', 'Excluir debitos', '2020-08-11', 'concluido', NULL),
(20, 8, 3, 'Quando acesso a tela meusRelatosErros o rodapé fala que estou logado na base de teste. mesmo estando na base oficial ', 'Mensagem rodapé conexão', '2020-08-17', 'clinica', NULL),
(21, 9, 1, 'Ao clicar em editar crédito, o sistema da erro.  ', 'Editar crédito', '2020-09-01', 'concluido', NULL),
(22, 10, 1, 'Ao editar os debitos, as imagens não são carregadas corretamente ', 'Erro editar debito/credit', '2020-10-02', 'concluido', NULL),
(23, 11, 1, 'Há debitos que quando lançados os itens (mesmo que seja de mesmo valor) o sistema acusar o valor é maior. Exm: 3,82 valor 2,99 KG', 'Aumentar 1 centavo item d', '2020-10-08', 'concluido', NULL),
(24, 12, 1, ' teste', 'teste', '2020-10-24', 'concluido', NULL),
(25, 13, 1, 'Não foi desenvolvido a edição do o contas a receber', 'Editar contas receber', '2020-10-28', 'concluido', NULL),
(26, 14, 1, 'Ao consultar os créditos, verificar tbm no contas a receber. Ou ao ser creditado os contas a receber, jogar dentro da tabela entradas ', 'Consulta de Créditos', '2020-10-28', 'concluido', NULL),
(27, 15, 1, 'As informações de vencimento do contas pagar estão erradas. ex: carrefour vai vencer 12/05 e fala que ja venceu a 175 dias ', 'Vencimento contas pagar', '2020-11-03', 'concluido', NULL),
(28, 16, 1, 'TODOS os debitos estão com valores ZERADOS. foi algo da atualização de ontem ', 'Debitos zerados', '2020-11-11', 'concluido', NULL),
(29, 17, 1, ' O sistema está permitindo efetuar e cancelar débitos no contas a pagar e no contas a receber, mesmo com a competência encerrada', 'Contas a pagar mes fechad', '2020-11-17', 'concluido', NULL),
(30, 18, 1, 'Quando mostra o vr de debito para os proximos x dias, não está contando a data de hoje.. somente os dias porteriores a hoje ', 'Erro no aviso de debito', '2020-12-02', 'concluido', NULL),
(31, 19, 1, ' Após debitar um contas pagar, o sistema deixa eu excluir o débito e depois cancelar o contas a pagar. Isso credita um valor a mais do que deveria ', 'Cancelar contas pagar', '2020-12-02', 'concluido', NULL),
(32, 20, 1, 'Os extratos com exclusão de débito estão aparecendo como negativos  no relatório', 'Extratos', '2020-12-07', 'concluido', NULL),
(33, 21, 1, ' aparece mes 10 11 e 12 depois 3 4 até 9', 'Ordem errada das colunas', '2020-12-23', 'concluido', NULL),
(34, 22, 1, 'Column not found: 1054 Unknown column desconto in field list', 'Erro ao importar XML', '2021-01-04', 'concluido', NULL),
(35, 23, 1, 'Retirar aspas simples pq da erro no SQL ', 'Erro ao inserir erro', '2021-01-04', 'concluido', NULL),
(36, 24, 1, ' Ao debitar contas pagar . Erro do código', 'Ao debitar contas pagar', '2021-01-04', 'concluido', NULL),
(37, 25, 1, 'Quando um debito for repetir para mais meses, o sistema deve procurar o item debito para jogar para o lançamento futuro. O Sistema não deve criar o item via código e jogar o estabelecimento como produto. ', 'Lançamentos futuros', '2021-02-03', 'clinica', NULL),
(38, 26, 1, 'As observações do contas  a pagar devem ir para as observações dos débitos assim que forem debitados ', 'Observações do cont.pagar', '2021-02-04', 'concluido', NULL),
(39, 27, 1, 'A palavra \'agente\"  está escrito errado. O correto é \"A gente\" (separados)', 'Texto email', '2021-02-11', 'concluido', NULL),
(40, 28, 1, ' O crédito do contas a receber não veio como lucro real. o campo veio nulo', 'Credito contas receber', '2021-02-11', 'concluido', NULL),
(41, 29, 1, 'Quando um credito ou debito vence hoje, o finanças mostra que vencerá em 0 dias. O certo é mostrar que vence Hoje! ', 'Mensagem contas rec./pag', '2021-02-23', 'concluido', NULL),
(42, 30, 1, 'Após escrever msg de erro o sistema mostra a mensagem com erros de português. ', 'Mensagem Erro', '2021-02-23', 'concluido', NULL),
(43, 31, 1, 'Quando pesquiso contas a receber pelo proximos X dias, o dia não fica gravado na tela para analise. Sempre aparece 5 dias ', 'Erro dias contas receber', '2021-02-23', 'concluido', NULL),
(44, 32, 1, 'Cadastrei um debito que ira se repetir por 7meses. valor total de R$ 400,00. quando coloquei o item (no valor de  R$ 400,00)  o finanças da erro dizendo que execi o valor', 'Valor exedido', '2021-02-24', 'clinica', NULL),
(45, 33, 1, 'Quando o sistema perde a sessão, o sistema da um erro de conexão com o BD na tela. O correto é ir para tela de login ', 'Perda de sessão', '2021-02-25', 'clinica', NULL),
(46, 34, 1, 'Ocorreu um erro ao excluir um contas a receber ', 'Deletar contas receber', '2021-03-02', 'clinica', NULL),
(47, 35, 1, 'Tela inicial diz q não há contas a receber nos próximos dias sendo que no dia em questão ja esta vencendo um contas a receber ', 'Aviso contas receber', '2021-03-15', 'clinica', NULL),
(48, 36, 1, 'A tela esta mostrando o saldo positivo incorreto no mês ', 'Saldo do mês', '2021-03-15', 'clinica', NULL),
(49, 37, 2, 'Meu grafico nao aparece corretamente ', 'Grafico errado', '2021-04-02', 'clinica', NULL),
(50, 38, 1, 'Ao realizar débitos do contas a pagar, As UND não são inseridas. Colina ativo tbm não ', 'Não importa UND', '2021-07-12', 'clinica', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `data_nascimento` date NOT NULL COMMENT 'data de nascimento do usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `codigo`, `nome`, `sobreNome`, `senha`, `idade`, `email`, `recebe_email`, `desenvolvedor`, `data_cadastro`, `ativo`, `data_nascimento`) VALUES
(57, '1', 'rodolfo', 'silva', '18c1db06e9fd090e341b17a9cd87bb84', '24', 'rodolfo0ti@gmail.com', 'S', 'S', '2018-01-01', 'S', '1995-06-05'),
(62, '2', 'kelly', 'cristina', 'fcb4d39900732a6a3c960c1cc83eb82a', '26', 'ckellym14@gmail.com ', 'S', 'N', '2019-01-01', 'S', '1993-06-08'),
(65, '3', 'teste', 'teste', '18c1db06e9fd090e341b17a9cd87bb84', NULL, 'rodolfo0ti@gmail.com', 'S', 'N', '2021-04-05', 'S', '1995-06-05');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `relatosErro`
--
ALTER TABLE `relatosErro`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_UNIQUE` (`codigo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `relatosErro`
--
ALTER TABLE `relatosErro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
