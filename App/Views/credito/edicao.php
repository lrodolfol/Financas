<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <fildset>
                <legend>Alteração de dados do crédito</legend>
                <form action="http://<?php echo APP_HOST; ?>/credito/atualizar" method="post" id="form_cadastro" enctype="multipart/form-data">
                    <?php foreach ($viewVar['credito'] as $contasReceber) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nome">Descrição</label>
                                    <input type="text" value="<?php echo $contasReceber->getDescricao() ?>" class="form-control"  name="descricao" placeholder="Descricao" required>
                                </div>
                                <div class="form-group">
                                    <label for="preco">Observações</label>
                                    <textarea class="form-control" name="observacao" placeholder="Observações" required><?php echo $contasReceber->obs ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantidade">Valor</label>
                                    <input type="number" value="<?php echo $contasReceber->getValor() ?>" class="form-control" name="valor" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantidade">Data do crédito</label>
                                    <input type="date" value="<?php echo $contasReceber->data ?>" class="form-control" name="data" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="arquivo">Imagem comprovante</label>
                                    <input type="file" class="form-control" name="arquivo_imagem">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" name="ativo" value="S" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" name="codigo" value="<?php echo $contasReceber->getCodigo() ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fixo">Crédito mensal fixo</label>
                                    <input type="checkbox" name="fixo" value="fixo" <?php if ($contasReceber->getFixo() == 'S') { ?> checked="true" <?php } ?> >
                                </div>
                            </div>
                        </div>
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