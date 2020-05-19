<div class="page-title">
	<h2><span class="fa fa-file"></span> <?=$this->Html->tipoDocumento[$this->request->data['OrdenCompraFactura']['tipo_documento']]; ?> <?= ($this->request->data['OrdenCompraFactura']['pagada']) ? '<label class="label label-success label-form">PAGADA</label>' : '' ; ?>: #<?=$this->request->data['OrdenCompraFactura']['folio']; ?> - Proveedor: <?=$this->request->data['Proveedor']['nombre']; ?></h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 <?= (!empty($this->request->data['OrdenCompra'])) ? 'col-md-5' : '' ; ?>">
			<div class="panel panel-primary" >
				<div class="panel-heading">
					<h3 class="panel-title">Información de la <?=$this->Html->tipoDocumento[$this->request->data['OrdenCompraFactura']['tipo_documento']];?></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tbody>
								<? if (!empty($this->request->data['OrdenCompra'])) : ?>
								<tr>
									<td>Orden de compra</td>
									<td>#<?=$this->request->data['OrdenCompraFactura']['orden_compra_id'];?></td>
								</tr>
								<? endif; ?>
								<tr>
									<td>Folio</td>
									<td><?=$this->request->data['OrdenCompraFactura']['folio'];?></td>
								</tr>
								<tr>
									<td>Tipo de documento</td>
									<td><?=$this->Html->tipoDocumento[$this->request->data['OrdenCompraFactura']['tipo_documento']]; ?></td>
								</tr>
								<tr>
									<td>Nota interna</td>
									<td><?=$this->request->data['OrdenCompraFactura']['nota']; ?></td>
								</tr>
								<tr>
									<td>Fecha de creación</td>
									<td><?=$this->request->data['OrdenCompraFactura']['created']; ?></td>
								</tr>
								<tr>
									<td>Última modificación</td>
									<td><?=$this->request->data['OrdenCompraFactura']['modified']; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<? if (!empty($this->request->data['LibreDte'])) : ?>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<caption>Información del emisor</caption>
							<tr>
								<td>Rut emisor</td>
								<td><?=$this->request->data['LibreDte']['Emisor']['rut']; ?>-<?=$this->request->data['LibreDte']['Emisor']['dv']; ?></td>
							</tr>
							<tr>
								<td>Razon social emisor</td>
								<td><?=$this->request->data['LibreDte']['Emisor']['razon_social']; ?></td>
							</tr>
							<tr>
								<td>Giro emisor</td>
								<td><?=$this->request->data['LibreDte']['Emisor']['giro']; ?></td>
							</tr>
							<tr>
								<td>Dirección emisor</td>
								<td><?=$this->request->data['LibreDte']['Emisor']['direccion']; ?>, <?=$this->request->data['LibreDte']['Emisor']['comuna_glosa']; ?></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<caption>Listado de productos facturados por el proveedor</caption>
							<th>Código item</th>
							<th>Nombre item</th>
							<th>Cantidad</th>
							<th>Precio</th>
							<th>Total</th>
							<tbody>
							<? foreach($this->request->data['LibreDte']['detalle'] as $item) : ?>
								<tr>
									<td><?=$item['CdgItem']['VlrCodigo']; ?></td>
									<td><?=$item['NmbItem']; ?></td>
									<td><?=$item['QtyItem']; ?></td>
									<td><?=CakeNumber::currency($item['PrcItem'], 'CLP'); ?></td>
									<td><?=CakeNumber::currency($item['MontoItem'], 'CLP'); ?></td>
								</tr>
							<? endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3"></td>
									<td>Total neto</td>
									<td><?=CakeNumber::currency($this->request->data['LibreDte']['neto'], 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="3"></td>
									<td>Iva</td>
									<td><?=CakeNumber::currency($this->request->data['LibreDte']['iva'], 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="3"></td>
									<td>Total bruto</td>
									<td><?=CakeNumber::currency($this->request->data['LibreDte']['total'], 'CLP');?></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<? endif; ?>
			</div>
		</div> <!-- end col -->

		
		<div class="col-xs-12 col-md-7">
			<div class="row">
				<div class="col-xs-12 col-md-6">
					<div class="widget widget-primary">
                        <div class="widget-title">Total facturado</div>
                        <div class="widget-subtitle">bruto</div>
                        <? if (!isset($this->request->data['LibreDte']['total'])) : ?>
                        <div class="widget-int" id="total-facturado" data-facturado="<?=$this->request->data['OrdenCompraFactura']['monto_facturado']; ?>"><?=CakeNumber::currency($this->request->data['OrdenCompraFactura']['monto_facturado'], 'CLP');?></div>
                        <? else : ?>
                        <div class="widget-int" id="total-facturado" data-facturado="<?=$this->request->data['LibreDte']['total']; ?>"><?=CakeNumber::currency($this->request->data['LibreDte']['total'], 'CLP');?></div>
                    	<? endif; ?>
                    </div>
				</div>
				<div class="col-xs-12 col-md-6">
					<? if ($this->request->data['OrdenCompraFactura']['monto_pagado'] == 0) : ?>
					<div class="widget widget-danger">
                        <div class="widget-title">Total pagado</div>
                        <div class="widget-subtitle">bruto</div>
                        <div class="widget-int" id="total-asignado" data-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>" data-original-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>"><?=CakeNumber::currency($this->request->data['OrdenCompraFactura']['monto_pagado'], 'CLP');?></div>
                    </div>
                    <? elseif (isset($this->request->data['LibreDte']['total'])) : ?>
						<? if ($this->request->data['OrdenCompraFactura']['monto_pagado'] > 0 && $this->request->data['OrdenCompraFactura']['monto_pagado'] < $this->request->data['LibreDte']['total']) : ?>
						<div class="widget widget-warning">
	                        <div class="widget-title">Total pagado</div>
	                        <div class="widget-subtitle">bruto</div>
	                        <div class="widget-int" id="total-asignado" data-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>" data-original-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>"><?=CakeNumber::currency($this->request->data['OrdenCompraFactura']['monto_pagado'], 'CLP');?></div>
	                    </div>
	                	<? endif; ?>

	                	<? if ($this->request->data['OrdenCompraFactura']['monto_pagado'] >= $this->request->data['LibreDte']['total'] ) : ?>
						<div class="widget widget-success">
	                        <div class="widget-title">Total pagado</div>
	                        <div class="widget-subtitle">bruto</div>
	                        <div class="widget-int" id="total-asignado" data-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>" data-original-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>"><?=CakeNumber::currency($this->request->data['OrdenCompraFactura']['monto_pagado'], 'CLP');?></div>
	                    </div>
	                	<? endif; ?>
                    <? else : ?>
                    	<? if ($this->request->data['OrdenCompraFactura']['monto_pagado'] > 0 && $this->request->data['OrdenCompraFactura']['monto_pagado'] < $this->request->data['OrdenCompraFactura']['monto_facturado']) : ?>
						<div class="widget widget-warning">
	                        <div class="widget-title">Total pagado</div>
	                        <div class="widget-subtitle">bruto</div>
	                        <div class="widget-int" id="total-asignado" data-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>" data-original-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>"><?=CakeNumber::currency($this->request->data['OrdenCompraFactura']['monto_pagado'], 'CLP');?></div>
	                    </div>
	                	<? endif; ?>

	                	<? if ($this->request->data['OrdenCompraFactura']['monto_pagado'] >= $this->request->data['OrdenCompraFactura']['monto_facturado'] ) : ?>
						<div class="widget widget-success">
	                        <div class="widget-title">Total pagado</div>
	                        <div class="widget-subtitle">bruto</div>
	                        <div class="widget-int" id="total-asignado" data-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>" data-original-pagado="<?=$this->request->data['OrdenCompraFactura']['monto_pagado']; ?>"><?=CakeNumber::currency($this->request->data['OrdenCompraFactura']['monto_pagado'], 'CLP');?></div>
	                    </div>
	                	<? endif; ?>
                	<? endif; ?>

                	
				</div>
			</div>
		
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Pagos asignados</h3>
				</div>
				<div class="panel-body">
					<h4><i class="fa fa-exclamation-circle text-warning"></i> ¡Atención!</h4>
					<p>Listado de pagos finalizados para ésta factura. La factura quedará pagada una vez que todos los pagos hayan finalizado.</p>

					<div class="table-responsive">
						<table class="table table-bordered">
							<th>Identificador</th>
							<th>Cuenta</th>
							<th>Monto disponible</th>
							<th>Método de pago</th>
							<th>Fecha pago</th>
							<th>Pagado</th>
							<th>Documento</th>
							<tbody id="pagos-asignados-contenedor">
							<? foreach ( $this->request->data['Pago'] as $ip => $pago ) : ?>
								<tr>
									<td>
										<?=$this->Form->hidden(sprintf('Pago.%d.orden_compra_pago_id', $ip), array('value' => $pago['id'])); ?>
										<?=$pago['identificador']; ?>
									</td>
									<td><?=@$pago['CuentaBancaria']['alias']; ?> - <?=@$pago['CuentaBancaria']['numero_cuenta']; ?></td>
									<td>
										<?=CakeNumber::currency($pago['monto_pagado'], 'CLP'); ?>
									</td>
									<td><?=$pago['Moneda']['nombre']; ?></td>
									<td><?=$pago['fecha_pago']; ?></td>
									<td class="text-center"><?= ($pago['pagado']) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>' ; ?></td>
									<td>
									<? if (!empty($pago['adjunto'])) : ?>
										<a href="<?=sprintf('%simg/Pago/%d/%s', $this->webroot, $pago['id'], $pago['adjunto']); ?>" class="btn btn-xs btn-info" target="_blank"><i class="fa fa-eye"></i> ver</a>
									<? elseif (!empty($pago['OrdenCompraAdjunto'])) : ?>
										<a href="<?=sprintf('%simg/OrdenCompraAdjunto/%d/%s', $this->webroot, $pago['OrdenCompraAdjunto']['id'], $pago['OrdenCompraAdjunto']['adjunto']); ?>" class="btn btn-xs btn-info" target="_blank"><i class="fa fa-eye"></i> ver</a>
									<? else : ?>
										--
									<? endif; ?>
									</td>
								</tr>	
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-left">
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
					<? if ($this->request->data['OrdenCompraFactura']['monto_pagado'] > 0) : ?>
					<div class="pull-right">
						<?= $this->Html->link('Volver a notificar pagos al proveedor', array('action' => 'notificar_pagos', $this->request->data['OrdenCompraFactura']['id']), array('class' => 'btn btn-primary')); ?>
					</div>
					<? endif; ?>
				</div>
			</div>
		</div>
	</div> <!-- end row -->
</div>

