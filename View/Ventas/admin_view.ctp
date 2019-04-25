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
			    		<li><a href="#tab-envio" data-toggle="tab"><i class="fa fa-truck"></i> Envio</a></li>
			    	<?php endif; ?>
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
											<th>Dirección despacho</th>
											<td><?= $venta['Venta']['direccion_entrega']; ?></td>
										</tr>
										<tr>
											<th>Comuna despacho</th>
											<td><?= $venta['Venta']['comuna_entrega']; ?></td>
										</tr>
										<tr>
											<th>Teléfono despacho</th>
											<td><?= $venta['Venta']['fono_receptor']; ?></td>
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

					</div> <!-- end col -->
					

					
					<div class="col-xs-12 col-sm-4">

						<!-- TOTAL VENTA -->
						<a class="tile tile-primary">
			                <?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?>
			                <p><?=__('Total documento');?></p>
			            </a>
							
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

								<li class="list-group-item"><span class="fa fa-envelope"></span> <?= (!empty($venta['VentaCliente']['email'])) ? $venta['VentaCliente']['email'] : 'xxxxx@xxxx.xx'; ?></li>

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

					</div>

					<?php //-------------------------------------------------- Tab dte's asociados -------------------------------------------------- ?>
			    	<div class="tab-pane panel-body" id="tab-dtes">

			    		<div class="container-fluid" style="padding: 0;">

				    		<div class="row">

				    			<div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0;">
									
									<? if($permisos['edit']) : ?>
						    		<div class="btn-group pull-right">
						    			<?= $this->Html->link('<i class="fa fa-file"></i> Generar Dte para esta Orden', array('controller' => 'ordenes', 'action' => 'generar', $venta['Venta']['id']), array('class' => 'btn btn-warning', 'rel' => 'tooltip', 'title' => 'Generar Dte', 'escape' => false)); ?>
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
															<?= $this->Html->link('<i class="fa fa-download"></i>', array('action' => 'obtener_etiqueta', $venta['Venta']['id'] , $envio['id']), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Descargar', 'escape' => false, 'target' => '_blank')); ?>
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
				<div class="panel-footer">
					<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Actualizar Estado">
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
	<? endif; ?>
</div>


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