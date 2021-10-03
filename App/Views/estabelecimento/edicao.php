<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-12">
            <fildset>
                <legend>Novo Estabelecimento</legend>
                <form action="http://<?php echo APP_HOST; ?>/estabelecimento/atualizar" method="post" id="form_cadastro">
                    <?php foreach ($viewVar['estabelecimento'] as $estabelecimento) { ?>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="codigo">Código</label>
                                    <input type="number" class="form-control"  name="codigo" required readonly="true" value="<?php echo $estabelecimento->getCodigo(); ?>" >
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control"  name="nome" placeholder="nome" required value="<?php echo $estabelecimento->getNome(); ?>" >
                                </div>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="cnpj">CNPJ</label>
                                    <input type="text" class="form-control" name="cnpj" value="<?php echo $estabelecimento->getCnpj(); ?>" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tipo">Tipo</label>
                                    <input type="text" class="form-control" name="tipo" value="<?php echo $estabelecimento->tipo_comercio; ?>" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="cidade">Cidade</label>
                                    <input type="text" class="form-control" name="cidade"  value="<?php echo $estabelecimento->getCidade(); ?>" >
                                </div>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ativo">Ativo</label>
                                    <input type="checkbox" name="ativo" value="aativo" checked=" 
                                        <?php if ($estabelecimento->getAtivo() == "S") { ?> 
                                           true <?php 
                                        } else { ?> 
                                           false <?php 
                                        } ?>">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                    <?php } ?>
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