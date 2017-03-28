<!DOCTYPE html>
<html lang="es">
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<?= $this->Html->css(array(
			'/backend/css/cotizacion'
		), null, array('fullBase' => true) ); ?>
		<?= $this->fetch('css'); ?>
</head>
<body>
	<div class="container">
		<?php echo $this->fetch('content'); ?>
	</div>
</body>
</html>