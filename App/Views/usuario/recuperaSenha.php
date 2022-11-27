<div class="container">
    <div class="centro-tela">
        <div class="starter-template">
            <legend>Recuperação de Senha</legend>
            <label>

                <?php
                if ($viewVar['recuperarSenhaCodigo'] == "TRUE") {
                    ?>
                    <form action="http://<?php echo APP_HOST; ?>/usuario/trocarSenha" method="post" id="form_cadastro">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="senha">Informe sua nova senha</label>
                                    <input type="password" class="form-control"  name="senha" placeholder="Senha" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="senha">Confirme a nova senha</label>
                                    <input type="password" class="form-control"  name="senha_confirmada" placeholder="Confirmação de senha" required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="User" value="true">
                        <input type="hidden" name="email" value="<?php echo $email ?>">
                        <button type="submit" class="btn btn-success btn-sm">Confirmar</button>

                    </form>
                    <?php
                } elseif (!$viewVar['recuperarSenha'] == "TRUE") {
                    ?>
                    <form action="http://<?php echo APP_HOST; ?>/usuario/recuperarSenhaEmail" method="post" id="form_cadastro">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nome">Informe Seu e-mail</label>
                                    <input type="email" class="form-control"  name="email" placeholder="email" required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="User" value="true">
                        <button type="submit" class="btn btn-success btn-sm">Confirmar</button>

                    </form>

                <?php
                } else {
                    ?>
                    <fieldset <?php if (!$viewVar['recuperarSenha'] == "TRUE") { ?> disabled <?php } ?> >
                        <form action="http://<?php echo APP_HOST; ?>/usuario/recuperarSenha" method="post" id="form_cadastro">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nome">Código enviado ao email</label>
                                        <input type="text" class="form-control"  name="codigo" placeholder="codigo" required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="User" value="true">
                            <input type="hidden" name="codigo_cookie" value="<?php echo $_COOKIE['codigoRecuperarSenha'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">Confirmar</button>
                        </form>
                    </fieldset>

    <?php echo 'Confirme o código enviado ao seu endereço de e-mail ' . ""; //$_COOKIE['codigoRecuperarSenha'] . '';
} ?> 


            </label>
                <?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $Sessao::retornaMensagem() ?>
                </div>
<?php } ?>
        </div>
    </div>
</div>