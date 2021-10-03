<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\DAO\DebitoDAO;
use App\Models\Entidades\Debito;
use App\Models\DAO\CaixaDAO;
use App\Models\Entidades\Caixa;
use App\Models\Validacao\DebitoValidador;
use App\Controllers\AgendaLancamentoController;
use App\Models\DAO\FormaPagamentoDAO;
use App\Models\DAO\EstabelecimentoDAO;
use App\Models\DAO\AgendaLancamentoDAO;
use App\Models\Entidades\AgendaLancamento;
use App\Lib\Paginacao;

//use App\Models\Validacao\ProdutoValidador;

class DebitoController extends Controller {

    public function novo($params) {
        
        //SE TIVER PASSANDO ALGUM params, É PQ ESTA COLOCANDO UM ITEM DE DEBITO SOMENTE. PELO RELATORIO DE DEBITOS SEM ITENS
        if(isset($params)) {
            $codigoItem = $params[0];
            Sessao::gravaCodigo($codigoItem);
        }
        
        $EstabelecimentoDAO = new EstabelecimentoDAO();
        $FormaPagamento = new FormaPagamentoDAO();
        //self::setViewParam('estabelecimentos', $DebitoDAO->carregaEstabelecimentos());
        self::setViewParam('estabelecimentos', $EstabelecimentoDAO->carregaEstabelecimento("S"));
        self::setViewParam('formaPagamento', $FormaPagamento->carregaFormaPagamento("S", "debitar"));
        $this->render('/debito/novo');
    }

    public function salvar() {
        $this->salvaDebito();
        $this->redirect('/debito/novo');
    }

    public function salvarItens() {
        $DebitoItens = new Debito();
        $DebitoItens->setCodigoCabecalho($_POST['codigoSaidaCabecalho']);
        $DebitoItens->setProduto($_POST['produto']);
        $DebitoItens->setQtdProduto($_POST['quantidadeProduto']);
        $DebitoItens->setValorProduto($_POST['valorProduto']);
        $DebitoItens->setAtivoProduto(isset($_POST['ativoProduto']) ? 'S' : 'N');
        $DebitoItens->setUnidadeMedida($_POST['unidadeMedida']);

        $DebitoValidador = new DebitoValidador();
        $ErroDebito = $DebitoValidador->validarItens($DebitoItens);
        if (!empty($ErroDebito)) {
            Sessao::gravaCodigo($DebitoItens->getCodigoCabecalho());
            Sessao::gravaErro($ErroDebito);
            $this->redirect('/debito/novo');
        }

        //INSERE OS VALORES NA ENTRADA
        $DebitoDAO = new DebitoDAO();
        $rowDebito = $DebitoDAO->salvarItens($DebitoItens);
        
        //VERIFICA SE O DÉBITO SE REPETE PARA MAIS MESES E SE HÁ ITENS PARA SEREM LANÇADOS NO ITES DO LANÇ.FUTURO
        $itensFixo = $DebitoDAO->verificaItensDebitosFixo($DebitoItens);
        if($itensFixo) { //SERÁ LANÇADO OS ITENS DO DEBITO FIXO PARA O PROXIMO DEBITO
            //DELETA OS ITENS DESSE LANÇAMENTO FUTURO(POIS PODE TER SIDO ATUALIZADO)
            $AgendaLancamentoDAO = new AgendaLancamentoDAO();
            $AgendaLancamento = new AgendaLancamento();
            $AgendaLancamento->setCodigoCabecalho($DebitoItens->getCodigoCabecalho());
            $AgendaLancamentoDAO->excluirItens($AgendaLancamento);
            
            //$AgendaLancamentoController = new AgendaLancamentoController($this);
            //$AgendaLancamentoController->insereItensDebitoFixo($itensFixo);
        }

        if ($rowDebito > 0) {
            Sessao::gravaCodigo($DebitoItens->getCodigoCabecalho());
            Sessao::gravaMensagem("Item de saida gravado com sucesso. Cod: " . $DebitoItens->getCodigoProduto());
        } else {
            Sessao::gravaErro("Ocorreu um eror ao inserir o débito na caixa");
        }

        $this->redirect('/debito/novo');
    }

