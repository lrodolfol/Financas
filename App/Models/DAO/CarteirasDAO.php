<?php

namespace App\Models\DAO;

use App\Models\Entidades\Carteira;

class CarteirasDAO extends BaseDAO {

    public function salvar(Carteira $carteira) {
        $nome = $carteira->getNome();
        $data = $carteira->getData();
        $valor = $carteira->getValor();
        $formaPagamento = $carteira->getFormaPagamento();

        return $this->insert(
                        $carteira::$table, "nome,:data,:valor,:forma_pagamento", [
                    ':nome' => "'" . $nome . "'",
                    ':data' => "'" . $data . "'",
                    ':valor' => $valor,
                    ':forma_pagamento' => $formaPagamento,
                        ]
        );
    }

    public function retornaCarteiras() {
        $sql = "SELECT * FROM " . Carteira::$table . " ";
        return $this->retornaObject($sql);
    }

    public function registrarMovimentacao(Carteira $carteira) {
        $data = $carteira->getData();
        $valor = $carteira->getValor();
        $formaPagamento = $carteira->getFormaPagamento();
        $codEntrada = $carteira->getCodEntrada() ? $carteira->getCodEntrada() : 'null';
        $codSaidaCabecalho = $carteira->getCodSaidaCabecalho() ? $carteira->getCodSaidaCabecalho() : 'null';
        $observacao = $carteira->getObservacao();

        $sql = " INSERT INTO carteiras (nome,data,valor,forma_pagamento,cod_saida_cabecalho,cod_entrada, observacao) "
        . " SELECT nome, '{$data}', valor ";
        $sql .= $codSaidaCabecalho ? ' - ' : ' + '; //SE TIVER COD DE SAIDA ENTAO SUBTRAI. SENAO SOMA, POIS É UMA ENTRADA
        $sql .= $valor . ", {$formaPagamento}, {$codSaidaCabecalho}, {$codEntrada}, '{$observacao}' "
        . " FROM {$carteira::$table} WHERE forma_pagamento = {$formaPagamento} ORDER BY id DESC LIMIT 1  ";

        return $this->insertSubquery($sql);

    }

}
