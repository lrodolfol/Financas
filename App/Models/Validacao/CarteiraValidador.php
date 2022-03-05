<?php

namespace App\Models\Validacao;

use App\Models\Entidades\Carteira;
use App\Models\DAO\CarteirasDAO;

class CarteiraValidador {

    static $msgErro;

    public static function validar(Carteira $carteira) {
        $carteiras = (new CarteirasDAO())->retornaCarteiras();

        foreach ($carteiras as $key => $value) {
            if ($value->nome == $carteira->getNome()) {
                self::$msgErro = "Nome de carteira já existe";
                return false;
            }
            if ($value->forma_pagamento == $carteira->getFormaPagamento()) {
                self::$msgErro = "Forma de pagamento já vinculado";
                return false;
            }
        }

        return true;
    }

}
