<div class="container">
    <div class="row">
        <br>
        <div class="col-md-12">
            <a href="http://<?php echo APP_HOST; ?>/formaPagamento/novo" class="btn btn-success btn-sm">Adicionar</a>
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
            if (!count($viewVar['listaFormaPagamento'])) {
                ?>
                <div class="alert alert-info" role="alert">Nenhuma forma de pagamento encontrada até agora</div>
                <?php
            } else {
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td class="info">Codigo</td>
                            <td class="info">Descrição</td>
                            <td class="info">Ativo</td>
                            <td width="5" class="info">Fechamento</td>
                            <td width="5" class="info">Vencimento</td>
                            <td width="250" class="info">Opções</td>
                        </tr>
                        <?php
                        foreach ($viewVar['listaFormaPagamento'] as $FormaPagamento) {
                            $cripty = base64_encode(1995 + $FormaPagamento->getCodigo(). "77FIN" . $FormaPagamento->getDescricao());
                            ?>
                            <tr>
                                <td><?php echo $FormaPagamento->getCodigo(); ?></td>
                                <td><?php echo $FormaPagamento->getDescricao(); ?></td>
                                <td><?php echo $FormaPagamento->getAtivo(); ?></td>
                                <td><?php echo $FormaPagamento->dia_fechamento; ?></td>
                                <td><?php echo $FormaPagamento->dia_vencimento; ?></td>
                                <td>
                                    <a href="http://<?php echo APP_HOST; ?>/formaPagamento/excluir/<?php echo $FormaPagamento->getCodigo(); ?>" onclick="return confirm('Deletar forma de pagamento?')"  class="btn btn-danger btn-sm">Excluir</a>
                                    <a href="http://<?php echo APP_HOST; ?>/formaPagamento/edicao/<?php echo $cripty; ?>" class="btn btn-success btn-sm">Editar</a>
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