<?php

namespace App\Models\Entidades;

use DateTime;

class Usuario {

    private $id;
    private $nome;
    private $sobreNome;
    private $senha;
    private $codigo;
    private $idade;
    private $email;
    private $recebeEmail;
    private $desenvolvedor;
    private $dataCadastro;
    private $dataNascimento;

    function getDesenvolvedor() {
        return $this->desenvolvedor;
    }

    function getDataCadastro() {
        return $this->dataCadastro;
    }

    function setDataCadastro($dataCadastro) {
        $this->dataCadastro = $dataCadastro;
    }

    function setDesenvolvedor($desenvolvedor) {
        $this->desenvolvedor = $desenvolvedor;
    }

    function getRecebeEmail() {
        return $this->recebeEmail;
    }

    function setRecebeEmail($recebeEmail) {
        $this->recebeEmail = $recebeEmail;
    }

    function getSobreNome() {
        return $this->sobreNome;
    }

    function setSobreNome($sobreNome) {
        $this->sobreNome = $sobreNome;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function getIdade() {
        return $this->idade;
    }

    function setIdade($idade) {
        $this->idade = $idade;
    }

    function getEmail() {
        return $this->email;
    }

    function setEmail($email) {
        $this->email = $email;
    }
    
    function getDataNascimento() {
        return new DateTime($this->dataNascimento);
    }

    function setDataNascimento($dataNascimento): void {
        $this->dataNascimento = $dataNascimento;
    }

}
