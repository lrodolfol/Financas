<?php

namespace App\Models\Validacao;

use \App\Models\Entidades\Contasreceber;
use App\Models\DAO\ContasReceberDAO;

class ContasReceberValidador {
    
    static $mensagem;

    public function validaContasReceber(Contasreceber $contaReceber) {
        $dataHoje = date('Y-m-d');
        if($contaReceber->getDataCompensacao()->format('Y-m-d') < date($dataHoje)) {
            self::$mensagem = "Data de compensação menor que a data atual";
            return false;
        }      
        
        if($contaReceber->getValor() < 0) {
            $mensagem = "O valor não deve ser negativo. Para isso, use o contas a pagar";
            return false;
        }
        
        return true;
    }

}
