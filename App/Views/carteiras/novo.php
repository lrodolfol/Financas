<?php
$formasPagamento = $viewVar['formasPagamento'];
?>
<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <fildset>
                <legend>Nova Carteira</legend>
                <form action="http://<?php echo APP_HOST; ?>/carteiras/salvar" method="post" id="form_cadastro" enctype="multipart/form-data">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome_forma_pagamento">nome</label>
                                <input type="text" class="form-control"  name="nome_forma_pagamento" placeholder="Nubank.. PicPay.. Cofre.. Bolso.. etc.." required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valor_forma_pagamento">Valor total do débito</label>
                                <input type="number" class="form-control" min="0.00" step="0.01" name="valor_forma_pagamento" placeholder="Valor na carteira hoje" value ="0.00" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="codigo_forma_pagamento">Vinculo Forma pagamento</label>
                                <select class="form-control" for="estabelecimento" name="codigo_forma_pagamento" required>
                                    <option></option>
                                    <?php foreach ($formasPagamento as $estabelecimento) { ?>
                                        <option value="<?= $estabelecimento->getCodigo(); ?>"><?= $estabelecimento->getDescricao(); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                </form>
            </fildset>

            <?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem() ?>
                </div>
            <?php } ?>
            <?php if (\App\Lib\Sessao::retornaErro()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaErro() ?>
                </div>
            <?php } ?>

        </div>
        <div class="col-md-3"></div>
    </div>
</div>