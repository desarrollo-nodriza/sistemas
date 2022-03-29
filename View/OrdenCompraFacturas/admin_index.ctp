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
				$sub_sta   = (isset($this->request->params['named']['sub_sta'])) ? $this->request->params['named']['sub_sta'] : '' ;
				$dtf   = (isset($this->request->params['named']['dtf'])) ? $this->request->params['named']['dtf'] : '' ;
				$dtt   = (isset($this->request->params['named']['dtt'])) ? $this->request->params['named']['dtt'] : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Orden de compra:</label>
							<?=$this->Form->input('oc',
								array(
									'type' => 'text',
									'class' => 'tagsinput',
									'value' => $oc,
									'placeholder' => 'Id de oc'
								));?>
						</div>
					</div>
					<div class="col-sm-4 col-xs-12">
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
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Folio:</label>
							<?=$this->Form->input('folio',
								array(
									'type' => 'text',
									'class' => 'tagsinput',
									'value' => $folio,
									'placeholder' => 'Folio'
								));?>
						</div>
					</div>
					
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<br>
							<label>Estado del DTE</label>
							<?=$this->Form->select('sta', array(
									'n' => 'No pagado',
									'y' => 'Pagado'
								),
								array(
								'class' => 'form-control select',
								'empty' => 'Seleccione Estado',
								'value' => $sta
								)
							);?>
						</div>
					</div>
					
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<br>
							<label>Sub Estado del DTE</label>
							<?=$this->Form->select('sub_sta', $estados_pagos,
								array(
								'class' => 'form-control select',
								'empty' => 'Seleccione Sub estado',
								'value' => $sub_sta
								)
							);?>
						</div>
					</div>

					<div class="col-sm-4 col-xs-12">
						<br>
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
                            <?=$this->element('items_por_pagina'); ?>
                            <? $export = array(
							'action' => 'exportar'
							);

							if (isset($this->request->params['named'])) {
								$export = array_replace_recursive($export, $this->request->params['named']);
							}?>
							
							<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', $export, array('class' => 'btn btn-primary', 'escape' => false)); ?>
							<button class="btn btn-warning" data-toggle="modal" data-target="#modalObtenercompas">Actualizar DTE de compra</button>
                            <button class="btn btn-info" data-toggle="modal" data-target="#modalCompasExportar">Exportar DTE de compra</button>
						</div>
					</div>
					<div class="panel-body">

						<?= $this->element('contador_resultados', array('col' => false)); ?>

						<div class="table-responsive">
							<table class="table">
								<caption>Los dtes se marcarán como pagados cuando los pagos asignados hayan sido "pagados"</caption>
								<thead>
									<tr class="sort">
										<th><label><input type="checkbox" id="seleccionar-todo"></label></th>
										<th><?= $this->Paginator->sort('id', 'Identificador', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('orden_compra_id', 'OC', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('created', 'Fecha creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('proveedor_id', 'Proveedor', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('folio', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('total_items', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('neto', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('iva', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('bruto', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('monto_pagado', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('anulado', 'Estado Sii', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('', 'Sub estados', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $facturas as $if => $factura ) : ?>
									<tr>
										<td><input type="checkbox" value="<?=$factura['OrdenCompraFactura']['id']; ?>" data-id="<?=$factura['OrdenCompraFactura']['id']; ?>" data-oc="<?=$factura['OrdenCompraFactura']['orden_compra_id']; ?>" data-proveedor="<?=$factura['OrdenCompraFactura']['proveedor_id']; ?>" class="js-factura-id" name="data[OrdenCompraFactura][<?=$if;?>][id]" <? if ($factura['OrdenCompraFactura']['pagada'] || !empty($factura['Pago'] || empty($factura['Proveedor']))) : ?> disabled <? endif;?>></td>
										<td><?= h($factura['OrdenCompraFactura']['id'])?></td>
										<td>#<?= h($factura['OrdenCompraFactura']['orden_compra_id'])?></td>
										<td><?= h($factura['OrdenCompraFactura']['created'])?></td>
										<td><?= h($factura['Proveedor']['nombre'])?></td>
										<td><?= h($factura['OrdenCompraFactura']['folio'])?></td>
										<td><?= h($factura['OrdenCompraFactura']['total_items'])?></td>
										<td><?= h(CakeNumber::currency($factura['OrdenCompraFactura']['neto'], 'CLP'))?></td>
										<td><?= h(CakeNumber::currency($factura['OrdenCompraFactura']['iva'], 'CLP'))?></td>
										<td><?= h(CakeNumber::currency($factura['OrdenCompraFactura']['bruto'], 'CLP'))?></td>
										<td><?= h(CakeNumber::currency($factura['OrdenCompraFactura']['monto_pagado'], 'CLP'))?></td>
										<td><?= (!$factura['OrdenCompraFactura']['anulado']) ? '<label class="label label-success"><i class="fa fa-check"></i> Aceptado</label>' : '<label class="label label-danger"><i class="fa fa-close"></i> Anulado</label>' ;?></td>
										<td>
										<? foreach ($factura['OrdenCompraFactura']['estados'] as $estado) : ?>
											<? 	switch ($estado) :
													case 'pagado':
														echo '<label class="label label-success"><i class="fa fa-check"></i> Pagada</label>';
														break;
													case 'sin_pago':
														echo '<label class="label label-danger"><i class="fa fa-close"></i> No pagado</label>';
														break;
													case 'agendamineto_pendiente':
														echo '<label class="label label-warning"><i class="fa fa-clock-o"></i> Pendiente de agendamiento</label>';
														break;
													case 'agendado':
														echo '<label class="label label-info"><i class="fa fa-clock-o"></i> Pago agendado</label>';
														break;
													case 'pago_pendiente':
														echo '<label class="label label-warning"><i class="fa fa-close"></i> Pendiente de pago</label>';
														break;
													default:
														echo '<label class="label label-default"><i class="fa fa-eye-slash"></i> No aplica</label>';
														break;
											 	endswitch;?>

										<? endforeach; ?>
										
										<td>
										<? if ($permisos['edit']) : ?>
											<?= $this->Html->link('<i class="fa fa-list"></i> Ver detalles', array('action' => 'view', $factura['OrdenCompraFactura']['id']), array('target' => '_blank', 'class' => 'btn btn-xs btn-primary', 'rel' => 'tooltip', 'title' => 'Ir a este registro', 'escape' => false)); ?>
											<? if (!in_array('sin_moneda', $factura['OrdenCompraFactura']['estados']) && !empty($factura['Proveedor']) && !$factura['OrdenCompraFactura']['pagada']) : ?>
											<?= $this->Html->link('<i class="fa fa-money"></i> Configurar pagos', array('controller' => 'pagos', 'action' => 'configuracion', $factura['OrdenCompraFactura']['id']), array('target' => '_blank', 'class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Ir a este registro', 'escape' => false)); ?>
											<? endif; ?>
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
							<input type="submit" class="btn btn-primary esperar-carga pull-right" autocomplete="off" data-loading-text="Espera un momento..." value="Pagar facturas seleccionadas">
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

<!-- Modal -->
<div class="modal fade" id="modalObtenercompas" tabindex="-1" role="dialog" aria-labelledby="modalObtenercompasLabel">
  <div class="modal-dialog" role="document">
  	<?= $this->Form->create('OrdenCompraFacturas', array('url' => array('controller' => 'ordenCompraFacturas', 'action' => 'obtener_compras_manual'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="modalObtenercompasLabel"><i class="fa fa-spin"></i> Actualizar documentos de compra</h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="form-group col-md-12">
                    <?=$this->Form->label('periodo', 'Periodo a obtener/actualizar'); ?>
                    <?=$this->Form->select('periodo', $periodos, array('class' => 'form-control', 'empty' => false)); ?>
                </div>
                <div class="form-group col-md-12">
                    <?=$this->Form->label('tipo_compra', 'Estado del documento'); ?>
                    <?=$this->Form->select('tipo_compra', $tipo_compras, array('class' => 'form-control', 'empty' => false)); ?>
                </div>
			</div>						
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
			<button type="submit" class="btn btn-primary start-loading-then-redirect">Obtener/actualizar</button>
		</div>
	</div>
	<?= $this->Form->end(); ?>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="modalCompasExportar" tabindex="-1" role="dialog" aria-labelledby="modalCompasExportarLabel">
  <div class="modal-dialog" role="document">
  	<?= $this->Form->create('OrdenCompraFacturas', array('url' => array('controller' => 'ordenCompraFacturas', 'action' => 'exportar_compras'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="modalCompasExportarLabel"><i class="fa fa-spin"></i> Exportar documentos de compra</h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="form-group col-md-12">
                    <?=$this->Form->label('periodo', 'Filtrar por periodo'); ?>
                    <?=$this->Form->select('periodo', $periodos2, array('class' => 'form-control', 'empty' => 'Seleccione periodo')); ?>
                </div>
                <div class="form-group col-md-12">
                    <?=$this->Form->label('tipo_compra', 'Filtrar por estado'); ?>
                    <?=$this->Form->select('tipo_compra', $tipo_compras, array('class' => 'form-control', 'empty' => 'Seleccione estado')); ?>
                </div>
			</div>						
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
			<button type="submit" class="btn btn-primary">Exportar</button>
		</div>
	</div>
	<?= $this->Form->end(); ?>
  </div>
</div>
