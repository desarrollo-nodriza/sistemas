<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Ver OC generadas</h2>
</div>

<div class="page-content-wrap">
	<? foreach ($ocs as $oc) : ?>

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title text-uppercase"><i class="fa fa-file"></i> OC para <b><?=$oc['Proveedor']['nombre'];?></b></h3>
				</div>
				<div class="panel-body">
					
					<? 

					$estado = $this->Html->estadosOc($oc['OrdenCompra']['estado']);
					$estadoOpts = $this->Html->estadoOcOpt($oc['OrdenCompra']['estado']);

					?>

					<div class="row">
						<div class="col-xs-12">
							<div class="alert alert-lg" style="background-color: <?=$estadoOpts['bgr']; ?>" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong style=" color:<?= $estadoOpts['txt']; ?>;">Estado del la OC: <?= '<i class="fa ' . $estadoOpts['ico'] . '"></i> ' . $estado; ?></strong>
                            </div>
						</div>
						<? if (!empty($oc['OrdenCompra']['razon_cancelada'])) : ?>
						<div class="col-xs-12">
							<div class="table-responsive">
								<table class="table table-bordered">
									<tr>
										<th style="width: 300px">Razón o motivo de la cancelación</th>
										<td><?= $this->Text->autoParagraph($oc['OrdenCompra']['razon_cancelada']); ?></td>
									</tr>
								</table>
							</div>
						</div>
                        <? endif; ?>
					</div>
					
					<? if (!empty($oc['OrdenCompra']['comentario_validar'])) : ?>
		
						<div class="row">
							<div class="col-xs-12">
								<div class="panel panel-danger">
									<div class="panel-heading">
										<h3 class="panel-title"><i class="fa fa-comments"></i> Anotación del administrador</h3>
									</div>
									<div class="panel-body">
										<?=$this->Text->autoParagraph($oc['OrdenCompra']['comentario_validar']);?>
									</div>
								</div>
							</div>
						</div>

					<? endif; ?>


					<? if (!empty($oc['OrdenCompra']['comentario_finanza'])) : ?>
						
						<div class="row">
							<div class="col-xs-12">
								<div class="panel panel-danger">
									<div class="panel-heading">
										<h3 class="panel-title"><i class="fa fa-money"></i> Anotación de finanzas</h3>
									</div>
									<div class="panel-body">
										<?=$this->Text->autoParagraph($oc['OrdenCompra']['comentario_finanza']);?>
									</div>
								</div>
							</div>
						</div>

					<? endif; ?>

					<div class="row">
						<? if (!empty($oc['OrdenCompra']['adjunto']['path'])) : ?>
						<div class="col-xs-12 col-sm-6">
							<div class="panel panel-info">
								<div class="panel-heading">
									<h3 class="panel-title"><i class="fa fa-file"></i> Documento Finanzas</h3>
								</div>
								<div class="panel-body">
									<?= $this->Html->link(
									'<i class="fa fa-eye"></i> Comprobante de pago',
									sprintf('/img/%s', $oc['OrdenCompra']['adjunto']['path']),
									array(
										'class' => 'btn btn-info btn-lg btn-block', 
										'target' => '_blank', 
										'fullbase' => true,
										'escape' => false) 
									); ?>
								</div>
							</div>
						</div>
						<? endif; ?>

						<? if (!empty($oc['OrdenCompra']['pdf'])) : ?>
						<div class="col-xs-12 <?= (!empty($oc['OrdenCompra']['adjunto']['path'])) ? 'col-sm-6' : ''; ?>">
							<div class="panel panel-info">
								<div class="panel-heading">
									<h3 class="panel-title"><i class="fa fa-file"></i> Documento OC</h3>
								</div>
								<div class="panel-body">
									<?= $this->Html->link(
									'<i class="fa fa-eye"></i> OC en PDF',
									sprintf('/Pdf/OrdenCompra/%d/%s', $oc['OrdenCompra']['id'], $oc['OrdenCompra']['pdf']),
									array(
										'class' => 'btn btn-success btn-lg btn-block', 
										'target' => '_blank',
										'escape' => false) 
									); ?>
								</div>
							</div>
						</div>
						<? endif; ?>
					</div>


					<div class="table-responsive">
						<table class="table">
							<tr>
								<td>
									<table class="table table-bordered">
										<tr>
											<td colspan="2"><b>Datos de la empresa</b></td>
										</tr>
										<tr>
											<td>Rut empresa: </td>
											<td><?=$oc['OrdenCompra']['rut_empresa'];?></td>
										</tr>
										<tr>
											<td>Razón Social: </td>
											<td><?=$oc['OrdenCompra']['razon_social_empresa'];?></td>
										</tr>
										<tr>
											<td>Giro: </td>
											<td><?=$oc['OrdenCompra']['giro_empresa'];?></td>
										</tr>
										<tr>
											<td>Nombre de contacto: </td>
											<td><?=$oc['OrdenCompra']['nombre_contacto_empresa']?></td>
										</tr>
										<tr>
											<td>Email: </td>
											<td><?=$oc['OrdenCompra']['email_contacto_empresa'];?></td>
										</tr>
										<tr>
											<td>Teléfono: </td>
											<td><?=$oc['OrdenCompra']['fono_contacto_empresa']?></td>
										</tr>
										<tr>
											<td>Dirección comercial: </td>
											<td><?=$oc['OrdenCompra']['direccion_comercial_empresa'];?></td>
										</tr>
									</table>
								</td>
								<td>
									<table class="table table-bordered">
										<tr>
											<td colspan="2"><b>Despacho</b></td>
										</tr>
										<tr>
											<td>Fecha: </td>
											<td><?=$oc['OrdenCompra']['fecha'];?></td>
										</tr>
										<tr>
											<td>Forma de pago: </td>
											
											<td><?=$oc['Moneda']['nombre'];?></td>
											
										</tr>
										<tr>
											<td>Vendedor: </td>
											<td><?=$oc['OrdenCompra']['vendedor'];?></td>
										</tr>
										<tr>
											<td>Descuento: </td>
											<td><?=$oc['OrdenCompra']['descuento'];?></td>
										</tr>

										<tr>
											<td>Bodega: </td>
											<td>
												<div class="form-group">
													<?= $this->Form->create('OrdenCompras', array('url' => array('action' => 'view', $oc['OrdenCompra']['id']), 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
													<?= $this->Form->input('id',['default'=> $oc['OrdenCompra']['id'],'type'=>'hidden']);?>
													<?= $this->Form->select('bodega_id', $bodegas , array('required','class' => 'form-control select not-blank','disabled' => !in_array($oc['OrdenCompra']['estado'],$estados) ,'default' =>$oc['OrdenCompra']['bodega_id'],'empty' => false));?>
													<?= $this->Form->button('Guardar cambios', array('disabled' => !in_array($oc['OrdenCompra']['estado'],$estados),'type' 	=> 'submit', 'class' => 'btn btn-warning btn-block mt-5', 'id'	=> 'ModificarBodega')); ?> 
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						
						<table class="table table-bordered js-clone-wrapper">
							<thead>
								<th>Item</th>
								<th>Código</th>
								<th>Descripción</th>
								<th>Cantidad pedida</th>
								<th>Cantidad validada proveedor</th>
								<th>Cantidad Recibida</th>
								<th>N. Unitario</th>
								<th>Descuento ($)</th>
								<th>Total Neto</th>
								<th>Estado proveedor</th>
								<th>Comentario proveedor</th>
							</thead>
							<tboby class="">
							<? foreach ($oc['VentaDetalleProducto'] as $ipp => $ocp) : ?>	
								
								<tr class="<?= ($ocp['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor'] == $ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida'] && ($ocp['OrdenComprasVentaDetalleProducto']['estado_proveedor'] == 'accept' || $ocp['OrdenComprasVentaDetalleProducto']['estado_proveedor'] == 'modified')) ? 'success' : 'danger' ;?>" >
									<td><?=$ocp['id'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['codigo'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida'];?></td>
									<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['precio_unitario'] , 'CLP');?></td>
									<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['descuento_producto'] , 'CLP');?></td>
									<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['total_neto'] , 'CLP');?></td>
									<td><label class="label label-default"><?= (empty($ocp['OrdenComprasVentaDetalleProducto']['estado_proveedor'])) ? 'No aplica' : $ocp['OrdenComprasVentaDetalleProducto']['estado_proveedor'] ; ?></label></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['nota_proveedor']; ?></td>
								</tr>
								
							<? endforeach; ?>
							
							</tboby>
							<tfoot>
								<tr>
									<td colspan="7"></td>
									<td>Total neto</td>
									<td><?=CakeNumber::currency($oc['OrdenCompra']['total_neto'] , 'CLP');?></td>
									<td colspan="2"></td>
								</tr>
								<tr>
									<td colspan="7"></td>
									<td>Total Descuento</td>
									<td><?=CakeNumber::currency($oc['OrdenCompra']['descuento_monto'] , 'CLP');?></td>
									<td colspan="2"></td>
								</tr>
								<tr>
									<td colspan="7"></td>
									<td>IVA</td>
									<td><?=CakeNumber::currency($oc['OrdenCompra']['iva'] , 'CLP');?></td>
									<td colspan="2"></td>
								</tr>
								<tr>
									<td colspan="7"></td>
									<td>Total</td>
									<td><?=CakeNumber::currency($oc['OrdenCompra']['total'] , 'CLP');?></td>
									<td colspan="2"></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-file" aria-hidden="true"></i> <?=__('Documentos');?></h5>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('Documentos recibidos en ésta OC'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Tipo');?></th>
									<th><?= __('Folio');?></th>
									<th><?= __('Emisor');?></th>
									<th><?= __('Monto facturado');?></th>
									<th><?= __('Monto pagado');?></th>
									<th><?= __('Nota interna');?></th>
								</tr>
							</thead>
							<tbody class="">

								<? if (!empty($oc['OrdenCompraFactura'])) :  ?>
								<? foreach($oc['OrdenCompraFactura'] as $ip => $dte) : ?>
								<tr>
									<td>
										<?=$this->Html->link('<i class="fa fa-eye"></i> ' . $this->Html->tipoDocumento[$dte['tipo_documento']], array('controller' => 'ordenCompraFacturas', 'action' => 'view', $dte['id']), array('target' => '_blank', 'escape' => false)); ?>
									</td>
									<td>
										#<?= $dte['folio']; ?>
									</td>
									<td>
										<?= $dte['emisor']; ?>
									</td>
									<td>
										<?= CakeNumber::currency($dte['monto_facturado'], 'CLP'); ?>
									</td>
									<td>
										<?= CakeNumber::currency($dte['monto_pagado'], 'CLP'); ?>
									</td>
									<td>
										<?=$dte['nota'];?>
									</td>
								</tr>
								<? endforeach; ?>
								<? endif; ?>
								
							</tbody>
						</table>
					</div>
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
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-list" aria-hidden="true"></i> <?=__('Histórico');?></h5>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('Historico de cambios de la OC'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Estado');?></th>
									<th><?= __('Responsable');?></th>
									<th><?= __('Fecha del cambio');?></th>
									<th><?= __('Evidencia');?></th>
								</tr>
							</thead>
							<tbody class="">

								<? if (!empty($oc['OrdenCompraHistorico'])) :  ?>
								<? foreach($oc['OrdenCompraHistorico'] as $ip => $historico) : ?>
								<tr>
									<td>
										<?= $this->Html->estadosOc($historico['estado']); ?>
									</td>
									<td>
										<?=h($historico['responsable']);?>
									</td>
									<td>
										<?=$historico['created'];?>
									</td>
									<td>

									<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalHistorico<?=$historico['id'];?>">
										<i class="fa fa-eye"></i> Ver
									</button>
										
									<div class="modal fade" id="modalHistorico<?=$historico['id'];?>" tabindex="-1" role="dialog" aria-labelledby="modalHistorico<?=$historico['id'];?>Label">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="modalHistorico<?=$historico['id'];?>Label">Evidencia histórico <?=$this->Html->estadosOc($historico['estado']);?> - <?=$historico['created']; ?></h4>
											</div>
											<div class="modal-body">
												<pre><?print_r(json_decode($historico['evidencia'], true)); ?></pre>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											</div>
											</div>
										</div>
									</div>

									</td>
								</tr>
								<? endforeach; ?>
								<? endif; ?>
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<? endforeach; ?>
</div>
