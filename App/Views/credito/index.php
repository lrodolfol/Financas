<?php
$dataInicial = $_REQUEST['data_inicial'];
$dataFinal = $_REQUEST['data_final'];
$palavra = $_REQUEST['palavra'];
$lucroReal = isset($_REQUEST['lucro_real']) ? 'S' : 'N';
$valorTotalPesquisa = 0;
?>

<div class="container">
    <div class="row">
        <br>
        <div class="col-md-12">
            <a href="http://<?php echo APP_HOST; ?>/credito/novo" class="btn btn-success btn-sm">Adicionar</a>

            <form method="POST" action="http://<?php echo APP_HOST; ?>/credito/index">
                <div class="col-md-12">
                    <div class="row">
                        
                        <div class="col-md-1">
                            <h5>Lucro real</h5>
                            <div class="form-group">
                            <input type="checkbox" name="lucro_real" <?php if($lucroReal == 'S') { ?> checked <?php } else { ?>  <?php } ?> >
                            </div>
                        </div>
                        
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

                        <div class="col-md-4">
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

                        <div class="col-md-2">
                            <h5>Busca por palavra</h5>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="palavra" value="<?php echo $palavra ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1">
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
            if (!count($viewVar['listaCredito'])) {
                ?>
                <div class="alert alert-info" role="alert">Nenhum débito encontrado até agora</div>
                <?php
            } else {
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td class="info">Codigo</td>
                            <td class="info">Descricao</td>
                            <td class="info">Valor</td>
                            <td class="info">Data</td>
                            <td class="info">obs</td>
                            <td class="info">Ativo</td>
                            <td width="250" class="info">Opções</td>
                        </tr>
                        <?php
                        foreach ($viewVar['listaCredito'] as $contasReceber) {
                            $valorTotalPesquisa += $contasReceber->getValor();
                            ?>
                            <tr>
                                <td><?php echo $contasReceber->getCodigo(); ?></td>
                                <td><?php echo $contasReceber->getDescricao(); ?></td>
                                <td><?php echo number_format($contasReceber->getValor(), '2', ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($contasReceber->data)); ?></td>
                                <td><?php echo $contasReceber->obs; ?></td>
                                <td><?php echo $contasReceber->getAtivo(); ?></td>
                                <td>
                                    <?php $cripty =  base64_encode($contasReceber->getCodigo()) . "99Fin" . base64_encode(\App\Lib\Sessao::retornaUsuario() . $contasReceber->getDescricao()); ?>
                                    
                                    <a href="http://<?php echo APP_HOST; ?>/credito/edicao/<?php echo $cripty; ?>" class="btn btn-success btn-sm">Editar</a>
                                    <a href="http://<?php echo APP_HOST; ?>/credito/excluir/<?php echo $contasReceber->getCodigo(); ?>" onclick="return confirm('Deletar crédito?')" class="btn btn-danger btn-sm">Excluir</a>
                                    
                                    <?php
                                    $caminhoImagem = "http://" . APP_HOST . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/creditos/credito" . $contasReceber->getCodigo() . "/credito_codigo_" . $contasReceber->getCodigo() . ".jpg";
                                    
                                    //O CAMINHO PARA CONDIÇÃO DO 'file_exists' NÃO DEVE SER FEITO ATRAVÉS DE URL. PRECISA USAR CAMINHO DE ARQUIVO NO DISCO RÍGIDO 
                                    $caminhoCondicao = RAIZ_SITE . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/creditos/credito" . $contasReceber->getCodigo() . "/credito_codigo_" . $contasReceber->getCodigo() . ".jpg";
                                    if (file_exists($caminhoCondicao)) {
  									  ?> <a href="http://<?php echo APP_HOST; ?>/credito/openFile/<?php echo base64_encode("-public-comprovantes-" . $Sessao::retornaUsuario() . "-creditos-credito" . $contasReceber->getCodigo() . "-credito_codigo_" . $contasReceber->getCodigo() . ".jpg"); ?>" target="_blank"> <img src="http://<?php echo APP_HOST; ?>/public/images/visualizar.png" /></a> <?php    
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
                echo 'Total de R$' . number_format($valorTotalPesquisa, 2, ',', '.');
                echo $viewVar['paginacao'];
            }
            ?>
        </div>
    </div>
</div>