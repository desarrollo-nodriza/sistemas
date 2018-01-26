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
			<h2 id="dte"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Generar DTE'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<? if (!empty($this->request->data['CustomUserdata'])) : ?>
					<div class="row">
						<div class="col-xs-12">
							<h4><?=__('Documento solicitado por Cliente');?></h4>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>	
										<th><?=__('Campo');?></th>
										<th><?=__('Información');?></th>
									</thead>
									<tbody>
									<? foreach ($this->request->data['CustomUserdata'] as $i => $field) : 
										$f = array(
											'field_type' => $field['CustomField']['field_type'],
											'field_value' => $field['field_value']
										)
									?>
										<tr>
											<td><?=$field['CustomField']['Lang'][0]['CustomFieldLang']['field_name'];?></td>
											<td><?=$this->Html->getFormatedValue($f);?></td>
										</tr>
									<? endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<? endif; ?>
					<div class="row">
						<div class="col-xs-12 col-sm-4 js-dte-factura">
							<div class="form-group">
								<br>
								<label><?=__('Seleccione tipo de documento');?></label>
								<?=$this->Form->select('tipo_documento', $tipoDocumento, array('class' => 'form-control js-dte-tipo', 'escape' => false, 'empty' => false));?>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Rut Receptor');?></label>
								<?=$this->Form->input('rut_receptor', array('type' => 'text', 'class' => 'rut-contribuyente form-control', 'placeholder' => 'Ingrese rut del receptor'));?>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Razón Social Receptor');?></label>
								<?=$this->Form->input('razon_social_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese razón social del receptor'));?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-5">
							<div class="form-group">
								<br>
								<label><?=__('Giro Receptor');?></label>
								<?=$this->Form->input('giro_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese giro del receptor'));?>
							</div>
						</div>
						<div class="col-xs-12 col-sm-5">
							<div class="form-group">
								<br>
								<label><?=__('Dirección receptor');?></label>
								<?=$this->Form->input('direccion_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese dirección del receptor'));?>
							</div>
						</div>
						<div class="col-xs-12 col-sm-2">
							<div class="form-group">
								<br>
								<label><?=__('Comuna receptor');?></label>
								<?=$this->Form->select('comuna_receptor', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna'));?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Medio de pago');?></label>
								<?=$this->Form->select('medio_de_pago', $medioDePago , array('class' => 'form-control', 'escape' => false, 'empty' => 'Sin medio de pago'));?>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Fecha');?></label>
								<?=$this->Form->input('fecha', array('class' => 'form-control datepicker', 'escape' => false, 'value' => date('Y-m-d')));?>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table js-clon-scope table-bordered">
							<thead>
								<tr>
									<th><?= __('Documento referenciado');?></th>
									<th><?= __('Fecha Referencia');?></th>
									<th><?= __('Código ref.');?></th>
									<th><?= __('Razón referencia');?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody class="js-clon-contenedor js-clon-blank">
								<tr class="js-clon-base hidden">
									
									<td>
										<?= $this->Form->select('DteReferencia.999.dte_referencia', $dteEmitidos, array('disabled' => true, 'class' => 'form-control id-referencia', 'empty' => 'Seleccione folio de ref.')); ?>
										<?= $this->Form->input('DteReferencia.999.folio', array('type' => 'hidden', 'diabled' => true, 'class' => 'folio-referencia'))?>
										<?= $this->Form->input('DteReferencia.999.tipo_documento', array('type' => 'hidden', 'diabled' => true, 'class' => 'tipo-referencia'))?>
									</td>
									<td><?= $this->Form->input('DteReferencia.999.fecha', array('type' => 'text', 'disabled' => true, 'class' => 'form-control fecha-referencia')); ?></td>
									<td><?= $this->Form->select('DteReferencia.999.codigo_referencia', $codigoReferencia , array('disabled' => true, 'class' => 'form-control', 'empty' => 'Seleccione código de ref.')); ?></td>
									<td><?= $this->Form->input('DteReferencia.999.razon', array('disabled' => true)); ?></td>
									<td>
										<a href="#" class="btn btn-xs btn-danger js-clon-eliminar"><i class="fa fa-trash"></i> Eliminar</a>
										<!--<a href="#" class="btn btn-xs btn-primary js-clon-clonar"><i class="fa fa-clone"></i> Duplicar</a>-->
									</td>
								</tr>
								<? if ( ! empty($this->request->data['DteReferencia']) ) : ?>
								<? foreach ( $this->request->data['DteReferencia'] as $index => $referencia ) : ?>
								<tr>
									<td>
										<?= $this->Form->select(sprintf('DteReferencia.%d.dte_referencia', $index), $dteEmitidos, array('class' => 'form-control id-referencia', 'empty' => 'Seleccione folio de ref.')); ?>
										<?= $this->Form->input(sprintf('DteReferencia.%d.folio', $index), array('type' => 'hidden', 'class' => 'folio-referencia')); ?>
										<?= $this->Form->input(sprintf('DteReferencia.%d.tipo_documento', $index), array('type' => 'hidden', 'class' => 'tipo-referencia')); ?>
									</td>
									<td><?= $this->Form->input(sprintf('DteReferencia.%d.fecha', $index), array('type' => 'text', 'class' => 'form-control')); ?></td>
									<td><?= $this->Form->select(sprintf('DteReferencia.%d.codigo_referencia', $index),$codigoReferencia , array('class' => 'form-control', 'empty' => 'Seleccione código de ref.')); ?></td>
									<td><?= $this->Form->input(sprintf('DteReferencia.%d.razon', $index), array('class' => 'form-control')); ?></td>
									<td>
										<a href="#" class="btn btn-xs btn-danger js-clon-eliminar"><i class="fa fa-trash"></i> Eliminar</a>
										<!--<a href="#" class="btn btn-xs btn-primary js-clon-clonar"><i class="fa fa-clone"></i> Duplicar</a>-->
									</td>
								</tr>
								<? endforeach; ?>
								<? endif; ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">&nbsp;</td>
									<td><a href="#" class="btn btn-xs btn-success js-clon-agregar"><i class="fa fa-plus"></i> Agregar referencia</a></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="panel-body js-despacho hide">
					<div class="row">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<th><?=__('Tipo traslado');?></th>
									<th><?=__('Dirección');?></th>
									<th><?=__('Comuna');?></th>
									<th><?=__('Transportista');?></th>
									<th><?=__('Patente');?></th>
									<th><?=__('RUT chofer');?></th>
									<th><?=__('Nombre chofer');?></th>
								</thead>
								<tbody>
									<tr>
										<td><?=$this->Form->select('tipo_traslado', $traslados , array('diabled' => true, 'class' => 'form-control', 'escape' => false, 'empty' => false));?></td>
										<td><?=$this->Form->input('direccion_traslado', array('diabled' => true, 'type' => 'text', 'class' => 'form-control', 'placeholder' => 'Dirección de destino'));?></td>
										<td><?=$this->Form->select('comuna_traslado', $comunas , array('diabled' => true, 'class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna'));?></td>
										<td><?=$this->Form->input('rut_transportista', array('diabled' => true, 'type' => 'text', 'class' => 'rut-input form-control', 'placeholder' => 'Rut Transportista'));?></td>
										<td><?=$this->Form->input('patente', array('diabled' => true, 'type' => 'text', 'class' => 'form-control', 'placeholder' => 'Patente'));?></td>
										<td><?=$this->Form->input('rut_chofer', array('diabled' => true, 'type' => 'text', 'class' => 'rut-input form-control', 'placeholder' => 'Rut chofer'));?></td>
										<td><?=$this->Form->input('nombre_chofer', array('diabled' => true, 'type' => 'text', 'class' => 'form-control', 'placeholder' => 'Nombre chofer'));?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<br>
								<label><?=__('Glosa (opcional)');?></label>
								<?=$this->Form->input('glosa', array('class' => 'form-control', 'escape' => false, 'placeholder' => 'Máximo 100 carácteres'));?>
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
	<!-- Fin Dte -->
</div>
<?= $this->Form->end(); ?>
