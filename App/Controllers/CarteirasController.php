<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\DAO\FormaPagamentoDAO;
use App\Models\DAO\CarteirasDAO;
use App\Models\Entidades\Carteira;
use App\Models\Validacao\CarteiraValidador;

class CarteirasController extends Controller {

    public function novo() {
        $formasPagamento = (new FormaPagamentoDAO())->carregaFormaPagamento("S", "DEBITAR");
        self::setViewParam('formasPagamento', $formasPagamento);

        $this->render('/carteiras/novo');
    }

    public function salvar() {
        $nomeCarteira = filter_var($_POST['nome_forma_pagamento'], FILTER_SANITIZE_STRING);
        $codFormaPagamento = filter_var($_POST['codigo_forma_pagamento'], FILTER_SANITIZE_NUMBER_INT);
        $valorCarteira = filter_var($_POST['valor_forma_pagamento'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $carteira = new Carteira();
        $carteira->setNome($nomeCarteira);
        $carteira->setFormaPagamento($codFormaPagamento);
        $carteira->setData(date('Y/m/d'));
        $carteira->setValor($valorCarteira);

        if (CarteiraValidador::validar($carteira)) {
            $CarteiraDAO = new CarteirasDAO();
            $salvo = $CarteiraDAO->salvar($carteira);
            if ($salvo) {
                Sessao::gravaMensagem("Carteira " . $nomeCarteira . " cadastrada com sucesso");
            } else {
                Sessao::gravaErro("Ocorreu um eror ao cadastrar nova carteira. Tente novamente");
            }
        } else {
            Sessao::gravaMensagem("Erro ao salvar cartira: " . CarteiraValidador::$msgErro);
        }



        $this->novo();
    }
  
    public function index() {
        
    }

    public function excluir($params) {
        
    }

    public function edicao($params) {
        
    }

    public function atualizar($params) {
        
    }

}
