<div class="container">
    <div class="starter-template">
        <h2>Relatório de débitos sem itens</h2>

        <?php if (isset($viewVar['debitosSemItens'])) {
            ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr>
                        <td width="1" class="info">Código</td>
                        <td class="info">data</td>
                        <td class="info">Valor</td>
                        <td class="info">Estab.</td>
                        <td class="info">Forma Pag.</td>
                        <td class="info">Obs</td>
                        <td class="info"></td>
                    </tr>
                    <?php foreach ($viewVar['debitosSemItens'] as $debitos) { ?>
                        <tr>
                            <td><?php echo $debitos['codigo']; ?></td>
                            <td><?php echo $debitos['data_compra']; ?></td>
                            <td><?php echo $debitos['valor_total']; ?></td>
                            <td><?php echo $debitos['estabelecimento']; ?></td>
                            <td><?php echo $debitos['forma_pagamento']; ?></td>
                            <td><?php echo $debitos['obs']; ?></td>
                            <td>
                                <a target="_blank" href="http://<?php echo APP_HOST; ?>/debito/novo/<?php echo $debitos['codigo']; ?>" class="btn btn btn-sm"><img src="http://<?php echo APP_HOST; ?>/public/images/go.png" /></a>
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
    </div>
</div>