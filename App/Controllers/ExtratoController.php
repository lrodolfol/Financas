<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Lib\functions;
use App\Models\DAO\ExtratoDAO;
use App\Models\DAO\DebitoDAO;
use App\Models\DAO\CreditoDAO;
use App\Models\DAO\ContasReceberDAO;
use App\Lib\Paginacao;
use App\Controllers\TarefasController;

class ExtratoController extends Controller {

    public function Extrato() {

        $dataInicial = isset($_POST['dataInicial']) ? $_POST['dataInicial'] : null;
        $dataFinal = isset($_POST['dataFinal']) ? $_POST['dataFinal'] : null;

        if (isset($dataInicial) && isset($dataFinal)) {
            $ExtratoDAO = new ExtratoDAO();
            self::setViewParam('extratoPeriodo', $ExtratoDAO->Extrato($dataInicial, $dataFinal));
        }

        $this->render('extrato/extrato');
    }

    public function custoProduto($codProduto = null, $dataInicial = null, $dataFinal = null) {
        $ExtratoDAO = new ExtratoDAO();

        $codigo = ($codProduto ? $codProduto : null);
        $dataInicio = ($dataInicial ? $dataInicio : isset($_POST['dataInicial']) ? $_POST['dataInicial'] : null);
        $dataFim = ($dataFinal ? $dataFim : isset($_POST['dataFinal']) ? $_POST['dataFinal'] : null);

        self::setViewParam('extratoPeriodo', $ExtratoDAO->custoProduto($codigo, $dataInicio, $dataFim));

        $this->render('extrato/custoProduto');
    }

    public function creditoMensal() {
        $ExtratoDAO = new ExtratoDAO();

        self::setViewParam('creditoMensal', $ExtratoDAO->creditoMensal());
        self::setViewParam('debitoMensal', $ExtratoDAO->debitoMensal());


        $this->render('extrato/creditoMensal');
    }

    /* public function debitoMensal() {
      $ExtratoDAO = new ExtratoDAO();

      self::setViewParam('debitoMensal', $ExtratoDAO->debitoMensal());
      $this->render('extrato/debitoMensal');
      } */

    public function debitosSemItens() {
        $ExtratoDAO = new ExtratoDAO();
        $debitosSemItens = $ExtratoDAO->debitosSemItens();
        self::setViewParam('debitosSemItens', $debitosSemItens);
        $this->render('extrato/debitosSemItens');
    }

    public function debitosSemComprovantes() {
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

        $this->render('debito/semComprovantes');
    }

    public function creditosSemComprovantes() {
        $CreditoDAO = new CreditoDAO();

        $dataInicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : null;
        $dataFinal = isset($_POST['data_final']) ? $_POST['data_final'] : null;
        $palavra = isset($_POST['palavra']) ? $_POST['palavra'] : null;
        $paginaSelecionada = isset($_REQUEST['paginaSelecionada']) ? $_REQUEST['paginaSelecionada'] : 1;
        $totalPorPagina = isset($_REQUEST['totalPorPagina']) ? $_REQUEST['totalPorPagina'] : 5;
        $lucroReal = isset($_REQUEST['lucro_real']) ? 'S' : 'N';

        $listaCredito = $CreditoDAO->listar(null, $dataInicial, $dataFinal, $palavra, $paginaSelecionada, $totalPorPagina, $lucroReal);
        $paginacao = new Paginacao($listaCredito, "credito");

        self::setViewParam('paginacao', $paginacao->criarLink());
        self::setViewParam('listaCredito', $listaCredito['resultado']);
        self::setViewParam('totalPorPagina', $totalPorPagina);

        $_REQUEST['data_inicial'] = $dataInicial;

        $this->render('credito/semComprovantes');
    }

    public function extrairExcel($tabelas) {

        $camposEntrada = array("codigo", "descricao", "obs", "valor", "ativo", "fixo", "data", "lucro_real");
        $camposContasReceber = array("codigo", "descricao", "obs", "valor", "ativo", "fixo", "data_compensacao", "codigo_entrada");

        $entrada = new CreditoDAO();
        $contasReceber = new ContasReceberDAO();

        for ($i = 0; $i <= 1; $i++) {
            $camposTabela = "";

            if ($i == 0) {
                $dados = $entrada->sqlExporta();
                $camposTabela = $camposEntrada;
            } else if ($i == 1) {
                $dados = $contasReceber->listar(null);
                $camposTabela = $camposContasReceber;
            }

            $arqExcel = "<meta charset='UTF-8'>";
            $arqExcel .= "<table border='1'>
            <caption> Relatorio Entradas </caption>
            <thead>
                <tr> ";

            foreach ($camposTabela as $value) {
                $arqExcel .= "<th>" . $value . "</th>";
            }
            $arqExcel .= "</tr>
            </thead>
                <tbody>";

            foreach ($dados as $exibir_registros) {

                $arqExcel .= "
                        <tr>";
                $cont = 0;
                foreach ($exibir_registros as $key => $value) {
                    $arqExcel .= "<td align='center'>" . $exibir_registros[$camposTabela[$cont]] . "</td>";
                    $cont++;
                }
                $arqExcel .= "</tr>";
                /* <td align='center'>{$exibir_registros['codigo']}</td>
                  <td align='center'>{$exibir_registros['descricao']}</td>
                  <td align='center'>{$exibir_registros['obs']}</td>
                  <td align='center'>{$exibir_registros['valor']}</td>
                  <td align='center'>{$exibir_registros['ativo']}</td>
                  <td align='center'>{$exibir_registros['fixo']}</td>
                  <td align='center'>{$exibir_registros['data']}</td>
                  </tr>" */
            }
            $arqExcel .= " 
                </tbody>
                    </table>";

            $salvouArquivo = functions::salvaArquivo("backup_conta/excel", "Relatorio_excel_entradas", $arqExcel, "xlsx", true, true);
        }

        if ($salvouArquivo) {
            echo $arqExcel;
            return true;
        } else {
            return false;
        }
    }

}
