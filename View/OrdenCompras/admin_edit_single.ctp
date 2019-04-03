<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Editar OC</h2>
</div>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizontal js-validate-oc', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
	<?= $this->Form->input('id');?>
	<?= $this->Form->input('estado', array('value' => 'iniciado', 'type' => 'hidden')); ?>
	
	<div class="page-content-wrap">

		<? if (!empty($this->request->data['OrdenCompra']['comentario_validar'])) : ?>
		
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-comments"></i> Anotación del administrador</h3>
					</div>
					<div class="panel-body">
						<?=$this->Text->autoParagraph($this->request->data['OrdenCompra']['comentario_validar']);?>
					</div>
				</div>
			</div>
		</div>

		<? endif; ?>

		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-info panel-hidden-controls js-oc">
					<div class="panel-heading">
						<h3 class="panel-title text-uppercase"><i class="fa fa-file"></i> OC para <b><?=$this->request->data['Proveedor']['nombre'];?></b></h3>

						<ul class="panel-controls">
                            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        </ul>
					</div>
					<div class="panel-body">
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
												<td><?=$this->Form->input('rut_empresa', array('type' => 'text', 'class' => 'form-control not-blank is-rut') );?></td>
											</tr>
											<tr>
												<td>Razón Social: </td>
												<td><?=$this->Form->input('razon_social_empresa', array('type' => 'text', 'class' => 'form-control not-blank') );?></td>
											</tr>
											<tr>
												<td>Giro: </td>
												<td><?=$this->Form->input('giro_empresa', array('type' => 'text', 'class' => 'form-control not-blank') );?></td>
											</tr>
											<tr>
												<td>Nombre de contacto: </td>
												<td><?=$this->Form->input('nombre_contacto_empresa', array('type' => 'text', 'class' => 'form-control not-blank') );?></td>
											</tr>
											<tr>
												<td>Email: </td>
												<td><?=$this->Form->input('email_contacto_empresa', array('type' => 'text', 'class' => 'form-control not-blank is-email') );?></td>
											</tr>
											<tr>
												<td>Teléfono: </td>
												<td><?=$this->Form->input('fono_contacto_empresa', array('type' => 'text', 'class' => 'form-control') );?></td>
											</tr>
											<tr>
												<td>Dirección comercial: </td>
												<td><?=$this->Form->input('direccion_empresa', array('type' => 'text', 'class' => 'form-control') );?></td>
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
												<td><?=$this->Form->input('fecha', array('type' => 'text', 'class' => 'form-control datepicker not-blank is-date') );?></td>
											</tr>
											<tr>
												<td>Vendedor: </td>
												<td><?=$this->Form->input('vendedor', array('type' => 'text', 'class' => 'form-control not-blank' ) );?></td>
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
									<th>Descuento ($)</th>
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
										<td><?= $this->Form->input('VentaDetalleProducto.999.cantidad', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-cantidad-producto not-blank js-cantidad-producto not-blank is-number')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.precio_unitario', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-precio-producto not-blank')); ?></td>
										<td data-toggle="tooltip" data-placement="top" title="" class="js-descuento-valor"><?= $this->Form->input('VentaDetalleProducto.999.descuento_producto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-descuento-producto not-blank is-number')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.total_neto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-total-producto not-blank is-number')); ?></td>
										<td valign="center">
											<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
										</td>
									</tr>
								<? foreach ($this->request->data['VentaDetalleProducto'] as $ipp => $pp) : ?>	
									<tr>
										<td>
											<?= $this->Form->input(sprintf('VentaDetalleProducto.%d.venta_detalle_producto_id', $ipp), array('value' => $pp['id'], 'type' => 'text', 'class' => 'form-control js-id-producto not-blank')); ?>
										</td>
										<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.codigo', $ipp), array('type' => 'text', 'class' => 'form-control not-blank js-codigo-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['codigo'])); ?></td>

										<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.descripcion', $ipp), array('type' => 'text', 'class' => 'form-control not-blank js-descripcion-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['descripcion'], 'style' =>'width: 200px;')); ?></td>
								
										<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.cantidad', $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-cantidad-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['cantidad'])); ?></td>
										
										<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.precio_unitario', $ipp), array('type' => 'text', 'class' => 'form-control not-blank js-precio-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['precio_unitario'], 'readonly' => 'readonly')); ?></td>
										
										
										<td class="js-descuento-valor">
											<?= $this->Form->input(sprintf('VentaDetalleProducto.%d.descuento_producto', $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-descuento-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['descuento_producto'], 'readonly' => 'readonly', 'data-descuento' => $pp['OrdenComprasVentaDetalleProducto']['descuento_producto'])); ?>
										</td>
										
										<td>
											<?= $this->Form->input(sprintf('VentaDetalleProducto.%d.total_neto', $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['total_neto'], 'readonly' => 'readonly')); ?>
										</td>
										<td valign="center">
											<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
										</td>
									</tr>
			
								<? endforeach; ?>
								
								</tboby>
								<tfoot>
									<tr>
										<td colspan="6"></td>
										<td>Total neto</td>
										<td colspan="2"><?=$this->Form->input('total_neto', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-neto', 'value' => '') );?></td>
									</tr>
									<tr>
										<td colspan="6"></td>
										<td>Total Descuento</td>
										<td colspan="2">
											No aplicado aún.
											<!--<?=$this->Form->input('descuento_monto', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-descuento', 'value' => '') );?>--></td>
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
					</div>
				</div>
			</div>
		</div>


		<div class="row">
			<div class="col-xs-12">
				<div class="pull-right">
					<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Enviar a revisión">
					<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
				</div>
			</div>
		</div>
	</div>

<?= $this->Form->end(); ?>