<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\DAO\CaixaDAO;
use App\Models\Entidades\Caixa;
use App\Models\DAO\AgendaLancamentoDAO;
use App\Models\Entidades\AgendaLancamento;
use App\Models\Validacao\AgendaLancamentoValidador;
use App\Models\DAO\EstabelecimentoDAO;
use App\Models\DAO\FormaPagamentoDAO;
use DateTime;

//use App\Models\Validacao\ProdutoValidador;

class AgendaLancamentoController extends Controller {

    public function novo() {
        $EstabelecimentoDAO = new EstabelecimentoDAO();
        $FormaPagamento = new FormaPagamentoDAO();
        //self::setViewParam('estabelecimentos', $DebitoDAO->carregaEstabelecimentos());
        self::setViewParam('estabelecimentos', $EstabelecimentoDAO->carregaEstabelecimento("S"));
        self::setViewParam('formaPagamento', $FormaPagamento->carregaFormaPagamento("S", "agendar"));
        $this->render('/lancamentosFuturos/novo');
    }

    public function AgendaLancamento($diaFechamento = null, $diaVencimento = null) {
        $codigo = $_POST["formaPagamento"];
        $FormaPagamentoDAO = new FormaPagamentoDAO();
        $row = $FormaPagamentoDAO->retornaDataFechamento($codigo);
        $diaVencimento = $row['dia_vencimento'];
        $diaFechamento = $row['dia_fechamento'];
        //$Debito = new Debito();
        //$Debito->setDiaFechamento($dataFechamento);
        //$Debito->setDiaVencimento($diaVencimento);

        $AgendaLancamento = new AgendaLancamento();
        $AgendaLancamento->setDataCompra($_POST['dataCompra']);
        $AgendaLancamento->setDataDebito($_POST['dataDebito']);
        $AgendaLancamento->setValorTotal($_POST['valorTotal']);
        $AgendaLancamento->setEstabelecimento($_POST['estabelecimento']);
        $AgendaLancamento->setFormaPagamento($_POST['formaPagamento']);
        $AgendaLancamento->setQtdParcelas($_POST['qtdParcelas']);
        $AgendaLancamento->setObs($_POST['observacao']);
        $AgendaLancamento->setAtivo(isset($_POST['ativo']) ? 'S' : 'N');
        $AgendaLancamento->setDiaFechamento($diaFechamento);
        $AgendaLancamento->setDiaVencimento($diaVencimento);
        $AgendaLancamento->setJuros($_POST['juros']);

        $AgendaLancamentoDAO = new AgendaLancamentoDAO();

        $periodo = new DateTime($AgendaLancamento->getDataDebito());
        $mesEncerrado = $periodo->format('m');
        $anoEncerrado = $periodo->format('Y');
        $encerrado = $AgendaLancamentoDAO->verificaMesEncerrado($mesEncerrado, $anoEncerrado);
        
        if (!$encerrado) {
            $rowLancamento = $AgendaLancamentoDAO->agendarLancamento($AgendaLancamento);
            if ($rowLancamento) {
                $rowLancamento = $this->carregaImagem("AGENDAMENTOS", $AgendaLancamento->getCodigo());
                if (isset($_POST['ativo'])) {
                    Sessao::gravaMensagem("Agendamento de saida gravado com sucesso. Cod: " . $AgendaLancamento->getCodigo() . $rowLancamento);
                } else {
                    Sessao::gravaMensagem("Agendamento de saida gravado com sucesso. Cod: " . $AgendaLancamento->getCodigo() . ". Ele não esta ativo" . $rowLancamento);
                }
                Sessao::gravaCodigo($AgendaLancamento->getCodigo());
            } else {
                Sessao::gravaErro("Ocorreu um eror ao gravar novo agendamento de saida");
            }
        } else {
            Sessao::gravaErro("A competência " . $mesEncerrado . "/" . $anoEncerrado . " já esta encerrada! Contas a receber não inserido");
        }
        $this->redirect('/agendaLancamento/novo');
    }

