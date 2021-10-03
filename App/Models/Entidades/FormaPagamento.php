<?php

namespace App\Models\Entidades;

class FormaPagamento
{
    private $codigo;
    private $descricao;
    private $ativo;
    private $diaFechamento;
    private $diaVencimento;
    
    function getCodigo() {
        return $this->codigo;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getAtivo() {
        return $this->ativo;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setAtivo($ativo) {
        $this->ativo = $ativo;
    }

    function getDiaFechamento() {
        return $this->diaFechamento;
    }

    function getDiaVencimento() {
        return $this->diaVencimento;
    }

    function setDiaFechamento($diaFechamento): void {
        $this->diaFechamento = $diaFechamento;
    }

    function setDiaVencimento($diaVencimento): void {
        $this->diaVencimento = $diaVencimento;
    }

    
}