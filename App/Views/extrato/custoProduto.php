<?php
$totalPorcentagem = 0;
$totalProduto = 0;
$contProduto = 1;
$dataInicial = $_REQUEST['dataInicial'];
$dataFinal = $_REQUEST['dataFinal'];
?>
<div class="container">
    <div class="starter-template">
        <h2>Gastos por produto</h2>

        <form method="POST" action="http://<?php echo APP_HOST; ?>/extrato/custoProduto">
            <h5>Busca por periodo</h5>
            <div class = "row">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="date" class="form-control" name="dataInicial" value="<?php echo $dataInicial ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="date" class="form-control" name="dataFinal" value="<?php echo $dataFinal ?>">
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-sm">Buscar</button>
                </div>
            </div>

        </form>

        <?php
        if (count($viewVar['extratoPeriodo'])) {

            $ArrayPorcentagem = array();

            $totalEntradas = number_format($viewVar['extratoPeriodo'][0]->total_entradas, 2, ',', '.');
            ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr>
                        <td class="info">Produto</td>
                        <td class="info">Total</td>
						<td class="info">Quntdd</td>
						<td class="info">Média R$</td>
						<td class="info">Unid M.</td>
                        <td class="info">% de R$ <?php echo $totalEntradas; ?></td>
                    </tr>
                    <?php
                    foreach ($viewVar['extratoPeriodo'] as $debito) {
                        $totalPorcentagem = $totalPorcentagem + $debito->porcentagem_produto;
                        $totalProduto = $totalProduto + $debito->total_produto;

                        $ArrayPorcentagem[$contProduto - 1]["label"] = $debito->produto;
                        $ArrayPorcentagem[$contProduto - 1]["y"] = (float) $debito->porcentagem_produto;
                        ?>
                        <tr>
                            <td><?php echo $contProduto . " " . $debito->produto; ?></td>
                            <td><?php echo number_format($debito->total_produto, 2, ',', '.'); ?></td>
							<td><?php echo number_format($debito->total_qtd, 2, ',', '.'); ?></td>
							<td><?php echo number_format($debito->valor_media, 2, ',', '.'); ?></td>
							<td><?php echo $debito->unidade_medida; ?></td>
                            <td><?php echo number_format($debito->porcentagem_produto, 4, ',', '.'); ?></td>                            
                        </tr>
                        <?php
                        $contProduto ++;
                    }
                    echo 'Total Produto: ' . $totalProduto;
                    echo 'Total porcentagem: ' . $totalPorcentagem;
                    ?>
                </table>
            </div>

            <?php
        }
        ?>

        <script>
            window.onload = function () {

                var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    exportEnabled: true,
                    title: {
                        text: "Total de gasto por cada produto"
                    },
                    subtitles: [{
                            text: "Porcentagem em cima do total de R$ "
                        }],
                    data: [{
                            type: "pie",
                            showInLegend: "true",
                            legendText: "{label}",
                            indexLabelFontSize: 16,
                            indexLabel: "{label} - #percent%",
                            yValueFormatString: "฿#,##0",
                            dataPoints: <?php echo json_encode($ArrayPorcentagem, JSON_NUMERIC_CHECK); ?>
                        }]
                });
                chart.render();

            }
        </script>

        <div id="chartContainer" style="height: 370px; width: 100%;"></div>

    </div>
</div>