    public function salvarItens() {
        $AgendaLancamentoItens = new AgendaLancamento();
        $AgendaLancamentoItens->setCodigoCabecalho($_POST['codigoSaidaCabecalho']);
        $AgendaLancamentoItens->setProduto($_POST['produto']);
        $AgendaLancamentoItens->setQtdProduto($_POST['quantidadeProduto']);
        $AgendaLancamentoItens->setValorProduto($_POST['valorProduto']);
        $AgendaLancamentoItens->setAtivoProduto(isset($_POST['ativoProduto']) ? 'S' : 'N');
        $AgendaLancamentoItens->setUnidadeMedida($_POST['unidadeMedida']);

        $AgendaLancamentoValidador = new AgendaLancamentoValidador();
        $ErroAgendaLancamento = $AgendaLancamentoValidador->validarItens($AgendaLancamentoItens);
        if (!empty($ErroAgendaLancamento)) {
            Sessao::gravaErro($ErroAgendaLancamento);
            $this->redirect('/agendaLancamento/novo');
        }

        //INSERE OS VALORES NA ENTRADA
        $AgendaLancamentoDAO = new AgendaLancamentoDAO();
        $rowAgendaLancamento = $AgendaLancamentoDAO->salvarItens($AgendaLancamentoItens);

        if ($rowAgendaLancamento > 0) {
            Sessao::gravaCodigo($AgendaLancamentoItens->getCodigoCabecalho());
            Sessao::gravaMensagem("Item de Lançamento futuro gravado com sucesso. Cod: " . $AgendaLancamentoItens->getCodigoProduto());
        } else {
            Sessao::gravaErro("Ocorreu um erro ao gravar o Lançamento futuro");
        }

        $this->redirect('/agendaLancamento/novo');
    }
    
    public function insereItensDebitoFixo($itens){
        $AgendaLancamentoItens = new AgendaLancamento();
        $AgendaLancamentoItens->setCodigoCabecalho($itens['codigoCabecalho']);
        $AgendaLancamentoItens->setProduto($_POST['produto']);
        $AgendaLancamentoItens->setQtdProduto($_POST['quantidadeProduto']);
        $AgendaLancamentoItens->setValorProduto($_POST['valorProduto']);
        $AgendaLancamentoItens->setAtivoProduto(isset($_POST['ativoProduto']) ? 'S' : 'N');
        $AgendaLancamentoItens->setUnidadeMedida($_POST['unidadeMedida']);

        $AgendaLancamentoValidador = new AgendaLancamentoValidador();
        $ErroAgendaLancamento = $AgendaLancamentoValidador->validarItens($AgendaLancamentoItens);
        if (!empty($ErroAgendaLancamento)) {
            //return false;
        }

        //INSERE OS VALORES NA ENTRADA
        $AgendaLancamentoDAO = new AgendaLancamentoDAO();
        $rowAgendaLancamento = $AgendaLancamentoDAO->salvarItens($AgendaLancamentoItens);

        if ($rowAgendaLancamento > 0) {
            return true;
        } else {
            return false;
        }  
    }

    public function index() {
        $AgendaLancamentoDAO = new AgendaLancamentoDAO();
        $proximosDias = isset($_POST['proximosDiasLanc']) ? $_POST['proximosDiasLanc'] : null;
        $antecedeteDias = isset($_POST['antecedenteDiasLanc']) ? $_POST['antecedenteDiasLanc'] : null;
        $condicao = isset($_POST['condicao']) ? $_POST['condicao'] : null;
        ;

        self::setViewParam('listaLancamentoFuturo', $AgendaLancamentoDAO->listar(null, $proximosDias, $antecedeteDias, $condicao));
        self::setViewParam('condicao', $condicao);

        $this->render('/lancamentosFuturos/index');
        Sessao::limpaMensagem();
    }

    public function excluir($params) {
        $id = $params[0];
        $codigoLancamento = $params[1];
        $AgendaLancamento = new AgendaLancamento();
        $AgendaLancamento->setId($id);
        $AgendaLancamento->setCodigo($codigoLancamento);

        $AgendaLancamentoDAO = new AgendaLancamentoDAO();
        $rowLancamento = $AgendaLancamentoDAO->excluir($AgendaLancamento);

        if (!$rowLancamento) {
            Sessao::gravaMensagem("Não foi possivel excluir o Lançamento Futuro");
        } else {
            $this->deletaImagem("AGENDAMENTOS", $AgendaLancamento->getCodigo());
            Sessao::gravaMensagem("Lançamento Futuro excluido com sucesso");
        }
        $this->index();
    }
    
    public function deletaItens($codigo) {
        $AgendaLancamento = new AgendaLancamento();
        $AgendaLancamento->setCodigoCabecalho($codigoCabecalho);
        $AgendaLancamentoDAO = new AgendaLancamentoDAO();
        $AgendaLancamentoDAO->excluirItens($AgendaLancamento);
    }

    public function detalhes($params) {
        $codigo = $params[0];
        $AgendaLancamento = new AgendaLancamento();
        $AgendaLancamento->setCodigo($codigo);

        $AgendaLancamentoDAO = new AgendaLancamentoDAO();
        self::setViewParam('detalhesLancamentoFuturo', $AgendaLancamentoDAO->detalhes($codigo));

        $this->render('/lancamentosFuturos/detalhes');
    }

    public function edicao($params) {
        $codigo = explode("99Fin", $params[0]);
        $codigo = $codigo[0];
        echo 'PÁGINA EM CONSTRUÇÃO';
    }

}
