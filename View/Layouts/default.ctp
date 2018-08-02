<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Proyecto</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<?= $this->Html->meta('favicon.ico', '/favicon.ico', array('type' => 'icon')); ?>
		<?= $this->Html->css(array(
			'bootstrap.min', 'font-awesome.min'
		)); ?>
		<?= $this->Html->scriptBlock(sprintf("var webroot = '%s';", $this->webroot)); ?>
		<?= $this->Html->scriptBlock(sprintf("var fullwebroot = '%s';", $this->Html->url('/', true))); ?>
		<?= $this->Html->script(array(
			'jquery-1.11.3.min', 'bootstrap.min'
		)); ?>
		<?= $this->fetch('meta'); ?>
		<?= $this->fetch('css'); ?>
		<?= $this->fetch('script'); ?>
	</head>
	<body>
		<?= $this->fetch('content'); ?>
	</body>
</html>
