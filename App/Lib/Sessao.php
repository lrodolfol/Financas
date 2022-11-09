<?php

namespace App\Lib;

class Sessao {

    public static function gravaMensagem($mensagem) {
        $t = $mensagem;
        $_SESSION['mensagem'] = $mensagem;
    }

    public static function limpaMensagem() {
        unset($_SESSION['mensagem']);
    }

    public static function retornaMensagem() {
        //$t = $_SESSION['mensagem'];
        return (isset($_SESSION['mensagem'])) ? $_SESSION['mensagem'] : "";
    }

    public static function gravaFormulario($form) {
        $_SESSION['form'] = $form;
    }

    public static function limpaFormulario() {
        unset($_SESSION['form']);
    }

    public static function retornaValorFormulario($key) {
        return (isset($_SESSION['form'][$key])) ? $_SESSION['form'][$key] : "";
    }

    public static function existeFormulario() {
        return (isset($_SESSION['form'])) ? $_SESSION['form'] : "";
    }

    public static function gravaErro($erros) {
        $_SESSION['erro'] = $erros;
    }

    public static function retornaErro() {
        return (isset($_SESSION['erro'])) ? $_SESSION['erro'] : false;
    }

    public static function limpaErro() {
        unset($_SESSION['erro']);
    }

    public static function gravaUsuario($user) {
        $_SESSION['user'] = $user;
    }

    public static function retornaUsuario() {
        return (isset($_SESSION['user'])) ? $_SESSION['user'] : false;
    }

    public static function limpaUsuario() {
        unset($_SESSION['user']);
    }

    public static function gravaCodigoUsuario($user) {
        $_SESSION['codigoUser'] = $user;
    }

    public static function retornaCodigoUsuario() {
        return (isset($_SESSION['codigoUser'])) ? $_SESSION['codigoUser'] : false;
    }

    public static function limpaCodigoUsuario() {
        unset($_SESSION['codigoUser']);
    }

    public static function gravaCodigo($codigo) {
        $_SESSION['codigo'] = $codigo;
    }

    public static function limpaCodigo() {
        unset($_SESSION['codigo']);
    }

    public static function retornaCodigo() {
        return ($_SESSION['codigo']) ? $_SESSION['codigo'] : "";
    }

    public static function gravaValor($valor) {
        $_SESSION['valor'] = $valor;
    }

    public static function limpaValor() {
        unset($_SESSION['valor']);
    }

    public static function retornaValor() {
        return ($_SESSION['valor']) ? $_SESSION['valor'] : "";
    }

    public static function gravaEmail($email) {
        $_SESSION['email'] = $email;
    }

    public static function limpaEmail() {
        unset($_SESSION['email']);
    }

    public static function retornaEmail() {
        return ($_SESSION['email']) ? $_SESSION['email'] : "";
    }

    public static function gravaRecebeEmail($recebeEmail) {
        $_SESSION['recebeEmail'] = $recebeEmail;
    }

    public static function limpaRecebeEmail() {
        unset($_SESSION['recebeEmail']);
    }

    public static function retornaRecebeEmail() {
        return ($_SESSION['recebeEmail']) ? $_SESSION['recebeEmail'] : "";
    }

    public static function gravaQtdDebitoVencido($qtdDebitoVencido) {
        $_SESSION['qtdDebitoVencido'] = $qtdDebitoVencido;
    }

    public static function limpaQtdDebitoVencido() {
        unset($_SESSION['qtdDebitoVencido']);
    }

    public static function retornaQtdDebitoVencido() {
        return (isset($_SESSION['qtdDebitoVencido'])) ? $_SESSION['qtdDebitoVencido'] : "";
    }
    
    public static function gravaQtdCreditoVencido($qtdCreditoVencido) {
        $_SESSION['qtdCreditoVencido'] = $qtdCreditoVencido;
    }

    public static function limpaQtdCreditoVencido() {
        unset($_SESSION['qtdCreditoVencido']);
    }

    public static function retornaQtdCreditoVencido() {
        return (isset($_SESSION['qtdCreditoVencido'])) ? $_SESSION['qtdCreditoVencido'] : "";
    }

    public static function gravaDesenvolvedor($desenvolvedor) {
        $_SESSION['desenvolvedor'] = $desenvolvedor;
    }

    public static function limpaDesenvolvedor() {
        unset($_SESSION['desenvolvedor']);
    }

    public static function retornaDesenvolvedor() {
        return ($_SESSION['desenvolvedor']) ? $_SESSION['desenvolvedor'] : "";
    }

    public static function gravaDataCadastroUsuario($dataCadastroUsuario) {
        $_SESSION['dataCadastroUsuario'] = $dataCadastroUsuario;
    }

    public static function limpaDataCadastroUsuario() {
        unset($_SESSION['dataCadastroUsuario']);
    }

    public static function retornaDataCadastroUsuario() {
        return ($_SESSION['dataCadastroUsuario']) ? $_SESSION['dataCadastroUsuario'] : "";
    }

}
