<?php
$totalCredito = 0;
$totalDebito = 0;
$creditoMensal = [];
$debitoMensal = [];
$cont = 0;
$totalSaldo;
?>

<div class="container">
    <div class="starter-template">

        <?php
        if (count($viewVar['creditoMensal'])) {
            ?>
            <h2>Seus créditos mensais</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr>
                        <?php
                        foreach ($viewVar['creditoMensal'] as $contasReceber) {
                            ?> <td class="info"><?php echo $contasReceber->periodo_credito; ?> </td> 
                            <?php
                        }
                        ?>   
                    </tr>
                    <tr>
                        <?php
                        foreach ($viewVar['creditoMensal'] as $contasReceber) {
                            ?> <td><?php echo number_format($contasReceber->valor_credito, 2, ',', '.'); ?></td>
                            <?php
                            $totalCredito = $totalCredito + $contasReceber->valor_credito;
                            $creditoMensal[$cont] = $contasReceber->valor_credito;
                            $cont++;
                        }
                        ?>
                    </tr>

                </table>
            </div>
            <?php
            echo 'Total de R$ ' . number_format($totalCredito, 2, ',', '.');
        }
        ?>

        <?php
        if (count($viewVar['debitoMensal'])) {
            ?>
            <h2>Seus débitos mensais</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr>
                        <?php
                        foreach ($viewVar['debitoMensal'] as $debito) {
                            ?> <td class="info"><?php echo $debito->periodo_debito; ?> </td> 
                            <?php
                        }
                        ?>   
                    </tr>
                    <tr>
                        <?php
                        $cont = 0;
                        foreach ($viewVar['debitoMensal'] as $debito) {
                            ?> <td><?php echo number_format($debito->valor_debito, 2, ',', '.'); ?></td>
                            <?php
                            $totalDebito = $totalDebito + $debito->valor_debito;
                            $debitoMensal[$cont] = $debito->valor_debito;
                            $cont++;
                        }
                        ?>
                    </tr>

                </table>
            </div>
            <?php
            echo 'Total de R$ ' . number_format($totalDebito, 2, ',', '.');
        }
        ?>

        <h2>Totais</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <tr>
                    <?php
                    foreach ($viewVar['debitoMensal'] as $debito) {
                        ?> <td class="info"><?php echo $debito->periodo_debito; ?> </td> 
                        <?php
                    }
                    ?>   
                </tr>
                <tr>
                    <?php
                    $cont = 0;
                    for ($i = 0; $i < count($creditoMensal); $i++) {
                        $total = ($creditoMensal[$cont] - $debitoMensal[$cont]);
                        $color = $total <= 0 ? "red" : "blue";
                        ?> <td style="color: <?php echo $color; ?>"><?php echo number_format($total, 2, ',', '.'); ?></td>
                        <?php
                        $cont ++;
                        $totalSaldo = $totalSaldo + $total;
                    }
                    ?>
                </tr>

            </table>
        </div>
        <?php 
            echo "Total de  R$ " . number_format($totalSaldo,2,',','.');
        ?>



    </div>
</div>
