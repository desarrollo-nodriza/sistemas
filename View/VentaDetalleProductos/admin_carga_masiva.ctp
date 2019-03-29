<div class="page-title">
	<h2><span class="fa fa-tags"></span> Actualización Masiva</h2>
</div>

<div class="page-content-wrap">

	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('CargaMasiva', array('url' => array('controller' => 'ventaDetalleProductos', 'action' => 'carga_masiva'), 'inputDefaults' => array('div' => false, 'label' => false), 'type' => 'file')); ?>
			<div class="panel panel-success">
				<div class="panel-body">
					<div class="form-group col-xs-12">
						<label>Descarga el archivo de ejemplo</label>
						<?=$this->Html->link('<i class="fa fa-download"></i> Ejemplo', '/ejemplos/ejemplo-producto.csv', array('escape' => false, 'target' => '_blank', 'fullbase' => true, 'class' => 'btn btn-xs btn-info')); ?>
					</div>
					<div class="form-group col-xs-12 col-md-6">
						<label>Archivo CSV</label>
						<?=$this->Form->input('csv', array(
							'class' => '',
							'type' => 'file'
							))?>
					</div>
					<div class="form-group col-xs-12 col-md-6">
						<label>Delimitador</label>
						<?=$this->Form->select('delimitador', 
							array(
								',' => 'Coma (,)',
								';' => 'Punto y coma (;)'
							), 
							array(
							'class' => 'form-control',
							'default' => ',',
							'empty' => false
							))?>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-upload" aria-hidden="true"></i> Cargar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-success btn-block')); ?>
						</div>
					</div>
				</div>
			</div>
			<?= $this->Form->end(); ?>
		</div>
	</div>
	

	<? if (!empty($productos) && !empty($resultadoCabeceras)) :  ?>
	<?= $this->Form->create('ConfirmarCargaMasiva', array('url' => array('controller' => 'ventaDetalleProductos', 'action' => 'carga_masiva'), 'class' => 'js-validate-producto', 'inputDefaults' => array('div' => false, 'label' => false))); ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Productos encontrados <small>(Total: <?=count($productos);?>)</small></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<td>#</td>
									<? foreach ($productos as $ip => $p) : ?>
										<? foreach ($resultadoCabeceras['found'] as $ic => $c) : ?>
											<? if (array_key_exists($c, $p)) :  ?>
												<? if ($c == 'fecha inicio' ) : ?>
													<th>Fecha inicio</th>
													<th>Hora inicio</th>
												<? elseif ($c == 'fecha termino') : ?>
													<th>Fecha termino</th>
													<th>Hora termino</th>
												<? else : ?>
													<th><?=$c;?></th>
												<? endif; ?>
											<? endif; ?>
										<? endforeach; break; ?>
									<? endforeach; ?>
									<td></td>
								</tr>
							</thead>
							<tbody id="carga_masiva_result">
							<? foreach ($productos as $ip => $p) : ?>
							<tr>
								<td><?=$ip+1;?></td>
								<? foreach ($resultadoCabeceras['found'] as $ic => $c) : ?>
									<? if (array_key_exists($c, $p)) :  ?>
									<td>
										<? if ($c == 'tipo de descuento') : ?>

											<?=$this->Form->select(sprintf('%d.PrecioEspecificoProducto.%d.tipo_descuento', $ip, $ip), $tipoDescuento ,array('empty' => false, 'default' => $p[$c], 'class' => 'form-control js-tipo-descuento not-blank', 'style' => 'min-width: 100px;'));?>

										<? elseif ($c == 'descuento') : ?>

											<?=$this->Form->input(sprintf('%d.PrecioEspecificoProducto.%d.descuento', $ip, $ip), array('type' => 'text', 'value' => $p[$c], 'class' => 'form-control not-blank', 'style' => 'min-width: 100px;'));?>

										<? elseif ($c == 'fecha inicio') : ?>
											
											<? $fecha = date_create_from_format('d-m-Y H:i:s', $p[$c]);?>
	
											<?=$this->Form->input(sprintf('%d.PrecioEspecificoProducto.%d.fecha_inicio', $ip, $ip), array('type' => 'text', 'value' => @date_format($fecha, 'Y-m-d'), 'class' => 'form-control datepicker js-f-inicio not-blank', 'style' => 'min-width: 100px;'));?>

										</td>
										<td>

											<?=$this->Form->input(sprintf('%d.PrecioEspecificoProducto.%d.hora_inicio', $ip, $ip), array('type' => 'text', 'value' => @date_format($fecha, 'G:i:s'), 'class' => 'form-control timepicker24 js-h-inicio not-blank', 'style' => 'min-width: 100px;'));?>

										<? elseif ($c == 'fecha termino') : ?>
											
											<? $fecha = date_create_from_format('d-m-Y H:i:s', $p[$c]); ?>

											<?=$this->Form->input(sprintf('%d.PrecioEspecificoProducto.%d.fecha_termino', $ip, $ip), array('type' => 'text', 'value' => @date_format($fecha, 'Y-m-d'), 'class' => 'form-control datepicker js-f-inicio not-blank', 'style' => 'min-width: 100px;'));?>

										</td>
										<td>

											<?=$this->Form->input(sprintf('%d.PrecioEspecificoProducto.%d.hora_termino', $ip, $ip), array('type' => 'text', 'value' => @date_format($fecha, 'G:i:s'), 'class' => 'form-control timepicker24 js-h-inicio not-blank', 'style' => 'min-width: 100px;'));?>
										
										<? elseif ($c == 'activo') : ?>

											<?= $this->Form->input(sprintf('%d.PrecioEspecificoProducto.%d.activo', $ip, $ip), array('type' => 'checkbox', 'class' => '', 'checked' => $p[$c])); ?>
										
										<? elseif ($c == 'descuento') : ?>
											
											<?= $this->Form->input(sprintf('%d.PrecioEspecificoProducto.%d.descuento', $ip, $ip), array('type' => 'text', 'class' => 'form-control js-descuento-input not-blank', 'placeholder' => 'Ej: 10, 55990', 'value' => $p[$c], 'style' => 'min-width: 100px;')); ?>

										<? elseif ($c == 'nombre descuento') : ?>
											
											<?=$this->Form->input(sprintf('%d.PrecioEspecificoProducto.%d.nombre', $ip, $ip), array('type' => 'text', 'value' => $p[$c], 'class' => 'form-control js-nombre-producto not-blank', 'style' => 'min-width: 100px;'));?>

										<? elseif ($c == 'id') : ?>

											<?=$this->Form->input(sprintf('%d.VentaDetalleProducto.id', $ip), array('type' => 'text', 'value' => $p[$c], 'class' => 'form-control not-blank', 'style' => 'min-width: 100px;'));?>

										<? elseif ($c == 'nombre') : ?>
											
											<?=$this->Form->input(sprintf('%d.VentaDetalleProducto.nombre', $ip), array('type' => 'text', 'value' => utf8_encode($p[$c]), 'class' => 'form-control not-blank', 'style' => 'min-width: 100px;'));?>

										<? elseif ($c == 'codigo proveedor') : ?>
											
											<?=$this->Form->input(sprintf('%d.VentaDetalleProducto.codigo_proveedor', $ip), array('type' => 'text', 'value' => utf8_encode($p[$c]), 'class' => 'form-control not-blank', 'style' => 'min-width: 100px;'));?>

										<? elseif ($c == 'precio costo') : ?>
											
											<?=$this->Form->input(sprintf('%d.VentaDetalleProducto.precio_costo', $ip), array('type' => 'text', 'value' => $p[$c], 'class' => 'form-control not-blank', 'style' => 'min-width: 100px;'));?>

										<? elseif ($c == 'stock virtual') : ?>
											
											<?=$this->Form->input(sprintf('%d.VentaDetalleProducto.cantidad_virtual', $ip), array('type' => 'text', 'value' => $p[$c], 'class' => 'form-control not-blank', 'style' => 'min-width: 100px;'));?>

										<? endif; ?>
									</td>
									<? endif; ?>
								<? endforeach; ?>
								<td valign="center">
									<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
								</td>
							</tr>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<a type="button" class="btn btn-primary mb-control" data-box="#modal_alertas">Guardar cambios</a>
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->

	<!-- MESSAGE BOX-->
	<div class="message-box message-box-info animated fadeIn" data-sound="alert" id="modal_alertas">
	    <div class="mb-container">
	        <div class="mb-middle">
	            <div class="mb-title" id="modal_alertas_label"><i class="fa fa-alert"></i> ¿Actualizar canales?</div>
	            <div class="mb-content">
	                <p id="mensajeModal">¿Desea actualizar el stock en los distintos canales de ventas disponibles?</p>
	                <div class="form-group">
						<div class="checkbox">
	                        <label>
	                            <?=$this->Form->input('actualizar_canales', array('type' => 'checkbox', 'class' => '')); ?>
	                            Sí, actualizar stock
	                        </label>
	                    </div>
	                </div>
	                       
	            </div>
	            <div class="mb-footer">
	                <div class="pull-right">
	                	<input type="submit" class="btn btn-primary btn-lg esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
	                	<button class="btn btn-default btn-lg mb-control-close">Cerrar</button>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	<!-- END MESSAGE BOX-->

	<?= $this->Form->end(); ?>
	<? endif; ?>

</div>
