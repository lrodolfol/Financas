<footer class="footer">
    <div class="container">
        <p class="text-muted">
        &copy; 2020 <a href="https://br.linkedin.com/in/rodolfoj-silva" target="_blank" style="text-decoration: none">Rodolfo J.Silva</a>
        <?php
        if ($baseDadosOperante == "PRODUCAO") {
            echo 'Base de PRODUÇÃO';
        } elseif ($baseDadosOperante == "LOCAL") {
            echo 'Base LOCAL';
        }else{
            echo 'Base de TESTES';
        }
        ?>
        </p>
    </div>
</footer> 


<script src="http://<?php echo APP_HOST; ?>/public/js/jquery-3.2.1.min.js"></script>
<script src="http://<?php echo APP_HOST; ?>/public/js/jquery.validate.min.js"type="text/javascript"></script>
<script src="http://<?php echo APP_HOST; ?>/public/js/validacao.js"type="text/javascript"></script>
<script src="http://<?php echo APP_HOST; ?>/public/js/bootstrap.min.js"></script>
<script src="http://<?php echo APP_HOST; ?>/public/js/graficos.js"></script>

<?php
\App\Lib\Sessao::limpaErro();
\App\Lib\Sessao::limpaMensagem();
\App\lib\Sessao::limpaFormulario();
\App\lib\Sessao::limpaCodigo();
?>
</body>
</html>