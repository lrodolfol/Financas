<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\DAO\CreditoDAO;
use App\Models\DAO\ContasReceberDAO;
use App\Models\DAO\CaixaDAO;
use App\Models\DAO\CarteirasDAO;
use App\Models\Entidades\Credito;
use App\Models\Entidades\ContasReceber;
use App\Models\Entidades\Caixa;
use App\Models\Entidades\Carteira;
use App\Lib\Paginacao;
use DateTime;

//use App\Models\Validacao\ProdutoValidador;

class CreditoController extends Controller {

    public function index() {
        $CreditoDAO = new CreditoDAO();

        $dataInicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : null;
        $dataFinal = isset($_POST['data_final']) ? $_POST['data_final'] : null;
        $palavra = isset($_POST['palavra']) ? $_POST['palavra'] : null;
        $paginaSelecionada = isset($_REQUEST['paginaSelecionada']) ? $_REQUEST['paginaSelecionada'] : 1;
        $totalPorPagina = isset($_REQUEST['totalPorPagina']) ? $_REQUEST['totalPorPagina'] : 5;
        $lucroReal = isset($_REQUEST['lucro_real']) ? 'S' : 'N';

        //self::setViewParam('listaDebito', $CreditoDAO->listar(null, $dataInicial, $dataFinal, $palavra));

        $listaCredito = $CreditoDAO->listar(null, $dataInicial, $dataFinal, $palavra, $paginaSelecionada, $totalPorPagina, $lucroReal);
        $paginacao = new Paginacao($listaCredito, "credito");
        //self::setViewParam('listaDebito', $DebitoDAO->listar(null, $dataInicial, $dataFinal, $palavra, $paginaSelecionada, $totalPorPagina));

        self::setViewParam('paginacao', $paginacao->criarLink());
        self::setViewParam('listaCredito', $listaCredito['resultado']);
        self::setViewParam('totalPorPagina', $totalPorPagina);

        $_REQUEST['data_inicial'] = $dataInicial;

        $this->render('/credito/index');

        Sessao::limpaMensagem();
    }

    public function novo() {
        $carteiras = (new CarteirasDAO())->retornaCarteirasDistinctValor();
        self::setViewParam('carteiras', $carteiras);
        $this->render('/credito/novo');
    }

