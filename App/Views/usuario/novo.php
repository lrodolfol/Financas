<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-12">
            <fildset>
                <legend>Cadastro de Novo Usuário</legend>
                <form action="http://<?php echo APP_HOST; ?>/usuario/salvar" method="post" id="form_cadastro">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control"  name="nome" placeholder="nome" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sobreNome">Sobre Nome</label>
                                <input type="text" class="form-control"  name="sobreNome" placeholder="sobreNome" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dataNascimento">Data Nascimento</label>
                                <input type="date" class="form-control"  name="dataNascimento" placeholder="dataNascimento" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="senha">Senha</label>
                                <input type="password" class="form-control" name="senha"placeholder="senha" >
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" name="email" placeholder="email" required="true">
                            </div>
                        </div>

                    </div>
                    <div clas="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" name="recebeEmail" >
                                <label for="recebeEmail">aceita receber e-mails</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="User" value="true">
                    <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                </form>
            </fildset>
            <?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem() ?>
                </div>
                
                <a href="http://<?=APP_HOST?>">
                    <button type="button" class="btn btn-info">Inicio</button>
                </a>
                
            <?php } ?>

        </div>
        <div class=" col-md-3"></div>
    </div>
</div>