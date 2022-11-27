<?php

namespace App\Models\Entidades;

use DateTime;

class Caixa {

    private $descricao;
    private $obs;
    private $saldo;
    private $data;
    private $codigoSaidaCabecalho;
    private $codigoEntrada;

    function getDescricao() {
        return $this->descricao;
    }

    function getObs() {
        return $this->obs;
    }

    function getSaldo() {
        return $this->saldo;
    }

    function setDescricao($descricao): void {
        $this->descricao = $descricao;
    }

    function setObs($obs): void {
        $this->obs = $obs;
    }

    function setSaldo($saldo): void {
        $this->saldo = $saldo;
    }

    function getData() {
        return $this->data;
    }

    function setData($data): void {
        $this->data = $data;
    }
    
    function getCodigoSaidaCabecalho() {
        return $this->codigoSaidaCabecalho;
    }

    function getCodigoEntrada() {
        return $this->codigoEntrada;
    }

    function setCodigoSaidaCabecalho($codigoSaidaCabecalho): void {
        $this->codigoSaidaCabecalho = $codigoSaidaCabecalho;
    }

    function setCodigoEntrada($codigoEntrada): void {
        $this->codigoEntrada = $codigoEntrada;
    }



}
