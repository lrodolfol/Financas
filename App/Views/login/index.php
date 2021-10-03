<style>
    body{
        width: 100%;
        background-image: url('http://localhost/financas/public/images/bkg1.png');
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }

</style>
<div class="container">
    <div class="centro-tela">
        <div class="starter-template">
            <h1><?php echo 'Página de Login'; ?></h1>
            <label>
                <form action="http://<?php echo APP_HOST; ?>/login/logar" method="post" id="form_cadastro">
                    <div class="form-group">
                        <label for="user">Usuáio</label>
                        <input type="text" class="form-control"  name="user" placeholder="usuário" value="<?php echo $Sessao::retornaValorFormulario('user'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" class="form-control" name="senha" placeholder="senha" value="<?php echo $Sessao::retornaValorFormulario('senha'); ?>" required>
                    </div>
                    <input type="hidden" name="User" value="true">
                    <button type="submit" class="btn btn-success btn-sm">Logar no Sistema</button>
                    <p><p>
                        <a href="http://<?php echo APP_HOST; ?>/usuario/recuperaSenha" <button type="button" class="btn btn-danger btn-sm">Esqueci minha senha</a> 
                        <a href="http://<?php echo APP_HOST; ?>/usuario/novo" <button type="button" class="btn btn-info btn-sm">Novo usuário</a> 
                    </p></p>
                </form>
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
