<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Orden de compra #' . $this->request->data['Orden']['id_order']); ?></h2>
	<div class="pull-right">
		<button class="btn btn-warning" onclick="$('html, body').animate({scrollTop:$('#dte').offset().top},1000);">Generar DTE</button>
	</div>
</div>
<?= $this->Form->create('Dte', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id_order', array('type' => 'hidden', 'value' => $this->request->data['Orden']['id_order'])); ?>
<?= $this->Form->input('estado', array('type' => 'hidden', 'value' => __('no_generado'))); ?>

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
				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
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

	
	<!-- Dte -->
	<div class="row">
		<div class="col-xs-12">
			<h2 id="dte"><i class="fa fa-truck" aria-hidden="true"></i> <?=__('Generar OT'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-6 form-group">
							<?= $this->Form->label('')?>
						</div>
					</div>
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
	<!-- Fin Dte -->
</div>
<?= $this->Form->end(); ?>
