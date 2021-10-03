<?php

namespace App\Controllers;

use App\Lib\Sessao;
use App\Models\Entidades\Estabelecimentos;
use App\Models\DAO\EstabelecimentoDAO;

class EstabelecimentoController extends Controller {

    public function novo() {
        $EstabelecimentoDAO = new EstabelecimentoDAO();
        self::setViewParam('novoCodigoEstabelecimento', $EstabelecimentoDAO->retornaNovoCodigoEstabelecimento());
        $this->render('/estabelecimento/novo');
    }

    public function salvar() {
        $EstabelecimentoDAO = new EstabelecimentoDAO();
        $Estabelecimento= new Estabelecimentos();
        
        $objEstabelecimento = (object)$_POST; //TRANSFORMA O ARRAY POST EM OBJETO
        
        $Estabelecimento->setAtivo(isset($objEstabelecimento->ativo) ? 'S' : 'N');
        $Estabelecimento->setCodigo($objEstabelecimento->codigo);
        $Estabelecimento->setNome($objEstabelecimento->nome);
	$Estabelecimento->setCnpj($objEstabelecimento->cnpj);
	$Estabelecimento->setTipoComercio($objEstabelecimento->tipo);
	$Estabelecimento->setCidade($objEstabelecimento->cidade);
        
        /*$Estabelecimento->setAtivo(isset($_POST['ativo']) ? 'S' : 'N');
        $Estabelecimento->setCodigo($_POST['codigo']);
        $Estabelecimento->setNome($_POST['nome']);
	$Estabelecimento->setCnpj($_POST['cnpj']);
	$Estabelecimento->setTipoComercio($_POST['tipo']);
	$Estabelecimento->setCidade($_POST['cidade']);*/
		
        
        $rowEstabelecimento = $EstabelecimentoDAO->salvar($Estabelecimento);
         if ($rowEstabelecimento > 0) {
             Sessao::gravaMensagem("Novo estabelecimento gravado com sucesso. Cod: " . $Estabelecimento->getCodigo());
         }else{
             Sessao::gravaMensagem("Ocorreu um eror ao gravar novo estabelecimento");
         }
        $this->redirect('/estabelecimento/novo');
    }

    public function index() {
        $EstabelecimentoDAO = new EstabelecimentoDAO();

        self::setViewParam('listaEstabelecimento', $EstabelecimentoDAO->carregaEstabelecimento(null));

        $this->render('/estabelecimento/index');
    }
    
    public function excluir($params) {
        $codigo = $params[0];
        $Estabelecimento = new Estabelecimentos();
        $Estabelecimento->setCodigo($codigo);

        $EstabelecimentoDAO = new EstabelecimentoDAO();
        $rowEstabelecimento = $EstabelecimentoDAO->excluir($Estabelecimento);

        if (!$rowEstabelecimento) {
            Sessao::gravaMensagem("Não foi possivel excluir a forma de pagamento");
        } else {
            Sessao::gravaMensagem("Forma de pagamento excluido com sucesso");
        }

        $this->index();
    }
    
    public function edicao($params) {  
        $decode = base64_decode($params[0]);
        $codigo = ($decode / 1995); 
        $codigo -= 3;
        
        if(isset($codigo)) {
            $Estabelecimento  = new EstabelecimentoDAO();
            
            $EstabelecimentoDAO  = new EstabelecimentoDAO();
            
            self::setViewParam('estabelecimento', $EstabelecimentoDAO->carregaEstabelecimento("N",$codigo));
            $this->render('estabelecimento/edicao');
        }else{
            Sessao::gravaMensagem("Nenhum estabelecimento encontrado");
        }
        
    }
    
    public function atualizar(){
        $Estabelecimento = new Estabelecimentos();       
        $Estabelecimento->setAtivo(isset($_POST['ativo']) ? 'S' : 'N');
        $Estabelecimento->setCidade($_POST['cidade']);
        $Estabelecimento->setCnpj($_POST['cnpj']);
        $Estabelecimento->setNome($_POST['nome']);
        $Estabelecimento->setTipoComercio($_POST['tipo']);
        $Estabelecimento->setCodigo($_POST['codigo']);
        
        $EstabelecimentoDAO = new EstabelecimentoDAO();
        
        $rowEstabelcimentoDAO = $EstabelecimentoDAO->edicao($Estabelecimento);
        self::setViewParam('listaEstabelecimento', $EstabelecimentoDAO->carregaEstabelecimento("N"));
        if($rowEstabelcimentoDAO) {
            Sessao::gravaMensagem("Edição realizada com sucesso");
        }else{
            Sessao::gravaMensagem("Erro ao realizar a edição");
        }
        $this->render('estabelecimento/index');
    }

   

}
