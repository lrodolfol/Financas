<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\DAO\ContasReceberDAO;
use App\Models\Entidades\ContasReceber;
use App\Models\Validacao\ContasReceberValidador;
use App\Models\DAO\CaixaDAO;
use App\Models\Entidades\Caixa;
use App\Lib\Paginacao;
use DateTime;

//use App\Models\Validacao\ProdutoValidador;

class ContasReceberController extends Controller {

    public function index() {
        $ContasReceberDAO = new ContasReceberDAO();

        $dataInicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : null;
        $dataFinal = isset($_POST['data_final']) ? $_POST['data_final'] : null;
        $palavra = isset($_POST['palavra']) ? $_POST['palavra'] : null;
        $paginaSelecionada = isset($_REQUEST['paginaSelecionada']) ? $_REQUEST['paginaSelecionada'] : 1;
        $totalPorPagina = isset($_REQUEST['totalPorPagina']) ? $_REQUEST['totalPorPagina'] : 5;
        $lucroReal = isset($_REQUEST['lucro_real']) ? 'S' : 'N';

        $listaContaReceber = $ContasReceberDAO->listaPaginacao("contas_receber", null, $dataInicial, $dataFinal, $palavra, $paginaSelecionada, $totalPorPagina, $lucroReal);
        $paginacao = new Paginacao($listaContaReceber, "contas_receber");
        //self::setViewParam('listaDebito', $DebitoDAO->listar(null, $dataInicial, $dataFinal, $palavra, $paginaSelecionada, $totalPorPagina));

        self::setViewParam('paginacao', $paginacao->criarLink());
        self::setViewParam('listaContaReceber', $listaContaReceber['resultado']);
        self::setViewParam('totalPorPagina', $totalPorPagina);

        $_REQUEST['data_inicial'] = $dataInicial;

        Sessao::limpaMensagem();

        $this->render('/contasReceber/index');
    }

    public function novo() {
        $this->render('/contasReceber/novo');
    }

    public function salvar($params) {
        $codigo = $params[0];
        $ContasReceber = new ContasReceber();
        $ContasReceberDAO = new ContasReceberDAO();
        $contasReceberValidador = new ContasReceberValidador();

        $ContasReceber->setDescricao($_POST['descricao']);
        $ContasReceber->setObservacao($_POST['observacao']);
        $ContasReceber->setValor($_POST['valor']);
        $ContasReceber->setDataCompensacao($_POST['data']);
        $ContasReceber->setAtivo($_POST['ativo']);
        $ContasReceber->setFixo(isset($_POST['fixo']) ? 'S' : 'N');
        $ContasReceber->setLucroReal(isset($_POST['lucro_real']) ? 'S' : 'N');
        $ContasReceber->setCreditado("N"); //POR PADRÃO O CREDITO CADASTRADO NUNCA É CREDITADO

        $periodo = $ContasReceber->getDataCompensacao();
        $mesEncerrado = $periodo->format('m');
        $anoEncerrado = $periodo->format('Y');
        $encerrado = $ContasReceberDAO->verificaMesEncerrado($mesEncerrado, $anoEncerrado);
        if (!$encerrado) {

            $mensagem = "";
            if ($contasReceberValidador->validaContasReceber($ContasReceber)) {
                $row = $ContasReceberDAO->salvar($ContasReceber);
                if ($row) {
                    //SE GRAVOU TUDO CORRETAMENTE, ENTÃO CARREGA A IMAGEM DE CREDITO
                    $rowImagemContasReceber = $this->carregaImagem("CONTAS_RECEBER", $ContasReceber->getCodigo());
                    Sessao::gravaMensagem("Conta a receber cadastrada com sucesso " . $rowImagemContasReceber);
                } else {
                    Sessao::gravaMensagem("Contas a receber não efetuada. Algum erro encontrado");
                }
            } else {
                $mensagem = $contasReceberValidador::$mensagem;
                Sessao::gravaMensagem($mensagem);
            }
        } else {
            Sessao::gravaErro("A competência " . $mesEncerrado . "/" . $anoEncerrado . " já esta encerrada! Contas a receber não inserido");
        }
        $this->render('/contasReceber/novo');
    }

    public function edicao($params) {
        $codigo = explode("99Fin", $params[0]);
        $codigo = $codigo[0];

        $ContasReceberDAO = new ContasReceberDAO();

        $dataInicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : null;
        $dataFinal = isset($_POST['data_final']) ? $_POST['data_final'] : null;
        $palavra = isset($_POST['palavra']) ? $_POST['palavra'] : null;
        $paginaSelecionada = isset($_REQUEST['paginaSelecionada']) ? $_REQUEST['paginaSelecionada'] : 1;
        $totalPorPagina = isset($_REQUEST['totalPorPagina']) ? $_REQUEST['totalPorPagina'] : 5;
        $lucroReal = isset($_REQUEST['lucro_real']) ? 'S' : 'N';

        $ContasReceber = $ContasReceberDAO->listar($codigo);

        if (!$ContasReceber) {
            Sessao::gravaMensagem("Crédito inexistente");
            $this->redirect('/contasReceber');
        }

        self::setViewParam('credito', $ContasReceber);

        $this->render('/contasReceber/edicao');

        Sessao::limpaMensagem();
    }

    public function atualizar() {
        $ContasReceber = new ContasReceber();
        //$ContasReceber->setAtivo(isset($_POST['ativo']) ? 'S' : 'N');
        $ContasReceber->setCodigo($_POST['codigo']);
        $ContasReceber->setDataCompensacao($_POST['data']);
        $ContasReceber->setDescricao($_POST['descricao']);
        $ContasReceber->setLucroReal(isset($_POST['lucro_real']) ? 'S' : 'N');
        $ContasReceber->setObservacao($_POST['observacao']);
        $ContasReceber->setValor($_POST['valor']);

        $ContasReceberDAO = new ContasReceberDAO();
        $atualizou = $ContasReceberDAO->atualizar($ContasReceber);
        if ($atualizou) {
            if ($_FILES['arquivo_imagem']) {
                $rowImagemCredito = $this->carregaImagem("CONTAS_RECEBER", $ContasReceber->getCodigo());
            }
            Sessao::gravaMensagem("Atualizado com sucesso. ");
        } else {
            Sessao::gravaMensagem("Ocorreu um erro ao atualizar. ");
        }
        $this->index();
    }
    
     public function excluir($params) {
        $codigo = $params[0];
        
        $ContasReceber = new ContasReceber();
        $ContasReceber->setCodigo($codigo);
        
        $ContasReceberDAO = new ContasReceberDAO();
        if($ContasReceberDAO->excluir($ContasReceber)) {
            Sessao::gravaMensagem("Crédito a receber excluido com sucesso!");
        }else{
            Sessao::gravaMensagem("Erro ao excluir o contas a receber!");
        }
        
        $this->index();
    }

}
