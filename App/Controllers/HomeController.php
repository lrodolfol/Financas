<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\DAO\HomeDAO;
use App\Models\DAO\CreditoDAO;
use App\Models\DAO\DebitoDAO;
use App\Models\DAO\AgendaLancamentoDAO;
use App\Models\DAO\FormaPagamentoDAO;
use App\Models\DAO\EstabelecimentoDAO;
use App\Models\DAO\CaixaDAO;
use App\Models\Entidades\RelatoErro;
use App\Models\Entidades\Email;
use App\Controllers\TarefasController;

class HomeController extends Controller {

    public function index() {
        //EDITAR LANÇAMENTOS FUTUROS QUE PODEM SER HOJE
        //$Tarefas = new TarefasController();
        //$Tarefas->debitaLancamentosFuturos(null);
        //$this->render('/Tarefas/debitaLancamentosFuturos');
        $HomeDAO = new HomeDAO();
        //self::setViewParam('listaProdutos',$produtoDAO->listar());
        self::setViewParam('mostraSaldo', $HomeDAO->mostraSaldo());
        $proximosDiasDebito = "5";
        $proximosDiasCredito = "5";
        if (isset($_REQUEST['proximosDiasDebitos'])) {
            $proximosDiasDebito = $_REQUEST['proximosDiasDebitos'];
        }
        if (isset($_REQUEST['proximosDiasCreditos'])) {
            $proximosDiasCredito = $_REQUEST['proximosDiasCreditos'];
        }
        $debitoCreditoFuturo = $HomeDAO->consultaDebitosCreditosFuturos($proximosDiasDebito,$proximosDiasCredito);
        $qtdDebitoCreditoVencido = $HomeDAO->consultaDebitosCreditosVencidos();
        Sessao::gravaValor($debitoCreditoFuturo[0]);
        self::setViewParam('creditoFuturo', $debitoCreditoFuturo[1]);
        self::setViewParam('proximoMes', $debitoCreditoFuturo[2]);
        Sessao::gravaQtdDebitoVencido($qtdDebitoCreditoVencido[1]);
        Sessao::gravaQtdCreditoVencido($qtdDebitoCreditoVencido[0]);

        $graficoSaldoMax = $HomeDAO->geraGraficoSaldoMax();
        $graficoSaldoMin = $HomeDAO->geraGraficoSaldoMin();
        self::setViewParam('graficoSaldoMax', $graficoSaldoMax);
        self::setViewParam('graficoSaldoMin', $graficoSaldoMin);

        $this->render('home/index');
    }

    public function mudaBase($baseParaMudar) {
        if ($baseParaMudar[0] == "P") {
            $baseConexao = "PRODUCAO";
        } elseif ($baseParaMudar[0] == "T") {
            $baseConexao = "TESTE";
        } else {
            $baseConexao = "LOCAL";
        }
        $arquivo = fopen(TXT_BANCO, "w+");      //FAZ A ABERTURA DO ARQUIVO 'w+' leitura e escrita; coloca o ponteiro do arquivo no começo do arquivo
        fwrite($arquivo, $baseConexao);
        fclose($arquivo);
        $this->sair();
    }

    public function sair() {
        Sessao::limpaUsuario();
        Sessao::limpaCodigo();
        Sessao::limpaFormulario();
        Sessao::limpaMensagem();
        Sessao::limpaErro();
        Sessao::limpaValor();
        Sessao::limpaUsuario();
        Sessao::limpaRecebeEmail();
        Sessao::limpaEmail();
        Sessao::limpaQtdDebitoVencido();
        $this->redirect("");
    }

