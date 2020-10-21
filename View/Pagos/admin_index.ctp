<div class="page-title">
	<h2><span class="fa fa-usd"></span> Pagos</h2>
</div>


<div class="page-content-wrap">

	<div class="row">
		<div class="col-xs-12">

			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'pagos', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate')); ?>
			<? 
				$proveedor_id = (isset($this->request->params['named']['proveedor_id'])) ? $this->request->params['named']['proveedor_id'] : '' ;
				$identificador = (isset($this->request->params['named']['identificador'])) ? $this->request->params['named']['identificador'] : '' ;
				$monto_pagado = (isset($this->request->params['named']['monto_pagado'])) ? $this->request->params['named']['monto_pagado'] : '' ;
				$pagado       = (isset($this->request->params['named']['pagado'])) ? $this->request->params['named']['pagado'] : '' ;
				$moneda_id    = (isset($this->request->params['named']['moneda_id'])) ? $this->request->params['named']['moneda_id'] : '' ;
				$folio    = (isset($this->request->params['named']['folio'])) ? $this->request->params['named']['folio'] : '' ;
				$fecha_desde          = (isset($this->request->params['named']['fecha_desde'])) ? $this->request->params['named']['fecha_desde'] : '' ;
				$fecha_hasta          = (isset($this->request->params['named']['fecha_hasta'])) ? $this->request->params['named']['fecha_hasta'] : '' ;
			?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="form-group col-sm-4 col-xs-12">
                        <label>Identificador</label>
                        <?=$this->Form->input('identificador',
                            array(
                                'class' => 'form-control',
                                'placeholder' => 'Ingrese identificador',
                                'value' => $identificador
                            )
                        );?>
					</div>

					<div class="form-group col-sm-4 col-xs-12">
                        <label>Proveedor</label>
                        <?=$this->Form->select('proveedor_id',
                            $proveedores,
                            array(
                                'class' => 'form-control select',
                                'data-live-search' => true,
                                'empty' => 'Seleccione',
                                'value' => $proveedor_id
                            )
                        );?>
                    </div>
                    
                    <div class="form-group col-sm-4 col-xs-12">
							<label>Método de pago</label>
							<?=$this->Form->select('moneda_id',
								$monedas,
								array(
                                    'class' => 'form-control select',
                                    'data-live-search' => true,
                                    'empty' => 'Seleccione',
                                    'value' => $moneda_id
								)
							);?>
					</div>
                    
                    <div class="form-group col-sm-3 col-xs-12">
                        <label>Estado</label>
                        <?=$this->Form->select('pagado',
                            array(
                                'si' => 'Pagado',
                                'no' => 'No pagado'
                            ),
                            array(
                                'class' => 'form-control select',
                                'empty' => 'Seleccione',
                                'value' => $pagado
                            )
                        );?>
                    </div>
                    
                    <div class="form-group col-sm-3 col-xs-12">
                        <label>Monto</label>
                        <?=$this->Form->input('monto_pagado',
                            array(
                                'class' => 'form-control is-number',
                                'placeholder' => 'Ingrese monto',
                                'value' => $monto_pagado
                            )
                        );?>
					</div>

					<div class="form-group col-sm-3 col-xs-12">
                        <label>Folio de factura</label>
                        <?=$this->Form->input('folio',
                            array(
                                'class' => 'form-control is-number',
                                'placeholder' => 'Ingrese folio',
                                'value' => $folio
                            )
                        );?>
					</div>
					
					<div class="form-group col-sm-3 col-xs-12">
						<label>Rango de fecha</label>
						<div class="input-group">
							<?=$this->Form->input('fecha_desde', array(
								'class' => 'form-control datepicker',
								'type' => 'text',
								'value' => $fecha_desde
								))?>
                            <span class="input-group-addon add-on"> - </span>
                            <?=$this->Form->input('fecha_hasta', array(
								'class' => 'form-control datepicker',
								'type' => 'text',
								'value' => $fecha_hasta
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
			</div>
			<?= $this->Form->end(); ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Pagos</h3>

					<?= $this->Form->create('Export', array('url' => array('controller' => 'pagos', 'action' => 'exportar', 'formato' => 'pago'), 'inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-form-export', 'type' => 'GET')); ?>
					<div class="btn-group pull-right">

                    <? $export = array(
							'action' => 'exportar'
							);

						if (isset($this->request->params['named'])) {
							$export = array_replace_recursive($export, $this->request->params['named']);
						}?>
						<button type="submit" class="btn btn-warning" id="exportar_seleccion" disabled><i class="fa fa-file-excel-o"></i> Exportar selección</button>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar todo', $export, array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
					<?= $this->Form->end(); ?>
				</div>
				<div class="panel-body">
					<?=$this->element('contador_resultados'); ?>
					<div class="table-responsive">
						<table class="table table-striped table-middle">
							<thead>
								<tr class="sort">
									<th><input type="checkbox" class="js-select-all"></th>
									<th><?= $this->Paginator->sort('identificador', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('proveedor_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('moneda_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('fecha_pago', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('monto_pagado', 'Monto', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('pagado', 'Estado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('created', 'Fecha creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('modified', 'Última modificación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $pagos as $pago ) : ?>
								<tr data-toggle="tooltip" data-placement="top" title="<?= (empty($pago['Pago']['orden_compra_id']) && empty($pago['OrdenCompraFactura']) ) ? 'Este pago no tiene relación con ninguna OC ni factura' : '' ; ?>">
									<td><input name="Pago[<?=$pago['Pago']['id'];?>][id]" type="checkbox" class="agregar_export" value="<?=$pago['Pago']['id'];?>" data-id="<?=$pago['Pago']['id'];?>"></td>
									<td><?= h($pago['Pago']['identificador']); ?>&nbsp;</td>
									<td><?= h($pago['Proveedor']['nombre']); ?>&nbsp;</td>
									<td><?= h($pago['Moneda']['nombre']); ?>&nbsp;</td>
                                    <td><?= h($pago['Pago']['fecha_pago']); ?>&nbsp;</td>
									<td><?= CakeNumber::currency(h($pago['Pago']['monto_pagado']), 'CLP'); ?>&nbsp;</td>
									<td><?= ($pago['Pago']['pagado'] ? '<label class="label label-success"><i class="fa fa-check"></i> Pagado</label>' : '<label class="label label-danger"><i class="fa fa-close"></i> No pagado</label>'); ?>&nbsp;</td>
                                    <td><?= h($pago['Pago']['created']); ?>&nbsp;</td>
                                    <td><?= h($pago['Pago']['modified']); ?>&nbsp;</td>
									<td>
                                    <? if (!empty($pago['OrdenCompraFactura'])) : ?>
                                        <?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar facturas', array('action' => 'exportar_facturas', $pago['Pago']['id']), array('class' => 'btn btn-xs btn-primary btn-block', 'rel' => 'tooltip', 'title' => 'Exportar facturas', 'escape' => false)); ?>
									<? endif; ?>
									<? if ($permisos['view']) : ?>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('action' => 'view', $pago['Pago']['id']), array('class' => 'btn btn-xs btn-info btn-block', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
										<? if ($pago['Pago']['pagado']) : ?>
										<?= $this->Html->link('<i class="fa fa-envelope"></i> Notificar pago', array('action' => 'notificar_pago', $pago['Pago']['id']), array('class' => 'btn btn-xs btn-danger btn-block', 'rel' => 'tooltip', 'title' => 'Notificar este registro', 'escape' => false)); ?>
										<? endif;?>
									<? endif; ?>
									<? if ($permisos['edit'] && !$pago['Pago']['pagado'] && !empty($pago['OrdenCompraFactura']) ) : ?>
										<?= $this->Html->link('<i class="fa fa-pencil"></i> Editar', array('action' => 'edit', $pago['Pago']['id']), array('class' => 'btn btn-xs btn-warning btn-block', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? endif; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
	<div class="row">
		<div class="col-xs-12">
			<div class="pull-right">
				<ul class="pagination">
					<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
					<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
					<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
				</ul>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>