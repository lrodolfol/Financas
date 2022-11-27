<?php
$formataValor = function($valor) {
    return number_format($valor, 2, '.', '.');
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-12">
            <fildset>
                <legend>Alteração de dados cabeçalho do débito</legend>
                <form action="http://<?php echo APP_HOST; ?>/debito/atualizar" method="post" id="form_cadastro" enctype="multipart/form-data">
                    <?php foreach ($viewVar['debito'] as $debito) { ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dataCompra">Data da compra</label>
                                    <input type="date" value="<?php echo $debito->data_compra ?>" class="form-control"  name="dataCompra" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="valorTotal">Valor total do débito</label>
                                    <input type="number" value="<?php echo $formataValor($debito->valor_total) ?>" class="form-control"  name="valorTotal" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="valorTotal">Valor de Juros</label>
                                    <input type="number" value="<?php echo $formataValor($debito->getJuros()) ?>" class="form-control" min="0.00" step="0.01" name="juros" placeholder="juros" value="0.00" required>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label for="valorTotal">Valor de desconto</label>
                                    <input type="number" value="<?php echo $formataValor($debito->getDesconto()) ?>" class="form-control" min="0.00" step="0.01" name="desconto" placeholder="desconto" value="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="estabelecimento">Estabelecimento</label>
                                    <select class="form-control" for="estabelecimento" name="estabelecimento" >Estabelecimento
                                        <option></option>
                                        <?php
                                        foreach ($viewVar['estabelecimentos'] as $estabelecimento) {
                                            ?><option value="<?php echo $estabelecimento->getCodigo(); ?>" > <?php echo $estabelecimento->getNome(); ?> </option> <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="formaPagamento">Forma de pagamento</label>
                                    <select class="form-control" for="formaPagamento" name="formaPagamento" >Forma de Pagamento
                                        <option></option>
                                        <?php
                                        foreach ($viewVar['formaPagamento'] as $pagamento) {
                                            ?><option value="<?php echo $pagamento->getCodigo(); ?>" > <?php echo $pagamento->getDescricao(); ?> </option> <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="qtdParcelas">NºParcelas</label>
                                    <input type="number" value="<?php echo $debito->qtd_parcelas ?>" class="form-control" name="qtdParcelas" required readonly="true">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fixo">Despesa mensal fixa pelos próximos</label>
                                    <select class="form-control" for="fixo" name="fixo" >
                                        <?php
                                        for ($i = 0; $i < 25; $i++) {
                                            ?> <option value="<?php echo $i ?>" ><?php echo $i . ' Meses'; ?> </option> <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ativo">Ativo</label>
                                    <input type="checkbox" name="ativo" value="fixo" <?php if ($debito->getAtivo() == 'S') { ?> checked="true" <?php } ?> value="aativo" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="observacao">Observação</label>
                                    <textarea class="form-control" name="observacao" required><?php echo $debito->getObs() ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="arquivo">Imagem comprovante</label>
                                    <input type="file" class="form-control" name="arquivo_imagem">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="codigo" value="<?php echo $debito->getCodigo() ?>">

                    <?php } ?>
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
        <div class=" col-md-3"></div>
    </div>
</div>