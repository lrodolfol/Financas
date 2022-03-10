<?php
$carteiras = $viewVar['carteiras'];
$relatCarteiras = $viewVar['relatCarteiras'];
?>
<div class="container">
    <div class="row">        

        <div class="col-md-12">
            <fildset>
                <legend>Relatório carteiras
                    <img title="Transferência entre carteiras" src="<?php echo "http://" . APP_HOST . "/public/images/printer.ico" ?> ">
                </legend>
                <form action="http://<?php echo APP_HOST; ?>/carteiras/relatorio" method="post">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Data inicio</label>
                                <input type="date" class="form-control"  name="data_inicio" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Data final</label>
                                <input type="date" class="form-control"  name="data_final" >
                            </div>
                        </div>

                        <!--<div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Ordenação</label>
                                <select class="form-control" name="order">
                                    <option>Carteira</option>
                                    <option>Data</option>
                                </select>
                            </div>
                        </div> -->
                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php foreach ($carteiras as $carteira) : ?>
                                    <label><?= " | " . $carteira->nome; ?></label>
                                    <input type="checkbox" name="<?= $carteira->nome; ?>" >
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-sm">Gerar</button>
                </form>
            </fildset>
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
        </div>

        <?php if ($relatCarteiras): ?>
            <div class="col-md-12">
                <table class="table table-hover table-sm table-striped">
                    <thead>
                        <tr>
                            <th>
                                Carteira
                            </th>
                            <th>
                                Data
                            </th>
                            <th>
                                Observação
                            </th>
                            <th>
                                Valor
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatCarteiras as $key => $value) : ?>
                            <tr>
                                <td>
                                    <?= $value->nome; ?> 
                                </td>
                                <td>
                                    <?= $value->valor; ?> 
                                </td>
                                <td>
                                    
                                </td>
                                <td>
                                    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>