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
			'/public/vendor/bootstrapv4/css/bootstrap.min', 
			'/public/css/app', 
			'https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap'
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

		<div class="wrapper">
			<!-- Sidebar -->
		    <nav id="sidebar" class="d-none d-md-block">
		        <?= $this->element('public/sidebar-cliente'); ?>
		    </nav>

		    <div id="content">
				
				<?= $this->element('public/navbar-cliente'); ?>

				<?= $this->element('public/breadcrumbs-cliente'); ?>
				<div id="wrapper-content" class="py-3">
					<?= $this->fetch('content'); ?>
				</div>
			</div>
		</div>	

		<?= $this->Html->script(array(
			'/public/vendor/jqueryvalidate/dist/jquery.validate.min',
			'/public/vendor/jqueryvalidate/dist/localization/methods_es_CL.min',
			'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js',
			'/public/vendor/bootstrapv4/js/bootstrap.min',
			//'/public/vendor/bootstrap-table/bootstrap-table.min',
			//'/public/vendor/bootstrap-table/bootstrap-table-locale-all.min',
			'https://use.fontawesome.com/releases/v5.0.13/js/solid.js', 
			'https://kit.fontawesome.com/a67cb1691e.js',
			'/public/js/app'
		)); ?>
		<?= $this->fetch('script-bottom'); ?>
	</body>
</html>