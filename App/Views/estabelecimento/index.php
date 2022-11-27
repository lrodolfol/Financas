<div class="container">
    <div class="row">
        <br>
        <div class="col-md-12">
            <a href="http://<?php echo APP_HOST; ?>/estabelecimento/novo" class="btn btn-success btn-sm">Adicionar</a>
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
            if (!count($viewVar['listaEstabelecimento'])) {
                ?>
                <div class="alert alert-info" role="alert">Nenhum estabelecimento encontrada até agora</div>
                <?php
            } else {
                ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td class="info">Codigo</td>
                            <td class="info">Nome</td>
                            <td class="info">Ativo</td>
                            <td width="250" class="info">Opções</td>
                        </tr>
                        <?php
                        foreach ($viewVar['listaEstabelecimento'] as $Estabelecimento) {
                            $cripty = base64_encode(1995 * ($Estabelecimento->getCodigo() + 3));
                            ?>
                            <tr>
                                <td><?php echo $Estabelecimento->getCodigo(); ?></td>
                                <td><?php echo $Estabelecimento->getNome(); ?></td>
                                <td><?php echo $Estabelecimento->getAtivo(); ?></td>
                                <td>
                                    <a href="http://<?php echo APP_HOST; ?>/estabelecimento/excluir/<?php echo $Estabelecimento->getCodigo(); ?>" onclick="return confirm('Deletar estabelecimento?')"  class="btn btn-danger btn-sm">Excluir</a>
                                    <a href="http://<?php echo APP_HOST; ?>/estabelecimento/edicao/<?php echo $cripty; ?>" class="btn btn-info btn-sm">Editar</a>
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