    private function salvaDebito() {
        $Debito = new Debito();
        $Debito->setDataCompra($_POST['dataCompra']);
        $Debito->setDataDebito($_POST['dataCompra']);
        $Debito->setValorTotal($_POST['valorTotal']);
        $Debito->setEstabelecimento(strstr($_POST['estabelecimento'], ':', true));
        $Debito->setFormaPagamento($_POST['formaPagamento']);
        $Debito->setFormaPagamento($_POST['formaPagamento']);
        $Debito->setQtdParcelas($_POST['qtdParcelas']);
        $Debito->setObs($_POST['observacao']);
        $Debito->setAtivo(isset($_POST['ativo']) ? 'S' : 'N');
        $Debito->setFixo($_POST['fixo']);
        $Debito->setJuros($_POST['juros']);
        $Debito->setDesconto($_POST['desconto']);
        $nomeEstabelecimento = strstr($_POST['estabelecimento'], ':');
        
        //INSERE OS VALORES NA ENTRADA
        $DebitoValidador = new DebitoValidador();
        $ErroDebito = $DebitoValidador->validarCabecalho($Debito);
        if ($ErroDebito) {
            Sessao::gravaErro($ErroDebito);
            $this->redirect('/debito/novo');
        }

        $DebitoDAO = new DebitoDAO();
        $rowDebito = $DebitoDAO->salvar($Debito);
        if ($rowDebito > 0) {
            if (isset($_POST['ativo'])) {
                $Caixa = new Caixa();
                $Caixa->setDescricao("Debito gerado em" . ucfirst(strtolower($nomeEstabelecimento)));
                $Caixa->setObs($Debito->getObs());
                $Caixa->setSaldo(($Debito->getValorTotal() + $Debito->getJuros()) * -1);
                $Caixa->setData($Debito->getDataDebito());
                $Caixa->setCodigoSaidaCabecalho($Debito->getCodigo());
                //INSERE O VALOR NO CAIXA
                $CaixaDAO = new CaixaDAO();
                $rowCaixa = $CaixaDAO->salvar($Caixa);
                if ($rowCaixa > 0) {

                    //SE GRAVOU CORRETAMENTE E SE O DEBITO IRÁ SE REPETIR POR ALGUM PERIODO, ENTÃO É GERADO O AGENDAMENTO
                    if ($rowDebito && $Debito->getFixo() > 0) {
                        for ($i = 1; $i <= $Debito->getFixo(); $i++) {  //GRAVA UM AGENDAMENTO DE DEBITO DE ACORDO COM A QTD DE VEZES QUE O USUARIO PEDIU
                            $AgendaLancamentoDAO = new AgendaLancamentoDAO();
                            $AgendaLancamento = new AgendaLancamento();

                            $Debito->setDataCompra(date("Y-m-d", strtotime("+1 month", strtotime($Debito->getDataCompra())))); //ADICIONA 1 MÊS PARA GRAVAR NO MÊS SEGUINTE E NÃO NO MES DE COMPRA
                            $AgendaLancamento->setAtivo($Debito->getAtivo());
                            $AgendaLancamento->setDataCompra($Debito->getDataCompra());
                            $AgendaLancamento->setDataDebito($Debito->getDataCompra());
                            $AgendaLancamento->setEstabelecimento($Debito->getEstabelecimento());
                            $AgendaLancamento->setFormaPagamento($Debito->getFormaPagamento());
                            $AgendaLancamento->setQtdParcelas($Debito->getQtdParcelas());
                            $AgendaLancamento->setObs($Debito->getObs());
                            $AgendaLancamento->setValorTotal($Debito->getValorTotal());
                            $AgendaLancamento->setDiaFechamento($Debito->getDiaFechamento());
                            $AgendaLancamento->setDiaVencimento($Debito->getDiaVencimento());
                            $AgendaLancamento->setJuros($Debito->getJuros());

                            $AgendaLancamentoDAO->agendarLancamento($AgendaLancamento);
                        }
                    }

                    //SE GRAVOU TUDO CORRETAMENTE, ENTÃO MANDA E-MAIL SOBRE A OPERAÇÃO
                    if (Sessao::retornaRecebeEmail() == "S") {
                        $CaixaDAO->mandaEmail($Caixa);
                    }
                    //SE GRAVOU TUDO CORRETAMENTE, ENTÃO CARREGA IMAGEM
                    $rowImagemDebito = $this->carregaImagem("DEBITO", $Debito->getCodigo());

                    Sessao::gravaCodigo($Debito->getCodigo());
                    Sessao::gravaMensagem("Saida gravado com sucesso. Cod: " . $Debito->getCodigo() . ". " . $rowImagemDebito);
                } else {
                    Sessao::gravaErro("Ocorreu um eror ao inserir o débito na caixa");
                }
            } else {
                Sessao::gravaMensagem("Saida gravado com sucesso. Cod: " . $Debito->getCodigo() . ". Ela não esta ativa");
            }
        } else {
            Sessao::gravaErro("Erro ao gravar saida");
        }
    }

