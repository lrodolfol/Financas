<?php

namespace App\Models\Entidades;

use DateTime;

class Extrato
{
    private $id;
    private $data;
    private $descricao;
    private $obsevacao;
    private $valor;
    private $tipo;
    
    function getId() {
        return $this->id;
    }

    function getData() {
        return $this->data;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getObsevacao() {
        return $this->obsevacao;
    }

    function getValor() {
        return $this->valor;
    }

    function getTipo() {
        return $this->tipo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setObsevacao($obsevacao) {
        $this->obsevacao = $obsevacao;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }


}