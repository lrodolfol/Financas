<?php
$saldo = $viewVar["mostraSaldo"]["saldo"];
$lucro = $viewVar["mostraSaldo"]["lucro"];
$lucroReal = $viewVar["mostraSaldo"]["lucro_real"];
$saldoProximoMes = $viewVar['proximoMes'];
$corSaldo = "";
$corLucro = "";
$diasLancamentoFuturosDebito = (isset($_POST['proximosDiasDebitos'])) ? $_POST['proximosDiasDebitos'] : 5;
$diasCreditoFuturo = (isset($_POST['proximosDiasCreditos'])) ? $_POST['proximosDiasCreditos'] : 5;
if ($saldo < 0) {
    $corSaldo = "red";
} else {
    $corSaldo = "blue";
}
if ($lucro < 0) {
    $corLucro = "red";
} else {
    $corLucro = "blue";
}
$evolucaoValor = 0;

$dizimo = function($valor) {
    $percentualDizimo = 10;
    return ( ( $valor / 100) * $percentualDizimo);
};
$formataValor = function($valor) {
    return (isset($valor) ? number_format($valor, 2, ',', '.') : 0);
};
$retornaValorPorcentagem = function($valorMesAnterior, $valorMesAtual) {
    if(! isset($valorMesAnterior)) {
        $valorMesAnterior = $valorMesAtual;
    }
    $valorMaior = $valorMesAnterior > $valorMesAtual ? $valorMesAnterior : $valorMesAtual;
    $valorMenor = $valorMesAnterior < $valorMesAtual ? $valorMesAnterior : $valorMesAtual;
    
  /*  $valorMaior = isset($valorMaior) ? $valorMaior : 0;
    $valorMenor = isset($valorMenor) ? $valorMenor : 0;
        
    $valor = (($valorMaior - $valorMenor));
    $valor = $valorMenor = 0 ? ($valor / $valorMenor) : $valor;
    $valor = $valor * 100;*/
    
    //$valor = (($valorMaior - $valorMenor) / $valorMenor) * 100;
    if($valor <= 0) {
        $valor = 0;
        $valor = (($valorMaior - $valorMenor) / $valorMenor) * 100;   
    }else{
        $valor = (($valorMaior - $valorMenor) / $valorMenor) * 100;   
    }
    
    if($valorMaior == $valorMesAnterior) {
        $valor *= -1;
    }
    return number_format($valor, 2, ',', '.');
}
?>
<div class="container">

    <div class="starter-template">

        <div class="col-md-12">
            <h2><?php echo 'Bem vindo, ' . ucfirst($usuarioLogado) . ".	"; ?></h2>
            <div class="col-md-10">
                <p>Você tem em caixa um valor total de R$
                    <i style="color: <?php echo $corSaldo; ?>">
                        <?php echo $formataValor($saldo); ?>
                    </i>
                    <i>
                        <?php echo "(" . $formataValor($saldoProximoMes) . ") para o próximo mês " ?>
                    </i>
                </p>

                <p>Esse mês você esta com um saldo <?php echo $lucro >= 0 ? "positivo" : "negativo"; ?>
                    de R$
                    <i style="color: <?php echo $corLucro; ?>">
                        <?php echo $formataValor($lucro); ?>
                    </i> (R$ de entrada - R$ de saida desse mês)  </p>
            </div>

            <div class="col-md-2">
                <p>Dizimo: R$
                    <i style="color: <?php echo $corLucro; ?>">
                        <?php echo number_format($dizimo($lucroReal), 2, ',', '.'); ?>
                    </i>
            </div>

        </div>

        <div class="col-md-6">
            <h3>Contas a Pagar</h3>
            <?php
            if (\App\Lib\Sessao::retornaValor()) {
                $debitoFuturo = $formataValor(App\Lib\Sessao::retornaValor());
                ?>
                <form action="http://<?php echo APP_HOST; ?>/home/index" method="post" >
                    <?php echo "Você possui débitos no total de R$" . $debitoFuturo . " para os próximos " ?> 
                    <input class="input-sm" type="text" maxlength="3" name="proximosDiasDebitos" size="1" value="<?php echo $diasLancamentoFuturosDebito; ?>"> <?php echo " dias "; ?>
                    <button type="submit" class="btn btn-sm btn-success">&#10003;</button>
                </form>
                <?php
            } else {
                ?>
                <form action="http://<?php echo APP_HOST; ?>/home/index" method="post" >
                    <?php echo 'Você não possui débitos futuros. '; ?> <input class="input-sm" type="text" maxlength="3" name="proximosDiasDebitos" size="1" value="<?php echo $diasLancamentoFuturosDebito; ?>"> <?php echo " dias "; ?>
                    <button type="submit" class="btn btn-sm btn-success">&#10003;</button>
                </form> 
                <?php
            }

            $saldoRestante = number_format($saldo - (isset($debitoFuturo) ? $debitoFuturo : 0), '2', ',', '.');
            ?>

            <b><u>ATENÇÃO! seu saldo será de R$ <?php echo $saldoRestante; ?> a partir daí.</u></b>
        </div>

        <div class="col-md-6">
            <h3>Contas a Receber</h3>
            <?php
            $creditoFuturo = (isset($viewVar["creditoFuturo"]) ? $viewVar["creditoFuturo"] : 0);
            if ($creditoFuturo > 0) {
                ?>
                <form action="http://<?php echo APP_HOST; ?>/home/index" method="post" >
                    <?php echo "Você possui créditos no total de R$" . $creditoFuturo . " para os próximos " ?> 
                    <input class="input-sm" type="text" maxlength="3" name="proximosDiasCreditos" size="1" value="<?php echo $diasCreditoFuturo; ?>"> <?php echo " dias "; ?>
                    <button type="submit" class="btn btn-sm btn-success">&#10003;</button>
                </form>
                <?php
            } else {
                ?>
                <form action="http://<?php echo APP_HOST; ?>/home/index" method="post" >
                    <?php echo 'Você não possui créditos futuros. '; ?> <input class="input-sm" type="text" maxlength="3" name="proximosDiasCreditos" size="1" value="<?php echo $diasCreditoFuturo; ?>"> <?php echo " dias "; ?>
                    <button type="submit" class="btn btn-sm btn-success">&#10003;</button>
                </form> 
                <?php
            }

            $saldoRestante = number_format($saldo + $creditoFuturo, '2', ',', '.');
            ?>

            <b><u>ATENÇÃO! seu saldo será de R$ <?php echo $saldoRestante; ?> a partir daí.</u></b>
        </div>

    </div>
