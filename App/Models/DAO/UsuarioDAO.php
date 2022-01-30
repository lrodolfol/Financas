<?php

namespace App\Models\DAO;

use App\Models\Entidades\Usuario;

//use App\Models\DAO\BaseDAO

class UsuarioDAO extends BaseDAOFinancas {

    public function listar($codigo) {
        $sql = "SELECT * FROM usuarios ";
        if ($codigo) {
            $sql .= " WHERE codigo = " . $codigo . " ";
        }
        $row = $this->FinancasRetornaDado($sql);
        return $row;
    }

    public function recuperarSenha($email) {
        $sql = "SELECT * FROM usuarios WHERE email = '" . $email . "'";
        $row = $this->RetornaDado($sql);
        if (!$row) {
            return;
        } else {
            if (!$this->recuperarSenhaEmail($email)) {
                return;
            } else {
                return true;
            }
        }
    }

    public function trocaSenha($email, $novaSenha) {
        $rowUsuario = $this->update2(
                'usuarios', [
            'senha' => "md5('" . $novaSenha . "')",
                ], "email = '" . $email . "'"
        );
        return $rowUsuario;
    }

    public function logar(Usuario $usuario) {
        try {
            $nome = $usuario->getNome();
            $senha = $usuario->getSenha();
            $row = $this->FinancasRetornaDado("SELECT * FROM usuarios WHERE nome = '" . $nome . "' AND senha = md5('" . $senha . "') ");
            if ($row) {
                $usuario->setEmail($row['email']);
                $usuario->setRecebeEmail($row['recebe_email']);
                $usuario->setDesenvolvedor($row['desenvolvedor']);
                $usuario->setCodigo($row['codigo']);
                $usuario->setDataCadastro($row['data_cadastro']);
                return true;
            } else {
                return false;
            }
            //return $this->Consultaselect("SELECT * FROM usuarios WHERE nome = '" . $nome . "' AND senha = md5('" . $senha . "') ");
        } catch (\Exception $e) {
            //throw new \Exception("Erro na gravação de dados.", 500);
            return false;
        }
    }

    public function validaUsuario(Usuario $Usuario) {
        $sql = "SELECT * FROM usuarios WHERE nome = '" . $Usuario->getNome() . "'";
        $row = $this->FinancasRetornaDado($sql);
        return $row['nome'];
    }

    public function salvar(Usuario $usuario) {
        $codigo = "";
        $idade = $usuario->getIdade();
        $nome = $usuario->getNome();
        $sobreNome = $usuario->getSobreNome();
        $senha = $usuario->getSenha();

        $row = $this->FinancasRetornaDado("SELECT codigo FROM usuarios ORDER BY codigo DESC LIMIT 1");
        if (!$row) {
            $codigo = 1;
        } else {
            $codigo = $row["codigo"] + 1;
        }

        return $this->insert(
                        'usuarios', ":codigo,:idade,:nome,:sobreNome,:senha", [
                    ':codigo' => "'" . $codigo . "'",
                    ':idade' => "'" . $idade . "'",
                    ':nome' => "'" . $nome . "'",
                    ':sobrenome' => "'" . $sobreNome . "'",
                    ':senha' => "'" . $senha . "'",
                        ]
        );
    }