    private function agendaLancamento($diaVencimento, $diaFechamento) {
        AgendaLancamentoController::AgendaLancamento($diaVencimento, $diaFechamento);
    }

    public function index() {
        $DebitoDAO = new DebitoDAO();

        $dataInicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : null;
        $dataFinal = isset($_POST['data_final']) ? $_POST['data_final'] : null;
        $palavra = isset($_POST['palavra']) ? $_POST['palavra'] : null;
        $paginaSelecionada = isset($_REQUEST['paginaSelecionada']) ? $_REQUEST['paginaSelecionada'] : 1;
        $totalPorPagina = isset($_REQUEST['totalPorPagina']) ? $_REQUEST['totalPorPagina'] : 5;
        //echo $dataFinal; die().
        $listaDebito = $DebitoDAO->listar(null, $dataInicial, $dataFinal, $palavra, $paginaSelecionada, $totalPorPagina);
        $paginacao = new Paginacao($listaDebito, "debito");
        //self::setViewParam('listaDebito', $DebitoDAO->listar(null, $dataInicial, $dataFinal, $palavra, $paginaSelecionada, $totalPorPagina));

        self::setViewParam('paginacao', $paginacao->criarLink());
        self::setViewParam('listaDebito', $listaDebito['resultado']);
        self::setViewParam('totalPorPagina', $totalPorPagina);

        $this->render('/debito/index');

        Sessao::limpaMensagem();
    }

    public function edicao($params) {
        $codigo = explode("99Fin", $params[0]);
        $codigo = $codigo[0];

        $DebitoDAO = new DebitoDAO();

        $EstabelecimentoDAO = new EstabelecimentoDAO();
        $FormaPagamento = new FormaPagamentoDAO();
        self::setViewParam('estabelecimentos', $EstabelecimentoDAO->carregaEstabelecimento("S"));
        self::setViewParam('formaPagamento', $FormaPagamento->carregaFormaPagamento("S", "DEBITAR"));
        $Debito = $DebitoDAO->listarEdicao($codigo);

        if (!$Debito) {
            Sessao::gravaMensagem("Débito inexistente");
            $this->redirect('/debito');
        }

        self::setViewParam('debito', $Debito);

        $this->render('/debito/edicao');

        Sessao::limpaMensagem();
    }

    public function atualizar() {
        $Debito = new Debito();
        $DebitoDAO = new DebitoDAO();
        $CaixaDAO = new CaixaDAO();

        $Debito->setCodigoCabecalho($_POST['codigo']);
        $Debito->setObs($_POST['observacao']);
        $Debito->setDataCompra($_POST['dataCompra']);
        $Debito->setEstabelecimento($_POST['estabelecimento']);
        $Debito->setFormaPagamento($_POST['formaPagamento']);
        $Debito->setValorTotal($_POST['valorTotal']);
        $Debito->setJuros($_POST['juros']);
        $Debito->setFixo($_POST['fixo']);
        $Debito->setAtivo(isset($_POST['ativo']) ? 'S' : 'N');
        $Debito->setQtdParcelas($_POST['qtdParcelas']);
        $Debito->setDesconto($_POST['desconto']);

        //RETORNA COM EXPLODE O VALOR TOTAL DO DEBITO E SE ELE ESTAVA ATIVO OU NAO ATIVO
        $rowDebito = explode(",", $DebitoDAO->retornaUltimoSaldo($Debito->getCodigoCabecalho()));
        $valorDebitoAnterior = $rowDebito[0]; //VALOR TOTAL
        $ativoDebitoAnterior = $rowDebito[1]; //ATIVO OU NAO ATIVO

        Sessao::gravaFormulario($_POST);

        $rowDebito = $DebitoDAO->atualizar($Debito);
        if ($rowDebito) {
            //SE HOUVE ALTERAÇÃO NO VALOR DO CRÉDITO, ESSE VALOR É ATUALIZADO NO CAIXA
            if ($valorDebitoAnterior == ($Debito->getValorTotal() + $Debito->getJuros()) ) {
                
            } else {
                if ( ($Debito->getValorTotal() + $Debito->getJuros()) > $valorDebitoAnterior) {
                    $valorDebitoAnterior = ( ($Debito->getValorTotal() + $Debito->getJuros()) - $valorDebitoAnterior) * -1;
                } else {
                    $valorDebitoAnterior = $valorDebitoAnterior - ($Debito->getValorTotal() + $Debito->getJuros());
                }
                $Caixa = new Caixa();
                $Caixa->setDescricao("Correção de valor saida cod: " . $Debito->getCodigoCabecalho() . ". ");
                $Caixa->setObs($Debito->getObs());
                $Caixa->setSaldo($valorDebitoAnterior);
                $Caixa->setData($Debito->getDataDebito());
                $Caixa->setCodigoSaidaCabecalho($Debito->getCodigoCabecalho());
                $rowCaixa = $CaixaDAO->salvar($Caixa);
            }

            if ($ativoDebitoAnterior != $Debito->getAtivo()) {
                $Caixa = new Caixa();
                $Caixa->setDescricao("Correção de ativo saida cod: " . $Debito->getCodigoCabecalho() . ". ");
                $Caixa->setObs($Debito->getObs());
                $Caixa->setSaldo($ativoDebitoAnterior == "S" ? $valorDebitoAnterior : $valorDebitoAnterior * - 1);
                $Caixa->setData($Debito->getDataDebito());
                $Caixa->setCodigoSaidaCabecalho($Debito->getCodigoCabecalho());
                $rowCaixa = $CaixaDAO->salvar($Caixa);
            }
        }

        if (isset($_FILES['arquivo_imagem'])) {
            $Debito->setCodigo($_POST['codigo']);
            $rowImagemDebito = $this->carregaImagem("DEBITO", $Debito->getCodigo());
        }

        if ($rowDebito || $rowImagemDebito) {
            Sessao::gravaMensagem("Atualizado com sucesso. " . $rowImagemDebito . "");
        }

        $this->redirect('/debito');
    }

