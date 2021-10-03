<?php

namespace App\Models\DAO;

use App\Models\Entidades\Contasreceber;
use App\Models\DAO\ContasReceberDAO;
use DateTime;

class ContasReceberDAO extends BaseDAO {

    public function salvar(Contasreceber $ContasReceber) {
        $sql = "SELECT codigo FROM contas_receber ORDER BY codigo DESC LIMIT 1";
        $row = $this->RetornaDado($sql);
        $codigo = "";
        if (!$row) {
            $codigo = 1;
        } else {
            $codigo = $row["codigo"] + 1;
        }

        $descricao = $ContasReceber->getDescricao();
        $obs = $ContasReceber->getObservacao();
        $valor = $ContasReceber->getValor();
        $ativo = $ContasReceber->getAtivo();
        $lucroReal = $ContasReceber->getlucroReal();
        $fixo = $ContasReceber->getFixo();
        $dataCompensacao = $ContasReceber->getDataCompensacao()->format('Y-m-d');
        $ContasReceber->setCodigo($codigo);

        $row = $this->insert('contas_receber',
                ":descricao,:obs,:codigo,:valor,:ativo,:data_compensacao,:fixo,:lucro_real,:creditado",
                [
                    ':descricao' => "'" . $descricao . "'",
                    ':obs' => "'" . $obs . "'",
                    ':codigo' => $codigo,
                    ':valor' => $valor,
                    ':ativo' => "'" . $ativo . "'",
                    ':data_compensacao' => "'" . $dataCompensacao . "'",
                    ':fixo' => "'" . $fixo . "'",
                    ':lucro_real' => "'" . $lucroReal . "'",
                    ':creditado' => "'N'",
                ]
        );

        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function listar($codigo = null) {
        $sql = "SELECT * FROM contas_receber ";
        if ($codigo) {
            $sql .= " WHERE codigo = " . base64_decode($codigo) . "";
        }

        $query = $this->select($sql);
        $table = $query->fetchAll();
        if ($table) {
            return $table;
        } else {
            return null;
        }
    }

    public function verificaCreditosVencidos() {
        $sql = "SELECT * FROM contas_receber WHERE ativo = 'S' AND data_compensacao <= current_date() AND creditado = 'N' ";
        $query = $this->select($sql);
        $table = $query->fetchAll();
        if ($table) {
            return $table;
        } else {
            return null;
        }
    }

    public function marcaDesmarcaCreditado(Contasreceber $ContasReceber) {
        $creditou = $this->update2(
                'contas_receber',
                [
                    'creditado' => $ContasReceber->getCreditado() == 'S' ? "'N'" : "'S'",
                ],
                "codigo = " . $ContasReceber->getCodigo()
        );

        if ($creditou) {
            return true;
        } else {
            return false;
        }
    }
    
    public function atualizar(Contasreceber $contasReceber){
         try {

            $codigo = $contasReceber->getCodigo();
            $descricao = $contasReceber->getDescricao();
            $valor = $contasReceber->getValor();
            $observacao = $contasReceber->getObservacao();
            $dataCompensacao = $contasReceber->getDataCompensacao()->format('Y-m-d');
            $fixo = $contasReceber->getFixo();
            $codigoEntrada = $contasReceber->getCodigoEntrada();

            return $this->update2(
                            'contas_receber',
                            [
                                'descricao' => "'" . $descricao . "'",
                                'valor' => $valor,
                                'obs' => "'" . $observacao . "'",
                                'data_compensacao' => "'" . $dataCompensacao . "'",
                                'fixo' => "'" . $fixo . "'",
                                'codigo_entrada' => isset($codigoEntrada) ? $codigoEntrada : "null",
                            ],
                            "codigo = $codigo "
            );
        } catch (\Exception $e) {
            throw new \Exception("Erro na gravação de dados. " . $e, 500);
        }
    }

}
