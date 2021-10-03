<?php

namespace App\Models\DAO;

use App\Models\Entidades\Credito;

class CreditoDAO extends BaseDAO {
    
    public function sqlExporta(){
        $sql = "SELECT * FROM entradas e ";
        $resultado = $this->select($sql);
        return $resultado->fetchAll();
    }           

    public function listar($codigo = null, $dataInicial = null, $dataFinal = null, $palavra = null, $paginaSelecionada, $totalPorPagina, $lucro_real = null) {
        $where = "";
        if ($codigo) {
            $sql = "SELECT * FROM entradas e WHERE e.codigo = " . base64_decode($codigo) . "";
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
            
            $sql = "SELECT * FROM entradas e WHERE id IS NOT NULL ";
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
            //cho $sql; die();
            $sql .= " LIMIT " . $inicio . "," . $totalPorPagina;
            $sqlContador .= $where;
            $resultadoLinhas = $this->select($sqlContador);
            $totalLinhas = $resultadoLinhas->fetch()['total_linhas'];
            $resultado = $this->select($sql);

            return ['paginaSelecionada' => $paginaSelecionada,
                'totalPorPagina' => $totalPorPagina,
                'totalLinhas' => $totalLinhas,
                'resultado' => $resultado->fetchAll(\PDO::FETCH_CLASS, Credito::class)];
        }

        return false;
    }

    public function detalhes($codigo = null) {
        if ($codigo) {
            $resultado = $this->select("SELECT e.codigo, e.descricao, e.valor, e.data, e.obs, e.ativo FROM entradas WHERE e.codigo = $codigo ");
            return $resultado->fetchAll(\PDO::FETCH_CLASS, Credito::class);
        }
        return false;
    }

    public function salvar(Credito $credito) {
        try {

            $descricao = $credito->getDescricao();
            $observacao = $credito->getObservacao();
            $codigo = $credito->getCodigo();
            $valor = $credito->getValor();
            $ativo = $credito->getAtivo();
            $fixo = $credito->getFixo();
            $data = $credito->getDataCadastro();
            $lucroReal = $credito->getlucroReal();

            if (empty($codigo)) {
                $row = $this->RetornaDado("SELECT codigo FROM entradas ORDER BY codigo DESC LIMIT 1");
                if (!$row) {
                    $codigo = 1;
                } else {
                    $codigo = $row["codigo"] + 1;
                }
            }
            $credito->setCodigo($codigo);

            return $this->insert(
                            'entradas',
                            ":descricao,:obs,:codigo,:valor,:ativo,:data,:fixo,:lucro_real",
                            [
                                ':descricao' => "'" . $descricao . "'",
                                ':obs' => "'" . $observacao . "'",
                                ':codigo' => $codigo,
                                ':valor' => $valor,
                                ':ativo' => "'" . $ativo . "'",
                                ':data' => "'" . $data . "'",
                                ':fixo' => "'" . $fixo . "'",
								':lucro_real' => "'" . $lucroReal . "'",
                            ]
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

    public function atualizar(Credito $credito) {
        try {

            $codigo = $credito->getCodigo();
            $descricao = $credito->getDescricao();
            $valor = $credito->getValor();
            $observacao = $credito->getObservacao();
            $data = $credito->getDataCadastro();
            // 'departure_time' => date("H:i:s", strtotime(request('departureTime')));
            $ativo = $credito->getAtivo();
            $fixo = $credito->getFixo();

            /* return $this->update2(
              'entradas',
              "codigo = :codigo, descricao = :descricao, valor = :valor, obs = :obs, data = :data, ativo = :ativo, fixo = :fixo",
              [
              ':codigo'=>$codigo,
              ':descricao'=>"'" . $descricao . "'",
              ':valor'=>$valor,
              ':obs'=>"'" . $observacao . "'",
              ':data'=>"'" . $data . "'",
              ':ativo'=>"'" . $ativo . "'",
              ':fixo'=>"'" . $fixo . "'",
              ],
              "codigo = :codigo"
              ); */

            return $this->update2(
                            'entradas',
                            [
                                'codigo' => $codigo,
                                'descricao' => "'" . $descricao . "'",
                                'valor' => $valor,
                                'obs' => "'" . $observacao . "'",
                                'data' => "'" . $data . "'",
                                'ativo' => "'" . $ativo . "'",
                                'fixo' => "'" . $fixo . "'",
                            ],
                            "codigo = $codigo "
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados.", 500);
        }
    }

    public function excluir(Credito $credito) {
        try {
            $codigo = $credito->getCodigo();

            //PREENCHE A TABELA DE CREDITO PARA ATUALIZAR  CAXA DEPOIS
            $sql = "SELECT * FROM entradas WHERE codigo = $codigo";
            $rowCredito = $this->RetornaDado($sql);
            $credito->setAtivo($rowCredito['ativo']);
            $credito->setFixo($rowCredito['fixo']);
            $credito->setDataCadastro($rowCredito['data']);
            $credito->setDescricao("Exclusão do crédito: $codigo.");
            $credito->setObservacao($rowCredito['obs']);
            $credito->setValor($rowCredito['valor']);

            return $this->delete('entradas', "codigo = $codigo");
        } catch (Exception $e) {
            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function retornaUltimoSaldo($codigo) {
        $sql = "SELECT valor FROM entradas WHERE codigo = $codigo ORDER BY id LIMIT 1";
        $rowCredito = $this->RetornaDado($sql);
        return $rowCredito['valor'];
    }

}
