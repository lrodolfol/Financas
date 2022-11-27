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
        $carteiras = (new CarteirasDAO())->retornaCarteirasDistinctValor();
        self::setViewParam('carteiras', $carteiras);

        $this->render('carteiras/index');
    }

    public function relatorio() {
        $dados = new \stdClass();
        try {
            foreach ($_POST as $key => $value) {
                $dados->$key = $value;
            }

            $carteiras[] = new Carteira();
            $cont = 0;
            foreach ($dados as $key => $value) {
                if($key == 'data_inicio' || $key == 'data_final'){
                    continue;
                }
                $carteiras[$cont] = new Carteira();
                $carteiras[$cont]->setNome($key);
                $cont++;
            }
            
            $relatCarteiras = (new CarteirasDAO())->relatorio($carteiras);
            self::setViewParam('relatCarteiras', $relatCarteiras);
            
        } catch (Exception $ex) {
            
        } finally {
            $this->index();
        }
    }

    public function excluir($params) {
        
    }

    public function edicao($params) {
        
    }

    public function atualizar($params) {
        
    }

    //AQUI TO USANDO O MESMO METODO PARA REQ GET E POST.
    public function transferencia($params) {
        if (isset($_POST) && (!empty($_POST))) {

            $carteiraCredito = new Carteira();
            $carteiraCredito->setId($_POST['carteira_credito']);
            $carteiraCredito->setCodEntrada(null);
            $carteiraCredito->setCodSaidaCabecalho(null);
            $carteiraCredito->setData($_POST['data_transferencia']);
            $carteiraCredito->setFormaPagamento(null);
            $carteiraCredito->setNome(null);
            $carteiraCredito->setObservacao("Transferência entre carteiras de R$ " . number_format($_POST['valor_transferencia'], 2, ',', '.') . "{$_POST['observacao']}");
            $carteiraCredito->setValor($_POST['valor_transferencia']);

            $carteiraDebito = clone $carteiraCredito;
            $carteiraDebito->setId($_POST['carteira_debito']);

            //VALIDA DATA
            if ($carteiraCredito->getData() > DATE('Y/m/d')) {
                Sessao::gravaErro("Data de transferência não pode ser maior que hoje!");
            } else {
                $CarteiraDAO = new CarteirasDAO();
                $result = $CarteiraDAO->transferencia($carteiraCredito, $carteiraDebito);

                if ($result) {
                    Sessao::gravaMensagem("Transferência realizada com sucesso");
                } else {
                    Sessao::gravaErro("Erro ao realizar a transferência. Tente novamente");
                }
            }

            $carteiras = (new CarteirasDAO())->retornaCarteirasDistinctValor();
            self::setViewParam('carteiras', $carteiras);

            $this->render('/carteiras/transferencia');

            return;
        }

        $carteiras = (new CarteirasDAO())->retornaCarteirasDistinctValor();
        self::setViewParam('carteiras', $carteiras);
        $this->render('/carteiras/transferencia');
    }

}
