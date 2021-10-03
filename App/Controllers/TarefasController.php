<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\Entidades\AgendaLancamento;
use App\Models\DAO\AgendaLancamentoDAO;
use App\Models\DAO\TarefasDAO;
use App\Models\Entidades\ContasReceber;
use App\Models\DAO\ContasReceberDAO;
use App\Models\Entidades\Credito;
use App\Models\DAO\CreditoDAO;
use App\Models\Entidades\Caixa;
use App\Models\DAO\CaixaDAO;
use DateTime;

class TarefasController extends Controller {

    private $render = "/Home/index";

    public function index($params) {
        $this->debitaLancamentosFuturos($params);
        $this->creditaContasReceber(null);

        $this->redirect($render);
    }

    public function debitaLancamentosFuturos($params) {
        $temDebitos = true;
        $codigos = explode(",", $params[0]);
        $codigoAgendamento = $params ? $codigos[0] : null;
        $codigoDebito = $params ? $codigos[1] : null;
        $idLancamento = $params ? $codigos[2] : null;

        $AgendaLancamentoDAO = new AgendaLancamentoDAO;
        $AgendaLancamento = new AgendaLancamento();


        if (!empty($idLancamento)) {
            $AgendaLancamento->setId($idLancamento);
        }
        if (!empty($codigoAgendamento)) {
            $AgendaLancamento->setCodigo($codigoAgendamento);
        }
        if (!empty($codigoDebito)) {
            $AgendaLancamento->setCodigoDebito($codigoDebito);
        }

        do {
            $rowAgendaLancamento = $AgendaLancamentoDAO->carregaLancamentoFuturos($AgendaLancamento);

            //VERIFICA SE O MÊS JÁ FOI ENCERRADO
            $periodo = new DateTime($AgendaLancamento->getDataDebito());
            $mesEncerrado = $periodo->format('m');
            $anoEncerrado = $periodo->format('Y');
            $encerrado = $AgendaLancamentoDAO->verificaMesEncerrado($mesEncerrado, $anoEncerrado);
            if (!$encerrado) {

                if ($rowAgendaLancamento) {
                    $TarefasDAO = new TarefasDAO();
                    $TarefasDAO->DebitaLancamentosFuturos($AgendaLancamento);
                } else {
                    $temDebitos = false;
                }

                //SE VEIO PARA DEBITAR UMA CONTA PELA ROTINA DIARIO, É PRECISO ANULAR O CODIGO DE DEBITO CRIADO NA ROTINA PARA QUE A TAREFAS NÃO O PEGUE NOVAMENTE
                if (empty($codigoDebito)) {
                    $AgendaLancamento->setCodigo(null);
                    $AgendaLancamento->setId(null);
                }

                //APÓS DEBITADO, A IMAGEM DE COMPROVANTE VAI PARA A PASTA DE DEBITOS
                if ($AgendaLancamento->getDebitado() == "S") {
                    $this->moverImagem("DEBITO", "AGENDAMENTOS", $AgendaLancamento->getCodigoDebito(), $codigoAgendamento);
                } else {
                    $this->moverImagem("AGENDAMENTOS", "DEBITO", $codigoAgendamento, $AgendaLancamento->getCodigoDebito());
                }


                //SE HAVER CODIGO DE DEBITO QUER DIZER QUE FEZ OPERAÇÃO DE UM DEBITO ESPCIFICO. E DEVE SAIR DA ROTINA. 
                //SENÃO FICA EM LOOP INFINITO
                if (!empty($codigoDebito) || !empty($codigoAgendamento)) {
                    $temDebitos = false;

                    $msg = $AgendaLancamento->getDebitado() == "S" ? "Cancelamento de " : "";
                    $msg .= " Débito efetuado com sucesso!";
                    Sessao::gravaMensagem($msg);
                }

                if (!empty($AgendaLancamento->getCodigo()) || !empty($AgendaLancamento->getCodigoDebito())) {
                    $AgendaLancamento->setCodigo(null);
                    $AgendaLancamento->setCodigoDebito(null);
                    $AgendaLancamento->setId(null);
                }
            } else {
                $temDebitos = false;
            }
        } while ($temDebitos == true);

        if (!empty($codigoDebito) || !empty($codigoAgendamento)) {
            $this->render = "/AgendaLancamento/index";
            $this->redirect($this->render);
        } else {
            $this->deletaLancamentosAntigos();
            //$this->redirect("/Home/index");
        }
    }

    public function deletaLancamentosAntigos() {
        $AgendaLancamento = new AgendaLancamento();
        $AgendaLancamentoDAO = new AgendaLancamentoDAO();
        $excluiLancamentos = $AgendaLancamentoDAO->deletaLancamentosAntigos($AgendaLancamento);

        //APÓS DELETAR OS REGISTROS DO DB, EXCLUI OS COMPROVANTES DOS MESMOS.
        if ($excluiLancamentos) {
            foreach ($viewVar['listaDebito'] as $lancamento) {
                $codigoLancamento = $lancamento->getCodigo();
                $caminhoPastaImagem = "http://" . APP_HOST . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/debitos/debito" . $codigoLancamento;
                if (is_dir($caminhoPastaImagem)) {
                    $diretorio = dir($caminhoPastaImagem);
                    try {
                        while ($arquivo = $diretorio->read()) {
                            if (($arquivo != '.') && ($arquivo != '..')) {
                                unlink($caminhoPastaImagem . "/" . $arquivo);
                            }
                        }
                        $excluiPasta = rmdir($caminhoPastaImagem);
                    } catch (Exception $ex) {
                        
                    }
                }
            }
        }
    }

