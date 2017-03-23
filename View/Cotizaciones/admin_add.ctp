<div class="page-title">
	<h2><span class="fa fa-lightbulb-o"></span> Nueva Cotización</h2>
</div>
<?= $this->Form->create('Cotizacion', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Importante</h3>
			<p>Para crear la cotización, indique un nombre descriptivo con el cual pueda identificar la cotización en el futuro.</p>
			<p>La información cargada en la cotización viene desde el prospeto recién creado. Sí desea hacer una modificación de textos, precios, etc. considere que los calculos de IVA y descuentos deberá hacerlos manualmente.</p>
			<p>La información del cliente y los productos no modifica la información de la tienda. Es solo para generar la cotización.</p>
				<br>
			<p><b>La cotización se enviará al email ingresado en la sección "Datos de Cliente".</b></p>
		</div>
		<div class="col-xs-12 form-inline">
			<div class="form-group nombre-cotizacion">
				<?= $this->Form->label('nombre', 'Nombre'); ?><?= $this->Form->input('nombre'); ?>
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
												<td>Email:</td><td><?=$this->Form->input('email_cliente', array('value' => $cliente['Cliente']['email']));?></td>
											</tr>
											<tr>
												<td>Nombre:</td><td><?=$this->Form->input('nombre_cliente', array('value' => $cliente['Cliente']['firstname'] . ' ' . $cliente['Cliente']['lastname']));?></td>
											</tr>
											<tr>	
												<td>Teléfono:</td><td><?=$this->Form->input('fono_cliente', array('value' => $cliente['Clientedireccion'][0]['phone'], 'class' => 'mascara_fono form-control'));?></td>
											</tr>
											<tr>	
												<td>Dirección:</td><td><?=$this->Form->input('direccion_cliente', array('value' => $cliente['Clientedireccion'][0]['address1']));?></td>
											</tr>
											<tr>	
												<td>Asunto:</td><td><?=$this->Form->input('asunto_cliente', array('value' => $prospecto['Prospecto']['descripcion']));?></td>
											<? if ($cliente['Cliente']['ape'] == 2 && ! empty($cliente['Cliente']['siret'])) : ?>
												<tr>
													<td>Rut Empresa: </td><td><?=$this->Form->input('rut_empresa_cliente', array('value' => $cliente['Cliente']['siret'], 'class' => 'form-control rut'));?></td>
												</tr>
											<? endif; ?>
											<? if ($cliente['Cliente']['ape'] == 2 && ! empty($cliente['Cliente']['company'])) : ?>
												<tr>
													<td>Empresa: </td><td><?=$this->Form->input('nombre_empresa_cliente', array('value' => $cliente['Cliente']['company']));?></td>
												</tr>
											<? endif; ?>
											</tr>
										</table>
									</td>
									<td>
										<table class="table">
											<tr>
												<td>Fecha:</td><td><?=$this->Form->input('fecha_cotizacion', array('value' => date('d/m/Y')));?></td>
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
									<th>Neto</th>
									<th>Cantidad</th>
									<th>Descuento</th>
									<th>Neto con desc.</th>
									<th>Total</th>
								</thead>
								<tbody>
									<? foreach ($productos as $inx => $producto) : ?>
										<tr>
											<td>
												<?= $this->Form->input(sprintf('Productotienda.%d.id_product', $inx), array('value' => $producto['Productotienda']['id_product'], 'type' => 'hidden')); ?>
												<?=$producto['Productotienda']['reference'];?>
											</td>
											<td><?=$producto['Lang'][0]['ProductotiendaIdioma']['name'];?></td>
											<td><?=$producto['Productotienda']['precio_neto'];?></td>
											<td><?= $this->Form->input(sprintf('Productotienda.%d.cantidad', $inx), array('value' => $producto['Productotienda']['cantidad'])); ?></td>
											<td><?=$descuento_producto = ( ! empty($producto['Productotienda']['descuento']) ) ? $this->Form->input(sprintf('Productotienda.%d.descuento', $inx), array('value' => $producto['Productotienda']['descuento'] . '%')) : $this->Form->input(sprintf('Productotienda.%d.descuento', $inx), array('value' => 0)); ?></td>
											<td><?= $this->Form->input(sprintf('Productotienda.%d.precio_neto', $inx), array('value' => $producto['Productotienda']['precio_neto_desc'])); ?></td>
											<td><?= $this->Form->input(sprintf('Productotienda.%d.total_neto', $inx), array('value' => $producto['Productotienda']['total_neto_desc'])); ?></td>
										</tr>								
									<? endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6"><b>Total Neto</b></td><td><?=$this->Form->input('total_neto', array('value' => $prospecto['total_productos_neto_desc']));?></td>
									</tr>
									<tr>
										<td colspan="6"><b>Descuento <?=$descuento = (!empty($prospecto['Prospecto']['descuento'])) ?  '(' . $prospecto['Prospecto']['descuento'] . '%)' :  '' ; ?> </b></td><td><?=$this->Form->input('descuento', array('value' => $prospecto['total_descuento']));?></td>
									</tr>
									<tr>
										<td colspan="6"><b>IVA</b></td><td><?=$this->Form->input('iva', array('value' => $prospecto['iva']));?></td>
									</tr>
									<tr>
										<td colspan="6"><b>Total</b></td><td><?=$this->Form->input('total_bruto', array('value' => $prospecto['total_bruto']));?></td>
									</tr>
								</tfoot>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<? if ( ! empty($prospecto) ) : ?>
							<?= $this->Form->input('id_customer', array('value' => $prospecto['Prospecto']['id_customer'], 'type' => 'hidden')); ?>
						<? endif; ?>
						<? if ( ! empty($prospecto) ) : ?>
							<?= $this->Form->input('id_address', array('value' => $prospecto['Prospecto']['id_address'], 'type' => 'hidden')); ?>
						<? endif; ?>
						<? if ( ! empty($prospecto) ) : ?>
							<?= $this->Form->input('prospecto_id', array('value' => $prospecto['Prospecto']['id'], 'type' => 'hidden')); ?>
						<? endif; ?>
						<?= $this->Form->input('tienda_id', array('value' => $this->Session->read('Tienda.id'), 'type' => 'hidden')); ?>
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
<?= $this->Form->end(); ?>
