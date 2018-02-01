<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Orden de compra #' . $this->request->data['Orden']['id_order']); ?></h2>
	<div class="pull-right">
		<button class="btn btn-warning" onclick="$('html, body').animate({scrollTop:$('#ot').offset().top},1000);">Generar OT</button>
	</div>
</div>
<?= $this->Form->create('OrdenTransporte', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id_order', array('type' => 'hidden', 'value' => $this->request->data['Orden']['id_order'])); ?>


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
										<?=$this->Form->input('Transporte', array('type' => 'hidden', 'value' => $this->request->data['Orden']['total_shipping_tax_incl'] ));?>
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
			<h2 id="ot"><i class="fa fa-truck" aria-hidden="true"></i> <?=__('Generar OT'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.transporte'); ?>
							<?= $this->Form->select('OrdenTransporte.transporte', $curriers, array('class' => 'form-control select', 'empty' => false)); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_codigo_producto', 'Tipo de producto'); ?>
							<?= $this->Form->select('OrdenTransporte.e_codigo_producto', $codigoProductosChilexpress, array('class' => 'form-control select', 'empty' => 'Seleccione')); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_codigo_servicio', 'Tipo de servicio'); ?>
							<?= $this->Form->select('OrdenTransporte.e_codigo_servicio', $codigosServicio, array('class' => 'form-control select', 'empty' => 'Seleccione')); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_eoc', 'Tipo de despacho'); ?>
							<?= $this->Form->select('OrdenTransporte.e_eoc', array(
								0 => 'Despacho a domicilio',
								1 => 'Cliente retira en sucursal'), 
								array('class' => 'form-control select', 'empty' => 'Seleccione')); ?>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h3>Datos del remitente</h3>
					<br>
					<div class="row">
						<div class="col-xs-12 col-sm-6 form-group">
							<?= $this->Form->label('OrdenTransporte.e_numero_tcc', 'Identificador de cliente (TCC)'); ?>
							<?= $this->Form->select('OrdenTransporte.e_numero_tcc', $tcc, array('class' => 'form-control select', 'empty' => false)); ?>
						</div>
						<div class="col-xs-12 col-sm-6 form-group">
							<?= $this->Form->label('OrdenTransporte.e_comuna_origen', 'Comuna de origen'); ?>
							<?= $this->Form->select('OrdenTransporte.e_comuna_origen', $comunas, array('class' => 'form-control select', 'empty' => false, 'data-live-search' => 'true')); ?>
						</div>	
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_remitente_nombre', 'Nombre del remitente'); ?>
							<? if (empty($this->request->data['OrdenTransporte']['e_remitente_nombre'])) :  ?>
								<?= $this->Form->input('OrdenTransporte.e_remitente_nombre', array('class' => 'form-control', 'placeholder' => 'Ingrese nombre del remitente', 'value' => 'Toolmania')); ?>
							<? else : ?>
								<?= $this->Form->input('OrdenTransporte.e_remitente_nombre', array('class' => 'form-control', 'placeholder' => 'Ingrese nombre del remitente')); ?>
							<? endif; ?>
						</div>
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_remitente_email', 'Email del remitente'); ?>
							<? if (empty($this->request->data['OrdenTransporte']['e_remitente_email'])) :  ?>
								<?= $this->Form->input('OrdenTransporte.e_remitente_email', array('class' => 'form-control', 'placeholder' => 'Ingrese email del remitente', 'value' => 'ventas@toolmania.cl')); ?>
							<? else : ?>
								<?= $this->Form->input('OrdenTransporte.e_remitente_email', array('class' => 'form-control', 'placeholder' => 'Ingrese email del remitente')); ?>
							<? endif; ?>
						</div>
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_remitente_celular', 'Fono del remitente'); ?>
							<? if (empty($this->request->data['OrdenTransporte']['e_remitente_celular'])) :  ?>
								<?= $this->Form->input('OrdenTransporte.e_remitente_celular', array('class' => 'form-control', 'placeholder' => 'Ingrese fono del remitente', 'value' => '+562 23792180')); ?>
							<? else : ?>
								<?= $this->Form->input('OrdenTransporte.e_remitente_celular', array('class' => 'form-control', 'placeholder' => 'Ingrese fono del remitente')); ?>
							<? endif; ?>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h3>Datos del destinatario</h3>
					<br>
					<div class="row">
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_destinatario_nombre', 'Nombre del destinatario'); ?>
							<?= $this->Form->input('OrdenTransporte.e_destinatario_nombre', array('class' => 'form-control', 'placeholder' => 'Ingrese nombre del destinatario')); ?>
						</div>
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_destinatario_email', 'Email del destinatario'); ?>
							<?= $this->Form->input('OrdenTransporte.e_destinatario_email', array('class' => 'form-control', 'placeholder' => 'Ingrese email del destinatario')); ?>
						</div>
						<div class="col-xs-12 col-sm-4 form-group">
							<?= $this->Form->label('OrdenTransporte.e_destinatario_celular', 'Fono del destinatario'); ?>
							<?= $this->Form->input('OrdenTransporte.e_destinatario_celular', array('class' => 'form-control', 'placeholder' => 'Ingrese fono del destinatario')); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_comuna', 'Comuna de destino'); ?>
							<?= $this->Form->select('OrdenTransporte.e_direccion_comuna', $comunas, array('class' => 'form-control select', 'empty' => false, 'data-live-search' => 'true')); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_calle', 'Calle de destino'); ?>
							<?= $this->Form->input('OrdenTransporte.e_direccion_calle', array('class' => 'form-control', 'placeholder' => 'Ingrese nombre de la calle')); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_numero', 'Número de casa/dpto de destino'); ?>
							<?= $this->Form->input('OrdenTransporte.e_direccion_numero', array('class' => 'form-control', 'placeholder' => 'Ingrese número de casa/dpto')); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_complemento', 'Complemento de la dirección'); ?>
							<?= $this->Form->input('OrdenTransporte.e_direccion_complemento', array('class' => 'form-control', 'placeholder' => 'Ingrese un complemento')); ?>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h3>Datos para devolución</h3>
					<br>
					<div class="row">
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_d_comuna', 'Comuna para devolución'); ?>
							<?= $this->Form->select('OrdenTransporte.e_direccion_d_comuna', $comunas, array('class' => 'form-control select', 'empty' => false, 'data-live-search' => 'true')); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_d_calle', 'Calle para devolución'); ?>
							<?= $this->Form->input('OrdenTransporte.e_direccion_d_calle', array('class' => 'form-control', 'placeholder' => 'Ingrese calle para devolución')); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_d_numero', 'Número de casa/dpto para devolución'); ?>
							<?= $this->Form->input('OrdenTransporte.e_direccion_d_numero', array('class' => 'form-control', 'placeholder' => 'Ingrese número de casa/dpto')); ?>
						</div>
						<div class="col-xs-12 col-sm-3 form-group">
							<?= $this->Form->label('OrdenTransporte.e_direccion_d_complemento', 'Complemento de la dirección'); ?>
							<?= $this->Form->input('OrdenTransporte.e_direccion_d_complemento', array('class' => 'form-control', 'placeholder' => 'Ingrese complemento')); ?>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<h2>Precio del transporte: <?=CakeNumber::currency($this->request->data['Orden']['total_shipping_tax_incl'], 'CLP');?></h2>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<button type="submit" class="btn btn-primary"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Generar OT'); ?></button>
						<?= $this->Html->link('Cancelar y volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
	<!-- Fin OT -->
</div>
<?= $this->Form->end(); ?>
