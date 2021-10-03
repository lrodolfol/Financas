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
                <fildset>
                    <legend>Alteração de dados Forma de pagamento</legend>
                    <form action="http://<?php echo APP_HOST; ?>/formaPagamento/atualizar" method="post" id="form_cadastro">
                        <?php foreach ($viewVar['listaFormaPagamento'] as $FormaPagamento) { ?>
                            <div class = "row">
                                <div class = "col-md-2">
                                    <div class = "form-group">
                                        <label for = "codigo">Código</label>
                                        <input type = "number" class = "form-control" name = "codigo" required readonly = "true" value = "<?php echo $FormaPagamento->getCodigo(); ?>" >
                                    </div>
                                </div>
                                <div class = "col-md-10">
                                    <div class = "form-group">
                                        <label for = "descricao">Descrição</label>
                                        <input type="text" class="form-control" value="<?php echo $FormaPagamento->getDescricao(); ?>" name="descricao" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="diaFechamento">Dia do fechamento</label>
                                        <input type="text" class="form-control"  value="<?php echo $FormaPagamento->dia_fechamento; ?>" name="diaFechamento" >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="diaVencimento">Dia do vencimento</label>
                                        <input type="text" class="form-control" value="<?php echo $FormaPagamento->dia_vencimento; ?>" name="diaVencimento" >
                                    </div>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="ativo">Ativo</label>
                                        <input type="checkbox" name="ativo" value="aativo"  checked="  <?php if ($FormaPagamento->getAtivo() == "S") { ?> true <?php } else { ?> false <?php } ?>   " >
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                        <?php } ?>
                    </form>
                </fildset>
                <?php
            }
            ?>
        </div>
    </div>
</div>