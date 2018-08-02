<!DOCTYPE html>
<html lang="es" class="body-full-height">
	<head>
		<title>Proyecto | Administración</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<?= $this->Html->meta('icon'); ?>
		<?= $this->Html->css(array(
			'/backend/css/theme-dark',
			'/backend/css/custom'
		)); ?>
		<?= $this->Html->scriptBlock("var webroot = '{$this->webroot}';"); ?>
		<?= $this->Html->scriptBlock("var fullwebroot = '{$this->Html->url('', true)}';"); ?>
		<?= $this->Html->script(array(
			'/backend/js/plugins/jquery/jquery.min',
			'/backend/js/plugins/bootstrap/bootstrap.min',
			'/backend/js/custom'
		)); ?>
		<?= $this->fetch('meta'); ?>
		<?= $this->fetch('css'); ?>
		<?= $this->fetch('script'); ?>
	</head>
    <body>
		<div class="login-container">
			<?= $this->fetch('content'); ?>
        </div>
    </body>
</html>
