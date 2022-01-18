<?php

namespace App\Models\Entidades;

use DateTime;

class Debito {

    //VAR DO CABEÇALHO DO DÉBITO
    private $id;
    private $codigo;
    private $dataCompra;
    private $dataDebito;
    private $diaFechamento;
    private $diavencimento;
    private $valorTotal;
    private $estabelecimento;
    private $formaPagamento;
    private $qtdParcelas;
    private $ativo;
    private $obs;
    //VAR DOS ITENS DO DÉBITO
    private $codigoProduto;
    private $codigoCabecalho;
    private $produto;
    private $qtdProduto;
    private $valorProduto;
    private $ativoProduto;
    private $fixo;
    private $unidadeMedida;
    private $juros;
    private $desconto;
    private $atipico;

    function getAtipico() {
        return $this->atipico;
    }

    function setAtipico($atipico): void {
        $this->atipico = $atipico;
    }

    function getDesconto() {
        return $this->desconto;
    }

    function setDesconto($desconto): void {
        $this->desconto = $desconto;
    }
   
    function getJuros() {
        return $this->juros;
    }

    function setJuros($juros): void {
        $this->juros = $juros;
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

    function getDataCompra() {
        return $this->dataCompra;
    }

    function getDataDebito() {
        return $this->dataDebito;
    }

    function getValorTotal() {
        return $this->valorTotal;
    }

    function getEstabelecimento() {
        return $this->estabelecimento;
    }

    function getFormaPagamento() {
        return $this->formaPagamento;
    }

    function getQtdParcelas() {
        return $this->qtdParcelas;
    }

    function getAtivo() {
        return $this->ativo;
    }

    function getObs() {
        return $this->obs;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setDataCompra($dataCompra) {
        $this->dataCompra = $dataCompra;
    }

    function setDataDebito($dataDebito) {
        $this->dataDebito = $dataDebito;
    }

    function setValorTotal($valorTotal) {
        $this->valorTotal = $valorTotal;
    }

    function setEstabelecimento($estabelecimento) {
        $this->estabelecimento = $estabelecimento;
    }

    function setFormaPagamento($formaPagamento) {
        $this->formaPagamento = $formaPagamento;
    }

    function setQtdParcelas($qtdParcelas) {
        $this->qtdParcelas = $qtdParcelas;
    }

    function setAtivo($ativo) {
        $this->ativo = $ativo;
    }

    function setObs($obs) {
        $this->obs = $obs;
    }

    function getCodigoCabecalho() {
        return $this->codigoCabecalho;
    }

    function getProduto() {
        return $this->produto;
    }

    function getQtdproduto() {
        return $this->qtdProduto;
    }

    function getValorProduto() {
        return $this->valorProduto;
    }

    function getAtivoProduto() {
        return $this->ativoProduto;
    }

    function setCodigoCabecalho($codigoCabecalho): void {
        $this->codigoCabecalho = $codigoCabecalho;
    }

    function setProduto($produto): void {
        $this->produto = $produto;
    }

    function setQtdProduto($qtdProduto): void {
        $this->qtdProduto = $qtdProduto;
    }

    function setValorProduto($valorProduto): void {
        $this->valorProduto = $valorProduto;
    }

    function setAtivoProduto($ativoProduto): void {
        $this->ativoProduto = $ativoProduto;
    }

    function getCodigoProduto() {
        return $this->codigoProduto;
    }

    function setCodigoProduto($codigoProduto): void {
        $this->codigoProduto = $codigoProduto;
    }

    function getDiaFechamento() {
        return $this->$diaFechamento;
    }

    function setDiaFechamento($diaFechamento): void {
        $this->$diaFechamento = $diaFechamento;
    }

    function getDiaVencimento() {
        return $this->$diaVencimento;
    }

    function setDiaVencimento($diaVencimento): void {
        $this->$diaVencimento = $diaVencimento;
    }

    function getUnidadeMedida() {
        return $this->unidadeMedida;
    }

    function setUnidadeMedida($unidadeMedida) {
        $this->unidadeMedida = $unidadeMedida;
    }


    
}
