<!DOCTYPE html>
<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$qtdDebitoVencido = App\Lib\Sessao::retornaQtdDebitoVencido();
$qtdCreditoVencido = App\Lib\Sessao::retornaQtdCreditoVencido();
$baseDadosOperante = null;
$usuarioLogado = \App\Lib\Sessao::retornaUsuario();

//AQUI IRÁ VERIFICAR(através de cookie) EM QUAL BASE DE DADOS SERÁ FEITO AS OPERAÇÕES
if (defined('DB_NAME') && (DB_NAME == "kellye31_financas_" . $usuarioLogado . "")) {
    $baseDadosOperante = "PRODUCAO";
} elseif (defined('DB_NAME') && (DB_NAME == "financas_" . $usuarioLogado . "")) {
    $baseDadosOperante = "LOCAL";
} else {
    $baseDadosOperante = "TESTE";
}
?>

<html lang="pt">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title><?php echo TITLE; ?></title>

        <link href="http://<?php echo APP_HOST; ?>/public/css/bootstrap.min.css" rel="stylesheet">
        <link href="http://<?php echo APP_HOST; ?>/public/css/main.css" rel="stylesheet">
        <link href="http://<?php echo APP_HOST; ?>/public/css/style.css" rel="stylesheet">
        <link href="http://<?php echo APP_HOST; ?>/public/images/pencil.ico"  rel="shortcut icon" />
		
		<script languague="javascript">
function popup(){
window.open('https://kellyerodolfo.com.br/financas/calculadora.php','popup','width=250,height=200,top=0,left=0')
}
</script>
    </head>
    <body>

