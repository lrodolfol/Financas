<?php
$dataInicial = $_REQUEST['dataInicial'];
$dataFinal = $_REQUEST['dataFinal'];
$valorGravado = 0;
?>

<div class="container">
    <div class="starter-template">
        <h2>Extrato por periodo</h2>

        <fildset>
            <form action="http://<?php echo APP_HOST; ?>/extrato/extrato" method="POST" >
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataCompra">Periodo Inicial</label>
                            <input type="date" class="form-control"  name="dataInicial" value="<?php echo $dataInicial ?>" required >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataCompra">Periodo Final</label>
                            <input type="date" class="form-control"  name="dataFinal" value="<?php echo $dataFinal ?>" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-sm btn-success">Gerar Extrato</button>
            </form>
        </fildset>

        <?php
        //SE HOUVE SUBMIT NO BUTÃO. IRA GERAR O RELATORIO
        if (isset($viewVar['extratoPeriodo'])) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Cod</th>
                                <th>Dta.Lanç</th>
                                <th>Historico</th>
                                <th style="text-align:right">R$.Mov.</th>
                                <th style="text-align:right">Valor R$</th>
                                <th width="1px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($viewVar['extratoPeriodo'] as $extrato) {
                                $ehExclusao = false;
                                if (strpos($extrato->getDescricao(), "Exclusão") !== false) {
                                    $ehExclusao = true;
                                } else {
                                    $ehExclusao = false;
                                }

                                $cor = $extrato->getTipo() == "ENTRADA" ? $ehExclusao ? "red" : "blue" : "red";
                                $sinal = $extrato->getTipo() == "ENTRADA" ? $ehExclusao ? "-" : "+" : "-";

                                if ($extrato->getValor() > $valorGravado) { //APESAR DO REGISTRO SER DE SAIDA, PODE SER EXCLUSÃO DE DÉBITO, ENTÃO SE TORNA ENTRADA
                                    $cor = "blue";
                                    $sinal = "+";
                                }

                                if ($extrato->getTipo() == "ENTRADA" || $ehExclusao) {
                                    if ($extrato->getValor() > $valorGravado) {
                                        $valorMovimentado = $extrato->getValor() - $valorGravado;
                                    } else {
                                        $valorMovimentado = $valorGravado - $extrato->getValor();
                                    }
                                } else {
                                    if ($extrato->getValor() < $valorGravado) {
                                        $valorMovimentado = $valorGravado - $extrato->getValor();
                                    }
                                }

                                //$valorMovimentado = $extrato->getTipo() == "ENTRADA" ? $valorGravado + $extrato->getValor() : $valorGravado - $extrato->getValor();
                                ?>
                                <tr>
                                    <?php 
                                        $date = new DateTime($extrato->getData()); 
                                        $date = $date->format('d/m/Y');
                                    ?>
                                    <td><?php echo $extrato->getId(); ?></td>
                                    <td><?php echo $date; ?></td>
                                    <td><?php echo $extrato->getDescricao(); ?></td>
                                    <td align="right" style="color: <?php echo $cor ?>"><?php echo $sinal . number_format($valorMovimentado, '2', ',', '.'); ?></td>
                                    <td align="right"><?php echo number_format($extrato->getValor(), '2', ',', '.'); ?></td>
                                    <td>
                                        <?php
                                        if ($extrato->codigo_entrada > 0) {
                                            ?>
                                            <a href="#" class="btn btn btn-sm"><img src="http://<?php echo APP_HOST; ?>/public/images/go.png" /></a>
                                        <?php } else { ?>
                                             <a target="_blank" href="http://<?php echo APP_HOST; ?>/debito/detalhes/<?php echo base64_encode($extrato->codigo_saida_cabecalho); ?>" class="btn btn btn-sm"><img src="http://<?php echo APP_HOST; ?>/public/images/go.png" /></a>
                                                <?php
                                            }
                                            ?>

                                    </td>
                                </tr>
                                <?php
                                $valorGravado = $extrato->getValor();
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }
        ?>

    </div>
</div>
