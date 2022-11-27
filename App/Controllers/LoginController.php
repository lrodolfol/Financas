<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\DAO\UsuarioDAO;
use App\Models\DAO\LoginDAO;
use App\Models\Entidades\Usuario;

//use App\Models\Validacao\ProdutoValidador;

class LoginController extends Controller {

    public function index() {        
        $this->render('login/index');
    }

    public function logar() {
        $Usuario = new Usuario();
        $postArray = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $filterArray = [
          "user"   => FILTER_SANITIZE_STRIPPED,
          "senha"   => FILTER_SANITIZE_STRIPPED
        ];
        $formFilter = filter_input_array(INPUT_POST, $filterArray);
        $postArray['user'] = $formFilter['user'];
        $postArray['senha'] = $formFilter['senha'];
        
        /*$Usuario->setNome($_POST['user']);
        $Usuario->setSenha($_POST['senha']);*/
        $Usuario->setNome($postArray['user']);
        $Usuario->setSenha($postArray['senha']);
        $Usuario->setCodigo(0);

        Sessao::gravaFormulario($postArray);

        //CÓDIGO PARA VALIDAR CAMPOS VAZIOS
        /* $produtoValidador = new ProdutoValidador();
          $resultadoValidacao = $produtoValidador->validar($Produto);

          if($resultadoValidacao->getErros()){
          Sessao::gravaErro($resultadoValidacao->getErros());
          $this->redirect('/produto/cadastro');
          } */

        $usuarioDAO = new UsuarioDAO();

        if ($usuarioDAO->logar($Usuario)) {
            Sessao::gravaUsuario($Usuario->getNome());
            Sessao::gravaEmail($Usuario->getEmail());
            Sessao::gravaRecebeEmail($Usuario->getRecebeEmail());
            Sessao::gravaDesenvolvedor($Usuario->getDesenvolvedor());
            Sessao::gravaCodigoUsuario($Usuario->getCodigo());
            Sessao::gravaDataCadastroUsuario($Usuario->getDataCadastro());
            $this->redirect('/home/index');
        } else {
            Sessao::gravaMensagem("Usuário ou senha incorretos");
            $this->render('login/index');
            Sessao::limpaFormulario();
            Sessao::limpaMensagem();
            Sessao::limpaErro();
            Sessao::limpaDataCadastroUsuario();
        }

        /* Sessao::limpaFormulario();
          Sessao::limpaMensagem();
          Sessao::limpaErro(); */

        //$this->redirect('/produto');
    }

    public function cadastro() {
        $this->render('/produto/cadastro');

        Sessao::limpaFormulario();
        Sessao::limpaMensagem();
        Sessao::limpaErro();
    }


}
