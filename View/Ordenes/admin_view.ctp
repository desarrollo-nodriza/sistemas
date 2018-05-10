<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Orden de compra #' . $this->request->data['Orden']['id_order']); ?></h2>
	<div class="pull-right">
		<? if ( empty($this->request->data['Dte']) ) : ?>
			<button class="btn btn-warning" onclick="$('html, body').animate({scrollTop:$('#dte').offset().top},1000);">Generar DTE</button>
		<? endif; ?>
	</div>
</div>
<?= $this->Form->create('Dte', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id_order', array('type' => 'hidden', 'value' => $this->request->data['Orden']['id_order'])); ?>
<? if (!empty($this->request->data['Dte'])) : ?>
<?= $this->Form->input('id', array('type' => 'hidden', 'value' => $this->request->data['Dte'][0]['id'])); ?>
	<?  if (empty($this->request->data['Dte'][0]['estado'])) : ?>
	<?= $this->Form->input('estado', array('type' => 'hidden', 'value' => __('no_generado'))); ?>
	<? else : ?>
	<?= $this->Form->input('estado', array('type' => 'hidden', 'value' => $this->request->data['Dte'][0]['estado'])); ?>
	<? endif; ?>
<? else : ?>
	<?= $this->Form->input('estado', array('type' => 'hidden', 'value' => __('no_generado'))); ?>
<? endif; ?>
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
										<td><?=$producto['product_reference']?></td>
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


	<!-- DTE emitido -->

	<? if (!empty($this->request->data['Dte']) && $this->request->data['Dte'][0]['estado'] == 'dte_real_emitido' ) : ?>
	<div class="row">
		<div class="col-xs-12">
			<h2 id="dte"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Información del DTE'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label><?=__('Tipo de documento');?></label>
								<div class="form-control">
									<?=$tipoDocumento[$this->request->data['Dte'][0]['tipo_documento']];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label><?=__('Folio');?></label>
								<div class="form-control">
									#<?=$this->request->data['Dte'][0]['folio'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label><?=__('Rut Receptor');?></label>
								<div class="form-control">
									<?=$this->request->data['Dte'][0]['rut_receptor'];?>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Razón Social Receptor');?></label>
								<div class="form-control">
									<?=$this->request->data['Dte'][0]['razon_social_receptor'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Giro Receptor');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['giro_receptor'];?>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-8">
							<div class="form-group">
								<br>
								<label><?=__('Dirección receptor');?></label>
								<div class="form-control">
									<?=$this->request->data['Dte'][0]['direccion_receptor'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Comuna receptor');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['comuna_receptor']?>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<br>
								<label><?=__('Fecha emisión');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['fecha'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<br>
								<label><?=__('Tasa');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['tasa'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<br>
								<label><?=__('Exento');?></label>
								<div class="center-block">
									<?= $this->request->data['Dte'][0]['exento'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'; ?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<br>
								<label><?=__('Generado en certificación');?></label>
								<div class="center-block">
									<?= $this->request->data['Dte'][0]['certificacion'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Sucursal SII');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['sucursal_sii'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Usuario que emitió el DTE');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['usuario'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Código Seguimiento');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['track_id'];?>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Estado SII');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['revision_estado'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Detalle del estado SII');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte'][0]['revision_detalle'];?>
								</div>
							</div>
						</div>
					</div>
					<? if (!empty($this->request->data['Dte'][0]['pdf'])) : ?>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group">
								<br>
								<label><?=__('Ver documento');?></label>
								<?= $this->Html->link(
									'<i class="fa fa-eye"></i> Ver',
									sprintf('/Dte/%d/%d/%s', $this->request->data['Orden']['id_order'], $this->request->data['Dte'][0]['id'], $this->request->data['Dte'][0]['pdf']),
									array(
										'class' => 'btn btn-info btn-block', 
										'target' => '_blank', 
										'fullbase' => true,
										'escape' => false) 
									); ?>
								</div>
							</div>
						</div>
					<? else : ?>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group">
								<br>
								<label><?=__('No se ha generado el PDF para este documento. Refresque la página o presione el siguiente botón.');?></label>
								<?= $this->Html->link(
									'<i class="fa fa-eye"></i> Generar PDF',
									array('action' => 'view', $this->request->data['Orden']['id_order']),
									array(
										'class' => 'btn btn-info btn-block', 
										'fullbase' => true,
										'escape' => false) 
									); ?>
								</div>
							</div>
						</div>
					<? endif; ?>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h2 id="dte"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Opciones del DTE'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<ul>
								<li><?=$this->Html->link('Anular DTE Completo', array('action' => 'anular', $this->request->param['Dte'][0]['id'], true) );?></li>
								<li><?=$this->Html->link('Anular DTE Manualmente', array('action' => 'anular', $this->request->param['Dte'][0]['id']) );?></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<? endif; ?>

	<!-- Fin Dte Emitido -->

	<!-- Dte temporal emitido o Dte real no emitido -->
	<? if (isset($this->request->data['Dte'][0]['estado'])) : ?>
		<? if ( $this->request->data['Dte'][0]['estado'] == 'dte_temporal_emitido' || $this->request->data['Dte'][0]['estado'] == 'dte_real_no_emitido' ) : ?>
		<div class="row">
			<div class="col-xs-12">
				<h2 id="dte"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Generar DTE Real desde uno temporal.'); ?></h2>
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-xs-12">
								<label><?=__('Seleccione tipo de documento');?></label>
								<? if (!empty($this->request->data['Dte'])) : ?>
									<?=$this->Form->select('tipo_documento', $tipoDocumento, array('class' => 'form-control js-dte-tipo', 'escape' => false, 'empty' => false, 'value' => $this->request->data['Dte'][0]['tipo_documento']));?>
								<? else : ?>
									<?=$this->Form->select('tipo_documento', $tipoDocumento, array('class' => 'form-control js-dte-tipo', 'escape' => false, 'empty' => false));?>
								<? endif; ?>
							</div>
						</div>
						<div class="row js-dte-factura">
							<div class="col-xs-12 col-sm-4">
								<div class="form-group">
									<br>
									<label><?=__('Rut Receptor');?></label>
									<? if (!empty($this->request->data['Dte'])) : ?>
										<?=$this->Form->input('rut_receptor', array('type' => 'text', 'class' => 'rut-contribuyente form-control', 'placeholder' => 'Ingrese rut del receptor', 'value' => $this->request->data['Dte'][0]['rut_receptor']));?>
									<? else : ?>
										<?=$this->Form->input('rut_receptor', array('type' => 'text', 'class' => 'rut-contribuyente form-control', 'placeholder' => 'Ingrese rut del receptor'));?>
									<? endif; ?>
									
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<div class="form-group">
									<br>
									<label><?=__('Razón Social Receptor');?></label>
									<? if (!empty($this->request->data['Dte'])) : ?>
										<?=$this->Form->input('razon_social_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese razón social del receptor', 'value' => $this->request->data['Dte'][0]['razon_social_receptor']));?>
									<? else : ?>
										<?=$this->Form->input('razon_social_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese razón social del receptor'));?>
									<? endif; ?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<div class="form-group">
									<br>
									<label><?=__('Giro Receptor');?></label>
									<? if (!empty($this->request->data['Dte'])) : ?>
										<?=$this->Form->input('giro_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese giro del receptor', 'value' => $this->request->data['Dte'][0]['giro_receptor']));?>
									<? else : ?>
										<?=$this->Form->input('giro_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese giro del receptor'));?>
									<? endif; ?>
								</div>
							</div>
						</div>
						<div class="row js-dte-factura">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<br>
									<label><?=__('Dirección receptor');?></label>
									<? if (!empty($this->request->data['Dte'])) : ?>
										<?=$this->Form->input('direccion_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese dirección del receptor', 'value' => $this->request->data['Dte'][0]['direccion_receptor']));?>
									<? else : ?>
										<?=$this->Form->input('direccion_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese dirección del receptor'));?>
									<? endif; ?>
									
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<br>
									<label><?=__('Comuna receptor');?></label>
									<? if (!empty($this->request->data['Dte'])) : ?>
										<?=$this->Form->select('comuna_receptor', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna', 'value' => $this->request->data['Dte'][0]['comuna_receptor']));?>
									<? else : ?>
										<?=$this->Form->select('comuna_receptor', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna'));?>
									<? endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="pull-right">
							<?=$this->Html->link('<i class="fa fa-file-text" aria-hidden="true"></i> Ver PDF', array('controller' => 'ordenes', 'action' => 'getPdfDteTemporal', $this->request->data['Dte'][0]['receptor'], $this->request->data['Dte'][0]['tipo_documento'], $this->request->data['Dte'][0]['dte_temporal'], $this->request->data['Dte'][0]['emisor']), array('class' => 'btn btn-info', 'escape' => false)); ?>
							<button type="submit" class="btn btn-primary"><i class="fa fa-file-text" aria-hidden="true"></i> Generar DTE</button>
							<?= $this->Html->link('Cancelar y volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div> <!-- end col -->
		</div> <!-- end row -->
		<? endif; ?>
	<? endif; ?>

	<!-- Fin Dte temporal emitido o Dte real no emitido --> 


	<!-- Dte temporal no emitido -->

	<? if (!isset($this->request->data['Dte'][0]['estado']) || empty($this->request->data['Dte'][0]['estado']) || $this->request->data['Dte'][0]['estado'] == 'no_generado' || $this->request->data['Dte'][0]['estado'] == 'dte_temporal_no_emitido' ) : ?>
	<div class="row">
		<div class="col-xs-12">
			<h2 id="dte"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Generar DTE'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<label><?=__('Seleccione tipo de documento');?></label>
							<? if (!empty($this->request->data['Dte'])) : ?>
								<?=$this->Form->select('tipo_documento', $tipoDocumento, array('class' => 'form-control js-dte-tipo', 'escape' => false, 'empty' => false, 'value' => $this->request->data['Dte'][0]['tipo_documento']));?>
							<? else : ?>
								<?=$this->Form->select('tipo_documento', $tipoDocumento, array('class' => 'form-control js-dte-tipo', 'escape' => false, 'empty' => false));?>
							<? endif; ?>
						</div>
					</div>
					<div class="row js-dte-factura">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Rut Receptor');?></label>
								<? if (!empty($this->request->data['Dte'])) : ?>
									<?=$this->Form->input('rut_receptor', array('type' => 'text', 'class' => 'rut-contribuyente form-control', 'placeholder' => 'Ingrese rut del receptor', 'value' => $this->request->data['Dte'][0]['rut_receptor']));?>
								<? else : ?>
									<?=$this->Form->input('rut_receptor', array('type' => 'text', 'class' => 'rut-contribuyente form-control', 'placeholder' => 'Ingrese rut del receptor'));?>
								<? endif; ?>
								
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Razón Social Receptor');?></label>
								<? if (!empty($this->request->data['Dte'])) : ?>
									<?=$this->Form->input('razon_social_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese razón social del receptor', 'value' => $this->request->data['Dte'][0]['razon_social_receptor']));?>
								<? else : ?>
									<?=$this->Form->input('razon_social_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese razón social del receptor'));?>
								<? endif; ?>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Giro Receptor');?></label>
								<? if (!empty($this->request->data['Dte'])) : ?>
									<?=$this->Form->input('giro_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese giro del receptor', 'value' => $this->request->data['Dte'][0]['giro_receptor']));?>
								<? else : ?>
									<?=$this->Form->input('giro_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese giro del receptor'));?>
								<? endif; ?>
							</div>
						</div>
					</div>
					<div class="row js-dte-factura">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Dirección receptor');?></label>
								<? if (!empty($this->request->data['Dte'])) : ?>
									<?=$this->Form->input('direccion_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese dirección del receptor', 'value' => $this->request->data['Dte'][0]['direccion_receptor']));?>
								<? else : ?>
									<?=$this->Form->input('direccion_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese dirección del receptor'));?>
								<? endif; ?>
								
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Comuna receptor');?></label>
								<? if (!empty($this->request->data['Dte'])) : ?>
									<?=$this->Form->select('comuna_receptor', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna', 'value' => $this->request->data['Dte'][0]['comuna_receptor']));?>
								<? else : ?>
									<?=$this->Form->select('comuna_receptor', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna'));?>
								<? endif; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<button type="submit" class="btn btn-primary"><i class="fa fa-file-text" aria-hidden="true"></i> Generar DTE</button>
						<?= $this->Html->link('Cancelar y volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
	<? endif; ?>

	<!-- Fin Dte temporal no emitido -->
</div>
<?= $this->Form->end(); ?>
