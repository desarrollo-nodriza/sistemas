<!DOCTYPE html>
<html lang="es" class="body-full-height">
	<head>
		<title>Socios | Administración</title>
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
			'/backend/js/plugins/jquery/jquery-ui.min',
			'/backend/js/plugins/bootstrap/bootstrap.min',
			'/backend/js/plugins/bootstrap/bootstrap-datepicker',
			'/backend/js/plugins/datatables/jquery.dataTables.min',
			'/backend/js/plugins/morris/raphael-min',
			'/backend/js/plugins/morris/morris.min',
			'/backend/js/plugins/jquery-validation/jquery.validate',
			'/backend/js/custom',
			'/backend/js/revision_oc',
			'/backend/js/socios'
		)); ?>
		<?= $this->fetch('meta'); ?>
		<?= $this->fetch('css'); ?>
		<?= $this->fetch('script'); ?>
	</head>
    <body>
		<div id="socio" class="container-fluid">
			<?= $this->fetch('content'); ?>
        </div>
    </body>
</html>