    public function debitaDebitosFixos($debito) {
        
    }

    public function creditaContasReceber($params) {
        //$codigoContasReceber = $params[0];

        $codigos = explode(",", $params[0]);
        $codigoContasReceber = $params ? $codigos[0] : null;
        $codigoEntrada = $params ? $codigos[1] : null;

        $ContasReceberDAO = new ContasReceberDAO();
        if ($codigoContasReceber) {
            $tableContasReceber = $ContasReceberDAO->listar(base64_encode($codigoContasReceber));
        } else {
            $tableContasReceber = $ContasReceberDAO->verificaCreditosVencidos();
        }
        if ($tableContasReceber) {
            foreach ($tableContasReceber as $key => $value) {
                $ContasReceber = new ContasReceber();
                $ContasReceber->setCodigo($value['codigo']);
                $ContasReceber->setAtivo($value['ativo']);
                $ContasReceber->setDataCompensacao($value['data_compensacao']);
                $ContasReceber->setDescricao($value['descricao']);
                $ContasReceber->setFixo($value['fixo']);
                $ContasReceber->setLucroReal($value['lucro_real']);
                $ContasReceber->setObservacao($value['obs']);
                $ContasReceber->setValor($value['valor']);
                $ContasReceber->setCreditado($value['creditado']);
                $ContasReceber->setCodigoEntrada($codigoEntrada);

                $Credito = new Credito();
                $CreditoDAO = new CreditoDAO();
                $dataHoje = date('Y-m-d');
                if ($ContasReceber->getCreditado() == "S") {
                    $Credito->setCodigo($ContasReceber->getCodigoEntrada());
                    $CreditoDAO->excluir($Credito);
                    $ContasReceber->setCodigoEntrada(null);
                } else {
                    $Credito->setAtivo($ContasReceber->getAtivo());
                    $Credito->setDataCadastro($dataHoje);
                    $Credito->setDescricao($ContasReceber->getDescricao());
                    $Credito->setFixo($ContasReceber->getFixo());
                    $Credito->setLucroReal($ContasReceber->getlucroReal());
                    $Credito->setLucroReal($ContasReceber->getlucroReal());
                    $msgObs = $ContasReceber->getCreditado() == "S" ? "Cancelamento crédito cod: " : "Credito contas receber cod: ";
                    $Credito->setObservacao($msgObs . $ContasReceber->getCodigo() . ". " . $ContasReceber->getObservacao());
                    $Credito->setValor($ContasReceber->getCreditado() == "S" ? $ContasReceber->getValor() * - 1 : $ContasReceber->getValor());

                    $CreditoDAO->salvar($Credito);
                    //APÓS INSERIR O CÓDIGO DA ENTRADA, ATUALIZA O CAMPO codigo entrada DO CONTAS A RECEBER
                    $ContasReceber->setCodigoEntrada($Credito->getCodigo());

                    if ($Credito->getFixo() == 'S') {
                        $ContasReceberFixo = new ContasReceber();
                        $ContasReceberDAOFixo = new ContasReceberDAO();

                        $ContasReceberFixo->setAtivo($ContasReceber->getAtivo());
                        $ContasReceberFixo->setFixo($ContasReceber->getFixo());
                        $ContasReceberFixo->setDescricao($ContasReceber->getDescricao());
                        $ContasReceberFixo->setObservacao($ContasReceber->getObservacao());
                        $ContasReceberFixo->setObservacao($ContasReceber->getlucroReal());
                        $ContasReceberFixo->setValor($ContasReceber->getValor());
                        $ContasReceberFixo->setCreditado("N");
                        $dataCompensacao = new DateTime($ContasReceber->getDataCompensacao()->format("Y-m-d")); //PARA A DATA DE CADASTRO
                        $soma1MesData = new \DateInterval("P1M");
                        $dataCompensacao->add($soma1MesData);
                        $ContasReceberFixo->setDataCompensacao($dataCompensacao->format("Y-m-d"));

                        $ContasReceberDAOFixo = new ContasReceberDAO();
                        $ContasReceberDAOFixo->salvar($ContasReceberFixo);
                    }
                }

                $ContasReceberDAO->atualizar($ContasReceber);

                $Caixa = new Caixa();
                $Caixa->setCodigoEntrada($Credito->getCodigo());
                $Caixa->setCodigoSaidaCabecalho(null);
                $Caixa->setData($dataHoje);
                $Caixa->setDescricao($ContasReceber->getDescricao());
                $Caixa->setObs($msgObs . $ContasReceber->getCodigo() . ". " . $ContasReceber->getObservacao());
                $Caixa->setSaldo($ContasReceber->getCreditado() == "S" ? $ContasReceber->getValor() * - 1 : $ContasReceber->getValor());

                $CaixaDAO = new CaixaDAO();
                $salvaCaixa = $CaixaDAO->salvar($Caixa);

                if ($salvaCaixa) {
                    if (Sessao::retornaRecebeEmail() == "S") {
                        $CaixaDAO->mandaEmail($Caixa);
                    }
                    $ContasReceberDAO->marcaDesmarcaCreditado($ContasReceber);
                }
            }
        }

        if ($codigoContasReceber) {
            $this->redirect("/contasReceber/index");
        }
    }

}
