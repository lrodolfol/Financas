<?php

namespace App\Models\Entidades;

use DateTime;

class ContasReceber
{
    private $id;
    private $codigo;
    private $descricao;
    private $observacao;
    private $valor;
    private $ativo;
    private $dataCompensacao;
    private $fixo;
    private $lucroReal;
    private $creditado;
    private $codigoEntrada;
    
    function getCodigoEntrada() {
        return $this->codigoEntrada;
    }

    function setCodigoEntrada($codigoEntrada): void {
        $this->codigoEntrada = $codigoEntrada;
    }
    
    function getCreditado() {
        return $this->creditado;
    }

    function setCreditado($creditado): void {
        $this->creditado = $creditado;
    }
	
    function getlucroReal() {
        return $this->lucroReal;
    }

    function setLucroReal($lucroReal): void {
        $this->lucroReal = $lucroReal;
    }
    
    function getFixo() {
        return $this->fixo;
    }

    function setFixo($fixo): void {
        $this->fixo = $fixo;
    }

    function getId() {
        return $this->id;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getObservacao() {
        return $this->observacao;
    }

    function getValor() {
        return $this->valor;
    }

    function getAtivo() {
        return $this->ativo;
    }

    function getDataCompensacao() {
        return new DateTime($this->dataCompensacao);
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setObservacao($observacao) {
        $this->observacao = $observacao;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setAtivo($ativo) {
        $this->ativo = $ativo;
    }

    function setDataCompensacao($dataCompensacao) {
        $this->dataCompensacao = $dataCompensacao;
    }

}