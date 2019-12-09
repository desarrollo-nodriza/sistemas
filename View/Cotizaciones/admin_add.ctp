<div class="page-title">
	<h2><span class="fa fa-lightbulb-o"></span> Nueva Cotización</h2>
</div>
<?= $this->Form->create('Cotizacion', array('class' => 'form-horizontal js-formulario', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('venta_cliente_id', array('value' => $prospecto['Prospecto']['venta_cliente_id'], 'type' => 'hidden')); ?>
<?= $this->Form->input('direccion_id', array('value' => $prospecto['Prospecto']['direccion_id'], 'type' => 'hidden')); ?>
<?= $this->Form->input('prospecto_id', array('value' => $prospecto['Prospecto']['id'], 'type' => 'hidden')); ?>

<? if ( ! empty($prospecto['Prospecto']['transporte_id']) ) : ?>
	<?=$this->Form->input('transporte_id', array('value' => $prospecto['Prospecto']['transporte_id'], 'type' => 'hidden' ));?>
<? endif; ?>

<?= $this->Form->input('tienda_id', array('value' => $this->Session->read('Tienda.id'), 'type' => 'hidden')); ?>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Importante</h3>
			<p>Para crear la cotización, indique un nombre descriptivo con el cual pueda identificar la cotización en el futuro.</p>
			<p>La información cargada en la cotización viene desde el prospecto recién creado. Sí desea hacer una modificación de textos, precios, etc. considere que los calculos de IVA y descuentos deberá hacerlos manualmente.</p>
			<p>La información del cliente y los productos no modifica la información de la tienda. Es solo para generar la cotización.</p>
				<br>
			<p><b>La cotización se enviará al email ingresado en la sección "Datos de Cliente".</b></p>
		</div>
		<div class="col-xs-12 form-inline">
			<div class="form-group nombre-cotizacion">
				<?= $this->Form->label('nombre', 'Nombre'); ?><?= $this->Form->input('nombre', array('class' => 'form-control not-blank')); ?>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?=$this->Html->image($tienda['Tienda']['logo']['path'], array('class' => 'img-responsive logo-cotizacion'));?></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td style="width: 65%;">
									<table class="table">
										<tr>
											<td colspan="2"><b style="font-size: 16px;"><?=$tienda['Tienda']['nombre_fantasia'];?></b></td>
										</tr>
										<tr>
											<td><b>Rut:</b></td><td><?=$tienda['Tienda']['rut'];?></td>
										</tr>
										<tr>	
											<td><b>Dirección:</b></td><td><?=$tienda['Tienda']['direccion'];?></td>
										</tr>	
											<td><b>Giro:</b></td><td><?=$tienda['Tienda']['giro'];?></td>
										<tr>	
											<td><b>Fono:</b></td><td><?=$tienda['Tienda']['fono'];?></td>
										</tr>
										<? if ( $prospecto['Prospecto']['datos_bancarios'] && !empty($tienda['Tienda']['detalle_cuenta'])) : ?>
										<tr>
											<td><b>Datos para <br>la transerencia:</b></td><td><?=$this->Text->autoParagraph($tienda['Tienda']['detalle_cuenta']);?></td>
										</tr>
										<? endif; ?>
										<tr>	
											<td colspan="2"><a href="http://<?=$tienda['Tienda']['url'];?>" target="_blank"><?=$tienda['Tienda']['url'];?></a></td>
										</tr>
									</table>
								</td>
								<td style="vertical-align: middle; font-size: 30px; font-weight: bold; text-align: center;">
									Cotización XXXX
								</td>
							</tr>
						</table>
						<? if (!empty($prospecto['Prospecto']['comentarios'])) : ?>
						<table class="table" style="background-color: #DC5C60; color: #fff;">
							<tr>
								<td style=" width: 65%;"><b>Comentarios del prospecto:</b></td><td><?=$prospecto['Prospecto']['comentarios'];?></td>
							</tr>
						</table>
						<? endif; ?>
						<table class="table table-bordered">
							<thead>
								<th style=" width: 65%;">Datos de Cliente</th>
								<th>Condiciones comerciales de la oferta:</th>
							</thead>
							<tbody>
								<tr>
									<td>
										<table class="table">
											<tr>
												<td>Email:</td><td><?=$this->Form->input('email_cliente', array('value' => $prospecto['VentaCliente']['email']));?></td>
											</tr>
											<tr>
												<td>Nombre:</td><td><?=$this->Form->input('nombre_cliente', array('value' => $prospecto['VentaCliente']['nombre'] . ' ' . $prospecto['VentaCliente']['apellido']));?></td>
											</tr>
											<tr>
												<td>Teléfono:</td><td><?=$this->Form->input('fono_cliente', array('class' => 'mascara_fono form-control', 'value' => $prospecto['VentaCliente']['telefono']));?></td>
											</tr>
											<? if (!empty( $prospecto['Direccion']['calle'])) : ?>
											<tr>
												<td>Dirección:</td><td><?=$this->Form->input('direccion_cliente', array('value' => sprintf('%s #%s, %s. %s', $prospecto['Direccion']['calle'], $prospecto['Direccion']['numero'], $prospecto['Direccion']['depto'], $prospecto['Direccion']['Comuna']['nombre']) ));?></td>	
											</tr>
											<? else : ?>
											<tr>
												<td>Dirección:</td><td><?=$this->Form->input('direccion_cliente');?></td>	
											</tr>
											<? endif; ?>
											
											<tr>	
												<td>Asunto:</td><td><?=$this->Form->input('asunto_cliente', array('value' => $prospecto['Prospecto']['descripcion']));?></td>
											</tr>
											<? if ($prospecto['VentaCliente']['tipo_cliente'] == 'empresa') : ?>
											<tr>
												<td>Rut Empresa: </td><td><?=$this->Form->input('rut_empresa_cliente', array('value' => $prospecto['VentaCliente']['rut'], 'class' => 'form-control rut'));?></td>
											</tr>
											<? endif; ?>
											<? if ($prospecto['VentaCliente']['tipo_cliente'] == 'empresa') : ?>
											<tr>
												<td>Giro: </td><td><?=$this->Form->input('nombre_empresa_cliente', array('value' => $prospecto['VentaCliente']['giro_comercial']));?></td>
											</tr>
											<? endif; ?>
										</table>
									</td>
									<td>
										<table class="table">
											<tr>
												<td>Fecha:</td><td><?=$this->Form->input('fecha_cotizacion', array('value' => date('d-m-Y')));?></td>
											</tr>
											<tr>
												<td>Oferta válida solo:</td><td><?=$this->Form->input('validez_fecha_id');?></td>
											</tr>
											<tr>
												<td>Forma de pago:</td>
												<td>
												<? if ( ! empty($prospecto) ) : ?>
													<?= $this->Form->input('moneda_id', array('value' => $prospecto['Prospecto']['moneda_id'])); ?>
												<? else : ?>
													<?= $this->Form->input('moneda_id'); ?>
												<? endif; ?>
												</td>
											</tr>
											<tr>
												<td>Envío:</td><td><?=$this->Form->input('envio_cotizacion');?></td>
											</tr>
											<tr>
												<td>Vendedor:</td><td><?=$this->Form->input('vendedor', array('value' => $this->Session->read('Auth.Administrador.nombre')));;?></td>
											</tr>
											<tr>
												<td>Email vendedor:</td><td><?=$this->Form->input('email_vendedor', array('value' => $this->Session->read('Auth.Administrador.email')));;?></td>
											</tr>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
						<table class="table table-bordered">
							<tr>
								<thead>
									<th>Código</th>
									<th>Detalles del producto</th>
									<th>Cantidad</th>
									<th>Neto</th>
									<th>Total</th>
								</thead>
								<tbody>
									<? foreach ($prospecto['VentaDetalleProducto'] as $inx => $producto) : ?>
										<tr>
											<td>
												<?= $this->Form->hidden(sprintf('VentaDetalleProducto.%d.venta_detalle_producto_id', $inx), array('value' => $producto['id'])); ?>
												<?=$producto['codigo_proveedor'];?>
											</td>
											<td><?=$producto['nombre'];?></td>
											<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.cantidad', $inx), array('value' => $producto['ProductosProspecto']['cantidad'], 'class' => 'form-control not-blank is-number js-cantidad-item', 'min' => 1)); ?></td>
											<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.precio_neto', $inx), array('value' => $producto['monto_neto'], 'class' => 'form-control not-blank is-number js-neto-item')); ?></td>
											<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.total_neto', $inx), array('value' => $producto['total_neto'], 'class' => 'form-control not-blank is-number js-total-neto')); ?></td>
										</tr>								
									<? endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="4"><b>Total Neto</b></td><td><?=$this->Form->input('total_neto', array('value' => $prospecto['Prospecto']['total_neto'], 'class' => 'not-blank is-number form-control'));?></td>
									</tr>
									<tr>
										<td colspan="4"><b>IVA</b></td><td><?=$this->Form->input('iva', array('value' => $prospecto['Prospecto']['iva'], 'class' => 'not-blank is-number form-control'));?></td>
									</tr>
									<tr>
										<td colspan="4"><b>Descuento <?=$descuento = (!empty($prospecto['Prospecto']['descuento'])) ?  '(' . $prospecto['Prospecto']['descuento'] . '%)' :  '' ; ?> </b></td>
										<td><?=$this->Form->input('descuento', array('value' => $prospecto['Prospecto']['descuento_monto'], 'class' => 'form-control not-blank is-number js-calcular-totales', 'data-descuento' => $prospecto['Prospecto']['descuento']));?></td>
									</tr>
									<? if ( ! empty($prospecto['Prospecto']['transporte_id']) ) : ?>
									<tr>
										<td colspan="4"><b><?=$prospecto['Transporte']['nombre']; ?></b></td><td><?=$this->Form->input('transporte', array('value' => $prospecto['Transporte']['precio'], 'class' => 'not-blank is-number js-calcular-totales form-control'));?></td>
									</tr>
									<? endif; ?>
									<tr>
										<td colspan="4"><b>Total</b></td><td><?=$this->Form->input('total_bruto', array('value' => $prospecto['Prospecto']['total_bruto'], 'class' => 'form-control not-blank is-number'));?></td>
									</tr>
								</tfoot>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<a href="#" class="mb-control  btn btn-success" data-box="#mb-alerta-cotizacion"><i class="fa fa-floppy-o" aria-hidden="true"></i> Generar Cotización</a>
						<?= $this->Html->link('Volver al prospecto', array('controller' => 'prospectos', 'action' => 'edit', $prospecto['Prospecto']['id']), array('class' => 'btn btn-warning')); ?>
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
<div class="message-box message-box-banger animated fadeIn" data-sound="alert" id="mb-alerta-cotizacion">
	<div class="mb-container">
		<div class="mb-middle">
			<div class="mb-title"><span class="fa fa-floppy-o"></span>¿Generar <strong>Cotización</strong>?</div>
			<div class="mb-content">
				<p>¿Seguro que quieres generar esta cotización?</p>
				<p>La cotización se enviará <b>via email al cliente.</b></p>
				<p>Para cancelar presiona No</p>
			</div>
			<div class="mb-footer">
				<div class="pull-right">
					<input type="submit" class="btn btn-success esperar-carga btn-lg" autocomplete="off" data-loading-text="Espera un momento..." value="Generar cotización">
					<button class="btn btn-default btn-lg mb-control-close">No</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>


<?= $this->Html->script(array(
	'/backend/js/cotizacion')); ?>
<?= $this->fetch('script'); ?>