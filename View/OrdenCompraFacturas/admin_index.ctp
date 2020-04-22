<div class="page-title">
	<h2><span class="fa fa-file"></span> Facturas de compra</h2>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'ordenCompraFacturas', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$oc    = (isset($this->request->params['named']['oc'])) ? $this->request->params['named']['oc'] : '' ;
				$prov  = (isset($this->request->params['named']['prov'])) ? $this->request->params['named']['prov'] : '' ;
				$folio = (isset($this->request->params['named']['folio'])) ? $this->request->params['named']['folio'] : '' ;
				$sta   = (isset($this->request->params['named']['sta'])) ? $this->request->params['named']['sta'] : '' ;
				$dtf   = (isset($this->request->params['named']['dtf'])) ? $this->request->params['named']['dtf'] : '' ;
				$dtt   = (isset($this->request->params['named']['dtt'])) ? $this->request->params['named']['dtt'] : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<label>Orden de compra:</label>

							<?=$this->Form->select('oc', 
								$ocs,
								array(
								'type' => 'text',
								'class' => 'form-control select',
								'data-live-search' => true,
								'default' => $oc,
								'empty' => 'Seleccione OC'
								));?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<label>Proveedor:</label>

							<?=$this->Form->select('prov', 
								$proveedores,
								array(
								'type' => 'text',
								'class' => 'form-control select',
								'data-live-search' => true,
								'default' => $prov,
								'empty' => 'Seleccione proveedor'
								));?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<label>Folio:</label>

							<?=$this->Form->select('folio', 
								$folios,
								array(
								'type' => 'text',
								'class' => 'form-control select',
								'data-live-search' => true,
								'default' => $folio,
								'empty' => 'Seleccione folio'
								));?>
						</div>
					</div>
					
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<label>Estado del DTE</label>
							<?=$this->Form->select('sta', array(
								'n' => 'Pendiente de pago',
								'y' => 'Pagada'
								),
								array(
								'class' => 'form-control select',
								'empty' => 'Seleccione Estado',
								'value' => $sta
								)
							);?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<label>Creados entre</label>
						<div class="input-group">
							<?=$this->Form->input('dtf', array(
								'class' => 'form-control datepicker',
								'type' => 'text',
								'value' => $dtf
								))?>
                            <span class="input-group-addon add-on"> - </span>
                            <?=$this->Form->input('dtt', array(
								'class' => 'form-control datepicker',
								'type' => 'text',
								'value' => $dtt
								))?>
                        </div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Filtrar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
						</div>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Listado de Facturas</h3>
						<div class="btn-group pull-right">
							<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<caption>Los dtes se marcarán como pagados cuando los pagos asignados hayan sido "pagados"</caption>
								<thead>
									<tr class="sort">
										<th></th>
										<th><?= $this->Paginator->sort('id', 'Identificador', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('orden_compra_id', 'OC', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('proveedor_id', 'Proveedor', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('folio', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('total_items', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('neto', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('iva', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('bruto', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('monto_pagado', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('anulado', 'Estado Sii', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('pagada', 'Estado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $facturas as $if => $factura ) : ?>
									<tr>
										<td><input type="checkbox" value="<?=$factura['OrdenCompraFactura']['id']; ?>" data-id="<?=$factura['OrdenCompraFactura']['id']; ?>" data-oc="<?=$factura['OrdenCompraFactura']['orden_compra_id']; ?>" data-proveedor="<?=$factura['OrdenCompraFactura']['proveedor_id']; ?>" class="js-factura-id" name="data[OrdenCompraFactura][<?=$if;?>][id]"></td>
										<td><?= h($factura['OrdenCompraFactura']['id'])?></td>
										<td>#<?= h($factura['OrdenCompraFactura']['orden_compra_id'])?></td>
										<td><?= h($factura['Proveedor']['nombre'])?></td>
										<td><?= h($factura['OrdenCompraFactura']['folio'])?></td>
										<td><?= h($factura['OrdenCompraFactura']['total_items'])?></td>
										<td><?= h(CakeNumber::currency($factura['OrdenCompraFactura']['neto'], 'CLP'))?></td>
										<td><?= h(CakeNumber::currency($factura['OrdenCompraFactura']['iva'], 'CLP'))?></td>
										<td><?= h(CakeNumber::currency($factura['OrdenCompraFactura']['bruto'], 'CLP'))?></td>
										<td><?= h(CakeNumber::currency($factura['OrdenCompraFactura']['monto_pagado'], 'CLP'))?></td>
										<td><?= (!$factura['OrdenCompraFactura']['anulado']) ? '<label class="label label-success"><i class="fa fa-check"></i> Aceptado</label>' : '<label class="label label-danger"><i class="fa fa-close"></i> Anulado</label>' ;?></td>
										<td>
										<? if ($factura['OrdenCompraFactura']['pagada']) : ?>
											<label class="label label-success"><i class="fa fa-check"></i> Pagada</label>
										<? endif; ?>

										<? if ($factura['OrdenCompra']['Moneda']['tipo'] == 'agendar' && !$factura['OrdenCompraFactura']['pagada'] && count(Hash::extract($factura['OrdenCompra']['Pago'], '{n}[pagado=1].pagado')) == 0 ) : ?>
											<label class="label label-warning"><i class="fa fa-clock"></i> Pendiente de agendamiento</label>
										<? endif; ?>

										<? if ($factura['OrdenCompra']['Moneda']['tipo'] == 'agendar' && !$factura['OrdenCompraFactura']['pagada'] && count(Hash::extract($factura['OrdenCompra']['Pago'], '{n}[pagado=1].pagado')) > 0 ) : ?>
											<label class="label label-warning"><i class="fa fa-clock"></i> En proceso de pago</label>
										<? endif; ?>

										<? if ($factura['OrdenCompra']['Moneda']['tipo'] == 'esperar' && !$factura['OrdenCompraFactura']['pagada'] && count(Hash::extract($factura['OrdenCompra']['Pago'], '{n}[pagado=1].pagado')) == 0) : ?>
											<label class="label label-info"><i class="fa fa-clock"></i> En espera de configuracion de pagos</label>
										<? endif; ?>

										<? if ($factura['OrdenCompra']['Moneda']['tipo'] == 'esperar' && !$factura['OrdenCompraFactura']['pagada'] && count(Hash::extract($factura['OrdenCompra']['Pago'], '{n}[pagado=1].pagado')) > 0) : ?>
											<label class="label label-info"><i class="fa fa-clock"></i> En proceso de pago</label>
										<? endif; ?>

										<? if ($factura['OrdenCompra']['Moneda']['tipo'] == 'pagar' && !$factura['OrdenCompraFactura']['pagada']) : ?>
											<label class="label label-danger"><i class="fa fa-close"></i> Pendiente de pago</label>
										<? endif; ?>
										
										<td>
										<? if ($permisos['edit']) : ?>
											<?= $this->Html->link('<i class="fa fa-list"></i> Ver detalles', array('action' => 'view', $factura['OrdenCompraFactura']['id']), array('class' => 'btn btn-xs btn-primary', 'rel' => 'tooltip', 'title' => 'Ir a este registro', 'escape' => false)); ?>
											<?= $this->Html->link('<i class="fa fa-money"></i> Configurar pagos', array('controller' => 'pagos', 'action' => 'configuracion', $factura['OrdenCompraFactura']['orden_compra_id']), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Ir a este registro', 'escape' => false)); ?>
										<? endif; ?>
										</td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="panel-footer">
						<?= $this->Form->create('OrdenCompraFactura', array('url' => array('controller' => 'ordenCompraFacturas', 'action' => 'procesar'),'id' => "formulario-facturas-pago-masivo", 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<input type="submit" class="btn btn-primary esperar-carga pull-right" autocomplete="off" data-loading-text="Espera un momento..." value="Asignar pagos">
						<?= $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="pull-right">
				<ul class="pagination">
					<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
					<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
					<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
				</ul>
			</div>
		</div>
	</div>
</div>
