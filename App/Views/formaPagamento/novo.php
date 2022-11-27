<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-12">
            <fildset>
                <legend>Nova Forma de pagamento</legend>
                <form action="http://<?php echo APP_HOST; ?>/formaPagamento/salvar" method="post" id="form_cadastro">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="codigo">Código</label>
                                <input type="number" class="form-control"  name="codigo" required readonly="true" value="<?php echo $viewVar['novoCodigoFormaPagamento']; ?>" >
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="descricao">Descrição</label>
                                <input type="text" class="form-control"  name="descricao" placeholder="descrição" required>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="diaVencimento">Dia do vencimento</label>
                                <input type="text" class="form-control"  name="diaVencimento" placeholder="dia Vencimento">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="diaFechamento">fechamento</label>
                                <input type="number" class="form-control"  name="diaFechamento" placeholder="antes do vencimento">
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ativo">Ativo</label>
                                <input type="checkbox" name="ativo" value="aativo" checked >
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