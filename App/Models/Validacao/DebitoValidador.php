<?php

namespace App\Models\Validacao;

//use \App\Models\Validacao\ResultadoValidacao;
use \App\Models\Entidades\Debito;
use App\Models\DAO\DebitoDAO;
use DateTime;

class DebitoValidador {
    public function validarCabecalho(Debito $Debito) {
        $codigo = $Debito->getCodigo();
        $dataHoje = date('Y-m-d');
        if ($Debito->getDataCompra() > $dataHoje) {
            return "Data de compra maior que a data atual. Para tal, utilize a opção de lançamentos futuros.";
        }
        
        //VERIFICA SE O MÊS JÁ FOI ENCERRADO
        $DebitoDAO = new DebitoDAO();
        $periodo = new DateTime($Debito->getDataDebito());
        $mesEncerrado = $periodo->format('m');
        $anoEncerrado = $periodo->format('Y');
        $encerrado = $DebitoDAO->verificaMesEncerrado($mesEncerrado, $anoEncerrado);
        if($encerrado) {
            return "A competência " . $mesEncerrado . "/" .$anoEncerrado . " já esta encerrada! Débito não inserido.";
        }
        $dd = 2;
    }

    public function validarItens(Debito $Debito) {
        //$resultadoValidacao = new ResultadoValidacao();

        $sql = "SELECT valor_total, codigo FROM saida_cabecalho WHERE codigo = "
                . "" . $Debito->getCodigoCabecalho() . " AND ativo = 'S' ";
        $DebitoDAO = new DebitoDAO();
        $row = $DebitoDAO->RetornaDado($sql);

        if (!$row) {
            return "Código de débito não encontrado";
        } else {
            $codigo = $row['codigo'];
            $valorTotal = number_format($row['valor_total'], '2', '.', ',');
            $valorTotalComp = number_format($row['valor_total'],2,',','.');

            $sql = "SELECT SUM(valor_produto * qtd_produto) FROM saidas_itens WHERE codigo_cabecalho = " . $Debito->getCodigoCabecalho() . "";

            $valorItens = $DebitoDAO->RetornaDado($sql);

            if ($valorItens[0] > $valorTotalComp) {
                return "O valor total da saida de R$ " . number_format($valorTotalComp, '2', ',', '.') . " já foi contabilizado";
            } else {
                $valorValidacao = $Debito->getQtdProduto() * $Debito->getValorProduto() + $valorItens[0];
                $valorValidacao = floor($valorValidacao * 100) / 100;
                //$valorValidacao = number_format($valorValidacao, '2', '.', ',');
                $VRTESTE = $valorValidacao - $valorTotalComp;
                if ($valorValidacao > $valorTotal) {
                    //$rr = $Debito->getQtdProduto() * $Debito->getValorProduto() + $valorItens[0];
                    $valorRestante = $valorTotalComp - $valorItens[0];
                    return "O valor desse produto exede o valor disponivel de R$ " . number_format($valorRestante, '2', ',', '.') . " para essa saida. Total de R$ " . number_format($valorTotalComp, '2', ',', '.') . " ";
                } else {
                    return "";
                }
            }
        }
    }

}
