<div class="page-title">
	<h2><span class="fa fa-list-ol"></span> Movimientos de ubicación</h2>
</div>

<?= $this->Form->create('Zonificacion', array('inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate-producto')); ?>
<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-arrows" aria-hidden="true"></i> Mover inventario</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-stripped">
							<thead>
								
								<th>Ubicación origen</th>
								<th>Cantidad</th>
								<th>Ubicación destino</th>
								<th>Cantidad a Mover</th>
							</thead>
							<tbody>
							<? foreach ($zonificaciones as $key => $zonificacion ) : ?>
								<? if ($zonificacion[0]['cantidad']!=0) : ?>
									<tr>
										<td>
											<?= $this->Form->input(sprintf('%d.id', $key), array('default'=> $zonificacion['Zonificacion']['ubicacion_id'])); ?>
											<?= $ubicaciones[$zonificacion['Ubicacion']['id']];?>
										</td>
										<td>
											<?=  $zonificacion[0]['cantidad'];?>
										</td>
										<td style="width: 500px">
											<?php 
											$ubicacion = $ubicaciones;
											unset($ubicacion[$zonificacion['Zonificacion']['ubicacion_id']]);
											asort($ubicacion);
											?>
											<?=$this->Form->select(sprintf('%d.ubicacion_id', $key), $ubicacion, array('empty' => 'Seleccione Ubicación', 'class' =>[ 'form-control', 'mi-selector'],'style'=>"width: 400px")); ?>
										</td>
										<td>
											<?=$this->Form->input(sprintf('%d.cantidad', $key), array('type'=> "text", 'class' => 'is-number form-control', 'max' => $zonificacion['Zonificacion']['cantidad'], 'min' => 1));?>
										</td>
									</tr>

								<? endif; ?>
								
								
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-shopping-bag"></i> Ir al producto', array('controller' => 'ventaDetalleProductos', 'action' => 'edit',$zonificacion['Zonificacion']['producto_id']), array('class' => 'btn btn-primary btn-buscar btn-block', 'rel' => 'tooltip', 'title' => 'Ir al producto', 'escape' => false)); ?>
						</div>
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-send" aria-hidden="true"></i> Mover', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">

	jQuery(document).ready(function($){
		$(document).ready(function() {
			$('.mi-selector').select2();
		});
	});

</script>