    public function salvar() {
        $Credito = new Credito();

        $postArray = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $Credito->setDescricao($postArray['descricao']);
        $Credito->setObservacao($postArray['observacao']);
        $Credito->setAtivo($postArray['ativo']);
        $Credito->setFixo(isset($postArray['fixo']) ? 'S' : 'N');
        $Credito->setLucroReal(isset($postArray['lucroReal']) ? 'S' : 'N');
        $Credito->setCodigo($postArray['codigo']);
        $Credito->setValor($postArray['valor']);
        $Credito->setDataCadastro($postArray['data']);

        $CreditoDAO = new CreditoDAO();

        //VERIFICA SE O MÊS JÁ FOI ENCERRADO
        $periodo = new DateTime($Credito->getDataCadastro());
        $mesEncerrado = $periodo->format('m');
        $anoEncerrado = $periodo->format('Y');
        $encerrado = $CreditoDAO->verificaMesEncerrado($mesEncerrado, $anoEncerrado);
        if (!$encerrado) {

            //INSERE OS VALORES NA ENTRADA
            $rowCredito = $CreditoDAO->salvar($Credito);

            if ($rowCredito > 0) {
                //SE É CREDITO FIXO, ENTÃO JÁ JOGA PARA O CONTAS A RECEBER DA PRÓXIMA COMPETÊCIA.
                if ($Credito->getFixo() == 'S') {
                    $ContasReceber = new ContasReceber();
                    $ContasReceberDAO = new ContasReceberDAO();

                    $ContasReceber->setAtivo($Credito->getAtivo());
                    $ContasReceber->setFixo($Credito->getFixo());
                    $ContasReceber->setDescricao($Credito->getDescricao());
                    $ContasReceber->setObservacao($Credito->getObservacao());
                    $ContasReceber->setObservacao($Credito->getlucroReal());
                    $ContasReceber->setValor($Credito->getValor());
                    $ContasReceber->setCreditado("N");
                    $ContasReceber->setLucroReal($Credito->getlucroReal());
                    $dataCompensacao = new DateTime($Credito->getDataCadastro()); //PARA A DATA DE CADASTRO
                    $soma1MesData = new \DateInterval("P1M");
                    $dataCompensacao->add($soma1MesData);
                    $ContasReceber->setDataCompensacao($dataCompensacao->format("Y-m-d"));

                    $ContasReceberDAO = new ContasReceberDAO();
                    $ContasReceberDAO->salvar($ContasReceber);
                }



                $Caixa = new Caixa();
                $Caixa->setDescricao($Credito->getObservacao());
                $Caixa->setObs($Credito->getObservacao());
                $Caixa->setSaldo($Credito->getValor());
                $Caixa->setData($Credito->getDataCadastro());
                $Caixa->setCodigoEntrada($Credito->getCodigo());
                //INSERE O VALOR NO CAIXA
                $CaixaDAO = new CaixaDAO();
                $rowCaixa = $CaixaDAO->salvar($Caixa);
                if ($rowCaixa > 0) {
                    //SE GRAVOU TUDO CORRETAMENTE, ENTÃO MANDA E-MAIL SOBRE A OPERAÇÃO
                    if (Sessao::retornaRecebeEmail() == "S") {
                        $CaixaDAO->mandaEmail($Caixa);
                    }
                    //SE GRAVOU TUDO CORRETAMENTE, ENTÃO CARREGA A IMAGEM DE CREDITO
                    $rowImagemCredito = $this->carregaImagem("CREDITO", $Credito->getCodigo());
                    Sessao::gravaMensagem("Crédito gravado com sucesso. " . $rowImagemCredito . " " . $email);

                    //SE GRAVOU TUDO CORRETAMENTE, ENTÃO JOGA O CREDITO NA CARTEIRA CORRETA
                    $CarteiraDAO = new CarteirasDAO();
                    $carteiraCredito = new Carteira();
                    
                    $carteiraCredito->setId($postArray['carteira_credito']);
                    $carteiraCredito->setCodEntrada($Credito->getCodigo());
                    $carteiraCredito->setCodSaidaCabecalho(null);
                    $carteiraCredito->setData($Credito->getDataCadastro());
                    $carteiraCredito->setFormaPagamento(null);
                    $carteiraCredito->setNome(null);
                    $carteiraCredito->setObservacao("Crédito de " . number_format($Credito->getValor(), 2, ',', '.') . " {$Credito->getDescricao()}");
                    $carteiraCredito->setValor($Credito->getValor());
                    
                    $CarteiraDAO->creditar($carteiraCredito);
                } else {
                    Sessao::gravaErro("Ocorreu um eror ao inserir o crédito na caixa" . " " . $email);
                }
            } else {
                Sessao::gravaErro("Ocorreu um eror ao cadastrar novo crédito" . " " . $email);
            }
        } else {
            Sessao::gravaErro("A competência " . $mesEncerrado . "/" . $anoEncerrado . " já esta encerrada! Crédio não inserido");
        }
        $this->redirect('/credito/novo');
    }

    public function edicao($params) {

        $codigo = explode("99Fin", $params[0]);
        $codigo = $codigo[0];

        $CreditoDAO = new CreditoDAO();

        $dataInicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : null;
        $dataFinal = isset($_POST['data_final']) ? $_POST['data_final'] : null;
        $palavra = isset($_POST['palavra']) ? $_POST['palavra'] : null;
        $paginaSelecionada = isset($_REQUEST['paginaSelecionada']) ? $_REQUEST['paginaSelecionada'] : 1;
        $totalPorPagina = isset($_REQUEST['totalPorPagina']) ? $_REQUEST['totalPorPagina'] : 5;
        $lucroReal = isset($_REQUEST['lucro_real']) ? 'S' : 'N';

        $Credito = $CreditoDAO->listar($codigo, $dataInicial, $dataFinal, $palavra, $paginaSelecionada, $totalPorPagina, $lucroReal);

        if (!$Credito) {
            Sessao::gravaMensagem("Crédito inexistente");
            $this->redirect('/credito');
        }

        self::setViewParam('credito', $Credito);

        $this->render('/credito/edicao');

        Sessao::limpaMensagem();
    }

