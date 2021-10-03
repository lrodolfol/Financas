<?php

namespace App\Models\Entidades;

use DateTime;

class RelatoErro
{
    private $id;
    private $codigo;
    private $titlo;
    private $texto;
    private $usuario;
    private $data;
    
    function getId() {
        return $this->id;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getTitlo() {
        return $this->titlo;
    }

    function getTexto() {
        return $this->texto;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function getData() {
        return $this->data;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setTitlo($titlo) {
        $this->titlo = $titlo;
    }

    function setTexto($texto) {
        $this->texto = $texto;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function setData($data) {
        $this->data = $data;
    }

}