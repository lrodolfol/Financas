<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\DAO\UsuarioDAO;
use App\Models\Entidades\Usuario;

class UsuarioController extends Controller {

    public function index() {
        $usuarioDAO = new UsuarioDAO();
        $codigoUsuario = Sessao::retornaCodigoUsuario();
        $sql = "SELECT * FROM usuarios WHERE codigo = " . $codigoUsuario . " ";
        $dadosUsuario = $usuarioDAO->FinancasRetornaDT($sql);

        self::setViewParam('dadosUsuario', $dadosUsuario);
        $this->render('/usuario/index');
    }

    public function novaSenha() {
        $logado = true;
        $this->trocarSenha($logado);
    }

    public function recuperaSenha() {
        $this->render('/usuario/recuperaSenha');
    }

    public function recuperarSenhaEmail() {
        $email = $_POST['email'];

        $usuarioDAO = new UsuarioDAO;
        if ($usuarioDAO->recuperarSenha($email)) {
            //Sessao::gravaMensagem("tudo certo");
            self::setViewParam('recuperarSenha', "TRUE");
            Sessao::gravaEmail($email);
        } else {
            Sessao::gravaMensagem("Ocorreu um erro. Certifi-que se ter informado o e-mail correto cadastrado em nossa plataforma");
            self::setViewParam('recuperarSenha', "FALSE");
        }

        $this->render('/usuario/recuperaSenha');
    }

    public function recuperarSenha() {
        $codigo = $_POST['codigo'];
        $codigoCookie = $_POST['codigo_cookie'];

        if ($codigo == $codigoCookie) {
            self::setViewParam('recuperarSenhaCodigo', "TRUE");
            setcookie('codigoRecuperarSenha'); //DESTROI O COOKIE
        } else {
            Sessao::gravaMensagem("O código informado não esta correto.");
            self::setViewParam('recuperarSenha', "TRUE");
        }


        $this->render('/usuario/recuperaSenha');
    }

    public function trocarSenha($logado = null) {
        $novaSenha = $_POST['senha'];
        $novaSenhaConfirmacao = $_POST['senha_confirmada'];
        $email = Sessao::retornaEmail();

        if ($novaSenha != $novaSenhaConfirmacao) {
            Sessao::gravaMensagem("As senha informadas não coencidem!");
            self::setViewParam('recuperarSenhaCodigo', "TRUE");
        } else {
            $UsuarioDAO = new UsuarioDAO();
            if ($UsuarioDAO->trocaSenha($email, $novaSenha)) {
                Sessao::gravaMensagem("Nova senha atualizada com sucesso!");
            } else {
                Sessao::gravaMensagem("Ocorreu um erro ao atualizar a senha.");
                self::setViewParam('recuperarSenhaCodigo', "TRUE");
            }
        }

        if ($logado) {
            $this->render('/usuario/index');
        } else {

            if ($codigo == $codigoCookie) {
                self::setViewParam('recuperarSenhaCodigo', "TRUE");
                setcookie('codigoRecuperarSenha'); //DESTROI O COOKIE
            } else {
                Sessao::gravaMensagem("O código informado não esta correto.");
                self::setViewParam('recuperarSenha', "TRUE");
            }


            $this->render('/usuario/recuperaSenha');
        }
    }

    public function novo() {
        $this->render('/usuario/novo');
    }

    public function salvar() {
        $Usuario = new Usuario();
        $Usuario->setNome($_POST['nome']);
        $Usuario->setSobreNome(isset($_POST['sobreNome']) ? $_POST['sobreNome'] : "");
        $Usuario->setDataNascimento(!empty($_POST['dataNascimento']) ? $_POST['dataNascimento'] : 0);
        $Usuario->setSenha($_POST['senha']);
        $Usuario->setEmail($_POST['email']);
        $Usuario->setRecebeEmail(isset($_POST['recebeEmail']) ? "S" : "N");
        $dataCadastro = DATE('Y/m/d');
        $Usuario->setDataCadastro($dataCadastro);

        $UsuarioDAO = new UsuarioDAO();
        $nomeUsuarioExistente = $UsuarioDAO->validaUsuario($Usuario);
        if ($nomeUsuarioExistente == $Usuario->getNome()) {
            Sessao::gravaMensagem("Esse usuário não está disponivel, tente outro.");
        } else {
            $rowUsuario = $UsuarioDAO->criarBaseDadosUsuario($Usuario);
            if ($rowUsuario) {
                Sessao::gravaMensagem("Usuário(a) " . $$_POST['nome'] . " cadastrado(a) com sucesso");
            } else {
                Sessao::gravaMensagem("Não foi possivel criar base de dados para o usuario");
            }
        }
        $this->render('/usuario/novo');
    }

}
