<?php

namespace App\Models\DAO;

use App\Models\Entidades\FormaPagamento;

class FormaPagamentoDAO extends BaseDAO {

    public function carregaFormaPagamento($SomenteAtivo, $debitaAgeda, $codigo = null) {
        $sql = "SELECT * FROM formas_pagamento WHERE id > 0 ";
        if ($codigo) {
            $sql .= " AND codigo = " . $codigo . " ";
        }
        if ($SomenteAtivo) {
            $sql .= " AND ativo = 'S' ";
        }
        if (strtoupper($debitaAgeda) == "DEBITAR") {
            $sql .= " AND ( (dia_vencimento IS NULL AND dia_fechamento IS NULL) or (dia_vencimento = '' AND dia_fechamento = '') )";
        }
        $resultado = $this->select($sql);
        return $resultado->fetchAll(\PDO::FETCH_CLASS, FormaPagamento::class);
    }
    
    public function sqlExporta() {
        $sql = "SELECT * FROM formas_pagamento WHERE id > 0 ";
        $resultado = $this->select($sql);
        return $resultado->fetchAll();
    }

    public function retornaNovoCodigoFormaPagamento() {
        $sql = "SELECT * FROM formas_pagamento ORDER BY codigo DESC LIMIT 1 ";
        $row = $this->RetornaDado($sql);
        if ($row) {
            return $row["codigo"] + 1;
        } else {
            return 1;
        }
    }

    public function retornaDataFechamento($codigo) {
        if (!$codigo) {
            return null;
        } else {
            $sql = "SELECT dia_fechamento, dia_vencimento FROM formas_pagamento WHERE codigo = $codigo ";
            $row = $this->RetornaDado($sql);
            return $row;
        }
    }

    public function salvar(FormaPagamento $FormaPagamento) {
        $codigo = $FormaPagamento->getCodigo();
        $descricao = $FormaPagamento->getDescricao();
        $ativo = $FormaPagamento->getAtivo();
        $diaFechamento = $FormaPagamento->getDiaFechamento();
        $diaVencimento = $FormaPagamento->getDiaVencimento();

        return $this->insert(
                        'formas_pagamento',
                        "codigo,:descricao,:ativo,:dia_fechamento,:dia_vencimento",
                        [
                            ':codigo' => $codigo,
                            ':descricao' => "'" . $descricao . "'",
                            ':ativo' => "'" . $ativo . "'",
                            ':dia_fechamento' => "'" . $diaFechamento . "'",
                            ':dia_vencimento' => "'" . $diaVencimento . "'",
                        ]
        );
    }

    public function excluir(FormaPagamento $FormaPagamento) {
        try {
            $codigo = $FormaPagamento->getCodigo();

            return $this->delete('formas_pagamento', "codigo = $codigo");
        } catch (Exception $e) {
            throw new \Exception("Erro ao deletar", 500);
        }
    }

    public function atualizar(FormaPagamento $FormaPagamento) {
        try {
            $codigo = $FormaPagamento->getCodigo();
            if (!$codigo) {
                return false;
            }

            $diaFechamento = $FormaPagamento->getDiaFechamento();
            $diaVencimento = $FormaPagamento->getDiaVencimento();
            $ativo = $FormaPagamento->getAtivo();
            $descricao = $FormaPagamento->getDescricao();

            return $this->update2(
                            'formas_pagamento',
                            [
                                'descricao' => "'" . $descricao . "'",
                                'ativo' => "'" . $ativo . "'",
                                'dia_fechamento' => "'" . $diaFechamento . "'",
                                'dia_vencimento' => "'" . $diaVencimento . "'",
                            ],
                            "codigo = $codigo"
            );
        } catch (Exception $ex) {
            
        }
    }

}
