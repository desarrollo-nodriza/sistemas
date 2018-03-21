<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Orden de compra #' . $this->request->data['Orden']['id_order']); ?></h2>
	<div class="pull-right">
		<button class="btn btn-warning" onclick="$('html, body').animate({scrollTop:$('#ot').offset().top},1000);">Ver información OT</button>
	</div>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= __('Referencia'); ?></th>
								<td><?=$this->request->data['Orden']['reference']?></td>
							</tr>
							<tr>
								<th><?= __('Estado'); ?></th>
								<td><label class="label" style="background-color: <?=$this->request->data['OrdenEstado']['color']?>"><?= h($this->request->data['OrdenEstado']['Lang'][0]['OrdenEstadoIdioma']['name']); ?></label></td>
							</tr>
							<tr>
								<th><?= __('Medio de pago'); ?></th>
								<td><?=$this->request->data['Orden']['payment']?></td>
							</tr>
							<tr>
								<th><?= __('Fecha del pedido'); ?></th>
								<td><?=$this->request->data['Orden']['date_add']?></td>
							</tr>
							<tr>
								<th><?= __('Última actualización'); ?></th>
								<td><?=$this->request->data['Orden']['date_upd']?></td>
							</tr>
							<tr>
								<th><?= __('Cantidad de productos'); ?></th>
								<td><?=count($this->request->data['OrdenDetalle']);?></td>
							</tr>
						</table>
					</div>
				</div>

				<!-- Productos --> 
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<th><?=__('Id'); ?></th>
								<th><?=__('Referencia'); ?></th>
								<th><?=__('Detalle'); ?></th>
								<th><?=__('Precio unitario <small>(Neto)</small>'); ?></th>
								<th><?=__('Precio unitario <small>(Bruto)</small>'); ?></th>
								<th><?=__('Cantidad'); ?></th>
								<th><?=__('Precio total <small>(Neto)</small>'); ?></th>
							</thead>
							<tbody>
								<? foreach ($this->request->data['OrdenDetalle'] as $indice => $producto) : ?>
									<tr>
										<td><?=$producto['product_id']?></td>
										<td>
											<?=$producto['product_reference']?>
											<?=$this->Form->input(sprintf('Detalle.%d.VlrCodigo', $indice), array('type' => 'hidden', 'value' => sprintf('COD-%s', $producto['product_reference'])));?>
										</td>
										<td>
											<?=$this->Form->input(sprintf('Detalle.%d.NmbItem', $indice), array('type' => 'hidden', 'value' => $producto['product_name']));?>
											<?=$producto['product_name']?></td>
										<td>
											<?=CakeNumber::currency($producto['unit_price_tax_excl'], 'CLP'); ?></td>
										<td>
											<?=$this->Form->input(sprintf('Detalle.%d.PrcItem', $indice), array('type' =>'hidden', 'value' => round($producto['unit_price_tax_incl'] / 1.19, 2) ));?>
											<?=CakeNumber::currency($producto['unit_price_tax_incl'], 'CLP'); ?></td>
										<td>
											<?=$this->Form->input(sprintf('Detalle.%d.QtyItem', $indice), array('type' => 'hidden', 'value' => $producto['product_quantity'] ));?>
											<?=$producto['product_quantity']?>
										</td>
										<td><?=CakeNumber::currency($producto['total_price_tax_excl'], 'CLP'); ?></td>
									</tr>
								<? endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="6" class="text-right"><?=__('Productos <small>(Neto)</small>');?></th>
									<td><?=CakeNumber::currency($this->request->data['Orden']['total_products'], 'CLP');?></td>
								</tr>
								<tr>
									<th colspan="6" class="text-right"><?=__('IVA <small>(19%)</small>');?></th>
									<td><?=CakeNumber::currency((round($this->request->data['Orden']['total_products'])*0.19), 'CLP');?></td>
								</tr>
								<? if ($this->request->data['Orden']['total_discounts_tax_excl'] > 0) : ?>
									<tr>
										<th colspan="6" class="text-right"><?=__('Descuento <small>neto</small>');?></th>
										<td>
											<?=$this->Form->input('DscRcgGlobal.ValorDR', array('type' => 'hidden', 'value' => round($this->request->data['Orden']['total_discounts_tax_incl']) ));?>
											<?=CakeNumber::currency($this->request->data['Orden']['total_discounts_tax_excl'], 'CLP');?>
										</td>
									</tr>
								<? endif; ?>
								<tr class="info">	
									<th colspan="6" class="text-right"><?=__('Sub Total');?></th>
									<td><?=CakeNumber::currency($this->request->data['Orden']['total_paid_tax_excl'], 'CLP');?></td>
								</tr>
								<tr>
									<th colspan="6" class="text-right"><?=__('Transporte');?></th>
									<td>
										<?=$this->Form->input('e_monto_cobrar', array('type' => 'hidden', 'value' => $this->request->data['Orden']['total_shipping_tax_incl'] ));?>
										<?=CakeNumber::currency($this->request->data['Orden']['total_shipping_tax_incl'], 'CLP');?></td>
								</tr>
								<tr class="success">	
									<th colspan="6" class="text-right"><?=__('Total <small>(Bruto)</small>');?></th>
									<td><?=CakeNumber::currency($this->request->data['Orden']['total_paid_tax_incl'], 'CLP');?></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->

	
	<!-- Mensajes -->
	
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<h2><i class="fa fa-user" aria-hidden="true"></i> <?=__('Cliente'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Nombre');?></label>
								<div class="form-control"><?=$this->request->data['Cliente']['firstname']; ?></div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Apellido');?></label>
								<div class="form-control"><?=$this->request->data['Cliente']['lastname']; ?></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Email');?></label>
								<div class="form-control"><?=$this->request->data['Cliente']['email']; ?></div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Fecha íngreso');?></label>
								<div class="form-control"><?=$this->request->data['Cliente']['date_add']; ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<h2><i class="fa fa-envelope" aria-hidden="true"></i> <?=__('Mensajes'); ?> <small>(<?=$mensajes = (isset($this->request->data['ClienteHilo'][0]['ClienteMensaje'])) ? count($this->request->data['ClienteHilo'][0]['ClienteMensaje']) : 0 ; ?>)</small></h2>
			<div class="panel panel-default">
				<? if (isset($this->request->data['ClienteHilo'][0]['ClienteMensaje'])) : ?>
				<div class="panel-body">
					<div class="messages messages-img">
					<? foreach ($this->request->data['ClienteHilo'][0]['ClienteMensaje'] as $ind => $mensaje) : ?>
						<? if ( !empty($mensaje['Empleado']) ) : ?>
						<div class="item in">
                            <div class="image">
                                <img src="http://lorempixel.com/200/200/abstract/">
                            </div>
                            <div class="text">
                                <div class="heading">
                                    <a href="#"><?= $mensaje['Empleado']['firstname']; ?> <?= $mensaje['Empleado']['lastname']; ?> <?= ($mensaje['private']) ? '<label class="label label-info">Privado</label>' : '' ; ?></a>
                                    <span class="date"><?= $mensaje['date_add']; ?></span>
                                </div>
                                <?= $mensaje['message']; ?>
                            </div>
                        </div>
                    	<? else : ?>
                    	<div class="item">
                            <div class="image">
                                <img src="http://lorempixel.com/200/200/abstract/">
                            </div>
                            <div class="text">
                                <div class="heading">
                                    <a href="#"><?=$this->request->data['Cliente']['firstname']; ?> <?=$this->request->data['Cliente']['lastname']; ?> <?= ($mensaje['private']) ? '<label class="label label-info">Privado</label>' : '' ; ?></a>
                                    <span class="date"><?= $mensaje['date_add']; ?></span>
                                </div>
                                <?= $mensaje['message']; ?>
                            </div>
                        </div>
						<? endif; ?>
					<? endforeach; ?>
                    </div>
				</div>
				<? else : ?>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<br>
							<label><?=__('No registra mensajes');?></label>
						</div>
					</div>
				</div>
				<? endif; ?>
			</div>
		</div>
	</div>
	
	<!-- Fin Mensajes -->

	
	<!-- OT -->
	<div class="row">
		<div class="col-xs-12">
			<h2 id="ot"><i class="fa fa-truck" aria-hidden="true"></i> <?=__('Información de la OT'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_eoc', 'Transporte'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['transporte']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_eoc', 'Tipo de producto'); ?>
							<div class="form-control"><?= $codigoProductosChilexpress[$this->request->data['OrdenTransporte'][0]['e_codigo_producto']]; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_eoc', 'Tipo de servicio'); ?>
							<div class="form-control"><?= $codigosServicio[$this->request->data['OrdenTransporte'][0]['e_codigo_servicio']]; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_eoc', 'Tipo de despacho'); ?>
							<div class="form-control"><?= $codigoEoc[$this->request->data['OrdenTransporte'][0]['e_eoc']]; ?></div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h3>Datos del remitente</h3>
					<br>
					<div class="row">
						<div class="col-xs-12 col-sm-6 form-group">
							<?= $this->Form->label('OrdenTransporte.e_numero_tcc', 'Identificador de cliente (TCC)'); ?>
							<div class="form-control"><?= $tcc[$this->request->data['OrdenTransporte'][0]['e_numero_tcc']]; ?></div>
						</div>
						<div class="col-xs-12 col-sm-6 form-group">
							<?= $this->Form->label('OrdenTransporte.e_comuna_origen', 'Comuna de origen'); ?>
							<div class="form-control"><?= $comunas[$this->request->data['OrdenTransporte'][0]['e_comuna_origen']]; ?></div>
						</div>	
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_remitente_nombre', 'Nombre del remitente'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_remitente_nombre']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_remitente_email', 'Email del remitente'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_remitente_email']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_remitente_celular', 'Fono del remitente'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_remitente_celular']; ?></div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h3>Datos del destinatario</h3>
					<br>
					<div class="row">
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_destinatario_nombre', 'Nombre del destinatario'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_destinatario_nombre']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_destinatario_email', 'Email del destinatario'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_destinatario_email']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_destinatario_celular', 'Fono del destinatario'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_destinatario_celular']; ?></div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_comuna', 'Comuna de destino'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_direccion_comuna']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_calle', 'Calle de destino'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_direccion_calle']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_numero', 'Número de casa/dpto de destino'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_direccion_numero']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_complemento', 'Complemento de la dirección'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_direccion_complemento']; ?></div>
						</div>
					</div>
				</div>
				<div class="panel-body" id="containerButtons">
				</div>
				<div class="panel-body" id="containerResponse">
				</div>
				<div class="panel-body">
					<h3>Datos para devolución</h3>
					<br>
					<div class="row">
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_d_comuna', 'Comuna para devolución'); ?>
							<div class="form-control"><?= $comunas[$this->request->data['OrdenTransporte'][0]['e_direccion_d_comuna']]; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_d_calle', 'Calle para devolución'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_direccion_d_calle']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_d_numero', 'Número de casa/dpto para devolución'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_direccion_d_numero']; ?></div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_d_complemento', 'Complemento de la dirección'); ?>
							<div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_direccion_d_complemento']; ?></div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h3>Dimensiones del paquete</h3>
					<br>
					<div class="row">
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_peso', 'Peso del paquete'); ?>
							<div class="input-group">
                                <div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_peso']; ?></div>
                                <span class="input-group-addon">kg</span>
                            </div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_largo', 'Largo del paquete'); ?>
							<div class="input-group">
                                <div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_largo']; ?></div>
                                <span class="input-group-addon">cm</span>
                            </div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_ancho', 'Ancho del paquete'); ?>
							<div class="input-group">
                                <div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_ancho']; ?></div>
                                <span class="input-group-addon">cm</span>
                            </div>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_alto', 'Alto del paquete'); ?>
							<div class="input-group">
                                <div class="form-control"><?=$this->request->data['OrdenTransporte'][0]['e_alto']; ?></div>
                                <span class="input-group-addon">cm</span>
                            </div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h2>Precio del transporte: <?=CakeNumber::currency($this->request->data['Orden']['total_shipping_tax_incl'], 'CLP');?></h2>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Html->link('Volver a la orden de compra', array('action' => 'orden', $this->request->data['Orden']['id_order']), array('class' => 'btn btn-success')); ?>
						<?= $this->Html->link('Volver al inicio', array('action' => 'index'), array('class' => 'btn btn-primary')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
	<!-- Fin OT -->
</div>
