<?php
$arquivo = $viewVar['arquivo'];
$arquivo = str_replace("-","/",base64_decode($arquivo));
?>

<div>
	<iframe style="width: 100vw;height: 100vh" src="http://<?php echo APP_HOST . $arquivo ?>" ></iframe>
</div>