</div>

<div class="grafico" style="margin-top: 5%">
    <?php
//echo $viewVar["graficoSaldo"]; die(); 

    $dataPointsMax = array(count($viewVar["graficoSaldoMax"]));
    $cont = 0;
    foreach ($viewVar["graficoSaldoMax"] as $grafico => $value) {
        $dataPointsMax[$cont] = array(
            "y" => $value["total"],
            "label" => $value["periodo"]
        );
        $cont++;
    }
   
    $ultimoVr = 0;
    $pultimoVr = 0;
    $valor = 0;
    $evolucaoValor = 0;
    if(isset($viewVar["graficoSaldoMax"]) && count($viewVar["graficoSaldoMax"]) > 0) {
        $ultimoVr = $viewVar["graficoSaldoMax"][count($viewVar["graficoSaldoMax"]) - 1]["total"];
        $pultimoVr = $viewVar["graficoSaldoMax"][count($viewVar["graficoSaldoMax"]) - 2]["total"];
        $valor = $retornaValorPorcentagem($pultimoVr, $ultimoVr);
        $evolucaoValor = $ultimoVr - $pultimoVr;
    }
    
    /* $dataPoints = array(
      array("y" => 25, "label" => "Sunday"),
      array("y" => 15, "label" => "Monday"),
      array("y" => 25, "label" => "Tuesday"),
      array("y" => 5, "label" => "Wednesday"),
      array("y" => 10, "label" => "Thursday"),
      array("y" => 0, "label" => "Friday"),
      array("y" => 20, "label" => "Saturday")
      );


      foreach($viewVar["graficoSaldoMax"] as $grafico => $value) {
      //echo var_dump($value) . '<br>';
      array("y" => $value["perioro"], "label" => $value["total"]);
      } */

    /* array("y" => 25, "label" => "Sunday"),
      array("y" => 15, "label" => "Monday"),
      array("y" => 25, "label" => "Tuesday"),
      array("y" => 5, "label" => "Wednesday"),
      array("y" => 10, "label" => "Thursday"),
      array("y" => 0, "label" => "Friday"),
      array("y" => 20, "label" => "Saturday") */
    
     
    ?>

    <?php if($evolucaoValor <= 0) : ?>
    <div class="container">
        <div class="starter-template">
            <div class="col-md-12">
                <b><u>O gráfico de rentabilidade irá apacer após a 1ª movimentação de caixa</u></b>
            </div>    
        </div>
    </div>
    
    <?php endif; ?>
    
    <script>
        window.onload = function () {

            var chart = new CanvasJS.Chart("chartContainer", {
                title: {
                    text: "Variação de Saldo / Mês (1ano).   <?php echo $valor . "%" . "   (" . $formataValor($evolucaoValor) . ")" ; ?>" 
                },
                axisY: {
                    title: "Saldo em R$" 
                },
                data: [{
                        type: "line",
                        dataPoints: <?php echo json_encode($dataPointsMax, JSON_NUMERIC_CHECK); ?>
                    }]
            });
            chart.render();

        }
    </script>

    <div id="chartContainer" style="height: 370px; width: 60%;"></div>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</div>

