<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('DTE #' . $this->request->data['Dte']['id']); ?> <small><?=$this->Html->dteEstado($this->request->data['Dte']['estado'])?></small></h2>
	<div class="pull-right">

		<? if (!empty($this->request->data['Dte']['pdf'])) : ?>
			
			<?= $this->Html->link(
				'<i class="fa fa-eye"></i> Ver PDF DTE',
				sprintf('/Dte/%d/%d/%s', $this->request->data['Venta']['id'], $this->request->data['Dte']['id'], $this->request->data['Dte']['pdf']),
				array(
					'class' => 'btn btn-success', 
					'target' => '_blank', 
					'fullbase' => true,
					'escape' => false) 
				); ?>

		<? else : ?>
			
			<?= $this->Html->link(
				'<i class="fa fa-eye"></i> Generar PDF DTE',
				array('action' => 'editar', $this->request->data['Dte']['id']),
				array(
					'class' => 'btn btn-info', 
					'fullbase' => true,
					'escape' => false) 
				); ?>
					
		<? endif; ?>
		
		<button class="btn btn-warning" onclick="$('html, body').animate({scrollTop:$('#dte').offset().top},1000);">Información del DTE</button>
		<? if ($this->request->data['Dte']['estado'] == 'dte_temporal_emitido' || $this->request->data['Dte']['estado'] == 'dte_real_no_emitido') : ?>
		<?= $this->Form->postLink('Eliminar Dte temporal', array('action' => 'eliminarDteTemporal', $this->request->data['Dte']['id'], $this->request->data['Dte']['venta_id']), array('class' => 'btn btn-danger', 'rel' => 'tooltip', 'title' => 'Eliminar este DTE', 'escape' => false)); ?>
		<? endif; ?>
		<? if ($this->request->data['Dte']['estado'] == 'no_generado') : ?>
		<?= $this->Form->postLink('Eliminar Dte', array('action' => 'eliminarDteTemporal', $this->request->data['Dte']['id'], $this->request->data['Dte']['venta_id']), array('class' => 'btn btn-danger', 'rel' => 'tooltip', 'title' => 'Eliminar este DTE', 'escape' => false)); ?>
		<? endif; ?>
	</div>
