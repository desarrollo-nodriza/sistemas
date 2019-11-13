<div class="page-title">
	<h2><span class="fa fa-qr"></span> Obtener QR SEC</h2>
</div>

<div class="page-content-wrap">
	
	<? if (!empty($qr_obtenidos)) : ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-primary panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list" aria-hidden="true"></i> Resultados de la operación</h3>
					<ul class="panel-controls"> 
                    	<li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<div class="table-reponsive">
						<table class="table table-borderder">
							<caption><?= (count($qr_obtenidos) == 1) ? 'Se logró obtener 1 item con éxito.' : 'Se lograron obtener ' . count($qr_obtenidos) . ' itemes con éxito.' ; ?></caption>
							<th>Item</th>
							<th>Url externa</th>
							<th>Qr</th>
							<tbody>
							<? foreach ($qr_obtenidos as $qr) : ?>
								<tr>
									<td><?=$qr['item']; ?></td>
									<td><?=$qr['url']; ?></td>
									<td><img src="<?=$qr['qr']; ?>" class="img-responsive" style="max-width: 80px;"></td>
								</tr>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<? endif; ?>

	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Sec', array('url' => array('controller' => 'ventaDetalleProductos', 'action' => 'obtenerqrsec'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
	
			<?= $this->Form->hidden('buscar', array('value' => 1)); ?>

			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Url base</h3>
				</div>
				<div class="panel-body">
					<div class="alert alert-warning">
						<p>Ingrese la url base del contenedor de QR´s. El sistema buscará las imágenes que coincidan con la referencia del producto y la guardará en el sistema.</p>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
							<label>Url base</label>
							<?=$this->Form->input('url', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: http://www.makita.cl//wp-content/gallery/qr-motosierra/thumbs/'
								))?>
							<span class="help-block">Recuerda agregar protocolo http o https y un slash (/) al final de la url.</span>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Nombre imagen</label>
							<?=$this->Form->input('nombre', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: imagen-sec-{ref}.jpg'
								))?>
							<span class="help-block">{ref} será reemplazado por el código de proveedor registrado en nuestro sistema.</span>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Marcas</label>
							<?=$this->Form->select('marca', $marcas, array(
								'class' => 'form-control select',
								'data-live-search' => 'true',
								'empty' => 'Seleccione',
								'multiple' => true
								))?>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Procesar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar start-loading-then-redirect btn-success btn-block')); ?>
						</div>
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Cancelar', array('action' => 'obtenerqrsec'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
						</div>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>