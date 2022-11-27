<div class="container">
    <div class="row">
        <br>
        <div class="col-md-12">
            <?php if ($Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem(); ?>
                </div>
            <?php } ?>

            <?php
            if (!count($viewVar['detalhesDebito'])) {
                ?>
                <div class="alert alert-info" role="alert">Detalhes de débito não encontrado</div>
                <?php
            } else {
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td width="1"  class="info">Codigo</td>
                            <td width="1" class="info">Dta.Compra</td>
                            <td width="250" class="info">Produto</td>
                            <td width="1" class="info">UND Medida</td>
                            <td width="1"  class="info">Quntdd</td>
                            <td width="1"  class="info">Valor</td>
                            <td width="1"  class="info">Opções</td>
                        </tr>
                        <?php
                        $valorTotalDetalhes = 0;
                        foreach ($viewVar['detalhesDebito'] as $debito) {
                            $valorTotalDetalhes += ($debito->valor_produto * $debito->qtd_produto );
                            ?>
                            <tr>
                                <td><?php echo $debito->getCodigo(); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($debito->data_compra)); ?></td>
                                <td><?php echo $debito->getProduto(); ?></td>
                                <td><?php echo $debito->unidade_medida; ?></td>
                                <td><?php echo number_format($debito->qtd_produto, 2, ',', ''); ?></td>
                                <td><?php echo number_format($debito->valor_produto, 2, ',', '.'); ?></td>
                                <td>
                                    <a href="http://<?php echo APP_HOST; ?>/debito/excluirItem/<?php echo $debito->getCodigo(); ?>" onclick="return confirm('Deletar item de débito?')"  class="btn btn-danger btn-sm">Excluir</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
                echo "Total de R$ " . number_format($valorTotalDetalhes, 2, ',', '') . " em produtos para esse débito.";
            }
            ?>
        </div>
    </div>
</div>