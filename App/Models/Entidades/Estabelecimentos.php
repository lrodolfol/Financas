<?php

namespace App\Models\Entidades;

class Estabelecimentos
{
    private $id;
    private $codigo;
    private $nome;
    private $cnpj;
    private $tipoComercio;
    private $cidade;
    private $ativo;

    function getId() {
        return $this->id;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getNome() {
        return $this->nome;
    }

    function getCnpj() {
        return $this->cnpj;
    }

    function getTipoComercio() {
        return $this->tipoComercio;
    }

    function getCidade() {
        return $this->cidade;
    }

    function getAtivo() {
        return $this->ativo;
    }

    function setId($id): void {
        $this->id = $id;
    }

    function setCodigo($codigo): void {
        $this->codigo = $codigo;
    }

    function setNome($nome): void {
        $this->nome = $nome;
    }

    function setCnpj($cnpj): void {
        $this->cnpj = $cnpj;
    }

    function setTipoComercio($tipoComercio): void {
        $this->tipoComercio = $tipoComercio;
    }

    function setCidade($cidade): void {
        $this->cidade = $cidade;
    }

    function setAtivo($ativo): void {
        $this->ativo = $ativo;
    }

}