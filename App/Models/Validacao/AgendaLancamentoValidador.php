<?php

namespace App\Models\Validacao;

//use \App\Models\Validacao\ResultadoValidacao;
use App\Models\Entidades\AgendaLancamento;
use App\Models\DAO\AgendaLancamentoDAO;

class AgendaLancamentoValidador {

    public function validarItens(AgendaLancamento $AgendaLancamento) {
        //$resultadoValidacao = new ResultadoValidacao();

        $sql = "SELECT CAST(SUM(valor_total) AS DECIMAL(15,2)) as valor_total, codigo FROM lancamentos_futuros WHERE codigo = "
                . "" . $AgendaLancamento->getCodigoCabecalho() . " AND ativo = 'S' ";
        $AgendaLancamentoDAO = new AgendaLancamentoDAO();
        $row = $AgendaLancamentoDAO->RetornaDado($sql);

        if (!$row) {
            return "Código de Lançamento futuro não encontrado";
        } else {
            $codigo = $row['codigo'];
            $valorTotal = $row['valor_total'];

            $sql = "SELECT COALESCE(SUM(valor_produto * qtd_produto), 0) "
                    . "FROM lancamentos_futuros_itens WHERE "
                    . "codigo_cabecalho = " . $AgendaLancamento->getCodigoCabecalho() . "";

            $valorItens = $AgendaLancamentoDAO->RetornaDado($sql);

            if ($valorItens[0] >= $valorTotal) {
                return "O valor total da saida de R$ " . number_format($valorTotal, '2', ',', '.') . " já foi contabilizado";
            } else {
                if (($AgendaLancamento->getValorProduto() + $valorItens[0]) > $valorTotal) {
                    $valorRestante = $valorTotal - $valorItens[0];
                    return "O valor desse produto exede o valor disponivel de R$ " . number_format($valorRestante, '2', ',', '.') . " para essa saida. Total de R$ " . number_format($valorTotal, '2', ',', '.') . " ";
                } else {
                    return "";
                }
            }
        }
    }

}