    public function atualizar() {
        $Credito = new Credito();
        $CaixaDAO = new CaixaDAO();
        $CreditoDAO = new CreditoDAO();

        $Credito->setCodigo($_POST['codigo']);

        $Credito->setDescricao($_POST['descricao']);
        $Credito->setObservacao($_POST['observacao']);
        $Credito->setValor($_POST['valor']);
        $Credito->setDataCadastro($_POST['data']);
        $Credito->setAtivo($_POST['ativo']);
        $Credito->setFixo(isset($_POST['fixo']) ? 'S' : 'N');
        //$valorCreditoAnterior = $CaixaDAO->retornaSaldo("ENTRADA", $Credito->getCodigo());
        $valorCreditoAnterior = $CreditoDAO->retornaUltimoSaldo($Credito->getCodigo());

        Sessao::gravaFormulario($_POST);

        $rowCredito = $CreditoDAO->atualizar($Credito);

        if ($rowCredito) {

            //SE HOUVE ALTERAÇÃO NO VALOR DO CRÉDITO, ESSE VALOR É ATUALIZADO NO CAIXA
            if ($valorCreditoAnterior == $Credito->getValor()) {
                
            } else {
                if ($Credito->getValor() > $valorCreditoAnterior) {
                    $valorCreditoAtualizado = $Credito->getValor() - $valorCreditoAnterior;
                } else {
                    $valorCreditoAtualizado = ($valorCreditoAnterior - $Credito->getValor()) * -1;
                }
                $Caixa = new Caixa();
                $Caixa->setDescricao("Correção da entrada cod: " . $Credito->getCodigo() . ". ");
                $Caixa->setObs($Credito->getObservacao());
                //die();
                //$Caixa->setSaldo($Credito->getValor());
                $Caixa->setSaldo($valorCreditoAtualizado);
                $Caixa->setData($Credito->getDataCadastro());
                $Caixa->setCodigoEntrada($Credito->getCodigo());
                $rowCaixa = $CaixaDAO->salvar($Caixa);
            }
        } else {
            
        }

        if ($_FILES['arquivo_imagem']) {
            $rowImagemCredito = $this->carregaImagem("CREDITO", $Credito->getCodigo());
        }

        if ($rowCredito || $rowImagemCredito) {
            Sessao::gravaMensagem("Atualizado com sucesso. " . $rowImagemCredito . " ");
        }

        $this->redirect('/credito');
    }

    public function exclusao($params) {
        $id = $params[0];

        $produtoDAO = new ProdutoDAO();

        $produto = $produtoDAO->listar($id);

        if (!$produto) {
            Sessao::gravaMensagem("Produto inexistente");
            $this->redirect('/produto');
        }

        self::setViewParam('produto', $produto);

        $this->render('/produto/exclusao');

        Sessao::limpaMensagem();
    }

    public function excluir($params) {
        $codigo = $params[0];

        $Credito = new Credito();
        $Credito->setCodigo($codigo);

        $CreditoDAO = new CreditoDAO();

        $rowCredito = $CreditoDAO->excluir($Credito);
        if ($rowCredito) {
            $CaixaDAO = new CaixaDAO();
            $Caixa = new Caixa();
            $Caixa->setCodigoEntrada($codigo);
            $Caixa->setData($Credito->getDataCadastro());
            $Caixa->setDescricao($Credito->getDescricao());
            $Caixa->setObs($Credito->getObservacao());
            $Caixa->setSaldo($Credito->getValor() * -1);

            $rowCaixa = $CaixaDAO->salvar($Caixa);
            if ($rowCaixa) {
                if (Sessao::retornaRecebeEmail() == "S") {
                    $CaixaDAO->mandaEmail($Caixa);
                }
                $this->deletaImagem("CREDITO", $Credito->getCodigo());
                Sessao::gravaMensagem("Crédito excluido com sucesso!");
            } else {
                Sessao::gravaMensagem("Crédito excluido com eror ao atualizar o caixa.");
            }
        } else {
            Sessao::gravaMensagem("Crédito inexistente.");
        }
        $this->redirect('/Credito');
    }

    public function detalhes($params) {
        $codigo = $params[0];
        $Credito = new Debito();
        $Credito->setCodigo($codigo);

        $CreditoDAO = new CreditoDAO();
        self::setViewParam('detalhesDebito', $CreditoDAO->detalhes($codigo));

        $this->render('/credito/detalhes');
    }

}
