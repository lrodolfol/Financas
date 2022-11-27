<?php
//use DateTime;

$Usuario = new stdClass();
$Usuario->nome = $viewVar['dadosUsuario'][0]['nome'];
$Usuario->sobreNome = $viewVar['dadosUsuario'][0]['sobreNome'];
$Usuario->email = $viewVar['dadosUsuario'][0]['email'];
$Usuario->dataCadastro = $viewVar['dadosUsuario'][0]['data_cadastro'];

//CALCULA DIFERENÇA DE DATAS ENTRE DUAS DATAS
$dataHoje = new DateTime();
$nascimento = new DateTime($viewVar['dadosUsuario'][0]['data_nascimento']);
$idade = $dataHoje->diff($nascimento);

$Usuario->idade = $idade->y;
$Usuario->codigo = $viewVar['dadosUsuario'][0]['codigo'];
$Usuario->senha = md5($viewVar['dadosUsuario'][0]['senha']);
$Usuario->dataNascimento = $viewVar['dadosUsuario'][0]['data_nascimento'];
$Usuario->recebeEmail = $viewVar['dadosUsuario'][0]['recebe_email'];

$caminhoImagemUsuario = "http://" . APP_HOST . "/public/usuarios/" . \App\Lib\Sessao::retornaUsuario() . "/image_perfil/user.jpg";

//O CAMINHO PARA CONDIÇÃO DO 'file_exists' NÃO DEVE SER FEITO ATRAVÉS DE URL. PRECISA USAR CAMINHO DE ARQUIVO NO DISCO RÍGIDO 
$caminhoCondicao = RAIZ_SITE . "/public/usuarios/" . \App\Lib\Sessao::retornaUsuario() . "/image_perfil/user.jpg";

if (!file_exists($caminhoCondicao) || !is_file($caminhoCondicao)) {
    $caminhoImagemUsuario = "http://" . APP_HOST . "/public/images/exampleUser.png";
}

$alterarSenha = $_GET['alterarSenha'];
?>

<style>
    .image-perfil img{
        width: 100%;
        height: 100%;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="image-perfil">
                <img src="<?php echo $caminhoImagemUsuario; ?>" alt="Sua foto de usuário"/>
            </div>
            <button class="btn btn-primary btn-sm">Carregar</button>
        </div>
        <div class="col-md-9">
            <fildset>
                <legend>Dados do Usuário</legend>
                <form action="http://<?php echo APP_HOST; ?>/usuario/atualizar" method="post" id="form_cadastro" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <!-- O NOME NÃO PODE SER ALTERADO AINDA PORQUE TEM MUDAR OS CAMINHOS DE ARQUIVOS E NOME DE BANCO DE DADOS -->
                                <input type="text" class="form-control"  name="nome" placeholder="nome" value="<?php echo $Usuario->nome ?>" required readonly="true">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="preco">Sobre Nome</label>
                                <input type="text" class="form-control"  name="sobreNome" placeholder="sobre nome" value="<?php echo $Usuario->sobreNome ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="quantidade">Data Nasc.</label>
                                <input type="date" class="form-control" name="dataCadastro" value="<?php echo $Usuario->dataNascimento ?>" required readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="quantidade">Idade</label>
                                <input type="number" class="form-control" name="idade" placeholder="0" value="<?php echo $Usuario->idade ?>" required readonly>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="quantidade">Email</label>
                                <input type="mail" class="form-control" name="email" placeholder="email" value="<?php echo $Usuario->email ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantidade">Data Cadastro</label>
                                <input type="date" class="form-control" name="dataCadastro" value="<?php echo $Usuario->dataCadastro ?>" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fixo">Receber E-mails</label>
                                <input type="checkbox" name="recebeEmail" value="recebeEmail"
                                <?php if ($Usuario->recebeEmail == "S") {
                                    ?>
                                           checked="true"
                                           <?php
                                       }
                                       ?> >

                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                    <a href="?alterarSenha=TRUE"><button type="button" class="btn btn-info btn-sm">Aterar Senha</button></a>
                </form>
            </fildset>

            <div class="espacamento" style="margin-top: 40px">
            </div>

            <?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem() ?>
                </div>
            <?php } ?>

            <?php if (isset($alterarSenha) && $alterarSenha == "TRUE") { ?>
                <fildset>
                    <legend>Nova senha de acesso</legend>
                    <form action="http://<?php echo APP_HOST; ?>/usuario/novaSenha" method="post" id="form_cadastro">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="arquivo">Nova senha</label>
                                    <!-- GERAR SENHA ALEATORIA AQUI (UTILIZAR SOMENTE JAVASCRIPT)-->
                                    <input type="password" class="form-control"  name="senha" placeholder="senha" value="***" required>
                                </div>
                                <div class="form-group">
                                    <label for="arquivo">Confirme a senha</label>
                                    <!-- GERAR SENHA ALEATORIA AQUI (UTILIZAR SOMENTE JAVASCRIPT)-->
                                    <input type="password" class="form-control"  name="senha_confirmada" placeholder="confirme senha" value="***" required>
                                </div>
                            </div>
                        </div>
                        <div class=""row>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success btn-sm">Confirmar</button>
                            </div> 
                        </div>
                    </form>
                </fildset>

            <?php } ?>


        </div>  
    </div>
</div>

<?php
?>