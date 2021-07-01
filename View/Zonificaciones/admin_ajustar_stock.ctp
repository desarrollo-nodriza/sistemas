<div class="page-title">
	<h2><span class="fa fa-list-ol"></span> Ajustar stock de ubicación</h2>
</div>

<?= $this->Form->create('Zonificacion', array('inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate-producto')); ?>
<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-arrows" aria-hidden="true"></i> Ajustar inventario</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-stripped">
							<thead>
								
								<th>Ubicación</th>
								<th>Cantidad actual</th>
								<th>Cantidad a ajustar</th>
							</thead>
							<tbody>
							<? foreach ($zonificaciones as $key => $zonificacion ) : ?>
								<? if ($zonificacion[0]['cantidad']!=0) : ?>
									<tr>
										<td>
										
											<?= $this->Form->input(sprintf('%d.id', $key), array('default'=>$zonificacion['Ubicacion']['id'])); 
											 echo($zonificacion['Ubicacion']['Zona']['nombre'].' - '.$zonificacion['Ubicacion']['columna'].' - '.$zonificacion['Ubicacion']['fila']);
											?>
										</td>
										<td>
											<?=  $zonificacion[0]['cantidad'];?>
										</td>										
										<td>
											<?=$this->Form->input(sprintf('%d.cantidad', $key), array('type'=> "text", 'class' => 'is-number form-control', 'min' => 0));?>
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

