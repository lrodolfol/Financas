<div class="container">
    <div class="row">
        <div class="col-md-12">
            <legend>Informe os cadastros que você deseja zerar
                <h5><i>essa operação é irreversível</i></h5></legend>
            <form method="POST" action="http://<?php echo APP_HOST; ?>/Conta/zeraConta">
                <div class="row">
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="credito">Caixa</label>
                            <input type="checkbox"  name="caixa">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="credito">Crédito</label>
                            <input type="checkbox"  name="credito">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="credito">Contas receber</label>
                            <input type="checkbox"  name="contas_receber">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="debito">Débito</label>
                            <input type="checkbox"  name="debito">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="debito">Contas a pagar</label>
                            <input type="checkbox"  name="debitoFuturo">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="credito">Estabelecimentos</label>
                            <input type="checkbox"  name="estabelecimentos">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="debito">Forma Pagmento</label>
                            <input type="checkbox"  name="formaPagamento">
                        </div>
                    </div>
                </div>
                
                <input type="hidden"  name="tipoArquivo" value="JSON">
                
                <button onclick="return confirm('Isso irá deletar todos seus registro. Continuar?')" type="submit" class="btn btn-success btn-sm">Zerar</button>
            </form>
			<?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem() ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>