    public function excluir($params) {
        $codigo = $params[0];
        $Debito = new Debito();
        $Debito->setCodigo($codigo);

        $DebitoDAO = new DebitoDAO();
        $rowDebito = $DebitoDAO->excluir($Debito);

        if ($rowDebito) {
            $CaixaDAO = new CaixaDAO();
            $Caixa = new Caixa();

            $Caixa->setCodigoSaidaCabecalho($codigo);
            $Caixa->setCodigoSaidaCabecalho($codigo);
            //$Caixa->setData($Debito->getDataDebito());
            $dataHoje = DATE('Y-m-d');
            $Caixa->setData($dataHoje);
            $Caixa->setDescricao($Debito->getObs());
            $Caixa->setObs($Debito->getObs());
            $Caixa->setSaldo($Debito->getValorTotal() + $Debito->getJuros());
            $rowCaixa = $CaixaDAO->salvar($Caixa);
            if ($rowCaixa) {
                
                //AQUI DEVERÁ SETAR N PARA LANÇAMENTOS FUTUROS COM ESSE CÓDIGO DE DÉBITO
                $AgendaLancamentoDAO = new AgendaLancamentoDAO();
                $AgendaLancamentoDAO->mudaDebitado($Debito->getCodigo());
                
                if (Sessao::retornaRecebeEmail() == "S") {
                    $CaixaDAO->mandaEmail($Caixa);
                }
                $this->deletaImagem("DEBITO", $Debito->getCodigo());
                Sessao::gravaMensagem("Debito excluido com sucesso");
            } else {
                Sessao::gravaMensagem("Debito excluido com falha ao deletar do caixa");
            }
        } else {
            Sessao::gravaMensagem("Não foi possivel excluir o debito");
        }
        $this->index();
    }

    public function excluirItem($params) {
        $codigo = $params[0];
        $Debito = new Debito();
        $Debito->setCodigoProduto($codigo);

        $DebitoDAO = new DebitoDAO();
        $rowDebito = $DebitoDAO->excluirItem($Debito);

        if (!$rowDebito) {
            Sessao::gravaMensagem("Não foi possivel excluir o debito");
        } else {
            Sessao::gravaMensagem("Item de débito excluido com sucesso");
        }

        $this->index();
    }

    public function detalhes($params) {
        $codigo = $params[0];
        $Debito = new Debito();
        $Debito->setCodigo($codigo);

        $DebitoDAO = new DebitoDAO();
        self::setViewParam('detalhesDebito', $DebitoDAO->detalhes($codigo));

        $this->render('/debito/detalhes');
    }

}
