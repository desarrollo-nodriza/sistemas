<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Revisar OC generadas</h2>
</div>

<? if (!empty($productosIncompletos)) : ?>
<?= $this->Form->create('Form', array('url' => array('controller' => 'VentaDetalleProductos', 'action' => 'guardar_proveedores_producto' ), 'class' => 'form-horizontal js-validate-producto', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>

	<div class="page-content-wrap">
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-industry"></i> Relacionar proveedor</h3>
					</div>
					<div class="panel-body">
						<? foreach ($productosIncompletos as $ip => $p) : ?>
						<div class="row">
							<?= $this->Form->input(sprintf('%d.VentaDetalleProducto.id', $ip), array('value' => $p['VentaDetalleProducto']['id'], 'type' => 'hidden')); ?>

							<div class="form-group col-xs-12 col-md-4">
								<?=$p['VentaDetalleProducto']['nombre'];?>
							</div>
							<div class="form-group col-xs-12 col-md-4">
							<? if (!empty($p['Proveedor'])) : ?>
								<?= $this->Form->select(sprintf('%d.Proveedor.Proveedor', $ip), $proveedoresLista, array(
									'class' => 'form-control', 
									'multiple' => false,
									'empty' => 'Seleccione Proveedor',
									'default' => $p['Proveedor'][0]['id'])
									); ?>
							<? else : ?>
								<?= $this->Form->select(sprintf('%d.Proveedor.Proveedor', $ip), $proveedoresLista, array(
									'class' => 'form-control', 
									'multiple' => false,
									'empty' => 'Seleccione Proveedor')
									); ?>
							<? endif; ?>
							</div>
							<div class="form-group col-xs-12 col-md-4">
								<?= $this->Form->select(sprintf('%d.VentaDetalleProducto.marca_id', $ip), $marcas, array(
									'class' => 'form-control',
									'empty' => 'Seleccione Marca',
									'default' => $p['VentaDetalleProducto']['marca_id'])
									); ?>
							</div>
						</div>
						<? endforeach; ?>
					</div>
					<div class="panel-footer">
						<div class="pull-right">
							<button type="submit" class="btn btn-primary">Guardar relación y continuar</button>
							<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?= $this->Form->end(); ?>
<? else : ?>
	<?= $this->Form->create('OrdenesCompra', array('class' => 'form-horizontal js-validate-oc', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							

		<? if (!empty($proveedores) && !empty(Hash::extract($proveedores, '{n}.VentaDetalleProducto'))) : ?>
		<? foreach ($proveedores as $ip => $p) : ?>
			
			<?= $this->Form->input(sprintf('%d.OrdenCompra.administrador_id', $ip), array('value' => $this->Session->read('Auth.Administrador.id'), 'type' => 'hidden')); ?>
			<?= $this->Form->input(sprintf('%d.OrdenCompra.tienda_id', $ip), array('value' => $this->Session->read('Tienda.id'), 'type' => 'hidden')); ?>
			<?= $this->Form->input(sprintf('%d.OrdenCompra.proveedor_id', $ip), array('value' => $p['Proveedor']['id'], 'type' => 'hidden')); ?>
			<?= $this->Form->input(sprintf('%d.OrdenCompra.estado', $ip), array('value' => 'validacion_comercial', 'type' => 'hidden')); ?>


			<div class="page-content-wrap">
				<div class="row">
					<div class="col-xs-12">
						<div class="panel panel-info panel-hidden-controls js-oc">
							<div class="panel-heading">
								<h3 class="panel-title text-uppercase"><i class="fa fa-file"></i> OC para <b><?=$p['Proveedor']['nombre'];?></b></h3>

								<ul class="panel-controls">
		                            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
		                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
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
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.rut_empresa', $ip), array('type' => 'text', 'class' => 'form-control not-blank is-rut', 'value' => $p['Proveedor']['rut_empresa']) );?></td>
													</tr>
													<tr>
														<td>Razón Social: </td>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.razon_social_empresa', $ip), array('type' => 'text', 'class' => 'form-control not-blank', 'value' => $p['Proveedor']['nombre']) );?></td>
													</tr>
													<tr>
														<td>Giro: </td>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.giro_empresa', $ip), array('type' => 'text', 'class' => 'form-control not-blank', 'value' => $p['Proveedor']['giro']) );?></td>
													</tr>
													<tr>
														<td>Nombre de contacto: </td>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.nombre_contacto_empresa', $ip), array('type' => 'text', 'class' => 'form-control not-blank', 'value' => $p['Proveedor']['nombre_encargado']) );?></td>
													</tr>
													<tr>
														<td>Email: </td>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.email_contacto_empresa', $ip), array('type' => 'text', 'class' => 'form-control not-blank is-email', 'value' => $p['Proveedor']['email_contacto']) );?></td>
													</tr>
													<tr>
														<td>Teléfono: </td>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.fono_contacto_empresa', $ip), array('type' => 'text', 'class' => 'form-control', 'value' => $p['Proveedor']['fono_contacto']) );?></td>
													</tr>
													<tr>
														<td>Dirección comercial: </td>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.direccion_empresa', $ip), array('type' => 'text', 'class' => 'form-control', 'value' => $p['Proveedor']['direccion']) );?></td>
													</tr>
												</table>
											</td>
											<td>
												<table class="table table-bordered">
													<tr>
														<td colspan="2"><b>Información adicional</b></td>
													</tr>
													<tr>
														<td>Fecha: </td>
														<? if (!empty($p['OrdenCompra'])) : ?>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.fecha', $ip), array('type' => 'text', 'class' => 'form-control datepicker not-blank is-date', 'value' => $p['OrdenCompra'][0]['fecha']) );?></td>
														<? else : ?>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.fecha', $ip), array('type' => 'text', 'class' => 'form-control datepicker not-blank is-date', 'value' => date('Y-m-d')) );?></td>
														<? endif; ?>
													</tr>
													<!--<tr>
														<td>Forma de pago: </td>
														<? if (!empty($p['OrdenCompra'])) : ?>
														<td><?=$this->Form->select(sprintf('%d.OrdenCompra.moneda_id', $ip), $monedas, array('empty' => 'Seleccione', 'class' => 'form-control not-blank', 'default' => $p['OrdenCompra'][0]['moneda_id']) );?></td>
														<? else : ?>
														<td><?=$this->Form->select(sprintf('%d.OrdenCompra.moneda_id', $ip), $monedas, array('empty' => 'Seleccione', 'class' => 'form-control not-blank') );?></td>
														<? endif; ?>
													</tr>-->
													<tr>
														<td>Vendedor: </td>
														<td><?=$this->Form->input(sprintf('%d.OrdenCompra.vendedor', $ip), array('type' => 'text', 'class' => 'form-control not-blank', 'value' => $this->Session->read('Auth.Administrador.nombre') ) );?></td>
													</tr>
													<tr>
														<td>Método de entrega</td>
														<td>
														<?=$this->Form->select(sprintf('%d.OrdenCompra.tipo_entrega', $ip), array(
															'retiro' => 'Retiro',
															'despacho' => 'Despacho'
														), array('empty' => 'Seleccione', 'class' => 'form-control not-blank js-tipo-entrega') );?>
														</td>
													</tr>
													<tr class="hidden">
														<td>Encargado del retiro</td>
														<td>
															<?=$this->Form->input(sprintf('%d.OrdenCompra.receptor_informado', $ip), array('type' => 'text', 'class' => 'form-control js-receptor-informado', 'placeholder' => 'Ingrese nombre del receptor'))?>
														</td>
													</tr>
													<tr class="hidden">
														<td>Detalle del retiro (opcional)</td>
														<td>
															<?=$this->Form->input(sprintf('%d.OrdenCompra.informacion_entrega', $ip), array('class' => 'form-control js-informacion-entrega', 'placeholder' => 'Agregue información adicional de la entrega')); ?>
														</td>
													</tr>
													<!--<tr>
														<td>Descuento: </td>
														<td>
															<div class="form-group form-inline">
																<?=$this->Form->select(sprintf('%d.OrdenCompra.tipo_descuento', $ip), $tipoDescuento, array('empty' => false, 'class' => 'form-control not-blank js-tipo-descuento-proveedor') );?>
																<?=$this->Form->input(sprintf('%d.OrdenCompra.descuento', $ip), array('type' => 'text', 'class' => 'form-control not-blank is-number js-descuento-proveedor' ) );?>
															</div>
			                                            </td>
													</tr>-->
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
													<?= $this->Form->input($ip.'.VentaDetalleProducto.999.venta_detalle_producto_id', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-id-producto not-blank')); ?>
												</td>
												<td><?= $this->Form->input($ip.'.VentaDetalleProducto.999.codigo', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank js-codigo-producto')); ?></td>
												<td><?= $this->Form->input($ip.'.VentaDetalleProducto.999.descripcion', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-descripcion-producto js-buscar-producto not-blank', 'style' =>'width: 200px;')); ?></td>
												<td><?= $this->Form->input($ip.'.VentaDetalleProducto.999.cantidad', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-cantidad-producto not-blank js-cantidad-producto not-blank is-number')); ?></td>
												<td><?= $this->Form->input($ip.'.VentaDetalleProducto.999.precio_unitario', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-precio-producto not-blank is-number')); ?></td>
												<td data-toggle="tooltip" data-placement="top" title="" class="js-descuento-valor"><?= $this->Form->input($ip.'.VentaDetalleProducto.999.descuento_producto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-descuento-producto not-blank is-number')); ?></td>
												<td><?= $this->Form->input($ip.'.VentaDetalleProducto.999.total_neto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-total-producto not-blank is-number')); ?></td>
												<td valign="center">
													<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
												</td>
											</tr>
										<? foreach ($p['VentaDetalleProducto'] as $ipp => $pp) : ?>	
											<? if (count(Hash::extract($productosSolicitar, '{n}[id=' . $pp['id'] . '].id')) > 0) : ?>

											<? foreach ($venta_detalles as $iv => $venta) {
												if (count(Hash::extract($venta, 'VentaDetalle[venta_detalle_producto_id='.$pp['id'].'].venta_detalle_producto_id'))) {
													echo $this->Form->hidden(sprintf('%d.Venta.%d.venta_id', $ip, $iv), array('value' => $venta['VentaDetalle']['venta_id']));	
												}
											} ?>

											<tr>
												<td>
													<?= $this->Form->input(sprintf('%d.VentaDetalleProducto.%d.venta_detalle_producto_id', $ip, $ipp), array('value' => $pp['id'], 'type' => 'text', 'class' => 'form-control js-id-producto not-blank')); ?>
												</td>
												<td><?= $this->Form->input(sprintf('%d.VentaDetalleProducto.%d.codigo', $ip, $ipp), array('type' => 'text', 'class' => 'form-control not-blank js-codigo-producto', 'value' => $pp['codigo_proveedor'])); ?></td>
												<td><?= $this->Form->input(sprintf('%d.VentaDetalleProducto.%d.descripcion', $ip, $ipp), array('type' => 'text', 'class' => 'form-control not-blank js-descripcion-producto', 'value' => $pp['nombre'], 'style' =>'width: 200px;')); ?></td>
												<? if (!empty(Hash::extract($productosSolicitar, '{n}[id=' . $pp['id'] . '].cantidad_oc'))) : ?>
												<td><?= $this->Form->input(sprintf('%d.VentaDetalleProducto.%d.cantidad', $ip, $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-cantidad-producto', 'min' => $pp['cant_minima_compra'], 'value' => Hash::extract($productosSolicitar, '{n}[id=' . $pp['id'] . '].cantidad_oc')[0])); ?></td>
												<? else : ?>
												<td><?= $this->Form->input(sprintf('%d.VentaDetalleProducto.%d.cantidad', $ip, $ipp), array('type' => 'text', 'min' => $pp['cant_minima_compra'], 'class' => 'form-control not-blank is-number js-cantidad-producto')); ?></td>
												<? endif; ?>
												<td><?= $this->Form->input(sprintf('%d.VentaDetalleProducto.%d.precio_unitario', $ip, $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-precio-producto', 'value' => $pp['precio_costo'], 'readonly' => 'readonly')); ?></td>
												
												
												<td data-toggle="tooltip" data-placement="top" title="<?= (!empty($pp['nombre_descuento'])) ? $pp['nombre_descuento'] : '' ; ?>" class="js-descuento-valor">
													<?= $this->Form->input(sprintf('%d.VentaDetalleProducto.%d.descuento_producto', $ip, $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-descuento-producto', 'value' => $pp['total_descuento'], 'readonly' => 'readonly', 'data-descuento' => $pp['total_descuento'])); ?>
												</td>
												
												<td>
													<?= $this->Form->input(sprintf('%d.VentaDetalleProducto.%d.total_neto', $ip, $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-producto', 'value' => ($pp['precio_costo'] - $pp['total_descuento'])*Hash::extract($productosSolicitar, '{n}[id=' . $pp['id'] . '].cantidad_oc')[0], 'readonly' => 'readonly')); ?>
												</td>
												<td valign="center">
													<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
												</td>
											</tr>
											<? endif; ?>
										<? endforeach; ?>
										
										</tboby>
										<tfoot>
											<tr>
												<td colspan="6"></td>
												<td>Total neto</td>
												<td colspan="2"><?=$this->Form->input(sprintf('%d.OrdenCompra.total_neto', $ip), array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-neto', 'value' => '') );?></td>
											</tr>
											<tr>
												<td colspan="6"></td>
												<td>Total Descuento</td>
												<td colspan="2">
													No aplicado aún.
													<!--<?=$this->Form->input(sprintf('%d.OrdenCompra.descuento_monto', $ip), array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-descuento', 'value' => '') );?>--></td>
											</tr>
											<tr>
												<td colspan="6"></td>
												<td>IVA</td>
												<td colspan="2"><?=$this->Form->input(sprintf('%d.OrdenCompra.iva', $ip), array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-iva', 'value' => '') );?></td>
											</tr>
											<tr>
												<td colspan="6"></td>
												<td>Total</td>
												<td colspan="2"><?=$this->Form->input(sprintf('%d.OrdenCompra.total', $ip), array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-oc', 'value' => '') );?></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							<div class="panel-body">
								<div class="form-group">
									<?= $this->Form->label(sprintf('%d.OrdenCompra.mensaje_final', $ip), 'Mensaje para el proveedor');?>
									<?= $this->Form->input(sprintf('%d.OrdenCompra.mensaje_final', $ip), array('value' => sprintf('Estimado/a %s se envía adjunto la orden de compra y su comprobante de pago.', $p['Proveedor']['nombre_encargado']))); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<? endforeach; ?>

				<? else : ?>
					<div class="row">
						<div class="col-xs-12">
							<p>No hay proveedores disponibles.</p>
						</div>
					</div>
				<? endif; ?>

				<div class="row">
					<div class="col-xs-12">
						<div class="pull-right">
							<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Enviar a revisión">
							<?= $this->Html->link('Cancelar', array('action' => 'index', 'sta' => 'creada'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>

	<?= $this->Form->end(); ?>
<? endif; ?>