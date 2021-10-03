<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\Entidades\FormaPagamento;
use App\Models\DAO\FormaPagamentoDAO;

class FormaPagamentoController extends Controller {

    public function novo() {
        $FormaPagamentoDAO = new FormaPagamentoDAO();
        self::setViewParam('novoCodigoFormaPagamento', $FormaPagamentoDAO->retornaNovoCodigoFormaPagamento());
        $this->render('/formaPagamento/novo');
    }

    public function salvar() {
        $FormaPagamentoDAO = new FormaPagamentoDAO();
        $FormaPagamento = new FormaPagamento();
        
        $FormaPagamento->setAtivo(isset($_POST['ativo']) ? 'S' : 'N');
        $FormaPagamento->setCodigo($_POST['codigo']);
        $FormaPagamento->setDescricao($_POST['descricao']);
        $FormaPagamento->setDiaFechamento($_POST['diaFechamento']);
        $FormaPagamento->setDiaVencimento($_POST['diaVencimento']);
        
        $rowFormaPagamento = $FormaPagamentoDAO->salvar($FormaPagamento);
         if ($rowFormaPagamento > 0) {
             Sessao::gravaMensagem("Nova forma de pagamento gravado com sucesso. Cod: " . $FormaPagamento->getCodigo());
         }else{
             Sessao::gravaMensagem("Ocorreu um eror ao gravar nova forma de pagamento");
         }
        $this->redirect('/formaPagamento/novo');
    }

    public function index() {
        $FormaPagamentoDAO = new FormaPagamentoDAO();

        self::setViewParam('listaFormaPagamento', $FormaPagamentoDAO->carregaFormaPagamento(null,"",null));

        $this->render('/formaPagamento/index');
    }
    
    public function excluir($params) {
        $codigo = $params[0];
        $FormaPagamento = new FormaPagamento();
        $FormaPagamento->setCodigo($codigo);

        $FormaPagamentoDAO = new FormaPagamentoDAO();
        $rowFormaPagamento = $FormaPagamentoDAO->excluir($FormaPagamento);

        if (!$rowFormaPagamento) {
            Sessao::gravaMensagem("Não foi possivel excluir a forma de pagamento");
        } else {
            Sessao::gravaMensagem("Forma de pagamento excluido com sucesso");
        }

        $this->index();
    }
    
    public function edicao($params) {
        $decode = base64_decode($params[0]);
        $explode = explode("77FIN", $decode);
        $codigo = $explode[0] - 1995;
        
        $FormaPagamentoDAO = new FormaPagamentoDAO();
        $FormaPagamento = new FormaPagamento();
        
        self::setViewParam('listaFormaPagamento', $FormaPagamentoDAO->carregaFormaPagamento(null,null, $codigo));
        
        $this->render("/formaPagamento/edicao");
    }
    
    public function atualizar($params) {
        $codigo = $params[0];
        
        $FormaPagamentoDAO = new FormaPagamentoDAO();
        $FormaPagamento = new FormaPagamento();
        
        $FormaPagamento->setCodigo($_POST['codigo']);
        $FormaPagamento->setAtivo(isset($_POST['ativo']) ? 'S' : 'N');
        $FormaPagamento->setDescricao($_POST['descricao']);
        $FormaPagamento->setDiaFechamento($_POST['diaFechamento']);
        $FormaPagamento->setDiaVencimento($_POST['diaVencimento']);
        
        $rowFormaPagamento = $FormaPagamentoDAO->atualizar($FormaPagamento);
        
        if($rowFormaPagamento) {
            Sessao::gravaMensagem("Forma de pagamento atualizado com sucesso");
        }else{
            Sessao::gravaMensagem("Erro ao atualizar a Forma de pagamento");
        }
        
        $this->index();
    }

   

}
