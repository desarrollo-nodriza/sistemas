<div class="container">
	<div class="row">
		<div class="col-12">
			<ul class="nav nav-tabs justify-content-between" id="tabVenta" role="tablist">
			  <li class="nav-item flex-fill">
			    <a class="nav-link <?=($tab_activo == 'venta') ? 'active' : ''; ?> w-100 py-3 font-weight-light" id="venta-tab" data-toggle="tab" href="#venta" role="tab" aria-controls="venta" aria-selected="true"><i class="fa fa-money-bill-alt mr-2"></i> Venta</a>
			  </li>
			  <li class="nav-item flex-fill">
			    <a class="nav-link <?=($tab_activo == 'dte') ? 'active' : ''; ?> w-100 py-3 font-weight-light" id="dte-tab" data-toggle="tab" href="#dte" role="tab" aria-controls="dte" aria-selected="false"><i class="fa fa-file-invoice-dollar mr-2"></i> DTE</a>
			  </li>
			  <li class="nav-item flex-fill">
			    <a class="nav-link <?=($tab_activo == 'envio') ? 'active' : ''; ?> w-100 py-3 font-weight-light" id="envio-tab" data-toggle="tab" href="#envio" role="tab" aria-controls="envio" aria-selected="false"><i class="fa fa-shipping-fast mr-2"></i> Envio & Seguimiento</a>
			  </li>
			  <li class="nav-item flex-fill">
			    <a class="nav-link <?=($tab_activo == 'mensajes') ? 'active' : ''; ?> w-100 py-3 font-weight-light" id="mensajes-tab" data-toggle="tab" href="#mensajes" role="tab" aria-controls="mensajes" aria-selected="false"><i class="fa fa-comments mr-2"></i> Mensajes</a>
			  </li>
			</ul>


			<div class="tab-content" id="tabVentaContent">
			  <div class="tab-pane fade bg-white <?=($tab_activo == 'venta') ? 'show active' : ''; ?>" id="venta" role="tabpanel" aria-labelledby="venta-tab">
			  	<div class="row">
					<div class="col-12">
						<div class="table-reponsive">
							<table class="table table-bordered">
								<tr>
									<td>Identificador único</td>
									<td><?=$venta['Venta']['id']; ?></td>
								</tr>
								<tr>
									<td>Código de referencia</td>
									<td><?=$venta['Venta']['referencia']; ?></td>
								</tr>
								<tr>
									<td>Fecha de la venta</td>
									<td><?= date_format(date_create($venta['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?></td>
								</tr>
								<tr>
									<td>Estado de la venta</td>
									<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-sm text-white btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a></td>
								</tr>
								<tr>
									<td>Estado del paquete</td>
									<td><span class="btn btn-sm" style="color: #fff; background-color: <?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['color'];?>"><?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['label'];?></span> <span class="text-muted"><?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['leyenda'];?>.</span></td>
								</tr>
								<tr>
									<td>Medio de pago</td>
									<td><?= $venta['MedioPago']['nombre']; ?></td>
								</tr>
								<tr>
									<th>Tienda</th>
									<td><?= $venta['Tienda']['nombre']; ?></td>
								</tr>
								<? if (!empty($venta['Venta']['marketplace_id'])) : ?>
								<tr>
									<th>Marketplace</th>
									<td><?= $venta['Marketplace']['nombre']; ?>&nbsp;</td>
								</tr>
								<? endif; ?>
								<? if (!empty($venta['Venta']['fecha_entregado'])) : ?>
								<tr>
									<th>Fecha entregado</th>
									<td><?= date_format(date_create($venta['Venta']['fecha_entregado']), 'd/m/Y H:i:s'); ?></td>
								</tr>
								<? endif; ?>
							</table>
							<table class="table table-bordered">
								<thead>
									<th>Producto</th>
									<th>Precio</th>
									<th>Cantidad</th>
									<th>Cant anulada</th>
									<th>Subtotal</th>
								</thead>
								<tbody>
									<?php foreach ($venta['VentaDetalle'] as $indice => $detalle) :  ?>
										<tr class="<?= ($detalle['cantidad'] == $detalle['cantidad_anulada']) ? 'danger' : '' ; ?>" >
											<td data-toggle="tooltip" title="<?=$detalle['VentaDetalleProducto']['nombre'];?>" class="td-producto">
												<? if (!empty($detalle['VentaDetalleProducto']['imagenes'])) : ?>
												<img src="<?=Hash::extract($detalle['VentaDetalleProducto']['imagenes'], '{n}[principal=1].url')[0]; ?>" class="img-responsive producto-td-imagen mx-auto mr-md-2">
												<? endif; ?>
												<?= $detalle['VentaDetalleProducto']['nombre']; ?>
											</td>
											<td>
												<?= CakeNumber::currency(monto_bruto($detalle['precio']), 'CLP'); ?>
											</td>
											<td>
												<?= number_format($detalle['cantidad'], 0, ".", "."); ?>
											</td>
											<td>
												<?=$detalle['cantidad_anulada'];?>
											</td>
											<td>
												<?= CakeNumber::currency(monto_bruto($detalle['total_neto']), 'CLP'); ?>
											</td>
										</tr>
									<? endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="4" class="text-right">Descuento</th>
										<td>
											<?php if (!empty($venta['Venta']['descuento'])) {echo CakeNumber::currency($venta['Venta']['descuento'], 'CLP');} ?>
										</td>
									</tr>
									<tr>
										<th colspan="4" class="text-right">Transporte</th>
										<td>
											<?php if (!empty($venta['Venta']['costo_envio'])) {echo CakeNumber::currency($venta['Venta']['costo_envio'], 'CLP');} ?>
										</td>
									</tr>
									<tr class="success">
										<th colspan="4" class="text-right" style="font-size: 22px;">Total</th>
										<td style="font-size: 22px;"><?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
			  	</div>
			  </div>
			  
			  <div class="tab-pane fade bg-white <?=($tab_activo == 'dte') ? 'show active' : ''; ?>" id="dte" role="tabpanel" aria-labelledby="dte-tab">
			  	<div class="row">
					<div class="col-12">
						<div class="table-reponsive">
							<table class="table table-bordered">	
								<thead>
									<tr class="sort">
										<th>Folio</th>
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
														'<i class="fa fa-file mr-1"></i> Ver ' . $this->Text->truncate($this->Html->tipoDocumento[$dte['tipo_documento']], 15),
														sprintf('/Dte/%d/%d/%s', $venta['Venta']['id'], $dte['id'], $dte['pdf']),
														array(
															'class' => 'btn btn-success btn-sm', 
															'target' => '_blank',
															'title' => 'Ver ' . $this->Html->tipoDocumento[$dte['tipo_documento']],
															'fullbase' => true,
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
															'data-original-title' => 'Ver ' . $this->Html->tipoDocumento[$dte['tipo_documento']],
															'escape' => false) 
														); ?>
													<? endif; ?>
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
			  
			  <div class="tab-pane fade bg-white <?=($tab_activo == 'envio') ? 'show active' : ''; ?>" id="envio" role="tabpanel" aria-labelledby="envio-tab">
				<div class="row">
					<div class="col-12">
						<table class="table table-bordered">
							<tr>
								<td>Método de envio</td>
								<td>
								<? if (!empty($venta['MetodoEnvio'])) : ?>
									<span class="btn btn-sm btn-info"><?= $venta['MetodoEnvio']['nombre']; ?></span>
								<? else : ?>
									<span class="btn btn-sm btn-warning"><?= __('No obtenido');?></span>
								<? endif; ?>
								</td>
							</tr>
							
							<tr>
								<td>Receptor informado</td>
								<td><?= (!empty($venta['Venta']['nombre_receptor'])) ? $venta['Venta']['nombre_receptor'] : $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido'] ; ?></td>
							</tr>
							
							<? if (!$venta['MetodoEnvio']['retiro_local']) : ?>
								<? if (!empty($venta['Venta']['transporte_id'])) : ?>
								<tr>
									<td>Transporte usado</td>
									<td><?= $venta['Transporte']['nombre']; ?></td>
								</tr>
								<? endif; ?>
								<tr>
									<td>Comuna informada</td>
									<td><?=$venta['Venta']['comuna_entrega']; ?></td>
								</tr>
								<tr>
									<td>Dirección informada</td>
									<td><?=$venta['Venta']['direccion_entrega']; ?></td>
								</tr>
								<tr>
									<td>Teléfono informado</td>
									<td><?= $venta['Venta']['fono_receptor']; ?></td>
								</tr>
							<? endif; ?>
							<? if (!empty($venta['Venta']['fecha_entregado'])) : ?>
							<tr>
								<td>Fecha entregado</td>
								<td><?= date_format(date_create($venta['Venta']['fecha_entregado']), 'd/m/Y H:i:s'); ?></td>
							</tr>
							<? endif; ?>
							<? if (!empty($venta['Venta']['ci_receptor'])) : ?>
							<tr>
								<td>Cédula de identidad del receptor</td>
								<td>
									<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#cimodal"><i class="fa fa-file"></i> Ver cédula</button>

									<!-- Modal -->
									<div class="modal fade" id="cimodal" tabindex="-1" role="dialog" aria-labelledby="modalCI">
									  <div class="modal-dialog modal-lg" role="document">
									    <div class="modal-content">
									      <div class="modal-header bg-success">
									        <h5 class="modal-title font-weight-light text-white"><i class="fa fa-id-card mr-2"></i> Cédula de identidad del receptor</h5>
									        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									          <span aria-hidden="true">&times;</span>
									        </button>
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
						</table>
						<? if (!$venta['MetodoEnvio']['retiro_local']) : ?>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th><?=__('Transportista');?></th>
									<th><?=__('N° de seguimiento');?></th>
									<th><?=__('Etiqueta');?></th>
									<th><?=__('Plazo entrega aprox');?></th>
									<th><?=__('Seguimiento');?></th>
								</tr>
							</thead>
							<tbody class="">
								<? if (!empty($venta['Transporte'])) : ?>
									<? foreach ($venta['Transporte'] as $it => $transporte) : ?>
										<tr>
											<td><?=$transporte['nombre'];?></td>
											<td><?=$transporte['TransportesVenta']['cod_seguimiento'];?></td>
											<td>
												<? if (!empty($transporte['TransportesVenta']['etiqueta'])) : ?>
												<a href="<?=$transporte['TransportesVenta']['etiqueta']?>" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-file-pdf-o"></i> Ver</a>
												<? else : ?>													
												No aplica
												<? endif; ?>
											</td>
											<td><span class="js-fecha-entrega"><?=$transporte['tiempo_entrega']; ?></span></td>
											<td><span class="js-btn-seguimiento"><?=$transporte['url_seguimiento']; ?></td>
										</tr>
									<? endforeach; ?>
								<? else : ?>
									<tr>
										<td colspan="6" class="text-center">No registra información</td>
									</tr>
								<? endif; ?>

							</tbody>
						</table>
						<? endif; ?>
					</div>
				</div>
			  </div>

			  <div class="tab-pane fade bg-white <?=($tab_activo == 'mensajes') ? 'show active' : ''; ?>" id="mensajes" role="tabpanel" aria-labelledby="mensajes-tab">
			  		
					<div class="card p-3 rounded-0 border-top-0 border-right-0 border-left-0 border-primary">
						<!-- Enviar mensaje -->
						<?= $this->Form->create('Mensaje', array('url' => array('controller' => 'mensajes', 'action' => 'guardar_mensaje'), 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control js-formulario', 'autocomplete' => false))); ?>					
						<?= $this->Form->hidden('access_token', array('value' => $this->Session->read('Auth.Cliente.token'))); ?>
						<?= $this->Form->hidden('venta_cliente_id', array('value' => $this->Session->read('Auth.Cliente.id'))); ?>
						<?= $this->Form->hidden('autor', array('value' => $this->Session->read('Auth.Cliente.nombre') . ' ' . $this->Session->read('Auth.Cliente.apellido'))); ?>
						<?= $this->Form->hidden('venta_id', array('value' => $venta['Venta']['id'])); ?>

						<div class="form-group">
							<?= $this->Form->label('mensaje', '<i class="fa fa-share mr-2"></i> Nuevo mensaje', array('class' => 'font-weight-light')); ?>
							<?= $this->Form->textarea('mensaje', array('class' => 'form-control not-blank', 'placeholder' => 'Ingresa tu mensaje', 'rows' => 2)); ?>
						</div>
						<?= $this->Form->button('<i class="fa fa-send mr-2"></i> Enviar mensaje', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
						<?= $this->Form->end(); ?>
					</div>

					<? if (!empty($venta['Mensaje'])) : ?>
					<? foreach ($venta['Mensaje'] as $im => $mensaje) : ?>
					<div class="border-bottom border-primary mb-3 p-4">
						<div id="message-<?=$mensaje['id'];?>" class="card-message d-flex flex-column border-bottom pb-4">
					    	<h5 class="author mr-2 font-weight-light text-muted"><i class="fa text-primary fa-share mr-2"></i> <?= ($mensaje['origen'] == 'cliente') ? 'Yo' : $mensaje['autor'] ;?> - <small class="text-muted font-weight-light"><?=$mensaje['created'];?></small></h5>
					    	<p><?=$mensaje['mensaje'];?></p>
						</div>
						
						<? if (!empty($mensaje['RespuestaMensaje'])) : ?>
							<? foreach ($mensaje['RespuestaMensaje'] as $ir => $respuesta) : ?>
								<div id="message-<?=$respuesta['id'];?>" class="card-message d-flex flex-column text-right pt-4">
							    	<h5 class="author mr-2 font-weight-light text-muted"><i class="fa text-info fa-reply mr-2"></i> Res: <?= ($respuesta['origen'] == 'cliente') ? 'Yo' : $respuesta['autor'] ;?> - <small class="text-muted font-weight-light"><?=$respuesta['created'];?></small></h5>
							    	<p><?=$respuesta['mensaje'];?></p>
								</div>
							<? endforeach; ?>
						<? endif;?>

						<? if (empty($mensaje['RespuestaMensaje']) && $mensaje['origen'] == 'empleado') : ?>
						<div class="card p-3 mb-4">
							<!-- Enviar mensaje -->
							<?= $this->Form->create('Mensaje', array('url' => array('controller' => 'mensajes', 'action' => 'guardar_mensaje'), 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control js-formulario', 'autocomplete' => false))); ?>					
							<?= $this->Form->hidden('access_token', array('value' => $this->Session->read('Auth.Cliente.token'))); ?>
							<?= $this->Form->hidden('venta_cliente_id', array('value' => $this->Session->read('Auth.Cliente.id'))); ?>
							<?= $this->Form->hidden('autor', array('value' => $this->Session->read('Auth.Cliente.nombre') . ' ' . $this->Session->read('Auth.Cliente.apellido'))); ?>
							<?= $this->Form->hidden('venta_id', array('value' => $venta['Venta']['id'])); ?>
							<?= $this->Form->hidden('parent_id', array('value' => $mensaje['id'])); ?>

							<div class="form-group">
								<?= $this->Form->label('mensaje', '<i class="fa fa-reply mr-2"></i> Responder mensaje', array('class' => 'font-weight-light')); ?>
								<?= $this->Form->textarea('mensaje', array('class' => 'form-control not-blank', 'placeholder' => 'Ingresa tu respuesta', 'rows' => 2)); ?>
							</div>
							<?= $this->Form->button('<i class="fa fa-send mr-2"></i> Responder', array('type' => 'submit', 'class' => 'btn btn-primary')); ?>
							<?= $this->Form->end(); ?>
						</div>
						<? endif; ?>

					</div>
					<? endforeach; ?>
					<? else : ?>
						<p>No registra mensajes</p>
					<? endif; ?>
			  </div>
			</div>
		</div>
	</div>
</div>

<?= $this->Html->script(array(
	'/public/js/ventas'
)); ?>
<?= $this->fetch('script-bottom'); ?>