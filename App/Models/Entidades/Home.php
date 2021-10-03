<?php

namespace App\Models\Entidades;

use DateTime;

class Home
{
    private $codigo;
    private $descricao;
    private $obs;
    private $ativo;
    private $saldo;

    function getCodigo() {
        return $this->codigo;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getObs() {
        return $this->obs;
    }

    function getAtivo() {
        return $this->ativo;
    }

    function getSaldo() {
        return $this->saldo;
    }

    function setCodigo($codigo): void {
        $this->codigo = $codigo;
    }

    function setDescricao($descricao): void {
        $this->descricao = $descricao;
    }

    function setObs($obs): void {
        $this->obs = $obs;
    }

    function setAtivo($ativo): void {
        $this->ativo = $ativo;
    }

    function setSaldo($saldo): void {
        $this->saldo = $saldo;
    }



}