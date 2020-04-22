<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Crear OC manual</h2>
</div>


<?= $this->Form->create('OrdenCompra', array( 'class' => 'form-horizontal js-validate-oc', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
		
	<?= $this->Form->input('administrador_id', array('value' => $this->Session->read('Auth.Administrador.id'), 'type' => 'hidden')); ?>
	<?= $this->Form->input('tienda_id', array('value' => $this->Session->read('Tienda.id'), 'type' => 'hidden')); ?>
	<?= $this->Form->input('estado', array('value' => 'validacion_comercial', 'type' => 'hidden')); ?>
	<?= $this->Form->input('oc_manual', array('value' => 1, 'type' => 'hidden')); ?>

	<div class="page-content-wrap">
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-info js-oc">
					<div class="panel-body">
						<div class="form-group form-inline">
							<?=$this->Form->label('proveedor_id', 'Seleccione proveedor');?>
							<?=$this->Form->input('proveedor_id', array('class' => 'form-control js-select-proveedor not-blank'));?>
						</div>
					</div>
					<div class="panel-body hide">
						<div class="table-responsive">
							<table class="table">
								<tr>
									<td>
										<table class="table table-bordered">
											<tr>
												<td colspan="2"><b>Datos de la empresa</b></td>
											</tr>
											<tr>
												<td>Rut empresa: </td>
												<td><?=$this->Form->input('rut_empresa', array('type' => 'text', 'class' => 'form-control not-blank is-rut js-rut-proveedor') );?></td>
											</tr>
											<tr>
												<td>Razón Social: </td>
												<td><?=$this->Form->input('razon_social_empresa', array('type' => 'text', 'class' => 'form-control not-blank js-razon-social-proveedor') );?></td>
											</tr>
											<tr>
												<td>Giro: </td>
												<td><?=$this->Form->input('giro_empresa', array('type' => 'text', 'class' => 'form-control not-blank js-giro-proveedor') );?></td>
											</tr>
											<tr>
												<td>Nombre de contacto: </td>
												<td><?=$this->Form->input('nombre_contacto_empresa', array('type' => 'text', 'class' => 'form-control js-contacto-proveedor not-blank') );?></td>
											</tr>
											<tr>
												<td>Email: </td>
												<td><?=$this->Form->input('email_contacto_empresa', array('type' => 'text', 'class' => 'form-control js-email-proveedor not-blank is-email') );?></td>
											</tr>
											<tr>
												<td>Teléfono: </td>
												<td><?=$this->Form->input('fono_contacto_empresa', array('type' => 'text', 'class' => 'form-control js-fono-proveedor') );?></td>
											</tr>
											<tr>
												<td>Dirección comercial: </td>
												<td><?=$this->Form->input('direccion_empresa', array('type' => 'text', 'class' => 'form-control js-direccion-proveedor') );?></td>
											</tr>
										</table>
									</td>
									<td>
										<table class="table table-bordered">
											<tr>
												<td colspan="2"><b>Despacho</b></td>
											</tr>
											<tr>
												<td>Fecha: </td>
												<td><?=$this->Form->input('fecha', array('type' => 'text', 'class' => 'form-control datepicker not-blank is-date', 'value' => date('Y-m-d')) );?></td>
											</tr>
											<tr>
												<td>Vendedor: </td>
												<td><?=$this->Form->input('vendedor', array('type' => 'text', 'class' => 'form-control not-blank', 'value' => $this->Session->read('Auth.Administrador.nombre') ) );?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							
							<table class="table table-bordered js-clone-wrapper">
								<thead>
									<th>Item</th>
									<th>Código</th>
									<th>Descripción</th>
									<th>Cantidad</th>
									<th>N. Unitario</th>
									<th>Descuento</th>
									<th>Total Neto</th>
									<th><a href="#" class="copy_tr btn btn-rounded btn-primary"><span class="fa fa-plus"></span> agregar</a></th>
								</thead>
								<tboby class="">
									<tr class="hidden clone-tr">
										<td>
											<?= $this->Form->input('VentaDetalleProducto.999.venta_detalle_producto_id', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-id-producto not-blank')); ?>
										</td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.codigo', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank js-codigo-producto')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.descripcion', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-descripcion-producto js-buscar-producto not-blank', 'style' =>'width: 200px;')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.cantidad', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-cantidad-producto not-blank is-number')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.precio_unitario', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-precio-producto not-blank')); ?></td>
										<td data-toggle="tooltip" data-placement="top" title="" class="js-descuento-valor"><?= $this->Form->input('VentaDetalleProducto.999.descuento_producto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-descuento-producto not-blank is-number')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.total_neto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-total-producto not-blank is-number')); ?></td>
										<td valign="center">
											<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
										</td>
									</tr>
								
								</tboby>
								<tfoot>
									<tr>
										<td colspan="6"></td>
										<td>Total neto</td>
										<td colspan="2"><?=$this->Form->input('total_neto', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-neto', 'value' => '') );?></td>
									</tr>
									<tr>
										<td colspan="6"></td>
										<td>IVA</td>
										<td colspan="2"><?=$this->Form->input('iva', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-iva', 'value' => '') );?></td>
									</tr>
									<tr>
										<td colspan="6"></td>
										<td>Total</td>
										<td colspan="2"><?=$this->Form->input('total', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-oc', 'value' => '') );?></td>
									</tr>
								</tfoot>
							</table>
						</div>

						<div class="form-group">
							<?= $this->Form->label('mensaje_final', 'Mensaje para el proveedor');?>
							<?= $this->Form->input('mensaje_final', array('class' => 'form-control js-contacto-proveedor-input', 'placeholder' => 'Ingrese texto...')); ?>
						</div>
					</div>
					<div class="panel-footer hide">
						<div class="pull-right">
							<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Enviar a revisión">
							<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>

<?= $this->Form->end(); ?>