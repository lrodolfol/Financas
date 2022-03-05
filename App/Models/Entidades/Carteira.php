<?php

namespace App\Models\Entidades;

use DateTime;

class Carteira {

    private $id;
    private $nome;
    private $data;
    private $valor;
    private $formaPagamento;
    private $codSaidaCabecalho;
    private $codEntrada;
    private $observacao;
    static $table = 'carteiras';

    function getObservacao() {
        return $this->observacao;
    }

    function setObservacao($observacao) {
        $this->observacao = $observacao;
    }

    function getCodSaidaCabecalho() {
        return $this->codSaidaCabecalho;
    }

    function getCodEntrada() {
        return $this->codEntrada;
    }

    function setCodSaidaCabecalho($codSaidaCabecalho) {
        $this->codSaidaCabecalho = $codSaidaCabecalho;
    }

    function setCodEntrada($codEntrada) {
        $this->codEntrada = $codEntrada;
    }

    function getId() {
        return $this->id;
    }

    function getNome() {
        return $this->nome;
    }

    function getData() {
        return $this->data;
    }

    function getValor() {
        return $this->valor;
    }

    function getFormaPagamento() {
        return $this->formaPagamento;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setFormaPagamento($formaPagamento) {
        $this->formaPagamento = $formaPagamento;
    }

}
