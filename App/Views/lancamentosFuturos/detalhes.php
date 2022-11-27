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
            if (!count($viewVar['detalhesLancamentoFuturo'])) {
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
                            <td width="1"  class="info">Quntdd</td>
                            <td width="1"  class="info">Valor</td>
                            <td width="1"  class="info">Opções</td>
                        </tr>
                        <?php
                        foreach ($viewVar['detalhesLancamentoFuturo'] as $contasReceber) {
                            ?>
                            <tr>
                                <td><?php echo $contasReceber->getCodigo(); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($contasReceber->data_compra)); ?></td>
                                <td><?php echo $contasReceber->getProduto(); ?></td>
                                <td><?php echo $contasReceber->qtd_produto; ?></td>
                                <td><?php echo $contasReceber->valor_produto; ?></td>
                                <td>
                                    <a href="http://<?php echo APP_HOST; ?>/AgendaLancamento/excluirItem/<?php echo $contasReceber->getCodigo(); ?>" class="btn btn-danger btn-sm">Excluir</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>