    public function criarBaseDadosUsuario(Usuario $usuario) {
        $codigo = "";
        $dataNascimento = $usuario->getDataNascimento()->format('Y-m-d');
        $nome = $usuario->getNome();
        $sobreNome = $usuario->getSobreNome();
        $senha = $usuario->getSenha();
        $email = $usuario->getEmail();
        $recebeEmail = $usuario->getRecebeEmail();
        $dataCadastro = $usuario->getDataCadastro();

        $row = $this->FinancasRetornaDado("SELECT codigo FROM usuarios ORDER BY codigo DESC LIMIT 1");
        if (!$row) {
            $codigo = 1;
        } else {
            $codigo = $row["codigo"] + 1;
        }

        $sqlCreateDataBase = array();
        $sqlCreateTables = array();

        /*
         * ARQUIVO SQL DA RAZ DO PROJETO
         */
        define("SQL_ARQUIVO_USUARIO", $_SERVER['DOCUMENT_ROOT'] . "\\financas\\financas_usuario.sql"); //RECUPERA O DIRETORIO DO ARQUIVO
        if (is_file(SQL_ARQUIVO_USUARIO)) {
            $arquivo = fopen(SQL_ARQUIVO_USUARIO, "r");         //FAZ A ABERTURA DO ARQUIVO 'r' SOMENTE PARA LEITURA
            
            $bancoConexao = "CREATE DATABASE IF NOT EXISTS `financas_`" . $nome . " DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci; USE `financas_`" . $nome .";";
                       
            $tamanho = filesize(SQL_ARQUIVO_USUARIO);          //RECUPERA O TAMANHO DO ARQUIVO EM QUESTÃO
            $conexao = fread($arquivo, $tamanho);       //ATRIBUI O ARQUIVO A UMA VARIAVEL
            $bancoConexao .= utf8_encode($conexao);            //MOSTRA O ARQUIVO
            fclose($arquivo);                          //FECHA O ARQUIVO EM QUESTÃO
        }
        //var_dump($bancoConexao);
        //die();

        //SQL INSERT NA TABELA DE USUÁRIOS
        $sqlCreateDataBase[0] = "INSERT INTO usuarios (codigo, data_nascimento, nome, sobrenome, senha, email, recebe_email, data_cadastro) "
                . "VALUES (" . $codigo . ", '" . $dataNascimento . "', '" . $nome . "', '" . $sobreNome . "', md5('" . $senha . "'), '" . $email . "', '" . $recebeEmail . "', '" . $dataCadastro . "' )";

        //echo $sqlCreateDataBase[0]; die();
        //SQL CRIAR BASE DE DADOS PARA NOVO USUÁRIO
        $sqlCreateDataBase[1] = "CREATE DATABASE IF NOT EXISTS financas_" . $nome . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

        //CRIA TABELA CAIXA
        $sqlCreateTables[0] = "DROP TABLE IF EXISTS `caixa`";
        $sqlCreateTables[1] = "CREATE TABLE IF NOT EXISTS `caixa` ( "
                . " `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                . " `descricao` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '', "
                . "`obs` varchar(100) COLLATE latin1_general_ci DEFAULT NULL, "
                . "  `ativo` char(1) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `saldo` float NOT NULL, "
                . " `data` date DEFAULT NULL, "
                . " `codigo_saida_cabecalho` int(11) DEFAULT NULL, "
                . " `codigo_entrada` int(11) DEFAULT NULL, "
                . " UNIQUE KEY `id` (`id`) "
                . " ) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";

        //CRIA TABELA ENTRADAS
        $sqlCreateTables[2] = "DROP TABLE IF EXISTS `entradas`";
        $sqlCreateTables[3] = "CREATE TABLE IF NOT EXISTS `entradas` ( "
                . " `id` int(11) NOT NULL AUTO_INCREMENT, "
                . " `codigo` int(11) NOT NULL, "
                . " `descricao` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '', "
                . " `obs` varchar(50) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `valor` float NOT NULL DEFAULT '0', "
                . " `ativo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '0', "
                . " `fixo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N', "
                . " `data` date NOT NULL, "
                . " `lucro_real` CHAR(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N', "
                . " PRIMARY KEY (`codigo`), "
                . " UNIQUE KEY `id` (`id`) "
                . " ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";

        //CRIA TABELA ESTABELECIMENTOS
        $sqlCreateTables[4] = "DROP TABLE IF EXISTS `estabelecimentos`";
        $sqlCreateTables[5] = "CREATE TABLE IF NOT EXISTS `estabelecimentos` ( "
                . " `id` int(11) NOT NULL AUTO_INCREMENT, "
                . " `codigo` int(11) NOT NULL, "
                . " `nome` varchar(50) COLLATE latin1_general_ci NOT NULL, "
                . " `cnpj` varchar(14) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `tipo_comercio` varchar(20) COLLATE latin1_general_ci NOT NULL, "
                . " `cidade` varchar(30) COLLATE latin1_general_ci NOT NULL, "
                . " `ativo` char(1) COLLATE latin1_general_ci DEFAULT 'S', "
                . " PRIMARY KEY (`codigo`), "
                . " UNIQUE KEY `id` (`id`) "
                . " ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";

        //CRIA TABELA FORMA DE PAGAMENTO
        $sqlCreateTables[6] = "DROP TABLE IF EXISTS `formas_pagamento`";
        $sqlCreateTables[7] = "CREATE TABLE IF NOT EXISTS `formas_pagamento` ( "
                . " `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                . " `codigo` int(10) UNSIGNED NOT NULL, "
                . " `descricao` varchar(50) COLLATE utf8_unicode_ci NOT NULL, "
                . " `ativo` char(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '', "
                . " `dia_fechamento` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '''''dia vencimento, se for cartão de crédito''''',  "
                . " `dia_vencimento` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL, "
                . " PRIMARY KEY (`codigo`), "
                . " UNIQUE KEY `id` (`id`) "
                . " ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ";

        //CRIA TABELA LANÇAMENTOS FUTUROS
        $sqlCreateTables[8] = "DROP TABLE IF EXISTS `lancamentos_futuros`";
        $sqlCreateTables[9] = "CREATE TABLE IF NOT EXISTS `lancamentos_futuros` ( "
                . " `id` int(11) NOT NULL AUTO_INCREMENT, "
                . " `codigo` int(11) NOT NULL, "
                . " `data_compra` date NOT NULL, "
                . " `data_debito` date NOT NULL, "
                . " `valor_total` float NOT NULL DEFAULT '0', "
                . " `estabelecimento` int(11) NOT NULL, "
                . " `forma_pagamento` int(10) UNSIGNED NOT NULL, "
                . " `qtd_parcelas` int(11) NOT NULL, "
                . " `ativo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'S', "
                . " `debitado` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N', "
                . " `obs` varchar(75) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `codigo_cabecalho` int(11) DEFAULT NULL, "
                . " `codigo_debito` INT(11) DEFAULT NULL,  "
                . " `numero_parcela` INT NOT NULL, "
                . " `juros` float NOT NULL DEFAULT '0', "
                . " `total_geral` float NOT NULL DEFAULT '0', "
                . " UNIQUE KEY `id` (`id`), "
                . " KEY `FK1_ESTABELECIMENTO` (`estabelecimento`), "
                . " KEY `FK2_FORMA_PAGAMENTO` (`forma_pagamento`), "
                . " KEY `codigo_cabecalho_idx` (`codigo_cabecalho`) "
                . " ) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

        //CRIA TABELA LANÇAMENTOS FUTUROS ITENS
        $sqlCreateTables[10] = "DROP TABLE IF EXISTS `lancamentos_futuros_itens`";
        $sqlCreateTables[11] = "CREATE TABLE IF NOT EXISTS `lancamentos_futuros_itens` ( "
                . " `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                . " `codigo` int(10) UNSIGNED NOT NULL, "
                . " `codigo_cabecalho` int(11) NOT NULL, "
                . " `produto` varchar(20) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `qtd_produto` int(11) DEFAULT NULL, "
                . " `valor_produto` float DEFAULT NULL, "
                . " `ativo` char(1) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `unidade_medida` VARCHAR(6) CHARACTER SET 'latin1' COLLATE 'latin1_danish_ci' NULL DEFAULT 'UND',  "
                . " PRIMARY KEY (`codigo`), "
                . " UNIQUE KEY `id` (`id`), "
                . " KEY `FK1_codigo_cabecalho` (`codigo_cabecalho`) "
                . " ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";

        //CRIA TABELA LANÇAMENTOS SAIDAS ITENS
        $sqlCreateTables[12] = "DROP TABLE IF EXISTS `saidas_itens`";
        $sqlCreateTables[13] = "CREATE TABLE IF NOT EXISTS `saidas_itens` ( "
                . " `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
                . " `codigo` int(10) UNSIGNED NOT NULL, "
                . " `codigo_cabecalho` int(11) NOT NULL, "
                . " `produto` varchar(20) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `qtd_produto` int(11) DEFAULT NULL, "
                . " `valor_produto` float DEFAULT NULL, "
                . " `ativo` char(1) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `unidade_medida` VARCHAR(6) CHARACTER SET 'latin1' COLLATE 'latin1_danish_ci' NULL DEFAULT 'UND',  "
                . " PRIMARY KEY (`codigo`), "
                . " UNIQUE KEY `id` (`id`), "
                . " KEY `FK1_codigo_cabecalho` (`codigo_cabecalho`) "
                . " ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";


        //CRIA TABELA LANÇAMENTOS SAIDAS CABAÇALHO
        $sqlCreateTables[14] = "DROP TABLE IF EXISTS `saida_cabecalho`";
        $sqlCreateTables[15] = "CREATE TABLE IF NOT EXISTS `saida_cabecalho` ( "
                . " `id` int(11) NOT NULL AUTO_INCREMENT, "
                . " `codigo` int(11) NOT NULL, "
                . " `data_compra` date NOT NULL, "
                . " `data_debito` date NOT NULL, "
                . " `valor_total` float NOT NULL DEFAULT '0', "
                . " `estabelecimento` int(11) NOT NULL, "
                . " `forma_pagamento` int(10) UNSIGNED NOT NULL, "
                . " `qtd_parcelas` int(11) NOT NULL, "
                . " `ativo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'S', "
                . " `fixo` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N', "
                . " `obs` varchar(75) COLLATE latin1_general_ci DEFAULT NULL, "
                . " `juros` float NOT NULL DEFAULT '0', "
                . " `desconto` float NOT NULL DEFAULT '0', "
                . " `total_geral` float NOT NULL DEFAULT '0', "
                . " PRIMARY KEY (`codigo`), "
                . " UNIQUE KEY `id` (`id`), "
                . " KEY `FK1_ESTABELECIMENTO` (`estabelecimento`), "
                . " KEY `FK2_FORMA_PAGAMENTO` (`forma_pagamento`) "
                . " ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";

        $sqlCreateTables[16] = "DROP TABLE IF EXISTS `contas_receber`";
        $sqlCreateTables[17] = "CREATE TABLE `contas_receber` ( "
                . " `id` INT NOT NULL AUTO_INCREMENT, "
                . " `codigo` INT NOT NULL, "
                . " `descricao` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL, "
                . " `obs` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL, "
                . " `valor` DOUBLE NOT NULL, "
                . " `ativo` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'S', "
                . " `lucro_real` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'S', "
                . " `fixo` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'N', "
                . " `creditado` CHAR(1) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'N', "
                . " `data_compensacao` DATE NOT NULL, "
                . " PRIMARY KEY (`codigo`), "
                . " UNIQUE INDEX `id_UNIQUE` (`id` ASC)) "
                . " ENGINE = InnoDB "
                . " DEFAULT CHARACTER SET = utf8 "
                . " COMMENT = 'créditos para recebimento'";

        $sql[16] = "ALTER TABLE `lancamentos_futuros` "
                . " ADD CONSTRAINT `codigo_cabecalho` FOREIGN KEY (`codigo_cabecalho`) REFERENCES `saida_cabecalho` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION, "
                . " ADD CONSTRAINT `estabelecimento` FOREIGN KEY (`estabelecimento`) REFERENCES `estabelecimentos` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION; ";

        $sql[17] = "ALTER TABLE `saidas_itens` "
                . " ADD CONSTRAINT `FK1_codigo_cabecalho` FOREIGN KEY (`codigo_cabecalho`) REFERENCES `saida_cabecalho` (`codigo`) ";

        $sql[18] = "ALTER TABLE `saida_cabecalho` "
                . " ADD CONSTRAINT `FK1_ESTABELECIMENTO` FOREIGN KEY (`estabelecimento`) REFERENCES `estabelecimentos` (`codigo`), "
                . " ADD CONSTRAINT `FK2_FORMA_PAGAMENTO` FOREIGN KEY (`forma_pagamento`) REFERENCES `formas_pagamento` (`codigo`); "
                . "COMMIT ";

        $rowUsuario = $this->FinancasDDL($sqlCreateDataBase, $sqlCreateTables, $nome);
        //$rowUsuario = $this->FinancasDDL($sqlCreateDataBase, $sqlCreateTables, $nome); //$bancoConexao
        return $rowUsuario;
    }

}
