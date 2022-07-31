<?php

use App\AppLocal;
use App\AppTeste;
use App\App;
use App\Lib\Erro;

session_start();

error_reporting(E_ALL & ~E_NOTICE);

require_once("vendor/autoload.php");

/* IRA LER O AR */
$baseConexao = "";

//USE THIS IF NOT EXISTS SUB PATH IN PROJECT ....
//define("TXT_BANCO", $_SERVER['DOCUMENT_ROOT'] . "\\financas\\banco-conexao.txt"); //RECUPERA O DIRETORIO DO ARQUIVO

define("TXT_BANCO", $_SERVER['DOCUMENT_ROOT'] . "\\banco-conexao.txt"); //RECUPERA O DIRETORIO DO ARQUIVO
if(is_file(TXT_BANCO)){            
    $arquivo = fopen(TXT_BANCO, "r");    			  //FAZ A ABERTURA DO ARQUIVO 'r' SOMENTE PARA LEITURA
    $tamanho = filesize(TXT_BANCO);    				  //RECUPERA O TAMANHO DO ARQUIVO EM QUESTÃO
    $conexao = fread($arquivo, $tamanho);  			  //ATRIBUI O ARQUIVO A UMA VARIAVEL
    $bancoConexao = utf8_encode($conexao);            //MOSTRA O ARQUIVO
    fclose($arquivo);                     			  //FECHA O ARQUIVO EM QUESTÃO
} else {
	$bancoConexao = "PRODUCAO";
}

try {
    $app = null;
    if ($bancoConexao == "PRODUCAO") {
		$app = new App($bancoConexao);
    } elseif ($bancoConexao == "TESTE") {
		$app = new AppTeste($bancoConexao);
    }else{
	    $app = new AppLocal($bancoConexao);
	}

    $app->run();
} catch (\Exception $e) {
    $oError = new Erro($e);
    $oError->render();
}