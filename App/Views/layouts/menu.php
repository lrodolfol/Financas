<?php
//return;
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="http://<?php echo APP_HOST; ?>/">Finanças</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <button onclick="location.href = document.referrer;"  title="Voltar para página anterior">Back &larr;</button>
            <ul class="nav navbar-nav"> 
                <li <?php if ($viewVar['nameController'] == "HomeController") { ?> class="active" <?php } ?>>
                    <a href="http://<?php echo APP_HOST; ?>" >Home</a>
                </li>
                <li class="dropdown">
                    <!--
                    --============
                    -- Cadastros--
                    --============
                    -->
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Cadastros <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li <?php if ($viewVar['nameController'] == "CreditoController" && $viewVar['nameAction'] == "novo") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/credito/novo" >  
                                Crédito
                                <img title="cadastrar novo crédito" src="<?php echo "http://" . APP_HOST . "/public/images/plus.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "DebitoController" && ($viewVar['nameAction'] == "novo")) { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/debito/novo" >
                                Débito
                                <img title="cadastrar nova receita" src="<?php echo "http://" . APP_HOST . "/public/images/less.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ContasReceber" && ($viewVar['nameAction'] == "novo")) { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/contasReceber/novo" >Contas a receber
                                <img title="Caadstrar contas a receber R$" src="<?php echo "http://" . APP_HOST . "/public/images/dollar.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "AgendaLancamentoController" && ($viewVar['nameAction'] == "novo")) { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/agendaLancamento/novo" >Contas a pagar</a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "EstabelecimentoController" && $viewVar['nameAction'] == "novo") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/estabelecimento/novo" >Estabelecimento</a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "FormaPagamentoController" && $viewVar['nameAction'] == "novo") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/formaPagamento/novo" >Forma Pagamento</a>
                        </li>
                         <li <?php if ($viewVar['nameController'] == "CarteirasController" && $viewVar['nameAction'] == "novo") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/carteiras/novo" >Carteiras</a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "UsuarioController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/usuario/index" >
                                Usuário
                                <img title="Dados de usuario" src="<?php echo "http://" . APP_HOST . "/public/images/user.ico" ?> ">
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                     <!--
                    --============
                    -- Consultas--
                    --============
                    -->
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Consultas <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li <?php if ($viewVar['nameController'] == "CreditoController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/credito/index" >Créditos
                                <img title="consultar créditos" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "DebitoController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/debito/index" >Débitos
                                <img title="consultar receitas" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ContasReceberController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/contasReceber/index" >Contas a receber
                                <img title="consultar contas a receber" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "AgendaLancamento" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/AgendaLancamento/index" >Contas a pagar
                                <img title="consultar contas a pagar" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "EstabelecimentosController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Estabelecimento/index" >Estabelecimentos
                                <img title="consultar estabelecimentos" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "FormaPagamentoController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/formaPagamento/index" >Formas de Pagamento
                                <img title="consultar formas de pagamento" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "CarteirasController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Carteiras/index" >Carteiras
                                <img title="consultar saldo em carteiras" src="<?php echo "http://" . APP_HOST . "/public/images/dollar.ico" ?> ">
                            </a>
                        </li>
                    </ul>
                </li>
                
                 <!--
                    --============
                    -- Relatorios--
                    --============
                    -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Relatórios <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li <?php if ($viewVar['nameController'] == "ExtratoController" && $viewVar['nameAction'] == "extrato") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Extrato/extrato" >Extrato
                                <img title="visualizar extrato" src="<?php echo "http://" . APP_HOST . "/public/images/extrato.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ExtratoController" && $viewVar['nameAction'] == "custoProduto") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Extrato/custoProduto" >Custo/Produto
                                <img title="consultar preço por produto" src="<?php echo "http://" . APP_HOST . "/public/images/chart2.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ExtratoController" && $viewVar['nameAction'] == "creditoMensal") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Extrato/creditoMensal" >Credito Mensal
                                <img title="visualizar posição mensal R$" src="<?php echo "http://" . APP_HOST . "/public/images/dollar.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ExtratoController" && $viewVar['nameAction'] == "DebitosSemItens") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Extrato/debitosSemItens" >Debitos s/ itens
                                <img title="visualizar débitos sem itens" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> "></a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ExtratoController" && $viewVar['nameAction'] == "DebitosSemItens") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Extrato/creditosSemComprovantes" >Créditos. s/ comprov.
                                <img title="visualizar créditos com imagens" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> "></a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ExtratoController" && $viewVar['nameAction'] == "DebitosSemItens") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Extrato/debitosSemComprovantes" >Débitos. s/ comprov.
                                <img title="visualizar débitos sem itens" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> "></a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ExtratoController" && $viewVar['nameAction'] == "ExtrairExcel") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Extrato/extrairExcel" >Extrair Excel.
                                <img title="Gerar excel da conta" src="<?php echo "http://" . APP_HOST . "/public/images/lupa.ico" ?> "></a>
                        </li>
                    </ul>
                </li>
                
                 <!--
                    --============
                    -- Operações--
                    --============
                    -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Operações <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li <?php if ($viewVar['nameController'] == "FormaPagamentoController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Tarefas/index" >
                                Tarefas
                                <img title="rodar rotina automatica" src="<?php echo "http://" . APP_HOST . "/public/images/service.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "CarteirasControlle" && $viewVar['nameAction'] == "transferencia") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Carteiras/transferencia" >
                                Transf carteiras
                                <img title="Transferência entre carteiras" src="<?php echo "http://" . APP_HOST . "/public/images/wllet-transfer.ico" ?> ">
                            </a>
                        </li>
                        <li>
                            <a href="http://<?php echo APP_HOST; ?>/Home/importarMovimentacoes" >
                                Importar Movimentações
                                <img title="Transferência entre carteiras" src="<?php echo "http://" . APP_HOST . "/public/images/csv.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "FormaPagamentoController" && $viewVar['nameAction'] == "index") { ?> class="active" <?php } ?>>
                            <a href="javascript:popup()">
                                Calculadora
                                <img title="abrir calculadora simples" src="<?php echo "http://" . APP_HOST . "/public/images/calculator.ico" ?> ">
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Conta<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php
                        //SÓ IRA MOSTRAR OPÇÕES DE TROCAR DE CONEXÃO SE O USUÁRIO FOR 'desenvolvedor' (cad usuarios)
                        if (\App\Lib\Sessao::retornaDesenvolvedor() == "S") {
                            ?>
                            <li>
                                <a href="http://<?php echo APP_HOST; ?>/Home/mudaBase/P" >Muda base produção
                                    <img title="mudar para produção(hospedado)" src="<?php echo "http://" . APP_HOST . "/public/images/change.ico" ?> ">
                                </a>
                            </li>
                            <li>
                                <a href="http://<?php echo APP_HOST; ?>/Home/mudaBase/T" >Muda base teste
                                    <img title="mudar para teste(hospedado)" src="<?php echo "http://" . APP_HOST . "/public/images/change.ico" ?> ">
                                </a>
                            </li>
                            <li>
                                <a href="http://<?php echo APP_HOST; ?>/Home/mudaBase/L" >Muda base local
                                    <img title="mudar para local(disco local)" src="<?php echo "http://" . APP_HOST . "/public/images/change.ico" ?> ">
                                </a>
                            </li>
                        <?php } ?>
                        <li <?php if ($viewVar['nameController'] == "HomeController" && $viewVar['nameAction'] == "zerarConta") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Conta/zerarConta/" >Zerar a conta</a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "HomeController" && $viewVar['nameAction'] == "exportarConta") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Conta/exportarConta/" >Fazer Backup conta
                                <img title="realizar backup da conta" src="<?php echo "http://" . APP_HOST . "/public/images/download.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "HomeController" && $viewVar['nameAction'] == "importarConta") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Conta/importarConta/" >Importar Backup conta
                                <img title="importar arquivo de backup" src="<?php echo "http://" . APP_HOST . "/public/images/upload.ico" ?> ">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "ContaController" && $viewVar['nameAction'] == "bloqueioCompetencia") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Conta/bloqueioCompetencia/" >Bloquear Competência
                                <img title="" src="">
                            </a>
                        </li>
                        <li <?php if ($viewVar['nameController'] == "HomeController" && $viewVar['nameAction'] == "relatarErros") { ?> class="active" <?php } ?>>
                            <a href="http://<?php echo APP_HOST; ?>/Conta/relatarErros/" >Relatar erros
                                <img title="avisar sobre erros/bugs" src="<?php echo "http://" . APP_HOST . "/public/images/message.ico" ?> ">
                            </a>
                        </li>
                    </ul>
                </li>
            </ul> 
            <ul class="nav navbar-nav navbar-right">
                <!-- SE HOUVER LACÇAMENTOS COM DEBITOS VENCIDOS, DA ALERTA NA TELA  -->
                <?php if ($qtdDebitoVencido > 0) { ?>
                    <li>
                        <a> <img class="tooltiptext" title="Há débitos que ja veceram ou vencem hoje!" src="<?php echo "http://" . APP_HOST . "/public/images/alert.png" ?> "> </a>
                    </li>
                <?php }
                
                if ($qtdCreditoVencido > 0) { ?>
                    <li>
                        <a> <img class="tooltiptext" title="Há créditos que já deveriam ser creditados!" src="<?php echo "http://" . APP_HOST . "/public/images/attention.png" ?> "> </a>
                    </li>
                <?php }  ?>
                <li>
                    <?php $periodo = utf8_encode(ucfirst(strftime('%B de %Y'))); ?>
                    <a> <?php echo $periodo; ?></a>
                </li>
                <li>
                    <a href="http://<?php echo APP_HOST; ?>/Home/sair"> 
                        <img  title="Sair" src="<?php echo "http://" . APP_HOST . "/public/images/logout.ico" ?> "> 
                        Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>