<?php
$carteiras = $viewVar['carteiras'];
$dataHoje = DATE('d/m/Y');
?>

<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <fildset>
                <legend>Transferência entre carteiras 
                    <img title="Transferência entre carteiras" src="<?php echo "http://" . APP_HOST . "/public/images/wllet-transfer.ico" ?> ">
                </legend>
                <form action="http://<?php echo APP_HOST; ?>/carteiras/transferencia" method="post" id="form_cadastro" enctype="multipart/form-data">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observacao">observações</label>
                                <input type="text" class="form-control"  name="observacao" placeholder="observacao. (opcional)" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valor_transferencia">Valor transf</label>
                                <input type="number" class="form-control" min="0.00" step="0.01" name="valor_transferencia" value ="0.00" required>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="data_transferencia">Data da transf.</label>
                                <input type="date" class="form-control"  name="data_transferencia" value="<?php echo $dataHoje; ?>" required>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="carteira_debito">Retirar de: </label>
                                <select class="form-control" for="carteira_debito" name="carteira_debito" required>
                                    <option></option>
                                    <?php foreach ($carteiras as $carteira) { ?>
                                        <option value="<?= $carteira->id; ?>">
                                            <?= ($carteira->nome) . ' R$' . number_format($carteira->valor, 2, ',', '.'); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="carteira_credito">Colocar em: </label>
                                <select class="form-control" for="carteira_credito" name="carteira_credito" required>
                                    <option></option>
                                    <?php foreach ($carteiras as $carteira) { ?>
                                        <option value="<?= $carteira->id; ?>">
                                            <?= ($carteira->nome) . ' R$' . number_format($carteira->valor, 2, ',', '.'); ?>
                                        </option>
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