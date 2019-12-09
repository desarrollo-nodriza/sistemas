<?= $this->Html->script(array(
	'/backend/js/cotizacion',
	'/backend/js/clientes')); ?>
<?= $this->fetch('script'); ?>

<div class="page-title">
	<h2><span class="fa fa-bookmark"></span> Editar Prospecto</h2>
</div>

<?= $this->Form->create('Prospecto', array('class' => 'form-horizontal js-formulario js-prospecto', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'), 'data-token' => $token)); ?>

<?= $this->Form->hidden('id'); ?>
<?= $this->Form->hidden('venta_cliente_id'); ?>
<?= $this->Form->hidden('direccion_id'); ?>
<?= $this->Form->hidden('cotizacion'); ?>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-sm-5">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Información del prospecto</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('VentaCliente.email', 'Email del cliente'); ?></th>
								<td>
									<div class="input-group" style="max-width: 100%;">
                                        <input name="" type="text" id="obtener_cliente" class="form-control not-blank" placeholder="Ingrese email del cliente" >
                                        <span class="input-group-addon link" data-toggle="modal" data-target="#modalCrearCliente"><i class="fa fa-plus"></i> Crear nuevo</span>
                                    </div>
	                                
                            	</td>
							</tr>
							<tr>
								<th><?= $this->Form->label('moneda_id', 'Medio de pago (*)'); ?></th>
								<td><?= $this->Form->input('moneda_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('origen_id', 'Origen del contacto (*)'); ?></th>
								<td><?= $this->Form->input('origen_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('transporte_id', 'Transporte'); ?></th>
								<td><?= $this->Form->input('transporte_id', array('empty' => 'Seleccione transporte')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('datos_bancarios', 'Agregar información de <br>transferencia a la cotización'); ?></th>
								<td><?= $this->Form->input('datos_bancarios', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('descripcion', 'Descripcion del prospecto (*)'); ?></th>
								<td><?= $this->Form->input('descripcion', array('placeholder' => 'Ingrese una descripción para el prospecto (max 100 carácteres)')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('comentarios', 'Comentarios adicionales'); ?></th>
								<td><?= $this->Form->input('comentarios', array('placeholder' => 'Ingrese comentarios adicionales.')); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<input type="submit" class="btn btn-info js-a-cotizacion" autocomplete="off" data-loading-text="Espera un momento..." value="Pasar a cotización">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
		<div class="col-xs-12 col-sm-7">
			
			<div class="panel panel-primary" id="ProspectoDirecciones">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="fa fa-home"></span> Seleccione una dirección</h3>
				</div>
				<div class="panel-body">
					
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<input type="submit" class="btn btn-info js-a-cotizacion" autocomplete="off" data-loading-text="Espera un momento..." value="Pasar a cotización">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><span class="fa fa-shopping-bag"></span> Productos</h3>
					<ul class="panel-controls">
						<li><p style="color:#000;line-height: 26px;padding: 0 15px 0 0;">Ingrese la referencia del producto</p></li>
						<li><input class="form-control" id="obtener_productos" placeholder="RF2010C" type="text" style="min-width: 200px;"></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="col-xs-12">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<th>ID</th>
									<th>Referencia</th>
									<th>Nombre</th>
									<th>Precio venta</th>
									<th>Cantidad</th>
									<th>Acciones</th>
								</thead>
								<tbody id="ProspectoProductos">
								<? foreach ($this->request->data['VentaDetalleProducto'] as $ip => $producto) : ?>
									<tr data-id="<?=$producto['id']; ?>">
										<td>
											<input type="hidden" name="data[VentaDetalleProducto][<?=$producto['id']; ?>][venta_detalle_producto_id]" value="<?=$producto['id']; ?>">
											<?=$producto['id']; ?>
										</td>
										<td>
											<?=$producto['codigo_proveedor']; ?>		
										</td>
										<td>
											<?=$producto['nombre']; ?>
										</td>
										<td data-precio="<?=$producto['ProductosProspecto']['monto']; ?>">
											<?= CakeNumber::currency($producto['ProductosProspecto']['monto'], 'CLP'); ?>
											<input type="hidden" name="data[VentaDetalleProducto][<?=$producto['id']; ?>][monto]" value="<?=$producto['ProductosProspecto']['monto']; ?>">	
										</td>
										<td>
											<input type="text" class="form-control not-blank is-number" name="data[VentaDetalleProducto][<?=$producto['id']; ?>][cantidad]" value="<?=$producto['ProductosProspecto']['cantidad']?>" placeholder="">
										</td>
										<td>
											<button class="remove_tr_prospecto btn btn-danger btn-xs">Quitar</button>
										</td>
									</tr>
								<? endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="3"></td>
										<td colspan="2"><?= $this->Form->label('descuento', 'Descuento global (1-100 %)'); ?></td>
										<td><?= $this->Form->input('descuento', array('class' => 'form-control is-number', 'style' => 'max-width: 70px;', 'min' => 0, 'max' => 10000)); ?></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<input type="submit" class="btn btn-info js-a-cotizacion" autocomplete="off" data-loading-text="Espera un momento..." value="Pasar a cotización">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
			
		</div> <!-- end col -->
		
	</div> <!-- end row -->
	<div class="row">
		<div class="col-xs-12">
			
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>


<?=$this->element('clientes/form-add', array('token' => $token)); ?>

<?=$this->element('direcciones/form-add', array('token' => $token, 'comunas' => $comunas)); ?>