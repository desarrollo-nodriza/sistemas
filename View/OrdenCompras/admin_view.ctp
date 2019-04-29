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

					<div class="row">
						<div class="col-xs-12">
							<div class="alert alert-info" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong>Estado del la OC: </strong> <?= (!empty($oc['OrdenCompra']['estado'])) ? $oc['OrdenCompra']['estado'] : 'No iniciado' ;  ?>
                            </div>
						</div>
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
								<th>Cantidad Recibida</th>
								<th>N. Unitario</th>
								<th>Descuento ($)</th>
								<th>Total Neto</th>
								<th></th>
							</thead>
							<tboby class="">
							<? foreach ($oc['VentaDetalleProducto'] as $ipp => $ocp) : ?>	
								
								<tr class="<?= ($ocp['OrdenComprasVentaDetalleProducto']['cantidad'] == $ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida']) ? 'success' : 'danger' ;?>" >
									<td><?=$ocp['id'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['codigo'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida'];?></td>
									<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['precio_unitario'] , 'CLP');?></td>
									<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['descuento_producto'] , 'CLP');?></td>
									<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['total_neto'] , 'CLP');?></td>
									<td></td>
								</tr>
								
							<? endforeach; ?>
							
							</tboby>
							<tfoot>
								<tr>
									<td colspan="6"></td>
									<td>Total neto</td>
									<td colspan="2"><?=CakeNumber::currency($oc['OrdenCompra']['total_neto'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>Total Descuento</td>
									<td colspan="2"><?=CakeNumber::currency($oc['OrdenCompra']['descuento_monto'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>IVA</td>
									<td colspan="2"><?=CakeNumber::currency($oc['OrdenCompra']['iva'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>Total</td>
									<td colspan="2"><?=CakeNumber::currency($oc['OrdenCompra']['total'] , 'CLP');?></td>
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
							<caption><?= __('Folios y tipos de documentos'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Folio');?></th>
									<th><?= __('Tipo');?></th>
									<th><?= __('Comentario');?></th>
								</tr>
							</thead>
							<tbody class="">

								<? if (!empty($oc['OrdenCompra']['meta_dtes'])) :  ?>
								<? foreach($oc['OrdenCompra']['meta_dtes'] as $ip => $dte) : ?>
								<tr>
									<td>
										#<?= $dte['folio']; ?>
									</td>
									<td>
										<?=$dte['tipo'];?>
									</td>
									<td>
										<?=$dte['comentario'];?>
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