<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Sistemas | Nodriza Spa</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<?= $this->Html->meta('icon'); ?>
		<?= $this->Html->css(array(
			'/backend/css/theme-dark',
			'/backend/css/icheck/skins/flat/red',
			'/backend/css/custom',
			/*
			'/backend/css/ion/ion.rangeSlider',
			'/backend/css/ion/ion.rangeSlider.skinFlat',
			'/backend/css/cropper/cropper.min.css',
			'/backend/css/jstree/jstree.min'
			*/
		)); ?>
		<?= $this->fetch('css'); ?>
		<?= $this->Html->scriptBlock("var webroot = '{$this->webroot}';"); ?>
		<?= $this->Html->scriptBlock("var fullwebroot = '{$this->Html->url('', true)}';"); ?>
		<?= $this->Html->script(array(
			'/backend/js/plugins/jquery/jquery.min',
			'/backend/js/plugins/jquery/jquery-ui.min',
			'/backend/js/plugins/bootstrap/bootstrap.min',
			'/backend/js/plugins/bootstrap/bootstrap-select',
			'/backend/js/plugins/icheck/icheck.min',
			'/backend/js/plugins/clipboard.min',
			'/backend/js/plugins/bootstrap/bootstrap-colorpicker',
			'/backend/js/plugins/datatables/jquery.dataTables.min',
			'/backend/js/plugins/smartwizard/jquery.smartWizard-2.0.min',
			'/backend/js/plugins/jquery-validation/jquery.validate',
			'/backend/js/plugins/bootstrap/bootstrap-datepicker',
			'/backend/js/plugins/morris/raphael-min',
			'/backend/js/plugins/morris/morris.min',
			'/backend/js/plugins/nvd3/lib/d3.v3',
			'/backend/js/plugins/nvd3/nv.d3.min',
			'/backend/js/plugins/owl/owl.carousel.min',
			'/backend/js/custom',
			//'/backend/js/plugins',
			//'/backend/js/demo_charts_nvd3'
			//'/backend/js/demo_charts_morris'
			/*
			'/backend/js/plugins/bootstrap/bootstrap-datepicker',

			'/backend/js/plugins/icheck/icheck.min',
			'/backend/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min',
			'/backend/js/plugins/summernote/summernote',
			'/backend/js/plugins/codemirror/codemirror',
			'/backend/js/plugins/codemirror/mode/sql/sql',

			'/backend/js/plugins',
			'/backend/js/plugins/owl/owl.carousel.min',
			//'/backend/js/actions',
			//'/backend/js/demo_sliders',
			//'/backend/js/demo_charts_morris',

			'/backend/js/plugins/morris/raphael-min',
			'/backend/js/plugins/morris/morris.min',
			'/backend/js/custom',
			//'/backend/js/demo_dashboard',
			'/backend/js/plugins/ion/ion.rangeSlider.min',
			'/backend/js/plugins/rangeslider/jQAllRangeSliders-min',

			'/js/vendor/bootstrap3-typeahead'
			*/
		)); ?>
		<?= $this->fetch('script'); ?>
	</head>
	<body>
        <div class="page-container">
			<?= $this->element('admin_menu_lateral'); ?>
            <div class="page-content">
                <?= $this->element('admin_menu_superior'); ?>
				<?= $this->element('admin_alertas'); ?>
				<?=$this->element('breadcrumbs'); ?>
				<?= $this->fetch('content'); ?>
			</div>
		</div>
        <audio id="audio-alert" src="<?= $this->Html->url('/backend/audio/alert.mp3'); ?>" preload="auto"></audio>
        <audio id="audio-fail" src="<?= $this->Html->url('/backend/audio/fail.mp3'); ?>" preload="auto"></audio>
		<?= $this->Html->script(array('/backend/js/actions')); ?>
    </body>
</html>
