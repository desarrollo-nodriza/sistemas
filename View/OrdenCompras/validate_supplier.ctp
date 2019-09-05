<div class="page-content-wrap">
<? if (isset($this->request->query['success'])) : ?>
	<div class="row mt-5">
		<div class="col-xs-12 col-md-offset-3 col-md-6 mt-5">
			<div class="panel panel-success mt-5">
				<div class="panel-body">
					<?= $this->Html->image(sprintf('Tienda/%d/%s', $this->request->data['Tienda']['id'], $this->request->data['Tienda']['logo']), array('class' => 'img-responsive center-block', 'style' => "max-width: 150px; margin: 30px auto;"));?>
					<h2 class="text-center">OC #<?=$this->request->data['OrdenCompra']['id'];?> guardada con éxito</h2>
					<h4 class="text-center">Muchas gracias por ayudarnos en éste proceso. Pronto recibirás otra OC con las correcciones solicitadas.</h4>
				</div>
			</div>
			<p class="text-right text-muted">Ya puede cerrar ésta ventana.</p>
		</div>
	</div>
<? elseif (isset($this->request->query['fail'])) : ?>

<? else : ?>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizontal js-oc-proveedor', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
	
	<?= $this->Form->input('id');?>
	<?= $this->Form->hidden('validado_proveedor', array('value' => 1));?>
	<?= $this->Form->hidden('fecha_validado_proveedor', array('value' => date('Y-m-d H:i:s')));?>
	<?= $this->Form->hidden('estado', array('value' => 'validado_proveedor'));?>

	<div class="row" style="margin-top: 30px;">
		<div class="col-xs-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title text-uppercase"><i class="fa fa-check-circle text-success"></i> Validar productos <b><?=$this->request->data['Proveedor']['nombre'];?></b></h3>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td colspan="2" valign="center" style="vertical-align: middle; padding: 15px;"><?= $this->Html->image(sprintf('Tienda/%d/%s', $this->request->data['Tienda']['id'], $this->request->data['Tienda']['logo']), array('class' => 'img-responsive', 'style' => "max-width: 150px;"));?></td>
							</tr>
							<tr>
								<td>
									<table class="table table-bordered">
										<caption style="font-size: 14px; font-weight: 600;">Solicitante</caption>
										<tr>
											<th>Empresa:</th>
											<td><?=$this->request->data['Tienda']['nombre_fantasia']; ?></td>
										</tr>
										<tr>
											<th>Rut:</th>
											<td><?=$this->request->data['Tienda']['rut']; ?></td>
										</tr>
										<tr>
											<th>Dirección:</th>
											<td><?=$this->request->data['Tienda']['direccion']; ?></td>
										</tr>
										<tr>
											<th>Giro comercial:</th>
											<td><?=$this->request->data['Tienda']['giro']; ?></td>
										</tr>
										<tr>
											<th>Fono:</th>
											<td><?=$this->request->data['Tienda']['fono']; ?></td>
										</tr>
									</table>
								</td>
								<td valign="center" align="center" style="vertical-align: middle; padding: 15px;">
									<h1 class="text-center"><b>OC #<?= $this->request->data['OrdenCompra']['id']; ?></b></h1>
								</td>
							</tr>
							<tr>
								<td>
									<table class="table table-bordered">
										<caption style="font-size: 14px; font-weight: 600;">Proveedor</caption>
										<tr>
											<th>Empresa: </th>
											<td><?= $this->request->data['Proveedor']['nombre']; ?></td>
										</tr>
										<tr>
											<th>Rut empresa: </th>
											<td><?= $this->request->data['OrdenCompra']['rut_empresa']; ?></td>
										</tr>
										<tr>
											<th>Razón Social: </th>
											<td><?= $this->request->data['OrdenCompra']['razon_social_empresa']; ?></td>
										</tr>
										<tr>
											<th>Nombre de contacto: </th>
											<td><?= $this->request->data['OrdenCompra']['nombre_contacto_empresa']; ?></td>
										</tr>
									</table>
								</td>
								<td>
									<table class="table table-bordered">
										<caption style="font-size: 14px; font-weight: 600;">Condiciones</caption>
										<tr>
											<th>Fecha: </th>
											<td><?= $this->request->data['OrdenCompra']['fecha']; ?></td>
										</tr>
										<tr>
											<th>Solicitante: </th>
											<td><?= $this->request->data['OrdenCompra']['vendedor']; ?></td>
										</tr>
										<tr>
											<th>Método de pago: </th>
											<td><?=$this->request->data['Moneda']['nombre']; ?></td>
										</tr>
										<tr>
											<th>Descuento aplicado: </th>
											<td><?=$this->request->data['OrdenCompra']['descuento']; ?>%</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table class="table table-bordered">
							<caption style="font-size: 14px; font-weight: 600;">Productos <button class="btn btn-danger pull-right" id="rechazar-todo"><i class="fa fa-times"></i> Rechazar todo</button></caption>
							<thead>
								<th>Código</th>
								<th>Descripción</th>
								<th>Precio unitario</th>
								<th>Cantidad solicitada</th>
								<th>Cantidad a asignar</th>
								<th>Aceptar/Rechazar</th>
							</thead>
							<tboby class="">
							<? foreach ($this->request->data['VentaDetalleProducto'] as $ipp => $ocp) : ?>	

								<tr data-cantidad="<?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad'];?>">
									<td>
										
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.venta_detalle_producto_id', $ipp), array('value' => $ocp['id'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.codigo', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['codigo'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.descripcion', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['descripcion'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.precio_unitario', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['precio_unitario'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.total_neto', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['total_neto'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.descuento_producto', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['descuento_producto'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.tipo_descuento', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['tipo_descuento'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.estado_recibido', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['estado_recibido'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.cantidad_solicitada', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['cantidad'])); ?>
										<?=$ocp['OrdenComprasVentaDetalleProducto']['codigo'];?>
											
									</td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
									<td><?= CakeNumber::currency( ($ocp['OrdenComprasVentaDetalleProducto']['total_neto'] / $ocp['OrdenComprasVentaDetalleProducto']['cantidad']), 'CLP'); ?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=$this->Form->input(sprintf('VentaDetalleProducto.%d.cantidad', $ipp), array('class' => 'form-control is-number not-blank js-cantidad', 'placeholder' => 'Ingrese cantidad disponible', 'min' => 0, 'max' => $ocp['OrdenComprasVentaDetalleProducto']['cantidad'], 'value' => $ocp['OrdenComprasVentaDetalleProducto']['cantidad']))?></td>
									<td>
										<?= $this->Form->select(sprintf('VentaDetalleProducto.%d.estado_proveedor', $ipp), $estados, array('empty' => false, 'default' => 'accept', 'class' => 'form-control js-opcion'))?>
										<div class="hidden js-wrapper-nota">
											<?= $this->Form->label(sprintf('VentaDetalleProducto.%d.nota_proveedor', $ipp), 'Nota', array('class' => 'mt-5')); ?>
											<?= $this->Form->input(sprintf('VentaDetalleProducto.%d.nota_proveedor', $ipp), array('class' => 'form-control js-nota', 'rows' => 2)); ?>
										</div>		
									</td>
								</tr>
								
							<? endforeach; ?>
							
							</tboby>
							<tfoot>
								<tr>
									<td colspan="4"></td>
									<th>Total neto</th>
									<td colspan="2"><?= CakeNumber::currency($this->request->data['OrdenCompra']['total_neto'], 'CLP'); ?></td>
								</tr>
								<tr>
									<td colspan="4"></td>
									<th>Total Descuento</th>
									<td colspan="2"><?= CakeNumber::currency($this->request->data['OrdenCompra']['descuento_monto'], 'CLP'); ?></td>
								</tr>
								<tr>
									<td colspan="4"></td>
									<th>IVA</th>
									<td colspan="2"><?= CakeNumber::currency($this->request->data['OrdenCompra']['iva'], 'CLP'); ?></td>
								</tr>
								<tr>
									<td colspan="4"></td>
									<th>Total</th>
									<td colspan="2"><?= CakeNumber::currency($this->request->data['OrdenCompra']['total'], 'CLP'); ?></td>
								</tr>
							</tfoot>
						</table>
					</div>
					
					<div class="form-group">
						<?= $this->Form->label('nombre_validado_proveedor', 'Nombre de quien valida (requerido)'); ?>
						<?= $this->Form->input('nombre_validado_proveedor', array('class' => 'form-control not-blank', 'placeholder' => 'Ingrese su nombre')); ?>
					</div>
					
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?=$this->Form->button('<i class="fa fa-check"></i> Guardar y notificar cambios', array('type' => 'submit', 'class' => 'btn btn-success', 'escape' => false)); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

<?= $this->Form->end(); ?>
<? endif; ?>
</div>