</div>
<?= $this->Form->create('Dte', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('venta_id', array('type' => 'hidden', 'value' => $this->request->data['Dte']['venta_id'])); ?>
<?= $this->Form->input('id'); ?>
<?  if (empty($this->request->data['Dte']['estado'])) : ?>
<?= $this->Form->input('estado', array('type' => 'hidden', 'value' => __('no_generado'))); ?>
<? else : ?>
<?= $this->Form->input('estado', array('type' => 'hidden', 'value' => $this->request->data['Dte']['estado'])); ?>
<? endif; ?>

<div class="page-content-wrap">
	<? if (!empty($estadoSii)) : ?>
	<div class="col-xs-12">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-info" aria-hidden="true"></i> <?=__('Estado de este DTE en el SII'); ?></h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr>
							<td><b><?=__('Estado del DTE');?></b></td>
							<td><?=$estadoSii['estado'];?></td>
						</tr>
						<tr>
							<td><b><?=__('Detalle del DTE');?></b></td>
							<td><?=$estadoSii['detalle'];?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<? endif; ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th>Referencia</th>
								<td><?= $venta['Venta']['referencia']; ?></td>
							</tr>
							<tr>
								<th>ID Externo</th>
								<td><?= $venta['Venta']['id_externo']; ?></td>
							</tr>
							<tr>
								<th>Estado</th>
								<td><span class="btn btn-xs btn-<?= $venta['Venta']['VentaEstado']['VentaEstadoCategoria']['estilo']; ?>"><?= $venta['Venta']['VentaEstado']['VentaEstadoCategoria']['nombre']; ?></span></td>
							</tr>
							<tr>
								<th>Fecha</th>
								<td><?= date_format(date_create($venta['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?></td>
							</tr>
							<tr>
								<th>Medio de Pago</th>
								<td><?= $venta['Venta']['MedioPago']['nombre']; ?></td>
							</tr>
							<tr>
								<th>Tienda</th>
								<td><?= $venta['Venta']['Tienda']['nombre']; ?></td>
							</tr>
							<tr>
								<th>Marketplace</th>
								<td><?php if (!empty($venta['Venta']['marketplace_id'])) {echo $venta['Venta']['Marketplace']['nombre'];} ?>&nbsp;</td>
							</tr>
							<tr>
								<th>Atendida</th>
								<td><?= ($venta['Venta']['atendida'] ? "<span class='btn btn-xs btn-success'>Sí</span>" : "<span class='btn btn-xs btn-danger'>No</span>"); ?></td>
							</tr>
						</table>
					</div>
				</div>

				<!-- Productos --> 
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<th>ID Producto</th>
								<th>Nombre</th>
								<th>Precio Unitario <small>(Neto)</small></th>
								<th>Precio Unitario <small>(Bruto)</small></th>
								<th>Cantidad</th>
								<th>Subtotal</th>
							</thead>
							<tbody>
								<?php $TotalProductos = 0; foreach ($venta['Venta']['VentaDetalle'] as $indice => $detalle) : $TotalProductos = $TotalProductos + ($detalle['precio'] * $detalle['cantidad']); ?>
									<tr>
										<td>
											<?= $detalle['VentaDetalleProducto']['id']; ?>
											<?= $this->Form->input(sprintf('DteDetalle.%d.VlrCodigo', $indice), array('type' => 'hidden', 'value' => sprintf('COD-%s', $detalle['VentaDetalleProducto']['id']))) ;?>
											<?= $this->Form->input(sprintf('Detalle.%d.VlrCodigo', $indice), array('type' => 'hidden', 'value' => sprintf('COD-%s', $detalle['VentaDetalleProducto']['id']))) ;?>
										</td>
										<td>
											<?= $detalle['VentaDetalleProducto']['nombre']; ?>
											<?= $this->Form->input(sprintf('DteDetalle.%d.NmbItem', $indice), array('type' => 'hidden', 'value' => $detalle['VentaDetalleProducto']['nombre']));?>
											<?= $this->Form->input(sprintf('Detalle.%d.NmbItem', $indice), array('type' => 'hidden', 'value' => $detalle['VentaDetalleProducto']['nombre']));?>
										</td>
										<td>
											<?= CakeNumber::currency($detalle['precio'], 'CLP'); ?>
											<?=$this->Form->input(sprintf('DteDetalle.%d.PrcItem', $indice), array('type' =>'hidden', 'value' => $detalle['precio']));?>
											<?=$this->Form->input(sprintf('Detalle.%d.PrcItem', $indice), array('type' =>'hidden', 'value' => $detalle['precio']));?>
										</td>
										<td>
											<?= CakeNumber::currency($detalle['precio'] * 1.19, 'CLP'); ?>
										</td>
										<td>
											<?= number_format($detalle['cantidad'], 0, ".", "."); ?>
											<?= $this->Form->input(sprintf('DteDetalle.%d.QtyItem', $indice), array('type' => 'hidden', 'value' => $detalle['cantidad'])); ?>
											<?= $this->Form->input(sprintf('Detalle.%d.QtyItem', $indice), array('type' => 'hidden', 'value' => $detalle['cantidad'])); ?>
										</td>
										<td>
											<?= CakeNumber::currency($detalle['precio'] * $detalle['cantidad'], 'CLP'); ?>
										</td>
									</tr>
								<? endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="5" class="text-right">Total Productos</th>
									<td><?=CakeNumber::currency($TotalProductos, 'CLP');?></td>
								</tr>
								<tr>
									<th colspan="5" class="text-right">IVA <small>(19%)</small></th>
									<td><?=CakeNumber::currency(round($TotalProductos * 0.19), 'CLP');?></td>
								</tr>
								<tr>
									<th colspan="5" class="text-right">Descuento</th>
									<td>
										<?php if (!empty($venta['Venta']['descuento'])) {echo CakeNumber::currency($venta['Venta']['descuento'], 'CLP');} ?>
										<?= $this->Form->input('DscRcgGlobal.ValorDR', array('type' => 'hidden', 'value' => round($this->request->data['Venta']['descuento']) )); ?>
									</td>
								</tr>
								<tr>
									<th colspan="5" class="text-right">Transporte</th>
									<td><?php if (!empty($venta['Venta']['costo_envio'])) {echo CakeNumber::currency($venta['Venta']['costo_envio'], 'CLP');} ?></td>
								</tr>
								<tr class="success">
									<th colspan="5" class="text-right">Total</th>
									<td><?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Html->link('Volver a la orden', array('controller' => 'ventas', 'action' => 'view', $this->request->data['Venta']['id']), array('class' => 'btn btn-danger')); ?>
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
					<div class="panel-body">
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<br>
									<label>Nombre</label>
									<div class="form-control"><?= $venta['Venta']['VentaCliente']['nombre']; ?></div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<br>
									<label>Apellido</label>
									<div class="form-control"><?= $venta['Venta']['VentaCliente']['apellido']; ?></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<br>
									<label>Rut</label>
									<div class="form-control"><?= $venta['Venta']['VentaCliente']['rut']; ?></div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<br>
									<label>Teléfono</label>
									<div class="form-control"><?= $venta['Venta']['VentaCliente']['telefono']; ?></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<br>
									<label>Email</label>
									<div class="form-control"><?= $venta['Venta']['VentaCliente']['email']; ?></div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<br>
									<label>Fecha Ingreso</label>
									<div class="form-control"><?= date_format(date_create($venta['Venta']['VentaCliente']['created']), 'd/m/Y H:i:s'); ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php //-------------------------------------------------- mensajes -------------------------------------------------- ?>
		<div class="col-xs-12 col-sm-6">
			<h2><i class="fa fa-envelope" aria-hidden="true"></i> Mensajes</h2>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-12">

							<div class="table-responsive">
    			
								<table class="table table-striped">
									
									<thead>
										<tr class="sort">
											<th width="140px">Fecha</th>
											<th>Asunto</th>
											<th>Mensaje</th>
										</tr>
									</thead>
									
									<tbody>

										<?php
											if (!empty($venta['VentaMensaje'])) {
												foreach ($venta['VentaMensaje'] as $mensaje) {
												?>

													<tr>
														<td><?= $mensaje['fecha']; ?></td>
														<td><?= $mensaje['asunto']; ?></td>
														<td><?= $mensaje['mensaje']; ?></td>
													</tr>

												<?php
												}
											}
										?>

									</tbody>

								</table>

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	
	<!-- Fin Mensajes -->


	<!-- DTE emitido -->

	<? if ( $this->request->data['Dte']['estado'] == 'dte_real_emitido' ) : ?>
	<div class="row">
		<div class="col-xs-12">
			<h2 id="dte"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Información del DTE'); ?></h2>
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
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label><?=__('Tipo de documento');?></label>
								<div class="form-control">
									<?=$tipoDocumento[$this->request->data['Dte']['tipo_documento']];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label><?=__('Folio');?></label>
								<div class="form-control">
									#<?=$this->request->data['Dte']['folio'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label><?=__('Rut Receptor');?></label>
								<div class="form-control">
									<?=$this->request->data['Dte']['rut_receptor'];?>
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
									<?=$this->request->data['Dte']['razon_social_receptor'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Giro Receptor');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte']['giro_receptor'];?>
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
									<?=$this->request->data['Dte']['direccion_receptor'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Comuna receptor');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte']['comuna_receptor']?>
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
									<?= $this->request->data['Dte']['fecha'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<br>
								<label><?=__('Tasa');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte']['tasa'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<br>
								<label><?=__('Exento');?></label>
								<div class="center-block">
									<?= $this->request->data['Dte']['exento'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'; ?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<br>
								<label><?=__('Generado en certificación');?></label>
								<div class="center-block">
									<?= $this->request->data['Dte']['certificacion'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'; ?>
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
									<?= $this->request->data['Dte']['sucursal_sii'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Usuario que emitió el DTE');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte']['usuario'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<br>
								<label><?=__('Código Seguimiento');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte']['track_id'];?>
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
									<?= $this->request->data['Dte']['revision_estado'];?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<br>
								<label><?=__('Detalle del estado SII');?></label>
								<div class="form-control">
									<?= $this->request->data['Dte']['revision_detalle'];?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-body js-despacho <?=$tipo = ($this->request->data['Dte']['tipo_documento'] != 52) ? 'hide' : '' ; ?>">
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
										<td><?=$this->Form->select('tipo_traslado', $traslados , array('class' => 'form-control', 'escape' => false, 'empty' => false));?></td>
										<td><?=$this->Form->input('direccion_traslado', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Dirección de destino'));?></td>
										<td><?=$this->Form->select('comuna_traslado', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna'));?></td>
										<td><?=$this->Form->input('rut_transportista', array('type' => 'text', 'class' => 'rut-input form-control', 'placeholder' => 'Rut Transportista'));?></td>
										<td><?=$this->Form->input('patente', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Patente'));?></td>
										<td><?=$this->Form->input('rut_chofer', array('type' => 'text', 'class' => 'rut-input form-control', 'placeholder' => 'Rut chofer'));?></td>
										<td><?=$this->Form->input('nombre_chofer', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Nombre chofer'));?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<? if (!empty($this->request->data['Dte']['pdf'])) : ?>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group">
								<br>
								<label><?=__('Ver documento');?></label>
								<?= $this->Html->link(
									'<i class="fa fa-eye"></i> Ver',
									sprintf('/Dte/%d/%d/%s', $this->request->data['Venta']['id'], $this->request->data['Dte']['id'], $this->request->data['Dte']['pdf']),
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
									array('action' => 'editar', $this->request->data['Dte']['id'], $this->request->data['Venta']['id']),
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
						<?= $this->Html->link('Volver a la orden', array('action' => 'orden', $this->request->data['Venta']['id']), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<? endif; ?>

	<!-- Fin Dte Emitido -->

	<!-- Dte temporal emitido o Dte real no emitido -->
	<? if ( $this->request->data['Dte']['estado'] == 'dte_temporal_emitido' || $this->request->data['Dte']['estado'] == 'dte_real_no_emitido' ) : ?>
		<div class="row">
			<div class="col-xs-12">
				<h2 id="dte"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Generar DTE Real desde uno temporal.'); ?></h2>
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-xs-12">
								<label><?=__('Seleccione tipo de documento');?></label>
								<? if (!empty($this->request->data['Dte'])) : ?>
									<?=$this->Form->select('tipo_documento', $tipoDocumento, array('class' => 'form-control js-dte-tipo', 'escape' => false, 'empty' => false, 'value' => $this->request->data['Dte']['tipo_documento']));?>
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
										<?=$this->Form->input('rut_receptor', array('type' => 'text', 'class' => 'rut-contribuyente form-control', 'placeholder' => 'Ingrese rut del receptor', 'value' => $this->request->data['Dte']['rut_receptor']));?>
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
										<?=$this->Form->input('razon_social_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese razón social del receptor', 'value' => $this->request->data['Dte']['razon_social_receptor']));?>
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
										<?=$this->Form->input('giro_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese giro del receptor', 'value' => $this->request->data['Dte']['giro_receptor']));?>
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
										<?=$this->Form->input('direccion_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese dirección del receptor', 'value' => $this->request->data['Dte']['direccion_receptor']));?>
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
										<?=$this->Form->select('comuna_receptor', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna', 'value' => $this->request->data['Dte']['comuna_receptor']));?>
									<? else : ?>
										<?=$this->Form->select('comuna_receptor', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna'));?>
									<? endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body js-despacho <?=$tipo = ($this->request->data['Dte']['tipo_documento'] != 52) ? 'hide' : '' ; ?>">
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
											<td><?=$this->Form->select('tipo_traslado', $traslados , array('class' => 'form-control', 'escape' => false, 'empty' => false));?></td>
											<td><?=$this->Form->input('direccion_traslado', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Dirección de destino'));?></td>
											<td><?=$this->Form->select('comuna_traslado', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna'));?></td>
											<td><?=$this->Form->input('rut_transportista', array('type' => 'text', 'class' => 'rut-input form-control', 'placeholder' => 'Rut Transportista'));?></td>
											<td><?=$this->Form->input('patente', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Patente'));?></td>
											<td><?=$this->Form->input('rut_chofer', array('type' => 'text', 'class' => 'rut-input form-control', 'placeholder' => 'Rut chofer'));?></td>
											<td><?=$this->Form->input('nombre_chofer', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Nombre chofer'));?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="pull-right">
							<?=$this->Html->link('<i class="fa fa-file-text" aria-hidden="true"></i> Ver PDF', array('controller' => 'ordenes', 'action' => 'getPdfDteTemporal', $this->request->data['Dte']['receptor'], $this->request->data['Dte']['tipo_documento'], $this->request->data['Dte']['dte_temporal'], $this->request->data['Dte']['emisor']), array('class' => 'btn btn-info', 'escape' => false)); ?>
							<button type="submit" class="btn btn-primary"><i class="fa fa-file-text" aria-hidden="true"></i> Generar DTE</button>
							<?= $this->Html->link('Cancelar y volver', array('action' => 'orden', $this->request->data['Venta']['id']), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div> <!-- end col -->
		</div> <!-- end row -->
	<? endif; ?>

	<!-- Fin Dte temporal emitido o Dte real no emitido --> 


	<!-- Dte temporal no emitido -->

	<? if ($this->request->data['Dte']['estado'] == 'no_generado' || $this->request->data['Dte']['estado'] == 'dte_temporal_no_emitido' ) : ?>
	<!-- Dte -->
	<div class="row">
		<div class="col-xs-12">
			<h2 id="dte"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Generar DTE'); ?></h2>
			<div class="panel panel-default">
				<div class="panel-body">
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
	<? endif; ?>

	<!-- Fin Dte temporal no emitido -->
</div>
<?= $this->Form->end(); ?>

<!-- Enviar DTE por email -->
<? if ( $this->request->data['Dte']['estado'] == 'dte_real_emitido' && !empty($this->request->data['Dte']['pdf']) ) : ?>
<?= $this->Form->create('Orden', array(
	'url' => array(
		'action' => 'enviarDteViaEmail',
	), 'method' => 'post' ,'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
	<?= $this->Form->input('id_dte', array('type' => 'hidden', 'value' => $this->request->data['Dte']['id'])); ?>
	<?= $this->Form->input('id_orden', array('type' => 'hidden', 'value' => $this->request->data['Dte']['venta_id'])); ?>
	<?= $this->Form->input('dte', array('type' => 'hidden', 'value' => $this->request->data['Dte']['tipo_documento'])); ?>
	<?= $this->Form->input('folio', array('type' => 'hidden', 'value' => $this->request->data['Dte']['folio'])); ?>
	<?= $this->Form->input('emisor', array('type' => 'hidden', 'value' => $this->request->data['Dte']['emisor'])); ?>
	<div class="page-content-wrap">
		<div class="row">
			<div class="col-xs-12">
				<h2 id="dte"><i class="fa fa-envelope" aria-hidden="true"></i> <?=__('Enviar DTE vía email'); ?></h2>
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<tr>
									<th><?=__('Asunto');?></th>
									<td><?=$this->Form->input('asunto', array('value' => sprintf('Su %s ha sido emitida.', $this->Html->tipoDocumento[$this->request->data['Dte']['tipo_documento']]), 'placeholder' => 'Agregue un asunto para el email'));?></td>
								</tr>
								<tr>
									<th><?=__('Mensaje');?></th>
									<td><?=$this->Form->textarea('mensaje', array('value' => sprintf('Estimado/a %s. Hemos emitido su %s exitosamente para su compra referencia #%s. El documento los encontrará adjunto a este email. Por favor NO RESPONDA ESTE EMAIL ya que es generado automáticamente.', $this->request->data['Venta']['VentaCliente']['nombre']. ' ' .$this->request->data['Venta']['VentaCliente']['apellido'], $this->Html->tipoDocumento[$this->request->data['Dte']['tipo_documento']], $this->request->data['Venta']['referencia']), 
										'class' => 'form-control', 
										'placeholder' => 'Agregue un mensaje personalizado para este email.'));?></td>
								</tr>
								<tr>
									<th><?=__('Emails');?></th>
									<td><?= $this->Form->input('emails', array('value' => $this->request->data['Venta']['VentaCliente']['email'], 'class' => 'form-control', 'placeholder' => 'Email para enviar')); ?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="panel-footer">
						<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-paper-plane" aria-hidden="true"></i> Enviar Email</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?= $this->Form->end(); ?>
<? endif; ?>