    public function NAOUSADAimportaConta() {
        try {
            $arquivoNome = $_FILES["arquivo_importacao"]["name"];
            $arquivoTipo = $_FILES["arquivo_importacao"]["type"];
            $arquivoTamanho = $_FILES["arquivo_importacao"]["size"];
            $arquivoNomeTemp = $_FILES["arquivo_importacao"]["tmp_name"];
            $erro = $_FILES["arquivo_importacao"]["error"];
            $extensao = strrchr($_FILES['arquivo_importacao']['name'], '.');
            $extensoesPermitidas = array('.XML', '.JSON', '.CSV');

            $nomeTabelas = array("entradas", "estabelecimentos", "formas_pagamento", "caixa", "saida_cabecalho", "saidas_itens", "lancamentos_futuros", "lancamentos_futuros_itens");
            $tabelas = array(
                /* ENTRADAS */ array("codigo", "descricao", "obs", "valor", "ativo", "fixo", "data"),
                /* ESTAB */ array("codigo", "nome", "cnpj", "tipo_comercio", "cidade", "ativo"),
                /* FORM PAG */ array("codigo", "descricao", "ativo", "dia_fechamento", "dia_vencimento"),
                /* CAIXA */ array("descricao", "obs", "ativo", "saldo", "data", "codigo_saida_cabecalho", "codigo_entrada"),
                /* SAIDA CABEÇ */ array("codigo", "data_compra", "data_debito", "valor_total", "estabelecimento", "forma_pagamento", "qtd_parcelas", "ativo", "fixo", "obs"),
                /* SAIDA ITEN */ array("codigo", "codigo_cabecalho", "produto", "qtd_produto", "valor_produto", "ativo", "unidade_medida"),
                /* LANÇ. FUT */ array("codigo", "data_compra", "data_debito", "valor_total", "estabelecimento", "forma_pagamento", "qtd_parcelas", "ativo", "debitado", "obs", "codigo_cabecalho", "codigo_debito", "numero_parcela"),
                /* LANÇ. ITEN */ array("codigo", "codigo_cabecalho", "produto", "qtd_produto", "valor_produto", "ativo", "unidade_medida"),
            );

            if (!in_array(strtoupper($extensao), $extensoesPermitidas) === true) {
                Sessao::gravaErro("Escolha um arquivo em formato XML, JSON OU CSV.");
            } else {
                $dom = new \DOMDocument();
                $dom->load($arquivoNomeTemp);
                $sqlImportar = array();
                $contRegistro = 0;
                $contRegistroAux = 0;

                for ($x = 0; $x <= count($nomeTabelas); $x++) {

                    $entrada = $dom->getElementsByTagName($nomeTabelas[$x]);
                    foreach ($entrada as $value) {

                        //AQUI DEU UM PEQUENO PROBLEMA, POIS AO LER AS TAG DE DEBITOS, O SCRIPT ESTA LENDO O CAMPO 'forma_pagamente' 
                        //E ENTENDENDO COMO SE ELE FOSSE A TABELA 'forma_pagamento'. 
                        //POR ISSO COLOQUEI ESSA TRAVA. POIS CADA TABELA NÃO TEM TAMANHO MAIOR QUE 1 NO ARRAY
                        if (count($entrada) > 1) {
                            break;
                        }

                        $sqlImportar[$contRegistro] = "DELETE FROM $nomeTabelas[$x] WHERE id >= 1 ";
                        $contRegistro++;

                        $registros = $value->childNodes;
                        foreach ($registros as $registro) {

                            $sqlImportar[$contRegistro] = "INSERT INTO $nomeTabelas[$x] ( ";
                            for ($i = 0; $i <= count($tabelas[$x]); $i++) {
                                $sqlImportar[$contRegistro] .= $tabelas[$x][$i] . ",";
                            }
                            $sqlImportar[$contRegistro] = substr($sqlImportar[$contRegistro], 0, strlen($sqlImportar[$contRegistro]) - 2);
                            $sqlImportar[$contRegistro] .= ") VALUES (";

                            for ($i = 0; $i <= count($tabelas[$x]); $i++) {
                                //ALGUNA CAMPOS INTEGER NA TABELA NÃO PODEM CONTER '' SE NÃO TIVER VALOR, ENTÃO PASSARAM A SER NULL
                                //CAIXA CODIGO SAIDA E CODIGO ENTRADA
                                if (($nomeTabelas[$x] == "lancamentos_futuros" && ( ($tabelas[$x][$i] == 'codigo_cabecalho' or $tabelas[$x][$i] == 'codigo_debito') and ( empty($registro->getElementsByTagName($tabelas[$x][$i])->item(0)->nodeValue) ) )) or ( $nomeTabelas[$x] == "caixa" && ( ($tabelas[$x][$i] == 'codigo_saida_cabecalho' or $tabelas[$x][$i] == 'codigo_entrada') and ( empty($registro->getElementsByTagName($tabelas[$x][$i])->item(0)->nodeValue) ) ) )) {
                                    $sqlImportar[$contRegistro] .= "null,";
                                } else {
                                    $sqlImportar[$contRegistro] .= "'" . $registro->getElementsByTagName($tabelas[$x][$i])->item(0)->nodeValue . "',";
                                }
                            }
                            $sqlImportar[$contRegistro] = substr($sqlImportar[$contRegistro], 0, strlen($sqlImportar[$contRegistro]) - 4);
                            $sqlImportar[$contRegistro] .= ")";

                            $contRegistro++;
                        }
                        $DAO = new HomeDAO();
                        $DAO->executaSqlArray($sqlImportar);
                        $sqlImportar = array();
                        $contRegistro = 0;
                    }
                }
                Sessao::gravaMensagem("Conta Atualizada com sucesso. ");
            }
        } catch (Exception $exc) {
            Sessao::gravaErro("Erro ao atualizar a conta. " . $exc);
        }
        $this->render('/home/importarConta');
    }

    public function calculadora() {
        $this->render('home/calculadora');
    }

}
