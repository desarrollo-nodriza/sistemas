<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?=$PageTitle;?> | Nodriza Spa</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<?= $this->Html->meta('icon', '/backend/img/logo-small.png', array('type' => 'png')); ?>
		<?= $this->Html->css(array(
			'/public/vendor/bootstrapv4/css/bootstrap.min', 'font-awesome.min'
		)); ?>
		<?= $this->Html->scriptBlock(sprintf("var webroot = '%s';", $this->webroot)); ?>
		<?= $this->Html->scriptBlock(sprintf("var fullwebroot = '%s';", $this->Html->url('/', true))); ?>
		
		<?= $this->Html->script(array(
			'https://code.jquery.com/jquery-3.1.1.min.js'
		)); ?>

		<?= $this->fetch('meta'); ?>
		<?= $this->fetch('css'); ?>
		<?= $this->fetch('script'); ?>
	</head>
	<body>

		<?= $this->element('public/alertas'); ?>

		<?= $this->fetch('content'); ?>
		

		<?= $this->Html->script(array(
			'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js',
			'/public/vendor/bootstrapv4/js/bootstrap.min'
		)); ?>
		<?= $this->fetch('scriptBottom'); ?>
	</body>
</html>
