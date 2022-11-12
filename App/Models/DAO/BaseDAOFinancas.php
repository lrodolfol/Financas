<?php

namespace App\Models\DAO;

use App\Lib\Conexao;
use PDO;
use PDOException;
use App\Lib\Sessao;
use App\Models\Entidades\Caixa;
use App\Models\Entidades\Email;

abstract class BaseDAOFinancas {

    private $conexao;

    public function __construct() {
        $this->conexao = Conexao::getConnectionFinancas();
    }

    public function FinancasRetornaDT($sql) {
        $result = $this->FinancasSelect($sql);
        $row = $result->fetchAll();
        if ($row) {
            return $row;
        } else {
            return null;
        }
    }
    
    public function listarUsuario($sql) {
        $row = $this->FinancasRetornaDadoObject($sql);
        return $row;
    }

    public function FinancasRetornaDado($sql) {
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
    
    public function FinancasRetornaDadoObject($sql) : \stdClass{
        if (!empty($sql)) {
            if ($this->conexao->query($sql)) {
                $stmt = $this->conexao->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchObject();
                return $rows;
            } else {
                return null;
            }
        }
    }

    public function FinancasSelect($sql) {
        if (!empty($sql)) {
            return $this->conexao->query($sql);
        }
    }

    public function FinancasRetornaTabela($sql) {
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

    public function FinancasConsultaselect($sql) {
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

    public function FinancasInsert($table, $cols, $values) {
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

    public function FinancasInsertLoop($table, $cols, $values) {
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

    public function FinancasUpdate($table, $cols, $values, $where = null) {
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

    public function FinancasUpdate2($table, $values, $where) {
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

    public function FinancasDelete($table, $where = null) {
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

    public function FinancasExecutaSqlArray($sqlArray) {
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

    public function FinancasDDL($sqlCreateDataBase, $sqlCreateTables, $nomeUsuario) {
        try {
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexao->beginTransaction();
            foreach ($sqlCreateDataBase as $query) { //CRIA NOVO USUÁRIO E TENTA CRIAR A BASE DE DADOS DO USUARIO
                $this->conexao->exec($query);
            }
            //$this->conexao->commit();

            //TENTA FAZER A CONEXÃO COM A BASE DO USUÁRIO CRIADO
            $connectionLocal = "";
            $pdoConfigLocal = "mysql" . ":" . "host=" . "localhost" . ";";
            $pdoConfigLocal .= "dbname=financas_" . $nomeUsuario . ";";
            $pdoConfigLocal .= "charset=utf8;";
            try {
                $connectionLocal = new PDO($pdoConfigLocal, "root", DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
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
            //$connectionLocal->commit();
            return true;
        } catch (Exception $ex) {
            $connectionLocal->rollBack();
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

}
