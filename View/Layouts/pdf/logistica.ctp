<!DOCTYPE html>
<html lang="es">
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<?= $this->Html->css(array(
			'/backend/css/logistica'
		), null, array('fullBase' => true) ); ?>
		<?= $this->fetch('css'); ?>
</head>
<body>
	<?php echo $this->fetch('content'); ?>
</body>
</html>