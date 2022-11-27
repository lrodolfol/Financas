<?php
$dataInicial = $_REQUEST['data_inicial'];
$dataFinal = $_REQUEST['data_final'];
$palavra = $_REQUEST['palavra'];
$valorTotalPesquisa = 0;
$ehPasta = false;
?>

<div class="container">
    <div class="row">
        <br>
        <div class="col-md-12">
           
            <form method="POST" action="http://<?php echo APP_HOST; ?>/Extrato/debitosSemComprovantes">
                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-2">
                            <h5>Total por página:</h5>
                            <div class="form-group">
                                <select name="totalPorPagina" id="totalPorPagina" class="form-control input-sm" onchange="this.form.submit()">
                                    <option value="5" <?php echo ($viewVar['totalPorPagina'] == "5") ? "selected" : ""; ?>>5</option>
                                    <option value="10" <?php echo ($viewVar['totalPorPagina'] == "10") ? "selected" : ""; ?>>10</option>
                                    <option value="15" <?php echo ($viewVar['totalPorPagina'] == "15") ? "selected" : ""; ?>>15</option>
                                    <option value="20" <?php echo ($viewVar['totalPorPagina'] == "20") ? "selected" : ""; ?>>20</option>
                                    <option value="25" <?php echo ($viewVar['totalPorPagina'] == "25") ? "selected" : ""; ?>>25</option>
                                    <option value="30" <?php echo ($viewVar['totalPorPagina'] == "30") ? "selected" : ""; ?>>30</option>
                                    <option value="35" <?php echo ($viewVar['totalPorPagina'] == "35") ? "selected" : ""; ?>>35</option>
                                    <option value="40" <?php echo ($viewVar['totalPorPagina'] == "40") ? "selected" : ""; ?>>40</option>
                                    <option value="45" <?php echo ($viewVar['totalPorPagina'] == "45") ? "selected" : ""; ?>>45</option>
                                    <option value="50" <?php echo ($viewVar['totalPorPagina'] == "50") ? "selected" : ""; ?>>50</option>
                                    <option value="5000" <?php echo ($viewVar['totalPorPagina'] == "50") ? "selected" : ""; ?>>5000</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <hr>
        </div>

        <div class="col-md-12">
            <?php if ($Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem(); ?>
                </div>
            <?php } ?>

            <?php
            if (!count($viewVar['listaDebito'])) {
                ?>
                <div class="alert alert-info" role="alert">Nenhum débito encontrado até agora</div>
                <?php
            } else {
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td width="1" class="info">Codigo</td>
                            <td class="info">Descrição</td>
                            <td class="info">Dta Compra</td>
                            <td class="info">Lugar Compra</td>
                            <td class="info">Forma Pag.</td>
                            <td class="info">Vr Total</td>
                            <td width="1"  class="info">Ativo</td>
                            <td width="250" class="info">Opções</td>
                        </tr>
                        <?php
                        foreach ($viewVar['listaDebito'] as $debito) {
                            $caminhoImagem = "http://" . APP_HOST . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/debitos/debito" . $debito->getCodigo() . "/debito_codigo_" . $debito->getCodigo() . ".jpg";
                            //O CAMINHO PARA CONDIÇÃO DO 'file_exists' NÃO DEVE SER FEITO ATRAVÉS DE URL. PRECISA USAR CAMINHO DE ARQUIVO NO DISCO RÍGIDO 
                            $caminhoCondicao = RAIZ_SITE . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/debitos/debito" . $debito->getCodigo() . "/debito_codigo_" . $debito->getCodigo() . ".jpg";
                            if (file_exists($caminhoCondicao)) {  
                                continue;
                            }
                            ?>
                            <tr>
                                <td><?php echo $debito->getCodigo(); ?></td>
                                <td><?php echo $debito->getobs(); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($debito->data_compra)); ?></td>
                                <td><?php echo $debito->getEstabelecimento(); ?></td>
                                <td><?php echo $debito->forma_pagamento; ?></td>
                                <td><?php echo number_format($debito->valor_total, '2', ',', '.'); ?></td>
                                <?php $valorTotalPesquisa = $valorTotalPesquisa + $debito->valor_total; ?>
                                <td><?php echo $debito->getAtivo(); ?></td>
                                <td>
                                    <a href="http://<?php echo APP_HOST; ?>/debito/detalhes/<?php echo $debito->getCodigo(); ?>" class="btn btn-info btn-sm">Detalhes</a>
                                    <?php
                                    
                                    
                                    if (file_exists($caminhoCondicao)) {   
                                      ?> <a href="http://<?php echo APP_HOST; ?>/debito/openFile/<?php echo base64_encode("-public-comprovantes-" . $Sessao::retornaUsuario() . "-debitos-debito" . $debito->getCodigo() . "-debito_codigo_" . $debito->getCodigo() . ".jpg"); ?>" target="_blank"> <img src="http://<?php echo APP_HOST; ?>/public/images/visualizar.png" /></a> <?php    
                                    } else {
                                      ?> <a target="_blank"> <img src="http://<?php echo APP_HOST; ?>/public/images/visualizar-no.png" /></a> <?php  
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
                echo $viewVar['paginacao'];
            }
            echo 'Total de R$ <b>' . number_format($valorTotalPesquisa, '2', ',', '.') . '</b>';
            ?>
        </div>
    </div>
</div>