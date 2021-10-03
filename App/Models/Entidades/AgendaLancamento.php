<?php

namespace App\Models\Entidades;

class AgendaLancamento {

    //VAR DO CABEÇALHO DO DÉBITO
    private $id;
    private $codigo;
    private $dataCompra;
    private $dataDebito;
    private $diaFechamento;
    private $diaVencimento;
    private $valorTotal;
    private $estabelecimento;
    private $formaPagamento;
    private $qtdParcelas;
    private $ativo;
    private $debitado;
    private $obs;
    private $codigoDebito;
    private $numeroParcela;
    //VAR DOS ITENS DO DÉBITO
    private $codigoProduto;
    private $codigoCabecalho;
    private $produto;
    private $qtdProduto;
    private $valorProduto;
    private $ativoProduto;
    private $unidadeMedida;
    private $juros;
    
    function getJuros() {
        return $this->juros;
    }

    function setJuros($juros): void {
        $this->juros = $juros;
    }
    
    function getNumeroParcela() {
        return $this->numeroParcela;
    }

    function setNumeroParcela($numeroParcela) {
        $this->numeroParcela = $numeroParcela;
    }
    
    function getCodigoDebito() {
        return $this->codigoDebito;
    }

    function setCodigoDebito($codigoDebito): void {
        $this->codigoDebito = $codigoDebito;
    }

    function getDebitado() {
        return $this->debitado;
    }

    function setDebitado($debitado): void {
        $this->debitado = $debitado;
    }

    function setUnidadeMedida($unidadeMedida): void {
        $this->unidadeMedida = $unidadeMedida;
    }
    
    function getUnidadeMedida() {
        return $this->unidadeMedida;
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
        return $this->diaFechamento;
    }

    function getDiaVencimento() {
        return $this->diaVencimento;
    }

    function setDiaFechamento($diaFechamento) {
        $this->diaFechamento = $diaFechamento;
    }

    function setDiaVencimento($diaVencimento) {
        $this->diaVencimento = $diaVencimento;
    }

}
