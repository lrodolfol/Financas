<?php

namespace App\Models\DAO;

use App\Models\Entidades\Caixa;
use App\Lib\Sessao;

class CaixaDAO extends BaseDAO {

    public function listar($id = null) {
        $sql = "SELECT * FROM caixa c ";
        $retorno = $this->select($sql);
        $return = $retorno->fetchAll();
        
        return $return;
    }

    public function salvar(Caixa $caixa) {
        try {
            $descricao = $caixa->getDescricao();
            $obs = $caixa->getObs();
            $saldo = $caixa->getSaldo();
            $data = $caixa->getData();
            $data = date("Y-m-d", strtotime($data));
            $codigo_saida_cabecalho = $caixa->getCodigoSaidaCabecalho() ? $caixa->getCodigoSaidaCabecalho() : "null";
            $codigo_entrada = $caixa->getCodigoEntrada() ? $caixa->getCodigoEntrada() : "null";

            $row = $this->RetornaDado("SELECT saldo FROM caixa ORDER BY id DESC LIMIT 1");
            if (!$row) {
                $saldo = $saldo;
            } else {
                $saldo = $saldo + $row["saldo"];
            }

            $rowCaixa = $this->insert(
                    'caixa',
                    ":descricao,:obs,:saldo,:data,:ativo,:codigo_saida_cabecalho,:codigo_entrada",
                    [
                        ':descricao' => "'" . $descricao . "'",
                        ':obs' => "'" . $obs . "'",
                        ':valor' => $saldo,
                        ':data' => "'" . $data . "'",
                        ':ativo' => "'S'",
                        ':codigo_saida_cabecalho' => $codigo_saida_cabecalho,
                        ':codigo_entrada' => $codigo_entrada,
                    ]
            );
            return $rowCaixa;
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

    /* public function atualizar(Caixa $caixa) {
      try {

      $descricao = $caixa->getDescricao();
      $obs = $caixa->getObs();
      $saldo = $caixa->getSaldo();
      $data = $caixa->getData();
      $data =  date("Y-m-d", strtotime($data));
      $codigo_saida_cabecalho = $caixa->getCodigoSaidaCabecalho() ? $caixa->getCodigoSaidaCabecalho() : "null" ;
      $codigo_entrada = $caixa->getCodigoEntrada() ? $caixa->getCodigoEntrada() : "null" ;

      return $this->update(
      'produto',
      "nome = :nome, preco = :preco, quantidade = :quantidade, descricao = :descricao",
      [
      ':id' => $id,
      ':nome' => $nome,
      ':preco' => $preco,
      ':quantidade' => $quantidade,
      ':descricao' => $descricao,
      ],
      "id = :id"
      );
      } catch (\Exception $e) {
      throw new \Exception("Erro na gravação de dados.", 500);
      }
      } */

    public function excluir(Caixa $caixa, $opcao) {
        try {
            $codigo = $opcao == "SAIDA" ? $caixa->getCodigoSaidaCabecalho() : $caixa->getCodigoEntrada();
            $where = "";
            $opcao = "SAIDA" ? $where = "codigo_saida_cabecalho = " : "codigo_entrada = ";
            return $this->delete('caixa', $where . $codigo);
        } catch (Exception $e) {

            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function retornaSaldo($entradaSaida, $codigo) {
        $sql = "SELECT saldo FROM caixa WHERE ";

        if (strtoupper($entradaSaida) == "ENTRADA") {
            $sql .= " codigo_entrada = ";
        } else {
            $sql .= " codigo_saida_cabecalho = ";
        }
        $sql .= $codigo . " ORDER BY id DESC LIMIT 1 ";
        $row = $this->RetornaDado($sql);
        return $row['saldo'];
    }

}
