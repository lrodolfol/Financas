<?php
$dataCadastro = App\Lib\Sessao::retornaDataCadastroUsuario();
$anoCadastro = date('Y', strtotime($dataCadastro));
$anoAtual = date('Y');

$mesesBloqueados = ([]);
$anoBloqueado = $viewVar['anoBloqueado'];

if (isset($viewVar['compBloqueado'])) {
    for ($i = 0; $i <= 12; $i++) {
        if ($viewVar['compBloqueado'][$i]['encerrado'] == "S") {
            $mesesBloqueados[$i+1] = "S";
        }
    }
}
?>
<div class="container">
    <div class="row">

        <div class="col-md-12">
            <legend>Informe a competência para bloqueio
                <h5><i>Nenhuma operação de crédito/debito será realizada para esse competência</i></h5></legend>
            
            <form method="POST" action="http://<?php echo APP_HOST; ?>/Conta/bloqueioCompetencia">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="formaPagamento">Ano</label>
                            <select class="form-control" for="ano" name="ano" onchange="this.form.submit()" >Forma de Pagamento
                                <option></option>
                                <?php
                                for ($i = $anoCadastro; $i <= $anoAtual; $i++) {
                                    ?>
                                    <option <?php if ($i == $anoBloqueado) { ?> selected <?php } ?>  > <?php echo $i; ?> </option>
                                    <?php
                                }
                                ?>
                            </select> <button href="#" class="btn btn-info btn-sm" name="buscar" >Buscar</button>
                        </div>
                    </div>
                </div>
            </form>



        <form method="POST" action="http://<?php echo APP_HOST; ?>/Conta/bloquearCompetencia">
            <div class="row">
                <div class="col-md-4">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $mes = $anoAtual . "/" . str_pad($i, 2, '0', STR_PAD_LEFT) . "/01";
                        $mes = date('M', strtotime($mes))
                        ?>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="competencia"><?php echo $mes; ?></label>
                                <input type="checkbox" name="<?php echo $i; ?>" 
                                <?php
                                if (array_key_exists($i, $mesesBloqueados)) {
                                    $rrr = 0;
                                    ?> checked <?php
                                       } else {
                                           $rrr = 0;
                                       }
                                       ?>
                                       >
                            </div>
                        </div>
                    <?php } ?>
                    <input type="hidden" name="anoBloq" value="<?php echo $anoBloqueado; ?>">
                </div>
            </div>

            <div class="row">
                <button type="submit" class="btn btn-success btn-sm" name="submit" >Salvar</button>
            </div>

        </form>


        <?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
            <div class="alert alert-warning" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $Sessao::retornaMensagem() ?>
            </div>
        <?php } ?>
    </div>
</div>
</div>