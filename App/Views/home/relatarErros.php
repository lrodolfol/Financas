<?php
$dataHoje = date('Y-m-d');
?>

<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <?php
             if (($viewVar['permiteCriarErros'])) {
             ?>  <legend>Relatar Erros 
                    <h5><i>Somente usuários com mais de 1Ano de uso do Finanças podem criar relatos de erros.</i></h5></legend> 
                    <?php
             }else{
            ?>
            <fildset>
                <legend>Relatar Erros 
                    <h5><i>Voê pode nos informar erros encontrados no sistema</i></h5>
                <form action="http://<?php echo APP_HOST; ?>/conta/meusRelatosErros" method="post" id="form_cadastro" enctype="multipart/form-data">
                    <button type="submit" class="btn btn-info btn-sm">Acompanhar meus Relatos</button>
                    <input type="hidden" name="User" value="true">
                </form>
                    </legend>
                <form action="http://<?php echo APP_HOST; ?>/conta/salvarRelatoErros" method="post" id="form_cadastro" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Titutlo">Titutlo</label>
                                <input type="text" class="form-control"  name="titulo" placeholder="Descricao" maxlength="25" required>
                            </div>
                            <div class="form-group">
                                <label for="Erro">Erro:</label>
                                <textarea class="form-control" name="texto" placeholder="informe o erro aqui" required> </textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantidade">Usuário</label>
                                <input type="text" class="form-control" name="usuario" value="<?php echo $usuarioLogado; ?>" required readonly="true">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantidade">Data do crédito</label>
                                <input type="date" class="form-control" name="data" value="<?php echo $dataHoje; ?>" required readonly="true">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="arquivo">Imagem</label>
                                <input type="file" class="form-control-file" name="arquivo_imagem">
                            </div>
                        </div>
                        <input type="hidden" name="User" value="true">
                    </div>

                    <button type="submit" class="btn btn-success btn-sm">Enviar</button>
                </form>
            </fildset>
             <?php } ?>
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