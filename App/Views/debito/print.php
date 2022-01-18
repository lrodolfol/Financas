<?php
use Dompdf\Dompdf;
use Dompdf\Options;

$jsonDados = base64_decode($viewVar['jsonDados'][0]);
$jsonDados = json_decode($jsonDados, true);

$dataInicio = $jsonDados[0]['data'];
$dataFim = $jsonDados[count($jsonDados) - 1]['data'];
ob_start();

$optionsDomPdf = new Options();
$optionsDomPdf->setIsRemoteEnabled(true);
$domPdf = new Dompdf($optionsDomPdf);
$coresTabela = ['#C0C0C0', 'white'];
$cont = 0;

//$html = "<img src='http://" . RAIZ_SITE . "/public/images/pencil.ico'";

$html .= "<h2 style='text-align: center'>Sistema Finanças</h2>";
$html .= "<h3 style='text-align: center'> Relatório de debitos por periodo $dataInicio a $dataFim</h3>";
$html .= "<table border=1px solid black>
            <thead>
                <tr>
                    <td style='text-align:center'>Data Compra</td>
                    <td style='text-align:center'>Descrição/Observações</td> 
                    <td style='text-align:center'>Lugar Compra</td>
                    <td style='text-align:center'>Valor Total</td>
                </tr>
            </thead>

            <tbody>";
                foreach ($jsonDados as $debito => $value) {
                   $html .= "<tr style='background-color: $coresTabela[$cont]'> 
                        <td>" . date('d/m/Y', strtotime($value->data)) . "</td>
                        <td>" . $value->descricao ."</td>
                        <td>" . $value->lugar . "</td>
                        <td>" . number_format($value->valor, '2', ',', '.') . "</td>
                    </tr>";
                   
                   $cont = $cont >= count($coresTabela) - 1 ? 0 : $cont + 1;
                   
                }
            $html .= "</tbody>";
        $html .= "</table>";
                
        $hml .= "<footer>";
        $html .= "<p style='text-align: center'>Develop by: TNN - Sistema finanças</p>";
        $html .= "<p style='text-align: center'>Developer: Rodolfo J.Silva</p>";
        $html .= "<p style='text-align: center'>"; $html .= NOME_SITE; $html .= "</p>";
        $html .= "</footer>";
        
$domPdf->loadHtml($html);
$domPdf->setPaper("A4");
$domPdf->render();
$domPdf->stream('relatorio-debito.pdf');
?>

<div class="container">
    <div class="row">      

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <td class="info">Dta Compra</td>
                    <td class="info">Descrição</td> 
                    <td class="info">Lugar Compra</td>
                    <td class="info">Vr Total</td>
                </tr>
            </thead>

            <tbody>
                MOSTRANDO DADOS
                <?php
                foreach ($jsonDados as $debito => $value) {
                    ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($value->data)); ?></td>
                        <td><?php echo $value->descricao; ?></td>
                        <td><?php echo $value->lugar; ?></td>
                        <td><?php echo number_format($value->valor, '2', ',', '.'); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

    </div> 
</div>

