<?php
$dataHoje = DATE('d/m/Y');
if (isset($_SESSION['codigo'])) {
    ?> <script>
        window.location.href='#form_cadastro';
    </script>
    <?php
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-12">
            <fildset>
                <legend>Cabeçalho do débito</legend>
                <form action="http://<?php echo APP_HOST; ?>/debito/salvar" method="post" id="form_cadastro" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="dataCompra">Data da compra</label>
                                <input type="date" class="form-control"  name="dataCompra" placeholder="data da compra" value="<?php echo $dataHoje; ?>" required>
                            </div>
                        </div>
                        <!-- <div class="col-md-4">
                             <div class="form-group">
                                 <label for="dataDebito">Data do débito</label>
                                 <input type="date" class="form-control"  name="dataDebito" placeholder="data do debito" required>
                             </div>
                         </div> -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="valorTotal">Valor total do débito</label>
                                <input type="number" class="form-control" min="0.00" step="0.01" name="valorTotal" placeholder="valorTotal" value ="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="valorTotal">Valor de Juros</label>
                                <input type="number" class="form-control" min="0.00" step="0.01" name="juros" placeholder="juros" value="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="valorTotal">Valor de desconto (R$)</label>
                                <input type="number" class="form-control" min="0.00" step="0.01" name="desconto" placeholder="dento" value="0.00" required>
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
                                        ?><option value="<?php echo $estabelecimento->getCodigo() . ': ' . $estabelecimento->getNome(); ?>"><?php echo $estabelecimento->getNome(); ?> </option>
                                    <?php } ?>
                                </select>
                                <?php
                                foreach ($viewVar['estabelecimentos'] as $estabelecimento) {
                                    ?><input type="hidden" name="nomeEstabelecimento" value="<?php echo $estabelecimento->getNome(); ?>">
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="formaPagamento">Forma de pagamento</label>
                                <select class="form-control" for="formaPagamento" name="formaPagamento" >Forma de Pagamento
                                    <option></option>
                                    <?php
                                    foreach ($viewVar['formaPagamento'] as $pagamento) {
                                        ?><option value="<?php echo $pagamento->getCodigo(); ?>"><?php echo $pagamento->getDescricao(); ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="qtdParcelas">NºParcelas</label>
                                <input type="number" class="form-control" min="0" name="qtdParcelas" value="1" required readonly="true">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fixo">Despesa mensal fixa pelos próximos</label>
                                <select class="form-control" for="fixo" name="fixo" >
                                    <?php
                                    for ($i = 0; $i < 25; $i ++) {
                                        ?> <option value="<?php echo $i ?>" ><?php echo $i . ' Meses'; ?> </option> <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ativo">Ativo</label>
                                <input type="checkbox" name="ativo" value="aativo" checked>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="atipico">Atipico</label>
                                <input type="checkbox" name="atipico" value="aatipico">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="observacao">Observação</label>
                                <textarea class="form-control" style="height: 35px" name="observacao" required> </textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="arquivo">Imagem comprovante</label>
                                <input type="file" class="form-control" name="arquivo_imagem">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                </form>
            </fildset>

            <legend>Item do débito</legend>
            <div id="form_cadastro"></div>
            <form action="http://<?php echo APP_HOST; ?>/debito/salvarItens" method="post" id="form_cadastro">
                <div class="row">
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="codigoSaidaCabecalho">Cod.Venda</label>
                            <input type="text" class="form-control"  name="codigoSaidaCabecalho" placeholder="codigo saida" value="<?php echo $Sessao::retornaCodigo('codigo'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="produto">Produto</label>
                            <input type="text" class="form-control"  name="produto" placeholder="descrição do débito" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="quantidade">Quantidade</label>
                            <input type="number" class="form-control"  name="quantidadeProduto" placeholder="quantidade" min="0.001" step="0.001" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="quantidade">UND Media</label>
                            <input type="text" class="form-control"  name="unidadeMedida" placeholder="unidadeMedida" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="valorProduto">Valor</label>
                            <input type="number" class="form-control" name="valorProduto" placeholder="Valor do produto" min="0.01" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="ativo">Ativo</label>
                            <input type="checkbox" name="ativoProduto" value="aativoProduto" checked>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-sm">Salvar</button>
            </form>

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