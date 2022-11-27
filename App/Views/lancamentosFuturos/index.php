<?php
$diasLancamentoFuturosDebito = (isset($_REQUEST['proximosDiasLanc'])) ? $_REQUEST['proximosDiasLanc'] : 30;
$diasLancamentoAtecedente = (isset($_REQUEST['antecedenteDiasLanc'])) ? $_REQUEST['antecedenteDiasLanc'] : 30;
$totalGeral = 0;
$calculaDataVencida = function ($data) {
    $dataHoje = date_create(DATE('Y/m/d'));
    $data = new DateTime($data);

    $dataHoje = $dataHoje->format("Y/m/d");
    $data = $data->format("Y/m/d");

    $diferenca = strtotime($dataHoje) - strtotime($data);
    $dias = floor($diferenca / (60 * 60 * 24));

    //$diasDif = date_diff($dataHoje, $data, false);
    return $dias;
}
?>
<div class="container">
    <div class="row">
        <br>
        <div class="col-md-12">
            <a href="http://<?php echo APP_HOST; ?>/debito/novo" class="btn btn-success btn-sm">Adicionar</a>
            <hr>
        </div>
        <div class="col-md-12">
            <?php if ($Sessao::retornaMensagem()) { ?>
                <div class="alert alert-warning" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $Sessao::retornaMensagem(); ?>
                </div>
            <?php } ?>

            <form action="http://<?php echo APP_HOST; ?>/AgendaLancamento/index" method="post" >
                <p>
                <div class="col-md-3">
                    <h5>Pesquisar pelos próximos</h5>
                    <div class="form-group">
                        <input class="input-sm" type="text" maxlength="3" name="proximosDiasLanc" size="1" value="<?php echo $diasLancamentoFuturosDebito; ?>"> <?php echo " dias "; ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <h5>Pesquisar antecedendo</h5>
                    <div class="form-group">
                        <input class="input-sm" type="text" maxlength="3" name="antecedenteDiasLanc" size="1" value="<?php echo $diasLancamentoAtecedente; ?>"> <?php echo " dias "; ?>
                    </div>
                </div>

                <div class="col-md-2">
                    <h5>Lançamentos</h5>
                    <div class="form-group">
                        <select name="condicao" id="condicao" class="form-control input-sm">
                            <option value="" <?php echo ($viewVar['condicao'] == "") ? "selected" : ""; ?> >Todos</option>
                            <option value="S" <?php echo ($viewVar['condicao'] == "S") ? "selected" : ""; ?>>Debitados</option>
                            <option value="N" <?php echo ($viewVar['condicao'] == "N") ? "selected" : ""; ?>>Não debitados</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <h5>.</h5>
                    <button type="submit" class="btn btn-sm btn-success">&#10003;</button>
                </div>
                </p>
            </form>
        </div>
    </div>

    <?php
    if (!count($viewVar['listaLancamentoFuturo'])) {
        ?>
        <div class="alert alert-info" role="alert">Nenhum Lançamento encontrado até agora</div>
        <?php
    } else {

        echo $Sessao::retornaMensagem();
        ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <tr>
                    <td class="info" width="1">Codigo</td>
                    <td class="info">Dta Compra</td>
                    <td class="info">Inicio do Débito</td>
                    <td class="info">Estabelecimento</td>
                    <td class="info">Pagamento</td>
                    <td class="info">Vr Total</td>
                    <td class="info" width="1">Ativo</td>
                    <td class="info" width="1">Debitado</td>
                    <td class="info" width="1">Parcelas</td>
                    <td class="info" width="1">info</td>
                    <td class="info" width="350">Opções</td>
                </tr>
                <?php
                foreach ($viewVar['listaLancamentoFuturo'] as $contasReceber) {
                    $diaVenceu = $calculaDataVencida(date('Y/m/d', strtotime($contasReceber->data_debito)));
                    ?>
                    <tr>
                        <td><?php echo $contasReceber->getCodigo(); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($contasReceber->data_compra)); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($contasReceber->data_debito)); ?></td>
                        <td><?php echo $contasReceber->getEstabelecimento(); ?></td>
                        <td><?php echo $contasReceber->forma_pagamento; ?></td>
                        <td><?php echo number_format($contasReceber->valor_total, '2', ',', '.'); ?></td>
                        <td><?php echo $contasReceber->getAtivo(); ?></td>
                        <td><?php echo $contasReceber->getDebitado(); ?></td>
                        <td><?php echo $contasReceber->numero_parcela . " de " . $contasReceber->qtd_parcelas; ?></td>
                        <td><?php
                            if ($diaVenceu > 0) {
                                echo "Venceu há " . str_replace("+", "", $diaVenceu) . " dias ";
                            } else if($diaVenceu < 0) {
                                echo "Vencerá em " . str_replace("-", "", $diaVenceu) . " dias ";
                            }else{
                                echo "Vence Hoje!";
                            }
                            ?>
                        <td>
                            <?php $cripty = base64_encode($contasReceber->getCodigo()) . "99Fin" . base64_encode(\App\Lib\Sessao::retornaUsuario() . $contasReceber->getObs()); ?>

                            <a href="http://<?php echo APP_HOST; ?>/AgendaLancamento/edicao/<?php echo $cripty; ?>" class="btn btn-success btn-sm">Editar</a>
                            <a href="http://<?php echo APP_HOST; ?>/AgendaLancamento/excluir/<?php echo $contasReceber->getId() . "/" . $contasReceber->getCodigo(); ?>" 
                            <?php if ($contasReceber->getDebitado() == "S") {
                                ?> class="btn btn-danger btn-sm disabled" <?php
                               } else {
                                   ?> class="btn btn-danger btn-sm" <?php
                               }
                               ?> 
                               >Excluir</a>
                            <a href="http://<?php echo APP_HOST; ?>/AgendaLancamento/detalhes/<?php echo $contasReceber->getCodigo(); ?>" class="btn btn-info btn-sm">Detalhes</a>
                            <a href="http://<?php echo APP_HOST; ?>/Tarefas/debitaLancamentosFuturos/<?php echo $contasReceber->getCodigo() . "," . $contasReceber->codigo_debito . "," . $contasReceber->getId(); ?>" 
                            <?php if ($contasReceber->getDebitado() == "S") {
                                ?> class="btn btn-warning btn-sm disabled"> <?php
                               } else {
                                   ?> class="btn btn-warning btn-sm"> <?php }
                               ?>
                                Debitar
                            </a>
                            <a href="http://<?php echo APP_HOST; ?>/Tarefas/debitaLancamentosFuturos/<?php echo $contasReceber->getCodigo() . "," . $contasReceber->codigo_debito . "," . $contasReceber->getId(); ?>" 
                            <?php if ($contasReceber->getDebitado() == "S") {
                                ?> class="btn btn-warning btn-sm"> <?php
                                } else {
                                    ?> class="btn btn-warning btn-sm disabled"> <?php }
                                ?>
                                Cancelar
                            </a>
                            <?php
                            $caminhoImagem = "http://" . APP_HOST . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/agendamentos/agendamento" . $contasReceber->getCodigo() . "/agendamento_codigo_" . $contasReceber->getCodigo() . ".jpg";

                            //O CAMINHO PARA CONDIÇÃO DO 'file_exists' NÃO DEVE SER FEITO ATRAVÉS DE URL. PRECISA USAR CAMINHO DE ARQUIVO NO DISCO RÍGIDO 
                            $caminhoCondicao = RAIZ_SITE . "/public/comprovantes/" . $Sessao::retornaUsuario() . "/agendamentos/agendamento" . $contasReceber->getCodigo() . "/agendamento_codigo_" . $contasReceber->getCodigo() . ".jpg";
                            if (file_exists($caminhoCondicao)) {
                                ?> <a href="http://<?php echo APP_HOST; ?>/AgendaLancamento/openFile/<?php echo base64_encode("-public-comprovantes-" . $Sessao::retornaUsuario() . "-agendamentos-agendamento" . $contasReceber->getCodigo() . "-agendamento_codigo_" . $contasReceber->getCodigo() . ".jpg"); ?>" target="_blank"> <img src="http://<?php echo APP_HOST; ?>/public/images/visualizar.png" /></a> <?php
                            } else {
                                ?> <a target="_blank"> <img src="http://<?php echo APP_HOST; ?>/public/images/visualizar-no.png" /></a> <?php
                                }
                                ?>
                        </td>
                    </tr>
                    <?php
                    $totalGeral = $totalGeral + $contasReceber->valor_total;
                }
                ?>
            </table>
        </div>
        <?php
        echo 'Total de R$' . number_format($totalGeral, 2, ',', '.');
    }
    ?>
</div>