<?php
$dataHoje = date('Y-m-d');
?>

<div class="container">
    <div class="row">
        <div class="col-md-3"></div>

        <fildset>
            <legend>Meus relatos de erros</legend>
        </fildset>

        <?php
        if (($viewVar['detalhes'])) {
            echo "Consulta de relato de erro. codigo #" . str_pad($viewVar['detalhes'],5,'0',STR_PAD_LEFT) . "";
            ?>
        <fieldset>
          <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr >
                        <td class="info">Titulo</td>
                        <td class="info">Queixa</td>
                        <td class="info">Data</td>
                        <td width="10" class="info">status</td>
                    </tr>
                    <?php
                    foreach ($viewVar['relatosErrosPesquisa'] as $relatoErro) {
                        ?>
                        <tr>
                            <td><?php echo $relatoErro['titulo'] ?></td>
                            <td><?php echo $relatoErro['texto'] ?></td>
                            <td><?php echo date("d/m/Y", strtotime($relatoErro['data'])) ?></td>
                            <td><?php echo $relatoErro['status'] ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </fieldset>
            <?php
        }
        
        
        if (($viewVar['relatosErros'])) {
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr >
                        <td width="1" class="info">Codigo</td>
                        <td class="info">Titulo</td>
                        <td class="info">Data</td>
                        <td width="10" class="info">status</td>
                        <td width="1"  class="info">Opções</td>
                    </tr>
                    <?php
                    foreach ($viewVar['relatosErros'] as $relatoErro) {
                        ?>
                        <tr>
                            <td style="background-color: <?php if($relatoErro['status']=="concluido"){ ?> green <?php } elseif ($relatoErro['status']=="analise") { ?> grey <?php } ?> "><?php echo $relatoErro['codigo'] ?></td>
                            <td><?php echo $relatoErro['titulo'] ?></td>
                            <td><?php echo date("d/m/Y", strtotime($relatoErro['data'])) ?></td>
                            <td><?php echo $relatoErro['status'] ?></td>
                            <td>
                                <form method="POST" action="http://<?php echo APP_HOST; ?>/conta/meusRelatosErros">
                                    <input type="hidden" name="codigoErro" value="<?php echo $relatoErro['codigo'] ?>">
                                    <input type="hidden" name="User" value="true">
                                    <button class="btn btn-info btn-sm">Detalhes</button>
                                </form>
                                
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        }
        ?>



        <?php if (\App\Lib\Sessao::retornaMensagem()) { ?>
            <div class="alert alert-warning" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $Sessao::retornaMensagem() ?>
            </div>
        <?php } ?>
        <?php if (\App\Lib\Sessao::retornaErro()) { ?>
            <div class="alert alert-warning" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $Sessao::retornaErro() ?>
            </div>
        <?php } ?>

        <div class=" col-md-3"></div>
    </div>
</div>