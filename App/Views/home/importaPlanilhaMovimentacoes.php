<div class="container">
    <div class="row">
        <div class="col-md-12">
            <legend>Importação de planilha para movimentações</legend>
            <div class="row"><br> <br></div>

            Escolha uma das duas opções abaixo: <i>(se ambos selecionado, será considerado o arquivo enviado pelo usuário)</i>
            <div class="row"><br> <br></div>
            <form method="POST" action="http://<?php echo APP_HOST; ?>/Conta/uploadPlanilhaMovimentacoes" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="credito">selecione o Arquivo</label>
                            <input type="file" name="arquivo_importacao">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-sm">Importar</button>
            </form>

            <div class="row"><br></div>

            <?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem() ?>
                </div>
            <?php } ?>

            <?php if (\App\Lib\Sessao::retornaErro()) { ?>
                <div class="alert alert-danger" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaErro() ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>