<?php
$dataInicial = $_REQUEST['data_inicial'];
$dataFinal = $_REQUEST['data_final'];
$palavra = $_REQUEST['palavra'];
$valorTotalPesquisa = 0;
$ehPasta = false;

$tabelaPrint = '<table>'; //abre table
$tabelaPrint .= '<thead>'; //abre cabeçalho
$tabelaPrint .= '<tr>'; //abre uma linha

$tabelaPrint .= '<th>Codigo</th>'; //cabecalho 
$tabelaPrint .= '<th>Desc</th>'; //cabecalho 
$tabelaPrint .= '<th>Data</th>'; //cabecalho 
$tabelaPrint .= '<th>Lugar</th>'; //cabecalho 
$tabelaPrint .= '<th>Pag</th>'; //cabecalho 
$tabelaPrint .= '<th>Valor</th>'; //cabecalho 
$tabelaPrint .= '</tr>'; //cabecalho 
$tabelaPrint .= '</thead>'; //fecha cabeçalho
$tabelaPrint .= '<tbody>'; //abre corpo da tabela

$jsonDados = array(
    array("codigo" => 0,
        "valor" => 10),
    array("codigo" => 0,
        "valor" => 10)
);
$jsonDados = null;
$cont = 0;
?>

<div class="container">
    <div class="row">
        <br>
        <div class="col-md-12">
            <a href="http://<?php echo APP_HOST; ?>/debito/novo" class="btn btn-success btn-sm">Adicionar</a>


            <form method="POST" action="http://<?php echo APP_HOST; ?>/debito/index">
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

                        <div class="col-md-5">
                            <h5>Busca por periodo</h5>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="data_inicial" value="<?php echo $dataInicial ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="data_final" value="<?php echo $dataFinal ?>">
                                </div>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <h5>Busca por palavra</h5>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="palavra" value="<?php echo $palavra ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <h5>.</h5>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-sm">Buscar</button>
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
                            ?>
                            <tr>
                                <td><?php echo $debito->getCodigo() . ($debito->getAtipico() == 'S' ? ' - A' : '' ); ?></td>
                                <td><?php echo $debito->getobs(); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($debito->data_compra)); ?></td>
                                <td><?php echo $debito->getEstabelecimento(); ?></td>
                                <td><?php echo $debito->forma_pagamento; ?></td>
                                <td><?php echo number_format($debito->total_geral, '2', ',', '.'); ?></td>
                                <?php $valorTotalPesquisa = $valorTotalPesquisa + $debito->total_geral; ?>
                                <td><?php echo $debito->getAtivo(); ?></td>
                                <td>
                                    <?php $cripty = base64_encode($debito->getCodigo()) . "99Fin" . base64_encode(\App\Lib\Sessao::retornaUsuario() . $debito->getobs()); ?>

                                    <a href="http://<?php echo APP_HOST; ?>/debito/edicao/<?php echo $cripty; ?>" class="btn btn-success btn-sm">Editar</a>
                                    <a href="http://<?php echo APP_HOST; ?>/debito/excluir/<?php echo $debito->getCodigo(); ?>" onclick="return confirm('Deletar débito??')"  class="btn btn-danger btn-sm">Excluir</a>
                                    <a href="http://<?php echo APP_HOST; ?>/debito/detalhes/<?php echo base64_encode($debito->getCodigo()); ?>" class="btn btn-info btn-sm">Detalhes</a>
                                    <?php
                                    $caminhoImagem = "http://" . APP_HOST . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/debitos/debito" . $debito->getCodigo() . "/debito_codigo_" . $debito->getCodigo() . ".jpg";

                                    //O CAMINHO PARA CONDIÇÃO DO 'file_exists' NÃO DEVE SER FEITO ATRAVÉS DE URL. PRECISA USAR CAMINHO DE ARQUIVO NO DISCO RÍGIDO 
                                    $caminhoCondicao = RAIZ_SITE . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/debitos/debito" . $debito->getCodigo() . "/debito_codigo_" . $debito->getCodigo() . ".jpg";
                                    if (file_exists($caminhoCondicao)) {
                                        ?> <a href="http://<?php echo APP_HOST; ?>/debito/openFile/<?php echo base64_encode("-public-comprovantes-" . $Sessao::retornaUsuario() . "-debitos-debito" . $debito->getCodigo() . "-debito_codigo_" . $debito->getCodigo() . ".jpg"); ?>" target="_blank"> <img src="http://<?php echo APP_HOST; ?>/public/images/visualizar.png" /></a> <?php
                                    } else {
                                        ?> <a target="_blank"> <img src="http://<?php echo APP_HOST; ?>/public/images/visualizar-no.png" /></a> <?php
                                        }
                                        ?>
                                </td>
                            </tr>
                            <?php
                            if (strtoupper($debito->getAtivo()) == 'S') {
                                $tabelaPrint .= '<tr>';
                                $tabelaPrint .= '<td>' . $debito->getCodigo() . '</td>';
                                $tabelaPrint .= '<td>' . $debito->getobs() . '</td>';
                                $tabelaPrint .= '<td>' . date('d/m/Y', strtotime($debito->data_compra)) . '</td>';
                                $tabelaPrint .= '<td>' . $debito->getEstabelecimento() . '</td>';
                                $tabelaPrint .= '<td>' . $debito->forma_pagamento . '</td>';
                                $tabelaPrint .= '<td>' . number_format($debito->total_geral, '2', ',', '.') . '</td>';
                                $tabelaPrint .= '</tr>';

                                $jsonDados[$cont] = array(
                                    "data" => date('d/m/Y', strtotime($debito->data_compra)),
                                    "descricao" => $debito->getObs(),
                                    "lugar" => $debito->getEstabelecimento(),
                                    "valor" => number_format($debito->total_geral, '2', ',', '.')
                                );
                            }
                            $cont++;
                        }

                        $tabelaPrint .= '</tbody>';
                        $tabelaPrint .= '</table>';
                        ?>
                    </table>

                    <a href="http://<?php echo APP_HOST; ?>/debito/print/<?php echo base64_encode(json_encode($jsonDados)); ?>?2021-10-05" class="btn btn-success btn-sm"> 
                        <img src="http://<?php echo APP_HOST; ?>/public/images/printer.ico" />  Imprimir
                    </a>

                </div>
                <?php
                echo $viewVar['paginacao'];
            }
            echo 'Total de R$ <b>' . number_format($valorTotalPesquisa, '2', ',', '.') . '</b>';
            ?>
        </div>
    </div>
</div>