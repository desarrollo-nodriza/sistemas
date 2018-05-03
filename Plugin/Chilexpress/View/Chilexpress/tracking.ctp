<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3">
			<ul class="tiendas">
				<? foreach ($tiendas as $it => $tienda) : ?>
					<li>
					<?=$this->Html->image($tienda['Tienda']['logo']['path'], array('class' => 'img-responsive img-tienda')); ?>
					</li>
				<? endforeach; ?>
			</ul>
		</div>
	</div>

	
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3">
			<?=$this->element('tracking_alert');?>
			<div class="panel panel-default">
				<div class="panel-body">
				<?= $this->Form->create('Tracking', array('class' => '', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
					<div class="row">
						<div class="form-group col-xs-12">
							<h1 class="title">Ingrese el número de seguimiento</h1>
							<?=$this->Form->input('tracking_number', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'ej: 989785335', 'empty' => false)); ?>
						</div>
						<div class="col-xs-12">
							<button type="submit" class="btn-block btn btn-send"><?=__('Continuar');?> <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
						</div>
					</div>
				<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>