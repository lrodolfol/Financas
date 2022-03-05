<?php

namespace App\Models\DAO;

use App\Lib\Conexao;
use PDO;
use PDOException;
use App\Lib\Sessao;
use App\Models\Entidades\Caixa;
use App\Models\Entidades\Email;

abstract class BaseDAO {

    private $conexao;

    public function __construct() {
        $this->conexao = Conexao::getConnection();
    }

    public function select($sql) {
        if (!empty($sql)) {
            return $this->conexao->query($sql);
        }
    }

    public function RetornaDado($sql) {
        if (!empty($sql)) {
            if ($this->conexao->query($sql)) {
                $stmt = $this->conexao->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetch();
                return $rows;
            } else {
                return null;
            }
        }
    }

    public function retornaTabela($sql) {
        if (!empty($sql)) {
            if ($this->conexao->query($sql)) {
                $stmt = $this->conexao->prepare($sql);
                $stmt->execute();
                $rows = $stmt->rowcount();
                $rows = 4;
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }
    }

    /* public function RetornaDados($sql) 
      {
      if(!empty($sql))
      {
      if($this->conexao->query($sql)){
      $stmt = $this->conexao->prepare($sql);
      $stmt->execute();
      $rows = $stmt->rowcount();
      if($rows > 0){
      return true;
      }else{
      return false;
      }
      }else{
      return false;
      }

      }
      } */

    public function Consultaselect($sql) {
        if (!empty($sql)) {
            if ($this->conexao->query($sql)) {
                $stmt = $this->conexao->prepare($sql);
                $stmt->execute();
                $rows = $stmt->rowcount();
                if ($rows > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function retornaObject($sql) {
        if (!empty($sql)) {
            if ($this->conexao->query($sql)) {
                $stmt = $this->conexao->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll(\PDO::FETCH_CLASS);
                return $rows;
            } else {
                return false;
            }
        }
    }

    public function insertSubquery($sql) {
        $stmt = $this->conexao->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function insert($table, $cols, $values) {
        if (!empty($table) && !empty($cols) && !empty($values)) {
            $parametros = $cols;
            $colunas = str_replace(":", "", $cols);
            $val = "";
            foreach ($values as $value) {
                if ($val != "") {
                    $val = $val . ', ';
                }
                $val = $val . $value;
            }

            $stmt = $this->conexao->prepare("INSERT INTO $table ($colunas) VALUES ($val)");

            $stmt->execute();

            return $stmt->rowCount();
        } else {
            return false;
        }
    }

    public function insertLoop($table, $cols, $values) {
        if (!empty($table) && !empty($cols) && !empty($values)) {
            $parametros = $cols;
            $colunas = str_replace(":", "", $cols);
            $val = "";
            foreach ($values as $value) {
                if ($val != "") {
                    $val = $val . ', ';
                }
                $val = $val . $value;
            }

            $stmt = $this->conexao->prepare("INSERT INTO $table ($colunas) VALUES ($val)");

            $stmt->execute();

            return $stmt->rowCount();
        } else {
            return false;
        }
    }

    public function update($table, $cols, $values, $where = null) {
        if (!empty($table) && !empty($cols) && !empty($values)) {
            if ($where) {
                $where = " WHERE $where ";
            }

            $stmt = $this->conexao->prepare("UPDATE $table SET $cols $where");
            $stmt->execute($values);

            return $stmt->rowCount();
        } else {
            return false;
        }
    }

    public function update2($table, $values, $where) {
        if (!empty($table) && !empty($values)) {
            $sql = "UPDATE $table SET ";
            foreach ($values as $key => $value) {
                $sql = $sql . $key . '=' . $value . ', ';
            }
            $sql = substr($sql, 0, strlen($sql) - 2);
            $sql = $sql . " WHERE " . $where;
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();

            return $stmt->rowCount();
        } else {
            return false;
        }
    }

    public function delete($table, $where = null) {
        if (!empty($table)) {
            if ($where) {
                $where = " WHERE $where ";
            }

            $stmt = $this->conexao->prepare("DELETE FROM $table $where");
            $stmt->execute();

            return $stmt->rowCount();
        } else {
            return false;
        }
    }

    public function executaSqlArray($sqlArray) {
        $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conexao->beginTransaction();
        try {
            foreach ($sqlArray as $query) {
                $this->conexao->exec($query);
            }
            $this->conexao->commit();
            return true;
        } catch (Exception $ex) {
            $this->conexao->rollBack();
            return false;
        }
    }

    public function DDL($sqlCreateDataBase, $sqlCreateTables, $nomeUsuario) {
        try {
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexao->beginTransaction();
            foreach ($sqlCreateDataBase as $query) { //CRIA NOVO USUÁRIO E TENTA CRIAR A BASE DE DADOS DO USUARIO
                $this->conexao->exec($query);
            }
            $this->conexao->commit();

            //TENTA FAZER A CONEXÃO COM A BASE DO USUÁRIO CRIADA
            $connectionLocal = "";
            $pdoConfigLocal = "mysql" . ":" . "host=" . "localhost" . ";";
            $pdoConfigLocal .= "dbname=financas_" . $nomeUsuario . ";";
            $pdoConfigLocal .= "charset=utf8;";
            try {
                $connectionLocal = new PDO($pdoConfigLocal, "root", "", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $connectionLocal->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $connectionLocal->rollBack();
                return false;
            }
        } catch (Exception $ex) {
            $connectionLocal->rollBack();
            return false;
        }

        try {
            $connectionLocal->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connectionLocal->beginTransaction();
            foreach ($sqlCreateTables as $query) { //CRIA NOVO USUÁRIO E TENTA CRIAR A BASE DE DADOS DO USUARIO
                $connectionLocal->exec($query);
            }
            $connectionLocal->commit();
            return true;
        } catch (Exception $ex) {
            $connectionLocal->rollBack();
            return false;
        }
    }

    public function enviaEmail(Email $email) {
        $destinatario = $email->getDestinatario();
        $remetente = $email->getRemetente();
        $assunto = $email->getAssunto();
        $texto = $email->getTexto();
        $emailResposta = $email->getEmailResposta();

        $headers = 'From:' . $remetente . "\r\n" .
                'Reply-To:' . $emailResposta . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
        $envio = mail($destinatario, $assunto, $texto, $headers);
        if (!$envio) {
            return false;
        } else {
            return true;
        }
    }

    public function mandaEmail(Caixa $caixa) {
        $tabelaOperacao = "";
        $tipo = "";
        $sql = "";
        $assuntoEmail = "Movimentação registrada";
        //VERIFICA QUAL O TIPO DE OPERAÇÃO: crédito, debitou o agendamento
        /* switch ($tipoOperacao) {
          case "CREDITO":
          $tipo = "Crédito";
          $tabelaOperacao = "entradas";
          $sql = "SELECT valor as valor, concat(descricao, ' - ', obs) as obs, data as data FROM " . $tabelaOperacao . " WHERE codigo = " . $codigo . "";
          break;
          case "DEBITO":
          $tipo = "Débito";
          $tabelaOperacao = "saida_cabecalho";
          break;
          case "LANCAMENTO":
          $tipo = "Agendamento";
          $tabelaOperacao = "lancamentos_futuros";
          break;
          default:
          return "Tipo não defino";
          }

          //VALIDA EMAIL DO USUÁRIO
          $emailDestinatario = Sessao::retornaEmail();
          if(empty($emailDestinatario) || (!isset($emailDestinatario))) {
          return "Usuário não possui e-mail";
          }

          $rowEmail = $this->RetornaDado($sql);
          $valor = number_format($rowEmail['valor'], 2, ',', '.') ;
          $obs = $rowEmail['obs'];
          $data = $rowEmail['data']; */

        if ($caixa->getCodigoEntrada()) {
            $tipo = "CRÉDITO";
        } elseif ($caixa->getCodigoSaidaCabecalho()) {
            $tipo = "DÉBITO";
        }
        $valor = number_format($caixa->getSaldo(), '2', ',', '.');
        $obs = $caixa->getDescricao() . " - " . $caixa->getObs();
        $data = $caixa->getData();
        $emailDestinatario = Sessao::retornaEmail();

        $emailRemetente = "financas@" . NOME_SITE;

        /* $emailMsg = <<<DEM
          <html>
          <body>
          <article>
          <h1>Rodolfo JSILVA</h1>
          <h2>CEO TI NOS NEGÓCIOS</h2>
          <h3>Não basta saber que as coisas funcionan, preciso descobrir como elas funcionam</h3>
          </article>
          </body>
          </html>
          DEM; */

        $mensagem = "Olá " . ucfirst(Sessao::retornaUsuario()) . ", Uma nova operação foi feita em sua conta Finanças! \r\n"
                . "Você acabou de realizar uma nova operação em nossa plataforma! \r\n\r\n"
                . "Valor a ser movimentado: R$  " . $valor . "  " . $tipo . "\r\n"
                . "Detalhes: " . $obs . "\r\n"
                . "Data operação: " . $data . "\r\n\r\n"
                . "Controle seus gastos sempre que possivel e nunca se esqueça de informar sua operação. O resto a gente faz por você ;) \r\n\r\n\r\n\r\n"
                . "Caso não queira mais receber e-mail sobre suas operações clique aqui(em breve..) ";
        $headers = 'From:' . $emailRemetente . "\r\n" .
                'Reply-To:' . $emailRemetente . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
        $envio = mail($emailDestinatario, $assuntoEmail, $mensagem, $headers);
        if (!$envio) {
            return "Erro ao manda e-mail";
        } else {
            return "E-mail enviado";
        }
    }

    public function recuperarSenhaEmail($email) {
        $assuntoEmail = "Recuperação de senha";
        $emailRemetente = "financas@" . NOME_SITE;
        $emailDestinatario = $email;
        $codigo = rand(11111, 99999);

        $mensagem = "Olá " . ucfirst(Sessao::retornaUsuario()) . ", você solicitou uma recuperação de senha em sua conta Finanças! \r\n"
                . "Use o código " . $codigo . " para resetar a senha. \r\n\r\n"
                . "Esse código irá expirar em minutos \r\n";
        $headers = 'From:' . $emailRemetente . "\r\n" .
                'Reply-To:' . $emailRemetente . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        $envio = mail($emailDestinatario, $assuntoEmail, $mensagem, $headers);
        //setcookie("codigoRecuperarSenha"); //EXPIRA EM 7MINUTOS
        setcookie("codigoRecuperarSenha", $codigo, time() + 900); //EXPIRA EM 7MINUTOS
        //
        return $envio;
        //return true;
    }

    public function listaPaginacao($tabela, $codigo = null, $dataInicial = null, $dataFinal = null, $palavra = null, $paginaSelecionada, $totalPorPagina, $lucro_real = null) {
        $where = "";
        if ($codigo) {
            $sql = "SELECT * FROM " . $tabela . " e WHERE e.codigo = " . base64_decode($codigo) . "";
            if (isset($dataInicial)) {
                $sql .= " AND data >= $dataInicial ";
            }
            if (isset($dataInicial)) {
                $sql .= " AND data <= $dataFinal ";
            }
            if (!empty($palavra)) {
                $sql .= " AND obs like '%$palavra%' ";
            }
            $sql .= "  ORDER BY ativo, codigo DESC ";

            $resultado = $this->select($sql);

            //return $resultado->fetchObject(Credito::class);
            return $resultado->fetchAll(\PDO::FETCH_CLASS, Credito::class);
        } else {
            $inicio = (($paginaSelecionada - 1) * $totalPorPagina);

            $sql = "SELECT * FROM " . $tabela . " e WHERE id IS NOT NULL ";
            $sqlContador = "SELECT count(*) as total_linhas FROM entradas e WHERE id IS NOT NULL ";

            if (!empty($dataInicial)) {
                $where .= " AND data >= '$dataInicial' ";
            }
            if (!empty($dataInicial)) {
                $where .= " AND data <= '$dataFinal' ";
            }
            if (!empty($palavra)) {
                $where .= " AND e.obs LIKE '%$palavra%' OR e.descricao LIKE '%$palavra%' ";
            }
            if (!empty($lucro_real) && $lucro_real == 'S') {
                $sql .= " AND lucro_real = '$lucro_real' ";
            }
            $sql .= $where . " ORDER BY ativo, codigo DESC ";
            $sql .= " LIMIT " . $inicio . "," . $totalPorPagina;
            $sqlContador .= $where;
            $resultadoLinhas = $this->select($sqlContador);
            $totalLinhas = $resultadoLinhas->fetch()['total_linhas'];
            $resultado = $this->select($sql);

            return ['paginaSelecionada' => $paginaSelecionada,
                'totalPorPagina' => $totalPorPagina,
                'totalLinhas' => $totalLinhas,
                'resultado' => $resultado->fetchAll()];
        }

        return false;
    }

    public function verificaMesEncerrado($mes, $ano) {
        $sql = "SELECT * FROM encerramento WHERE mes = " . $mes . " AND "
                . " ano = " . $ano . " AND encerrado = 'S' ";
        $row = $this->Consultaselect($sql);
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

}
