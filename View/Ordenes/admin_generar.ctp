<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Venta #' . $this->request->data['Venta']['id']); ?></h2>
	<div class="pull-right">
		<button class="btn btn-warning" onclick="$('html, body').animate({scrollTop:$('#dte').offset().top},1000);">Generar DTE</button>
	</div>
</div>
<?= $this->Form->create('Dte', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('venta_id', array('type' => 'hidden', 'value' => $this->request->data['Venta']['id'])); ?>

<? if (!empty($venta['Dte'])) :  ?>
	<?= $this->Form->input('Dte.id', array('type' => 'hidden', 'value' => $venta['Dte'][0]['id'])); ?>
<? endif; ?>
<?= $this->Form->input('estado', array('type' => 'hidden', 'value' => __('no_generado'))); ?>
<?= $this->Form->hidden('externo', array('value' => $venta['Venta']['id_externo'])); ?>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-9">


			<!-- INFORMACIÓN DE LA VENTA -->
			<div class="panel panel panel-info panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-info" aria-hidden="true"></i> <?=__('Información de la venta'); ?></h3>
					<ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
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
										<td><span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= $venta['VentaEstado']['VentaEstadoCategoria']['estilo']; ?>"><?= $venta['VentaEstado']['VentaEstadoCategoria']['nombre']; ?></span></td>
									</tr>
									<tr>
										<th>Fecha</th>
										<td><?= date_format(date_create($venta['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?></td>
									</tr>
									<tr>
										<th>Medio de Pago</th>
										<td><?= $venta['MedioPago']['nombre']; ?></td>
									</tr>
									<tr>
										<th>Tienda</th>
										<td><?= $venta['Tienda']['nombre']; ?></td>
									</tr>
									<tr>
										<th>Marketplace</th>
										<td><?php if (!empty($venta['Venta']['marketplace_id'])) {echo $venta['Marketplace']['nombre'];} ?>&nbsp;</td>
									</tr>
									<tr>
										<th>Atendida</th>
										<td><?= ($venta['Venta']['atendida'] ? "<span class='btn btn-xs btn-success'>Sí</span>" : "<span class='btn btn-xs btn-danger'>No</span>"); ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- DTE -->
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 id="dte" class="panel-title"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Generar DTE'); ?></h3>
					<ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<? if (isset($documentos['content'][0])) : ?>
					<div class="row">
						<div class="col-xs-12">
							<div class="alert alert-success" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
								<?= __('Datos de facturación cargados con éxito'); ?>
							</div>
						</div>
					</div>
					<? endif; ?>
					<div class="row">
						<div class="col-xs-12  js-dte-factura">
							<div class="form-group">
								<br>
								<label><?=__('Seleccione tipo de documento');?></label>
								<?=$this->Form->select('tipo_documento', $tipoDocumento, array('class' => 'form-control js-dte-tipo', 'escape' => false, 'empty' => 'Seleccione tipo documento'));?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<br>
								<label><?=__('Rut Receptor');?></label>
								<?=$this->Form->input('rut_receptor', array('type' => 'text', 'class' => 'rut-contribuyente form-control', 'placeholder' => 'Ingrese rut del receptor'));?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6">
							<div class="form-group">
								<br>
								<label><?=__('Razón Social Receptor');?></label>
								<?=$this->Form->input('razon_social_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese razón social del receptor'));?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 js-no-boleta">
							<div class="form-group">
								<br>
								<label><?=__('Giro Receptor');?></label>
								<?=$this->Form->input('giro_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese giro del receptor'));?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6 js-no-boleta">
							<div class="form-group">
								<br>
								<label><?=__('Dirección receptor');?></label>
								<?=$this->Form->input('direccion_receptor', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese dirección del receptor'));?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6 js-no-boleta">
							<div class="form-group">
								<br>
								<label><?=__('Comuna receptor');?></label>
								<?=$this->Form->select('comuna_receptor', $comunas , array('class' => 'form-control', 'escape' => false, 'empty' => 'Seleccione comuna'));?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-md-6 ">
							<div class="form-group">
								<br>
								<label><?=__('Medio de pago');?></label>
								<?=$this->Form->select('medio_de_pago', $medioDePago , array('class' => 'form-control', 'escape' => false, 'empty' => 'Sin medio de pago'));?>
							</div>
						</div>
						<div class="col-xs-12 col-md-6 ">
							<div class="form-group">
								<br>
								<label><?=__('Fecha');?></label>
								<?=$this->Form->input('fecha', array('class' => 'form-control datepicker', 'escape' => false, 'value' => date('Y-m-d')));?>
							</div>
						</div>
					</div>
				</div>
				
				<!-- REFERENCIA -->
				<div class="panel-body js-referencia hide">
					<div class="row">
						<div class="col-xs-12">
							<h4 class="pull-left"><i class="fa fa-file"></i> Documentos Referenciados</h4>
						</div>
						<div class="col-xs-12">
							<div class="table-responsive">
								<table class="table js-clon-scope table-bordered">
									<thead>
										<tr>
											<th><?= __('Folio referenciado');?></th>
											<th><?= __('Tipo documento');?></th>
											<th><?= __('Fecha Referencia');?></th>
											<th><?= __('Código ref.');?></th>
											<th><?= __('Razón referencia');?></th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody class="js-clon-contenedor js-clon-blank">
										<tr class="js-clon-base hidden">
											
											<td>
												<!--<?= $this->Form->select('DteReferencia.999.dte_referencia', $dteEmitidos, array('disabled' => true, 'class' => 'form-control id-referencia', 'empty' => 'Seleccione folio de ref.')); ?>-->
												<?= $this->Form->input('DteReferencia.999.folio', array('type' => 'text', 'disabled' => true, 'class' => 'folio-referencia form-control', 'placeholder' => 'Igrese folio'))?>
											</td>
											<td><?= $this->Form->select('DteReferencia.999.tipo_documento', $tipoDocumentosReferencias, array('diabled' => true, 'class' => 'tipo-referencia form-control', 'empty' => 'Seleccione'))?></td>
											<td><?= $this->Form->input('DteReferencia.999.fecha', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker fecha-referencia')); ?></td>
											<td><?= $this->Form->select('DteReferencia.999.codigo_referencia', $codigoReferencia , array('disabled' => true, 'class' => 'form-control', 'empty' => 'Seleccione código de ref.')); ?></td>
											<td><?= $this->Form->input('DteReferencia.999.razon', array('disabled' => true)); ?></td>
											<td>
												<a href="#" class="btn btn-xs btn-danger js-clon-eliminar"><i class="fa fa-trash"></i> Eliminar</a>
											
											</td>
										</tr>
										<? if ( ! empty($this->request->data['Dte']['DteReferencia']) ) : ?>
										<? foreach ( $this->request->data['Dte']['DteReferencia'] as $index => $referencia ) : ?>
										<tr>
											<td>
												<!--<?= $this->Form->select(sprintf('DteReferencia.%d.dte_referencia', $index), $dteEmitidos, array('class' => 'form-control id-referencia', 'empty' => 'Seleccione folio de ref.')); ?>-->
												<?= $this->Form->input(sprintf('DteReferencia.%d.folio', $index), array('type' => 'text', 'class' => 'folio-referencia form-control', 'placeholder' => 'Ingrese folio', 'value' => $referencia['folio'])); ?>
											</td>
											<td><?= $this->Form->select(sprintf('DteReferencia.%d.tipo_documento', $index), $tipoDocumentosReferencias, array('class' => 'tipo-referencia form-control', 'empty' => 'Seleccione', 'default' => $referencia['tipo_documento']))?></td>
											<td><?= $this->Form->input(sprintf('DteReferencia.%d.fecha', $index), array('type' => 'text', 'class' => 'form-control datepicker fecha-referencia', 'value' => $referencia['fecha'])); ?></td>
											<td><?= $this->Form->select(sprintf('DteReferencia.%d.codigo_referencia', $index),$codigoReferencia , array('class' => 'form-control', 'empty' => 'Seleccione código de ref.')); ?></td>
											<td><?= $this->Form->input(sprintf('DteReferencia.%d.razon', $index), array('class' => 'form-control')); ?></td>
											<td>
												<a href="#" class="btn btn-xs btn-danger js-clon-eliminar"><i class="fa fa-trash"></i> Eliminar</a>
											
											</td>
										</tr>
										<? endforeach; ?>	
										<? endif; ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="5">&nbsp;</td>
											<td><a href="#" class="btn btn-xs btn-success js-clon-agregar"><i class="fa fa-plus"></i> Agregar referencia</a></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>


				<!-- Tipo de NDC -->
				<div class="panel-body js-tipo-ndc hide">
					<div class="row">
						<div class="col-xs-12">
							<h4 class="pull-left"><i class="fa fa-exclamation-triangle"></i> Tipo de Nota de crédito</h4>
						</div>
						<div class="col-xs-12">
							<div class="table-responsive">
								<table class="table table-bordered">
									<tr>
										<td>Seleccione tipo de nota de crédito</td>
										<td><?=$this->Form->select('tipo_ntc', $tipos_ndc, array('class' => 'form-control not-blank', 'empty' => 'Seleccione'))?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			

				<!-- ITEMS -->
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<h4 class="pull-left"><i class="fa fa-list"></i> Items</h4>
						</div>
						<div class="col-xs-12">
							<div class="table-responsive">
								<table class="table table-striped table-bordered js-productos-dte">
									<thead>
										<th style="width: 115px;">Código</th>
										<th style="width: 250px;">Nombre</th>
										<th>Precio Uni. <small>(Neto)</small></th>
										<th>Precio Uni. <small>(Bruto)</small></th>
										<th>Cantidad</th>
										<th>Subtotal</th>
										<th></th>
									</thead>
									<tbody>
										<tr class="hidden clone-tr" data-index="" style="min-height: 50px;">
											<td  class="permitido_modificar" valign="center">

												<input type="text" class="form-control editable required editVlrCodigo" name="editVlrCodigo[99]" value="" data-original="" disabled="disabled">
											
												<?= $this->Form->input(sprintf('DteDetalle.%d.VlrCodigo', 99), array('type' => 'hidden', 'class'=> 'editable_hidden', 'disabled' => true ,'value' => '')) ;?>
												<?= $this->Form->input(sprintf('Detalle.%d.VlrCodigo', 99), array('type' => 'hidden', 'class'=> 'editable_hidden', 'disabled' => true , 'value' => '')) ;?>

											</td>
											<td class="permitido_modificar" valign="center">
												
												<input type="text" class="form-control editable required editNmbItem" maxlength="80" name="editNmbItem[99]" value="" data-original="" disabled="disabled">
											
												<?= $this->Form->input(sprintf('DteDetalle.%d.NmbItem', 99), array('type' => 'hidden', 'maxlength' => '80', 'class'=> 'editable_hidden', 'disabled' => true , 'value' => ''));?>
												<?= $this->Form->input(sprintf('Detalle.%d.NmbItem', 99), array('type' => 'hidden', 'maxlength' => '80', 'class'=> 'editable_hidden', 'disabled' => true , 'value' => ''));?>

											</td>
											<td class="permitido_modificar" valign="center">
													
												<input type="text" class="form-control editable required editPrcItem" name="editPrcItem[99]" value="0" data-original="0" disabled="disabled">
											
												<?=$this->Form->input(sprintf('DteDetalle.%d.PrcItem', 99), array('type' => 'hidden', 'class'=> 'editable_hidden', 'disabled' => true , 'value' => 0));?>
												<?=$this->Form->input(sprintf('Detalle.%d.PrcItem', 99), array('type' => 'hidden', 'class'=> 'editable_hidden', 'disabled' => true , 'value' => 0));?>


											</td>
											<td valign="center">
												<input type="text" class="form-control mask_money editable required editPrcBrItem" name="editPrcBrItem[99]" value="" data-original="" disabled="disabled">
											</td>
											<td class="permitido_modificar" valign="center">
													
												<input type="text" class="form-control editable required editQtyItem" name="editQtyItem[99]" value="1" data-original="1" disabled="disabled">
											
												<?= $this->Form->input(sprintf('DteDetalle.%d.QtyItem', 99), array('type' => 'hidden', 'class'=> 'editable_hidden', 'disabled' => true , 'value' => 1)); ?>
												<?= $this->Form->input(sprintf('Detalle.%d.QtyItem', 99), array('type' => 'hidden', 'class'=> 'editable_hidden', 'disabled' => true , 'value' => 1)); ?>
											
											</td>
											<td valign="center">
												<span class="precio_iva_total_productos">0</span>
											</td>
											<td valign="center">
												<button class="duplicate_tr btn-success"><i class="fa fa-plus"></i></button>
												<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
											</td>
										</tr>

										<?php 

										// DTE ya tiene sus items
										if (!empty($venta['Dte']) && !empty(Hash::extract($venta['Dte'], '{n}.DteDetalle.{n}')) ) {
											
											$TotalProductos = 0; 

											foreach (Hash::extract($venta['Dte'], '{n}.DteDetalle.{n}') as $indice => $detalle) : 

												$TotalProductos = $TotalProductos + ($detalle['PrcItem'] * $detalle['QtyItem']); 

												echo $this->Form->input(sprintf('DteDetalle.%d.id', $indice), array('type' => 'hidden', 'value' => $detalle['id']));
											
											?>


											<tr class="copy_tr" data-index="<?=$indice;?>" style="min-height: 50px;">
												<td  class="permitido_modificar" valign="center">

													<input type="text" class="form-control editable required editVlrCodigo" name="editVlrCodigo[<?=$indice;?>]" value="<?=$detalle['VlrCodigo'];?>" data-original="<?=$detalle['VlrCodigo'];?>">
												
													<?= $this->Form->input(sprintf('DteDetalle.%d.VlrCodigo', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => $detalle['VlrCodigo'])) ;?>
													<?= $this->Form->input(sprintf('Detalle.%d.VlrCodigo', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => $detalle['VlrCodigo'])) ;?>

												</td>
												<td class="permitido_modificar" valign="center">
													
													<input type="text" class="form-control editable required editNmbItem" maxlength="80" name="editNmbItem[<?=$indice;?>]" value="<?=h($this->Text->truncate($detalle['NmbItem'], 80, array('ellipsis' => '')))?>" data-original="<?=h($this->Text->truncate($detalle['NmbItem'], 80, array('ellipsis' => '')))?>">
												
													<?= $this->Form->input(sprintf('DteDetalle.%d.NmbItem', $indice), array('type' => 'hidden', 'maxlength' => 80, 'class'=> 'editable_hidden', 'value' => h($this->Text->truncate($detalle['NmbItem'], 80, array('ellipsis' => '')))));?>
													<?= $this->Form->input(sprintf('Detalle.%d.NmbItem', $indice), array('type' => 'hidden', 'maxlength' => 80, 'class'=> 'editable_hidden', 'value' => h($this->Text->truncate($detalle['NmbItem'], 80, array('ellipsis' => '')))));?>

												</td>
												<td class="permitido_modificar" valign="center">
														
													<input type="text" class="form-control mask_money editable required editPrcItem" name="editPrcItem[<?=$indice;?>]" value="<?=$detalle['PrcItem'];?>" data-original="<?=$detalle['PrcItem'];?>">
												
													<?=$this->Form->input(sprintf('DteDetalle.%d.PrcItem', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => $detalle['PrcItem']));?>
													<?=$this->Form->input(sprintf('Detalle.%d.PrcItem', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => $detalle['PrcItem']));?>


												</td>
												<td valign="center">
													<input type="text" class="form-control mask_money editable required editPrcBrItem" name="editPrcBrItem[<?=$indice;?>]" value="<?=monto_bruto($detalle['PrcItem']);?>" data-original="<?=monto_bruto($detalle['PrcItem']);?>">
												</td>
												<td class="permitido_modificar" valign="center">
														
													<input type="text" class="form-control editable required editQtyItem" name="editQtyItem[<?=$indice;?>]" value="<?= number_format($detalle['QtyItem'], 0, ".", "."); ?>" data-original="<?=$detalle['QtyItem'];?>" max="<?=$detalle['cantidad'] - $detalle['cantidad_anulada'];?>">
												
													<?= $this->Form->input(sprintf('DteDetalle.%d.QtyItem', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => $detalle['QtyItem'])); ?>
													<?= $this->Form->input(sprintf('Detalle.%d.QtyItem', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => $detalle['QtyItem'])); ?>
												
												</td>
												<td valign="center">
													<span class="precio_iva_total_productos"><?= CakeNumber::currency($detalle['PrcItem'] * $detalle['QtyItem'], 'CLP'); ?></span>
												</td>
												<td valign="center">
													<button class="duplicate_tr btn-success"><i class="fa fa-plus"></i></button>
													<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
												</td>
											</tr>

											<?
											endforeach; 
											
											$venta['Venta']['total'] = $this->request->data['Venta']['total'] = ($TotalProductos * 1.19);
												
										}else{

											$TotalProductos = 0; 

											foreach ($venta['VentaDetalle'] as $indice => $detalle) : 

												if ($detalle['cantidad_anulada'] == $detalle['cantidad'])
													continue;

												$TotalProductos = $TotalProductos + ($detalle['precio'] * ($detalle['cantidad'] - $detalle['cantidad_anulada']) ); 

											?>


											<tr class="copy_tr" data-index="<?=$indice;?>" style="min-height: 50px;">
												<td  class="permitido_modificar" valign="center">

													<input type="text" class="form-control editable required editVlrCodigo" name="editVlrCodigo[<?=$indice;?>]" value="<?=sprintf('COD-%s', $detalle['VentaDetalleProducto']['id']);?>" data-original="<?=sprintf('COD-%s', $detalle['VentaDetalleProducto']['id']);?>">
												
													<?= $this->Form->input(sprintf('DteDetalle.%d.VlrCodigo', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => sprintf('COD-%s', $detalle['VentaDetalleProducto']['id']))) ;?>
													<?= $this->Form->input(sprintf('Detalle.%d.VlrCodigo', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => sprintf('COD-%s', $detalle['VentaDetalleProducto']['id']))) ;?>

												</td>
												<td class="permitido_modificar" valign="center">
													
													<input type="text" class="form-control editable required editNmbItem" maxlength="80" name="editNmbItem[<?=$indice;?>]" value="<?=h($this->Text->truncate($detalle['VentaDetalleProducto']['nombre'], 80, array('ellipsis' => '')));?>" data-original="<?=h($this->Text->truncate($detalle['VentaDetalleProducto']['nombre'], 80, array('ellipsis' => '')));?>">
												
													<?= $this->Form->input(sprintf('DteDetalle.%d.NmbItem', $indice), array('type' => 'hidden', 'maxlength' => 80, 'class'=> 'editable_hidden', 'value' => h($this->Text->truncate($detalle['VentaDetalleProducto']['nombre'], 80, array('ellipsis' => '')))));?>
													<?= $this->Form->input(sprintf('Detalle.%d.NmbItem', $indice), array('type' => 'hidden', 'maxlength' => 80, 'class'=> 'editable_hidden', 'value' => h($this->Text->truncate($detalle['VentaDetalleProducto']['nombre'], 80, array('ellipsis' => '')))));?>

												</td>
												<td class="permitido_modificar" valign="center">
														
													<input type="text" class="form-control mask_money editable required editPrcItem" name="editPrcItem[<?=$indice;?>]" value="<?=$detalle['precio'];?>" data-original="<?=$detalle['precio'];?>">
												
													<?=$this->Form->input(sprintf('DteDetalle.%d.PrcItem', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => $detalle['precio']));?>
													<?=$this->Form->input(sprintf('Detalle.%d.PrcItem', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => $detalle['precio']));?>


												</td>
												<td valign="center">
													<input type="text" class="form-control mask_money editable required editPrcBrItem" name="editPrcBrItem[<?=$indice;?>]" value="<?=monto_bruto($detalle['precio']);?>" data-original="<?=monto_bruto($detalle['precio']);?>">
												</td>
												<td class="permitido_modificar" valign="center">
														
													<input type="text" class="form-control editable required editQtyItem" name="editQtyItem[<?=$indice;?>]" value="<?= number_format($detalle['cantidad'] - $detalle['cantidad_anulada'], 0, ".", "."); ?>" data-original="<?=$detalle['cantidad'] - $detalle['cantidad_anulada'];?>" max="<?=$detalle['cantidad'] - $detalle['cantidad_anulada'];?>">
												
													<?= $this->Form->input(sprintf('DteDetalle.%d.QtyItem', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => ($detalle['cantidad']-$detalle['cantidad_anulada']) )); ?>
													<?= $this->Form->input(sprintf('Detalle.%d.QtyItem', $indice), array('type' => 'hidden', 'class'=> 'editable_hidden', 'value' => ($detalle['cantidad']-$detalle['cantidad_anulada']) )); ?>
												
												</td>
												<td valign="center">
													<span class="precio_iva_total_productos"><?= CakeNumber::currency($detalle['precio'] * ($detalle['cantidad'] - $detalle['cantidad_anulada'] ), 'CLP'); ?></span>
												</td>
												<td valign="center">
													<button class="duplicate_tr btn-success"><i class="fa fa-plus"></i></button>
													<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
												</td>
											</tr>

											<?
											endforeach; 
										} 
									?>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="5" class="text-right">Total Productos</th>
											<td colspan="2" class="total_neto"><?=CakeNumber::currency($TotalProductos, 'CLP');?></td>
										</tr>
										<tr>
											<th colspan="5" class="text-right">IVA <small>(19%)</small></th>
											<td colspan="2" class="total_iva"><?=CakeNumber::currency(round($TotalProductos * 0.19), 'CLP');?></td>
										</tr>
										<tr>
											<th colspan="5" class="text-right">Descuento <small>(Bruto)</small></th>
											<td colspan="2" class="permitido_modificar">
												<input type="text" class="form-control mask_money editable required editDiscount" name="editDiscount" value="<?= round( ($this->request->data['Venta']['descuento'] / 1.19)); ?>" data-original="<?= round(($this->request->data['Venta']['descuento'] / 1.19));?>" required>
												<?= $this->Form->input('DscRcgGlobal.ValorDR', array('type' => 'hidden' ,'class'=> 'editable_hidden', 'value' => round( ($this->request->data['Venta']['descuento'] / 1.19) ))); ?>
											</td>
										</tr>
										<tr>
											<th colspan="5" class="text-right">Transporte <small>(Bruto)</small></th>
											<td colspan="2" class="permitido_modificar">
												<input type="text" class="form-control mask_money editable required editTransport" name="editTransport" value="<?= $venta['Venta']['costo_envio']; ?>" data-original="<?=$venta['Venta']['costo_envio'];?>" required>
											
												<?= $this->Form->input('Dte.Transporte', array('type' => 'hidden' ,'class'=> 'editable_hidden', 'value' => $venta['Venta']['costo_envio'] ));?>
											</td>
										</tr>
										<tr class="success">
											<th colspan="5" class="text-right">Total <small>(Bruto)</small></th>
											<td colspan="2" class="total_bruto">
												<?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?>		
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
				
				<!-- TRANSPORTISTA -->
				<div class="panel-body js-despacho hide">
					<div class="row">
						<div class="col-xs-12">
							<h4 class="pull-left"><i class="fa fa-truck"></i> Datos de transportista</h4>
						</div>
						<div class="col-xs-12">
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
				</div>

				<!-- GLOSA -->
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
								<br>
								<label><?=__('Glosa (opcional)');?></label>
								<?=$this->Form->input('glosa', array('class' => 'form-control', 'escape' => false, 'placeholder' => 'Máximo 100 carácteres', 'maxlength' => 100, 'value' => 'Dte para venta #' . $venta['Venta']['id']));?>
							</div>
						</div>
					</div>
				</div>


				<div class="panel-footer">
					<div class="pull-right">
						<button type="submit" class="btn btn-primary start-loading-when-form-is-validate"><i class="fa fa-file-text" aria-hidden="true"></i> Generar DTE</button>
						<?= $this->Html->link('Cancelar y volver', array('controller' => 'ventas', 'action' => 'view', $this->request->data['Venta']['id']), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

		</div> <!-- end col -->
		

		
		<div class="col-xs-12 col-sm-3">

			<!-- TOTAL VENTA -->
			<a class="tile tile-primary">
                <?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?>
                <p><?=__('Total documento');?></p>
            </a>
			

			<!-- CLIENTE -->

			<div class="panel panel-default">
				<div class="panel-body profile bg-info">

					<div class="profile-image">
						<img src="https://picsum.photos/200/200/?random">
					</div>
					<div class="profile-data">
					<div class="profile-data-name"><?= $venta['VentaCliente']['nombre']; ?> <?= $venta['VentaCliente']['apellido']; ?></div>
					<div class="profile-data-title text-primary"><?= __('Cliente'); ?></div>
					</div>

				</div>
				<ul class="panel-body list-group">
								
					<li class="list-group-item"><span class="fa fa-user"></span> <?= (!empty($venta['VentaCliente']['rut'])) ? $venta['VentaCliente']['rut'] : 'xxxxxxxx-x'; ?></li>
					
					<li class="list-group-item"><span class="fa fa-phone"></span> <?= (!empty($venta['VentaCliente']['telefono'])) ? $venta['VentaCliente']['telefono'] : 'x xxxx xxxx'; ?></li>

					<li class="list-group-item"><span class="fa fa-phone"></span> <?= (!empty($venta['Venta']['fono_receptor'])) ? $venta['Venta']['fono_receptor'] : 'x xxxx xxxx'; ?></li>

					<li class="list-group-item"><span class="fa fa-envelope"></span> <?= (!empty($venta['VentaCliente']['email'])) ? $venta['VentaCliente']['email'] : 'xxxxx@xxxx.xx'; ?></li>

					<li class="list-group-item"><span class="fa fa-truck"></span> <?= (!empty($venta['Venta']['direccion_entrega'])) ? $venta['Venta']['direccion_entrega'] : 'No especificado'; ?></li>

					<li class="list-group-item"><span class="fa fa-map-marker"></span> <?= (!empty($venta['Venta']['comuna_entrega'])) ? $venta['Venta']['comuna_entrega'] : 'No especificado'; ?></li>

				</ul>                        
			</div>


			<!-- MENSAJES -->

			<div class="panel panel-default">
				<div class="panel-body">
					<h4><i class="fa fa-envelope" aria-hidden="true"></i> <?= __('Mensajes de la venta');?></h4>
				</div>
				<ul class="panel-body list-group messages-dte-box">
				<? 
				if (!empty($venta['VentaMensaje'])) :

					foreach ($venta['VentaMensaje'] as $mensaje) : ?>
					<li class="list-group-item">
						<span class="message-subject">
							<?= (!empty($mensaje['asunto'])) ? $mensaje['asunto'] : 'Sin Asunto'; ?>
						</span>
						<span class="message-message">
							<?= $mensaje['mensaje']; ?>
						</span>
						<span class="message-date">
							<?= $mensaje['fecha']; ?>
						</span>
					</li>
					<?
					endforeach;
				else : ?>
					
					<li class="list-group-item text-mutted">
						<?= __('No registra mensajes.'); ?>
					</li>

				<?	
				endif; ?>

				</ul>  
				
			</div>

		</div>

	</div> <!-- end row -->

	
	
	
	<!-- Dte -->
	<div class="row">
		
	</div> <!-- end row -->
	<!-- Fin Dte -->
</div>
<?= $this->Form->end(); ?>
