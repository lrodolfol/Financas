<?php

namespace App\Models\Entidades;

class Email {

    private $destinatario;
    private $remetente;
    private $emailResposta;
    private $assunto;
    private $texto;
    
    function getDestinatario() {
        return $this->destinatario;
    }

    function getRemetente() {
        return $this->remetente;
    }

    function getEmailResposta() {
        return $this->emailResposta;
    }

    function getAssunto() {
        return $this->assunto;
    }

    function getTexto() {
        return $this->texto;
    }

    function setDestinatario($destinatario) {
        $this->destinatario = $destinatario;
    }

    function setRemetente($remetente) {
        $this->remetente = $remetente;
    }

    function setEmailResposta($emailResposta) {
        $this->emailResposta = $emailResposta;
    }

    function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    function setTexto($texto) {
        $this->texto = $texto;
    }

}
