<div class="container">
    <div class="row">
        <div class="col-md-12">
            <legend>Informe os cadastros que você deseja exportar</legend>
            <form method="POST" action="http://<?php echo APP_HOST; ?>/Conta/exportaConta">
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
                            <label for="contasReceber">Conta receber</label>
                            <input type="checkbox"  name="contasReceber">
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
                            <label for="debito">Débito Futuro</label>
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="credito">Formato do arquivo para exportação</label>
                            <select class="form-control" for="tipoArquivo" name="tipoArquivo" >
                                <option>XML</option>
                                <option>Json</option>
                                <option>Excel</option>
                                <option>TXT</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-sm">Exportar</button>
            </form>
            
            <?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem() ?>
                </div>
            <?php } ?>

            <?php
            $dataBackup = date('d-m-Y');
            if ($viewVar['exportadoXML'] && $viewVar['exportadoXML'] == "TRUE") {
                $caminhoSalvarXML = RAIZ_SITE . "/public/usuarios/" . \App\Lib\Sessao::retornaUsuario() . "/backup_conta/XML";
                $arquivoXML = $caminhoSalvarXML . "/financas_" . \App\Lib\Sessao::retornaUsuario() . "_" . $dataBackup . ".xml";
                ?><a href="http://kellyerodolfo.com.br/financas/public/usuarios/<?php echo App\Lib\Sessao::retornaUsuario(); ?>/backup_conta/XML/financas_<?php echo App\Lib\Sessao::retornaUsuario(); ?>_<?php echo $dataBackup; ?>.xml" download="financas" class="btn btn-info btn-sm">Baixar</a><?php
            }
            if ($viewVar['exportadoJSON'] && $viewVar['exportadoJSON'] == "TRUE") {
                ?><a href="http://kellyerodolfo.com.br/financas/public/usuarios/<?php echo App\Lib\Sessao::retornaUsuario(); ?>/backup_conta/JSON/financas_<?php echo App\Lib\Sessao::retornaUsuario(); ?>_<?php echo $dataBackup; ?>.json" download="financas" class="btn btn-info btn-sm">Baixar</a><?php
            }
            ?>
        </div>
    </div>
</div>