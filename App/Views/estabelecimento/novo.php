<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-12">
            <fildset>
                <legend>Novo Estabelecimento</legend>
                <form action="http://<?php echo APP_HOST; ?>/estabelecimento/salvar" method="post" id="form_cadastro">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="codigo">Código</label>
                                <input type="number" class="form-control"  name="codigo" required readonly="true" value="<?php echo $viewVar['novoCodigoEstabelecimento']; ?>" >
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control"  name="nome" placeholder="nome" required>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cnpj">CNPJ</label>
                                <input type="text" class="form-control" name="cnpj" >
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="tipo">Tipo</label>
                                <input type="text" class="form-control" name="tipo" >
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" class="form-control" name="cidade"  >
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ativo">Ativo</label>
                                <input type="checkbox" name="ativo" value="aativo" checked>
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
        <div class=" col-md-3"></div>
    </div>
</div>