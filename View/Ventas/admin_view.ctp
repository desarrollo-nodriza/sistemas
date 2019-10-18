<div class="page-title">
	<h2><span class="fa fa-money"></span> Venta # <?= $venta['Venta']['id']; ?></h2>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default tabs">

			    <ul class="nav nav-tabs">
			        <li class="active"><a href="#tab-venta" data-toggle="tab"><i class="fa fa-money"></i> Venta</a></li>
			        	
					<? if ($venta['VentaEstado']['permitir_dte']) : ?>
			        <li><a href="#tab-dtes" data-toggle="tab"><i class="fa fa-file"></i> Dte's</a></li>
					<? endif; ?>
			        <?php if (isset($venta['Envio'])) :  ?>
			    	<li><a href="#tab-envio" data-toggle="tab"><i class="fa fa-truck"></i> Direcciones</a></li>
			    	<?php endif; ?>
					
					<li><a href="#tab-transporte" data-toggle="tab"><i class="fa fa-truck"></i> Transporte Externo</a></li>
			    	
			    </ul>

			    <div class="tab-content">

			    	<?php //-------------------------------------------------- Tab info de venta -------------------------------------------------- ?>
			    	<div class="tab-pane panel-body active" id="tab-venta">

						<div class="col-xs-12 col-md-8">

							<!-- INFORMACIÓN DE LA VENTA -->
							<div class="panel panel panel-info">
								<div class="panel-heading">
									<h3 class="panel-title"><i class="fa fa-info" aria-hidden="true"></i> <?=__('Información de la venta'); ?></h3>
								</div>
								<div class="panel-body">
								<?= $this->Form->create('Venta', array('url' => array('action' => 'edit', $venta['Venta']['id']), 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
									<?=$this->Form->input('id');?>

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
												<td><span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= $venta['VentaEstado']['VentaEstadoCategoria']['estilo']; ?>"><?= $venta['VentaEstado']['VentaEstadoCategoria']['nombre']; ?></span> <small><?=$venta['Venta']['venta_estado_responsable'];?></small></td>
											</tr>
											<? if (isset(ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']])) : ?>
											<tr>
												<th>Picking</th>
												<td><span class="btn btn-xs btn" style="color: #fff; background-color: <?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['color'];?>"><?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['label'];?></span></td>
											</tr>
											<? endif; ?>
											<tr>
												<th>Fecha</th>
												<td><?= date_format(date_create($venta['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?></td>
											</tr>
											<tr>
												<th>Medio de Pago</th>
												<td><?= $venta['MedioPago']['nombre']; ?></td>
											</tr>
											<tr>
												<th>Dirección despacho</th>
												<td><?= $venta['Venta']['direccion_entrega']; ?></td>
											</tr>
											<tr>
												<th>Comuna despacho</th>
												<td>
													<div class="input-group">
	                                                    <?=$this->Form->input('comuna_entrega', array('value' => $venta['Venta']['comuna_entrega'], 'class' => 'form-control js-comuna-entrega', 'readonly' => true, 'style' => 'min-width: 250px;', 'data-value' => $venta['Venta']['comuna_entrega']));?>
	                                                    <span class="input-group-btn">
	                                                        <button class="btn btn-default toggle" type="button"><i class="fa fa-refresh"></i> <i class="fa fa-close" style="display: none;"></i></button>
	                                                    </span>
	                                                </div>
	                                                <span class="comuna-select hide">
														<?=$this->Form->label('comuna_entrega', 'Cambiar comuna entrega', array('class' => 'mt-5 pt-5')); ?>
		                                                <?=$this->Form->select('comuna_entrega_select', $comunas, array('empty' => 'Seleccione', 'default' => $venta['Venta']['comuna_entrega'], 'class' => 'form-control js-comuna-select')); ?>
	                                                	<?=$this->Form->button('Guardar cambios', array('type' => 'submit', 'class' => 'btn btn-warning btn-block mt-5')); ?>
	                                                </span>    
                                                </td>
											</tr>
											<tr>
												<th>Método de envio</th>
												<td>
												<? if (!empty($venta['MetodoEnvio'])) : ?>
													<span class="btn btn-xs btn-info"><?= $venta['MetodoEnvio']['nombre']; ?></span>
												<? else : ?>
													<span class="btn btn-xs btn-warning"><?= __('No obtenido');?></span>
												<? endif; ?>
												</td>
											</tr>
											<tr>
												<th>Teléfono despacho</th>
												<td><?= $venta['Venta']['fono_receptor']; ?></td>
											</tr>
											<? if (!empty($venta['Venta']['ci_receptor'])) : ?>
											<tr>
												<th>Cédula de identidad receptor</th>
												<td>
													<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#cimodal"><i class="fa fa-file"></i> Ver cédula</button>

													<!-- Modal -->
													<div class="modal fade" id="cimodal" tabindex="-1" role="dialog" aria-labelledby="modalCI">
													  <div class="modal-dialog modal-lg" role="document">
													    <div class="modal-content">
													      <div class="modal-header">
													        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													        <h4 class="modal-title" id="modalCI">Cédula de identidad</h4>
													      </div>
													      <div class="modal-body">
													        <?=$this->Html->image($venta['Venta']['ci_receptor']['path'], array('alt' => 'CI receptor', 'class' => 'img-responsive')); ?>
													      </div>
													      <div class="modal-footer">
													        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													        <?=$this->Html->link('<i class="fa fa-download"></i> Descargar', sprintf('%swebroot/img/%s', $this->webroot, $venta['Venta']['ci_receptor']['path']) , array('class' => 'btn btn-primary', 'escape' => false, 'download'=> sprintf('ci-venta-%d.jpg', $venta['Venta']['id']))); ?>
													      </div>
													    </div>
													  </div>
													</div>
												</td>
											</tr>
											<? endif; ?>
											<? if (!empty($venta['Venta']['chofer_email'])) : ?>
											<tr>
												<th>Chofer designado</th>
												<td><?= $venta['Venta']['chofer_email']; ?></td>
											</tr>
											<? endif; ?>
											<? if (!empty($venta['Venta']['fecha_transito'])) : ?>
											<tr>
												<th>Fecha en transito</th>
												<td><?= date_format(date_create($venta['Venta']['fecha_transito']), 'd/m/Y H:i:s'); ?></td>
											</tr>
											<? endif; ?>
											<? if (!empty($venta['Venta']['fecha_enviado'])) : ?>
											<tr>
												<th>Fecha enviado</th>
												<td><?= date_format(date_create($venta['Venta']['fecha_enviado']), 'd/m/Y H:i:s'); ?></td>
											</tr>
											<? endif; ?>
											<? if (!empty($venta['Venta']['fecha_entregado'])) : ?>
											<tr>
												<th>Fecha entregado</th>
												<td><?= date_format(date_create($venta['Venta']['fecha_entregado']), 'd/m/Y H:i:s'); ?></td>
											</tr>
											<? endif; ?>
											<? if (!empty($venta['Venta']['transporte_id'])) : ?>
											<tr>
												<th>Transporte usado</th>
												<td><?= $venta['Transporte']['nombre']; ?></td>
											</tr>
											<? endif; ?>
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
								<?= $this->Form->end(); ?>
								</div>
								
								<? if ($venta['Venta']['picking_estado'] != 'empaquetado' && $permisos['storage']) : ?>
								<div class="panel-body">
									<?=$this->Html->link('<i class="fa fa-hand-paper-o"></i> Reservar stock manualmente', array('action' => 'reservar_stock_venta', $venta['Venta']['id']), array('class' => 'btn btn-primary pull-right', 'escape' => false))?>
								</div>
								<? endif; ?>

								<!-- Productos --> 
								<div class="panel-body">
									<div class="table-responsive">
										<table class="table table-striped table-bordered">
											<thead>
												<th>ID Producto</th>
												<th>Nombre</th>
												<th>Precio Neto</th>
												<th>Precio Bruto</th>
												<th>Cantidad</th>
												<th>Stock reservado</th>
												<th>Subtotal</th>
												<th>Opciones</th>
											</thead>
											<tbody>
												<?php $TotalProductos = 0; foreach ($venta['VentaDetalle'] as $indice => $detalle) : $TotalProductos = $TotalProductos + ($detalle['precio'] * $detalle['cantidad']); ?>
													<tr>
														<td>
															<?=($detalle['confirmado_app']) ? '<i class="fa fa-mobile text-success" data-toggle="tooltip" title="Confirmado vía app"></i>' : '' ; ?> 
															<? if ($permisos['edit']) : ?>
															<?= $this->Html->link($detalle['VentaDetalleProducto']['id'], array('controller' => 'ventaDetalleProductos', 'action' => 'edit', $detalle['VentaDetalleProducto']['id']), array('target' => '_blank')); ?>
															<? else : ?>
																<?= $detalle['VentaDetalleProducto']['id'];?>
															<? endif; ?>
														</td>
														<td data-toggle="tooltip" title="<?=$detalle['VentaDetalleProducto']['nombre'];?>" class="td-producto">
															<? if (!empty($detalle['VentaDetalleProducto']['imagenes'])) : ?>
															<img src="<?=Hash::extract($detalle['VentaDetalleProducto']['imagenes'], '{n}[principal=1].url')[0]; ?>" class="img-responsive producto-td-imagen">
															<? endif; ?>
															<?= $this->Text->truncate($detalle['VentaDetalleProducto']['nombre'], 40); ?>
														</td>
														<td>
															<?= CakeNumber::currency($detalle['precio'], 'CLP'); ?>
														</td>
														<td>
															<?= CakeNumber::currency($detalle['precio'] * 1.19, 'CLP'); ?>
														</td>
														<td>
															<?= number_format($detalle['cantidad'], 0, ".", "."); ?>
														</td>
														<td>
															<?=$detalle['cantidad_reservada'];?>
														</td>
														<td>
															<?= CakeNumber::currency($detalle['precio'] * $detalle['cantidad'], 'CLP'); ?>
														</td>
														<td>
														<? if ($venta['Venta']['picking_estado'] != 'empaquetado' && $permisos['storage']) : ?>
															<?=$this->Html->link('<i class="fa fa-ban"></i> Liberar', array('action' => 'liberar_stock_reservado', $venta['Venta']['id'], $detalle['id'], $detalle['cantidad_reservada']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Liberar stock'))?>
														<? endif; ?>
														</td>
													</tr>
												<? endforeach; ?>
											</tbody>
											<tfoot>
												<tr>
													<th colspan="7" class="text-right">Total Productos</th>
													<td><?=CakeNumber::currency($TotalProductos, 'CLP');?></td>
												</tr>
												<tr>
													<th colspan="7" class="text-right">IVA <small>(19%)</small></th>
													<td><?=CakeNumber::currency(round($TotalProductos * 0.19), 'CLP');?></td>
												</tr>
												<tr>
													<th colspan="7" class="text-right">Descuento</th>
													<td>
														<?php if (!empty($venta['Venta']['descuento'])) {echo CakeNumber::currency($venta['Venta']['descuento'], 'CLP');} ?>
														<?= $this->Form->input('DscRcgGlobal.ValorDR', array('type' => 'hidden', 'value' => round($this->request->data['Venta']['descuento']))); ?>
													</td>
												</tr>
												<tr>
													<th colspan="7" class="text-right">Transporte</th>
													<td>
														<?=$this->Form->hidden('Dte.Transporte', array('value' => $venta['Venta']['costo_envio'] ));?>
														<?php if (!empty($venta['Venta']['costo_envio'])) {echo CakeNumber::currency($venta['Venta']['costo_envio'], 'CLP');} ?>
													</td>
												</tr>
												<tr class="success">
													<th colspan="7" class="text-right" style="font-size: 22px;">Total</th>
													<td style="font-size: 22px;"><?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>	
							
							<!-- TRANSPORTISTAS -->
							<? if ($permisos['storage'] || $permisos['edit']) : ?>
							<?= $this->Form->create('Venta', array('url' => array('action' => 'registrar_seguimiento'), 'class' => 'form-horizontal js-validate-producto', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<div class="panel panel-info">
								<div class="panel-heading">
									<h5 class="panel-title"><i class="fa fa-truck" aria-hidden="true"></i> <?=__('Transportes');?></h5>
									<ul class="panel-controls">
				                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
				                    </ul>
								</div>
								<div class="panel-body">

									<div class="table-responsive">
										<table class="table table-bordered">
											
											<thead>
												<tr>
													<th><?=__('Transportista');?></th>
													<th><?=__('N° de seguimiento');?></th>
													<th><?=__('Etiqueta');?></th>
													<th><?=__('Plazo entrega aprox');?></th>
													<th><?=__('Seguimiento');?></th>
													<th><?=__('Acciones'); ?></th>
												</tr>
											</thead>
											<tbody class="">
												<tr class="hidden clone-tr">
													<td><?=$this->Form->select('Transporte.999.transporte_id', $transportes, array('disabled' => true, 'empty' => 'Seleccione', 'class' => 'form-control not-blank js-select-transporte'))?></td>
													<td><?=$this->Form->input('Transporte.999.cod_seguimiento', array('disabled' => true, 'class' => 'form-control not-blank', 'placeholder' => 'Ej: 9999999999'));?></td>
													<td>No aplica</td>
													<td><span class="js-fecha-entrega">Seleccione tranporte</span></td>
													<td><span class="js-btn-seguimiento">Seleccione tranporte</span></td>
													<td valign="center"><button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button></td>
												</tr>

												<? if (!empty($venta['Transporte'])) : ?>
													<? foreach ($venta['Transporte'] as $it => $transporte) : ?>
														<tr>
															<td><?=$this->Form->select(sprintf('Transporte.%d.transporte_id', $it), $transportes, array('empty' => 'Seleccione', 'class' => 'form-control not-blank js-select-transporte', 'default' => $transporte['id']))?></td>
															<td><?=$this->Form->input(sprintf('Transporte.%d.cod_seguimiento', $it), array('value' => $transporte['TransportesVenta']['cod_seguimiento'], 'class' => 'form-control not-blank', 'placeholder' => 'Ej: 9999999999'));?></td>
															<td>
																<? if (!empty($transporte['TransportesVenta']['etiqueta'])) : ?>
																<a href="<?=$transporte['TransportesVenta']['etiqueta']?>" class="btn btn-xs btn-primary" target="_blank"><i class="fa fa-file-pdf-o"></i> Ver</a>
																<? else : ?>													
																No aplica
																<? endif; ?>
															</td>
															<td><span class="js-fecha-entrega"><?=$transporte['tiempo_entrega']; ?></span></td>
															<td><span class="js-btn-seguimiento"><?=$transporte['url_seguimiento']; ?></td>
															<td valign="center">
																<button class="remove_tr btn-danger js-remove-seguimiento" data-id="<?=$transporte['TransportesVenta']['id'];?>"><i class="fa fa-minus"></i></button>
															</td>
														</tr>
													<? endforeach; ?>
												<? endif; ?>

											</tbody>
										</table>
									</div>
								</div>
								<div class="panel-footer">
									<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar">
								</div>
							</div>
							<?= $this->Form->end(); ?>
							<? endif; ?>
							<!-- / TRANSPORTISTA -->


							<!-- MENSAJES -->

							<div class="panel panel-info">
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

						</div> <!-- end col -->
					

					
						<div class="col-xs-12 col-sm-4">
								
							<!-- DESCARGAR DOCUMENTOS -->
				            <?= $this->Html->link('<i class="fa fa-file-pdf-o"></i><p>Envio, etiqueta, DTE</p>', array('controller' => 'ventas', 'action' => 'consultar_dte', $venta['Venta']['id']), array('class' => 'tile small tile-success js-generar-documentos-venta-modal', 'rel' => 'tooltip', 'title' => 'Generar Documentos', 'escape' => false)); ?>
							
							<? if (!empty($venta['Dte'])) : ?>
							
							<!-- DTE y ETIQUETA --> 
							<?= $this->Html->link('<i class="fa fa-file"></i><p>DTE y Etiqueta</p>', array('controller' => 'ventas', 'action' => 'generar_dte_etiqueta', $venta['Venta']['id'], 1), array('class' => 'tile small tile-primary js-generar-etiqueta-venta-dte', 'rel' => 'tooltip', 'title' => 'Generar Documentos', 'escape' => false)); ?>
							
							<? endif; ?>

				            <!-- DESCARGAR ETIQUETAS -->
							<? if (!empty($venta['Venta']['etiqueta_envio_externa'])) : ?>
				            	
								<a href="<?=$venta['Venta']['etiqueta_envio_externa']?>" target="_blank" class="tile small tile-info"><i class="fa fa-truck"></i><p>Etiqueta Externa</p></a>
								
							<? endif; ?>

							<?= $this->Html->link('<i class="fa fa-cube"></i><p>Etiqueta interna</p>', array('controller' => 'ventas', 'action' => 'generar_etiqueta', $venta['Venta']['id'], 1), array('class' => 'tile small tile-warning js-generar-etiqueta-venta', 'rel' => 'tooltip', 'title' => 'Generar Etiqueta', 'escape' => false)); ?>

				            <? if (!$venta['Venta']['prioritario']) : ?>
							<?= $this->Form->postLink('<i class="fa fa-check"></i><p>Marcar como prioritaria</p>', array('action' => 'marcar_prioritaria', $venta['Venta']['id']), array('class' => 'tile small tile-danger', 'rel' => 'tooltip', 'title' => 'Marcar Venta como Prioritaria', 'escape' => false));?>
							<? else : ?>
							<?= $this->Form->postLink('<i class="fa fa-remove"></i><p>Marcar no prioritaria</p>', array('action' => 'marcar_no_prioritaria', $venta['Venta']['id']), array('class' => 'tile small tile-default', 'rel' => 'tooltip', 'title' => 'Marcar Venta como Prioritaria', 'escape' => false));?>
							<? endif; ?>
							
							<? if (isset($venta['VentaExterna']['facturacion'])) : ?>
							<!-- Facturacion info -->
							<div class="panel panel-primary">
								<div class="panel-body">
									<h4><i class="fa fa-file" aria-hidden="true"></i> <?= __('Datos de facturación');?></h4>
								</div>
								<ul class="panel-body list-group">
									<li class="list-group-item"><?=__('Tipo de documento');?> : <?=$venta['VentaExterna']['facturacion']['glosa_tipo_documento'];?></li>
									<li class="list-group-item"><?=__('Rut');?> : <?=$venta['VentaExterna']['facturacion']['rut_receptor'];?></li>
									<li class="list-group-item"><?=__('Razon social');?> : <?=$venta['VentaExterna']['facturacion']['razon_social_receptor'];?></li>
									<li class="list-group-item"><?=__('Giro');?> : <?=$venta['VentaExterna']['facturacion']['giro_receptor'];?></li>
									<li class="list-group-item"><?=__('Direccion');?> : <?=$venta['VentaExterna']['facturacion']['direccion_receptor'];?></li>
									<li class="list-group-item"><?=__('Comuna');?> : <?=$venta['VentaExterna']['facturacion']['comuna_receptor'];?></li>
								</ul>
							</div>
							<? endif; ?>

							<!-- TRANSACCIONES -->
							<div class="panel panel-primary">
								<div class="panel-body">
									<h4><i class="fa fa-money" aria-hidden="true"></i> <?= __('Transacciones de la venta');?></h4>
								</div>
								<div class="panel-body">
									<div class="table-responsive">
										<table class="table table-bordered">
											<thead>
												<th><?=__('Fecha');?></th>
												<th><?=__('Alias');?></th>
												<th><?=__('Monto');?></th>
												<th><?=__('Fee');?></th>
											</thead>
											<tbody>
												<? foreach ($venta['VentaTransaccion'] as $transaccion) : ?>
													<tr>
														<td><?=$transaccion['created'];?></td>
														<td><?=$transaccion['nombre'];?></td>
														<td><?= CakeNumber::currency($transaccion['monto'], 'CLP'); ?></td>
														<td><?= CakeNumber::currency($transaccion['fee'], 'CLP'); ?></td>
													</tr>
												<? endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>


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

						</div>

					</div>

					<?php //-------------------------------------------------- Tab dte's asociados -------------------------------------------------- ?>
			    	<div class="tab-pane panel-body" id="tab-dtes">

			    		<div class="container-fluid" style="padding: 0;">

			    			<div class="row">
								
								<div class="col-xs-12 accordion" style="padding: 0;">
								
								<? if (isset($venta['VentaExterna']['facturacion'])) : ?>
									<!-- Facturacion info -->
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title"><a href="#accFacturacion"><i class="fa fa-plus" aria-hidden="true"></i> <?= __('Ver datos de facturación');?></a></h4>
										</div>
										<div id="accFacturacion" style="display: none;">
											<ul class="panel-body list-group">
												<li class="list-group-item"><b><?=__('Tipo de documento');?></b> : <?=$venta['VentaExterna']['facturacion']['glosa_tipo_documento'];?></li>
												<li class="list-group-item"><b><?=__('Rut');?></b> : <?=$venta['VentaExterna']['facturacion']['rut_receptor'];?></li>
												<li class="list-group-item"><b><?=__('Razon social');?></b> : <?=$venta['VentaExterna']['facturacion']['razon_social_receptor'];?></li>
												<li class="list-group-item"><b><?=__('Giro');?></b> : <?=$venta['VentaExterna']['facturacion']['giro_receptor'];?></li>
												<li class="list-group-item"><b><?=__('Direccion');?></b> : <?=$venta['VentaExterna']['facturacion']['direccion_receptor'];?></li>
												<li class="list-group-item"><b><?=__('Comuna');?></b> : <?=$venta['VentaExterna']['facturacion']['comuna_receptor'];?></li>
												<li class="list-group-item"><b><?=__('Monto del documento');?></b> : <?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?></li>
												<li class="list-group-item"><b><?=__('Itemes');?></b> :</li>
											</ul>
											<!-- Productos --> 
											<div class="panel-body list-group">
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
															<?php $TotalProductos = 0; foreach ($venta['VentaDetalle'] as $indice => $detalle) : $TotalProductos = $TotalProductos + ($detalle['precio'] * $detalle['cantidad']); ?>
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
																	<?= $this->Form->input('DscRcgGlobal.ValorDR', array('type' => 'hidden', 'value' => round($this->request->data['Venta']['descuento']))); ?>
																</td>
															</tr>
															<tr>
																<th colspan="5" class="text-right">Transporte</th>
																<td>
																	<?=$this->Form->hidden('Dte.Transporte', array('value' => $venta['Venta']['costo_envio'] ));?>
																	<?php if (!empty($venta['Venta']['costo_envio'])) {echo CakeNumber::currency($venta['Venta']['costo_envio'], 'CLP');} ?>
																</td>
															</tr>
															<tr class="success">
																<th colspan="5" class="text-right">Total</th>
																<td><?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?></td>
															</tr>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
									</div>						
								<? endif; ?>

								</div>

			    			</div>

				    		<div class="row">

				    			<div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0;">
									
									<? if($permisos['edit']) : ?>
						    		<div class="btn-group pull-right">
						    			<? if (isset($venta['VentaExterna']['facturacion'])) : ?>
						    			<?= $this->Html->link(sprintf('<i class="fa fa-file-pdf-o"></i> Generar %s 1 click', $venta['VentaExterna']['facturacion']['glosa_tipo_documento']), array('controller' => 'ventas', 'action' => 'crear_dte_one_click', $venta['Venta']['id']), array('class' => 'btn btn-success', 'rel' => 'tooltip', 'title' => 'Generar Dte 1 click', 'escape' => false)); ?>
						    			<? endif; ?>
						    			<?= $this->Html->link('<i class="fa fa-file"></i> Generar Dte Manual', array('controller' => 'ordenes', 'action' => 'generar', $venta['Venta']['id']), array('class' => 'btn btn-warning', 'rel' => 'tooltip', 'title' => 'Generar Dte', 'escape' => false)); ?>
									</div>
									<? endif; ?>

									<div class="clearfix"><br /><br /><br /></div>

						    		<div class="table-responsive">
						    			
										<table class="table table-striped">
											
											<thead>
												<tr class="sort">
													<th>Folio</th>
													<th>Administrador</th>
													<th style="max-width: 150px;">Tipo de Dte</th>
													<th>Rut Receptor</th>
													<th>Total</th>
													<th>Fecha</th>
													<th>Estado</th>
													<th>Invalidado</th>
													<th>Acciones</th>
												</tr>
											</thead>
											
											<tbody>
												
												<?php
													if (!empty($venta['Dte'])) :
														foreach ($venta['Dte'] as $dte) : ?>
														<tr>
															<td><?= h($dte['folio']); ?>&nbsp;</td>
															<td><small><?= (!empty($dte['Administrador'])) ?  $dte['Administrador']['email'] : 'Sin administrador' ; ?></small>&nbsp;</td>
															<td><?= $this->Html->tipoDocumento[$dte['tipo_documento']]; ?>&nbsp;</td>
															<td><?= h($dte['rut_receptor']); ?>&nbsp;</td>
															<td><?= CakeNumber::currency($dte['total'], 'CLP'); ?>&nbsp;</td>
															<td><?= h($dte['fecha']); ?>&nbsp;</td>
															<td><?= $dteestado = (isset($dte['estado'])) ? $this->Html->dteEstado($dte['estado']) : $this->Html->dteEstado() ; ?>&nbsp;</td>
															<td><?= $dteinvalidado = ($dte['invalidado']) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-close"></i>' ; ?>&nbsp;</td>
															<td>
															<div class="btn-group">
																<? if ($dte['estado'] == 'dte_real_emitido' && !empty($dte['pdf'])) : ?>
																	<?= $this->Html->link(
																	'<i class="fa fa-file"></i> Ver ' . $this->Text->truncate($this->Html->tipoDocumento[$dte['tipo_documento']], 15),
																	sprintf('/Dte/%d/%d/%s', $venta['Venta']['id'], $dte['id'], $dte['pdf']),
																	array(
																		'class' => 'btn btn-success btn-xs', 
																		'target' => '_blank',
																		'title' => 'Ver ' . $this->Html->tipoDocumento[$dte['tipo_documento']],
																		'fullbase' => true,
																		'data-toggle' => 'tooltip',
																		'data-placement' => 'top',
																		'data-original-title' => 'Ver ' . $this->Html->tipoDocumento[$dte['tipo_documento']],
																		'escape' => false) 
																	); ?>
																<? endif; ?>

																<?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('controller' => 'ordenes', 'action' => 'editar', $dte['id'], $this->request->data['Venta']['id']), array('class' => 'btn btn-info btn-xs', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>

		                                                        <? if($permisos['delete']) : ?>
		                                                        <!--<li>
																	<?= $this->Html->link('<i class="fa fa-undo"></i> Invalidar', array('controller' => 'ordenes','action' => 'invalidar', $dte['id'], $this->request->data['Venta']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Invalidar este registro', 'escape' => false)); ?>
																</li>-->
																	<? if ($dte['estado'] != 'dte_real_emitido') : ?>
																		<?= $this->Html->link('<i class="fa fa-trash"></i> Eliminar', array('controller' => 'ordenes','action' => 'delete_dte', $dte['id'], $this->request->data['Venta']['id']), array('class' => 'btn btn-danger btn-xs', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
																	<? endif; ?>
																<? endif; ?>                                                  
				                                                    </ul>
				                                                </div>
															</td>
														</tr>
												<?php
														endforeach;
													endif;
												?>

											</tbody>

										</table>

									</div>

								</div>

							</div>

						</div>

					</div>

					<?php //-------------------------------------------------- Tab Envio -------------------------------------------------- ?>
					<?php 
						if (isset($venta['Envio'])) { ?>

							<div class="tab-pane panel-body" id="tab-envio">
								<div class="row">
									<div class="col-xs-12">
										<!--<? if ( $venta['Marketplace']['marketplace_tipo_id'] == 1 && !$venta['Venta']['paquete_generado']) : ?>
											<button class="btn btn-lg pull-right btn-warning" data-toggle="modal" data-target="#ModalPaquete"><i class="fa fa-cogs"></i> Listo para envio</button>
											<br><br>
										<? elseif( $venta['Marketplace']['marketplace_tipo_id'] == 1 && $venta['Venta']['paquete_generado'] ) : ?>
											<?= $this->Html->link('<i class="fa fa-download"></i>', array('action' => 'obtener_etiqueta', $venta['Venta']['id'] ), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Descargar', 'escape' => false, 'target' => '_blank')); ?>
											<br><br>
										<? endif; ?>-->
										<? if (!empty($venta['VentaExterna']['tipodocumentos'])) : ?>
											<div class="form-group pull-right">
												<div class="btn-group">
				                                    <a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle" aria-expanded="true">Documentos <span class="caret"></span></a>
				                                    <ul class="dropdown-menu" role="menu">
				                                        <?php foreach ($venta['VentaExterna']['tipodocumentos'] as $documento) : ?>
												    	
												    	<li><?= $this->Html->link('<i class="fa fa-download"></i> ' . $documento, array('action' => 'obtener_etiqueta', $venta['Venta']['id'], $documento ), array('class' => '', 'rel' => 'tooltip', 'title' => 'Descargar', 'escape' => false, 'target' => '_blank')); ?></li>
												    	
												    <?php endforeach ?>                                               
				                                    </ul>
				                                </div>
				                            </div>

										<? endif; ?>
									</div>
									<div class="col-xs-12">
										<div class="table-responsive">
											<table class="table table-bordered">
												<thead>
													<th>Tipo de Servicio</th>
													<th>Estado</th>
													<th>Dirección entrega</th>
													<th>Nombre del receptor</th>
													<th>Fono del receptor</th>
													<th>Costo del despacho</th>
													<th>Fecha de entrega estimada</th>
													<th>Comentario del comprador</th>
													<th>Descargas</th>
												</thead>
												<tbody>
													<? foreach($venta['Envio'] as $i => $envio) : ?>
													<tr>
														<td><?=$envio['tipo'];?></td>
														<td><label class="label label-xs label-info"><?=$envio['estado'];?></label></td>
														<td><?=$envio['direccion_envio'];?></td>
														<td><?=$envio['nombre_receptor'];?></td>
														<td><?=$envio['fono_receptor'];?></td>
														<td><?=$envio['costo'];?></td>
														<td><?=$envio['fecha_entrega_estimada'];?></td>
														<td><?=$envio['comentario'];?></td>
														<td>
														<? if ($envio['mostrar_etiqueta']) : ?>
															<?= $this->Html->link('<i class="fa fa-download"></i>', array('action' => 'obtener_etiqueta', $venta['Venta']['id'] , $envio['id'], 'ext' => 'pdf'), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Descargar', 'escape' => false, 'target' => '_blank')); ?>
														<? endif; ?>
														</td>
													</tr>
													<? endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>

						<?php 
						
						} // fin Envio
						
						?>
					

					<?php //-------------------------------------------------- Tab transporte -------------------------------------------------- ?>
					<div class="tab-pane panel-body" id="tab-transporte">
						<div class="row mb-5 mt-5">
							<div class="col-xs-12 col-md-6">
								<h3><i class="fa fa-truck"></i> Transporte externo</h3>
							</div>
							<div class="col-xs-12 col-md-6">
								<div class="btn-group pull-right">
									<? if (!empty($enviame_info)) : ?>
									<a href="<?=$enviame_info['label']['PDF'];?>" target="_blank" class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Etiqueta Envíame</a>
									<a href="<?=$enviame_info['links'][2]['href'];?>" target="_blank" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Seguimiento</a>
									<? endif; ?>
								</div>
							</div>
						</div>
						<? if (!empty($enviame_info)) : ?>
						<div class="row mt-5">
							<div class="col-xs-12 col-md-6">
								<div class="table-responsive">
									<table class="table table-bordered">
										<caption>Información del Envio</caption>
										<tr>
											<th>Identificador Envíame</th>
											<td><?=$enviame_info['identifier']?></td>
										</tr>
										<tr>
											<th>Identificador Interno</th>
											<td><?=$enviame_info['imported_id']?></td>
										</tr>
										<tr>
											<th>Estado del envio</th>
											<td><?=$enviame_info['status']['name']?></td>
										</tr>
										<tr>
											<th>Carrier</th>
											<td><?=$enviame_info['carrier']?></td>
										</tr>
										<tr>
											<th>N° Seguimiento</th>
											<td><?=$enviame_info['tracking_number']?></td>
										</tr>
										<tr>
											<th>Fecha creación</th>
											<td><?=$enviame_info['created_at']?></td>
										</tr>
										<tr>
											<th>Entrega aproximada</th>
											<td><?=$enviame_info['deadline_at']?></td>
										</tr>
									</table>
								</div>
							</div>
							<div class="col-xs-12 col-md-6">
								<div class="table-responsive">
									<table class="table table-bordered">
										<caption>Receptor Informado</caption>
										<tr>
											<th>Nombre del receptor</th>
											<td><?=$enviame_info['customer']['full_name']?></td>
										</tr>
										<tr>
											<th>Fono del receptor</th>
											<td><?=$enviame_info['customer']['phone']?></td>
										</tr>
										<tr>
											<th>Email del receptor</th>
											<td><?=$enviame_info['customer']['email']?></td>
										</tr>
										<tr>
											<th>Dirección de entrega</th>
											<td><?=$enviame_info['shipping_address']['full_address']?></td>
										</tr>
										<tr>
											<th>Comuna de entrega</th>
											<td><?=$enviame_info['shipping_address']['place']?></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<? else : ?>
						<div class="row mt-5">
							<div class="col-xs-12">
								<?= $this->Html->link('Crear Envio en Envíame', array('action' => 'generar_envio_externo_manual', $venta['Venta']['id']), array('class' => 'btn btn-lg btn-success btn-block')); ?>
							</div>
						</div>
						<? endif; ?>
					</div>
			    </div>

			</div>
		</div>
	</div>
	
	<? if ($permisos['change_state']) : ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-danger">
				<?= $this->Form->create('Venta', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
				<?=$this->Form->input('id');?>
				<?=$this->Form->hidden('id_externo', array('value' => $this->request->data['Venta']['id_externo'])); ?>
				<?=$this->Form->hidden('atendida', array('value' => 0)); ?>
				<?=$this->Form->hidden('tienda_id', array('value' => $this->request->data['Venta']['tienda_id'])); ?>
				<?=$this->Form->hidden('marketplace_id', array('value' => $this->request->data['Venta']['marketplace_id'])); ?>
				<?=$this->Form->hidden('venta_estado_id_actual', array('value' => $this->request->data['Venta']['venta_estado_id'])); ?>

				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?=__('Cambiar estado'); ?></h3>
				</div>
				
				<!-- Prestashop -->
				<? if (empty($this->request->data['Venta']['marketplace_id'])) : ?>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('venta_estado_id', 'Estado de la venta'); ?></th>
								<td><?= $this->Form->input('venta_estado_id'); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<? endif; ?>


				<!-- Linio -->
				<? if ($this->request->data['Marketplace']['marketplace_tipo_id'] == 1) : ?>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('venta_estado_id', 'Estado de la venta'); ?></th>
								<td><?= $this->Form->input('venta_estado_id'); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<? endif; ?>


				<!-- Mercadolibre -->
				<? if ($this->request->data['Marketplace']['marketplace_tipo_id'] == 2) : ?>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('venta_estado_id', 'Estado de la venta'); ?></th>
								<td><?= $this->Form->input('venta_estado_id'); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<? endif; ?>


				<div class="panel-footer">
					<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Actualizar Estado">
					<?=$this->Html->link('<i class="fa fa-send"></i> Re-enviar email', array('controller' => 'ventas', 'action' => 'enviar_email_estado', $venta['Venta']['id']), array('class' => 'btn btn-success', 'escape' => false) );?>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
	<? endif; ?>
</div>


<!-- MESSAGE BOX-->
<div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="modal_alertas">
    <div class="mb-container">
        <div class="mb-middle">
            <div class="mb-title" id="modal_alertas_label"><i class="fa fa-exclamation-triangle"></i> Generar documentos</div>
            <div class="mb-content">
                <p id="mensajeModal" style="margin: 15px 0;">
                	No se ha creado DTE para esta venta. La herramienta intentará crear el DTE con la información proporcionada en la venta. <br>
                	Errores comunes: Venta no tiene productos cargados, la información de facturacón no está completa, etc.
                </p>                
                <p id="mensajeModal" style="margin: 15px 0;">
                	<b>¿Deseas intentar crearlo automáticamente?</b>
                </p>
            </div>
            <div class="mb-footer">
                <div class="btn-group">
                	<?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> Sí, crear dte y continuar.', array('controller' => 'ventas', 'action' => 'generar_documentos', $venta['Venta']['id'], true, true), array('class' => 'btn btn-success btn-lg js-generar-documentos-venta', 'rel' => 'tooltip', 'title' => 'Generar Documentos', 'escape' => false)); ?>
                	<?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> No, solo continuar.', array('controller' => 'ventas', 'action' => 'generar_documentos', $venta['Venta']['id'], true, false), array('class' => 'btn btn-primary btn-lg js-generar-documentos-venta js-generar-documentos-venta-primario', 'rel' => 'tooltip', 'title' => 'Generar Documentos', 'escape' => false)); ?>
                    <button class="btn btn-default btn-lg mb-control-close">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MESSAGE BOX-->

<!-- Modal seguimiento -->
<div class="modal fade" id="modalRegistrarSeguimiento" tabindex="-1" role="dialog" aria-labelledby="modalRegistrarSeguimiento">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<?= $this->Form->create('Venta', array('url' => array('action' => 'registrar_seguimiento'), 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
    	<?= $this->Form->hidden('created', array('value' => date('Y-m-d H:i:s')));?>
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="modalRegistrarSeguimiento"><i class="fa fa-truck"></i> Registrar seguimiento</h4>
	      </div>
	      
	      <div class="modal-body">
	      	<div class="form-group col-xs-12">
	      		<?=$this->Form->label('transporte_id', 'Transportista');?>
				<?=$this->Form->select('transporte_id', $transportes, array('empty' => 'Seleccione', 'class' => 'form-control not-blank')); ?>
	      	</div>
	      	<div class="form-group col-xs-12">
	      		<?=$this->Form->label('cod_seguimiento', 'N° Seguimiento');?>
				<?=$this->Form->input('cod_seguimiento', array('placeholder' => 'Ej: 1113411121212', 'class' => 'form-control not-blank')); ?>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="submit" class="btn btn-primary">Guardar</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
	      </div>
      	<?= $this->Form->end(); ?>
    </div>
  </div>
</div>
<!-- Fin modal seguimiento -->

<!-- Modal -->
<? if (!empty($venta['VentaExterna']['curriers'])) : ?>
<div class="modal fade" id="ModalPaquete" tabindex="-1" role="dialog" aria-labelledby="modalCrearPaquete">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<?= $this->Form->create('Venta', array(
      		'url' => array('action' => 'linio_generar_paquete'),
      		'method' => 'post',
      		'class' => 'form-horizontal', 
      		'type' => 'file', 
      		'inputDefaults' => array(
      			'label' => false, 
      			'div' => false, 
      			'class' => 'form-control'
      			)
      		)
      	); ?>
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="modalCrearPaquete"><i class="fa fa-cube"></i> Crear paquete</h4>
	      </div>
	      
	      <div class="modal-body">

			<?

			$idsItems = json_encode(Hash::extract($venta['VentaExterna']['Products'], '{n}.OrderItemId'));
			$delivery = json_encode(array_unique(Hash::extract($venta['VentaExterna']['Products'], '{n}.ShippingType')));

			echo $this->Form->hidden('OrderItemIds', array('value' => $idsItems));
			echo $this->Form->hidden('DeliveryType', array('value' => $delivery));

			?>

	      	<div class="form-group col-xs-12">
				<label>Transportistas Disponibles</label>
				<select name="data[Venta][ShippingProvider]" class="form-control">
					<?php foreach ($venta['VentaExterna']['curriers'] as $iq => $currier): ?>
						<option value="<?=$currier['Name']?>" <?=($currier['Cod']) ? 'selected' : '';?>><?=$currier['Name']?></option>
					<?php endforeach ?>
				</select>
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="submit" class="btn btn-primary">Crear paquete y continuar</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
	      </div>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>
<? endif; ?>