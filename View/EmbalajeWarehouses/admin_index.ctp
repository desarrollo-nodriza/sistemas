<div class="page-title">
	<h2><span class="fa fa-cubes"></span> Embalajes</h2>
</div>
<div class="page-content-wrap">

	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'embalajeWarehouses', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			
			<? 
			
			$id  = (isset($this->request->params['named']['id'])) ? $this->request->params['named']['id'] : '' ;
			$venta_id  = (isset($this->request->params['named']['venta_id'])) ? $this->request->params['named']['venta_id'] : '' ;
			$estado  = (isset($this->request->params['named']['estado'])) ? $this->request->params['named']['estado'] : '' ;
			$bodega_id = (isset($this->request->params['named']['bodega_id'])) ? $this->request->params['named']['bodega_id'] : '' ;
			$marketplace_id = (isset($this->request->params['named']['marketplace_id'])) ? $this->request->params['named']['marketplace_id'] : '' ;
			$comuna_id = (isset($this->request->params['named']['comuna_id'])) ? $this->request->params['named']['comuna_id'] : '' ;
			$prioritario = (isset($this->request->params['named']['prioritario'])) ? $this->request->params['named']['prioritario'] : '' ;
			$fecha_desde = (isset($this->request->params['named']['fecha_desde'])) ? $this->request->params['named']['fecha_desde'] : '' ;
			$fecha_hasta = (isset($this->request->params['named']['fecha_hasta'])) ? $this->request->params['named']['fecha_hasta'] : '' ;
			
			?>
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Embalaje id</label>
						<?= $this->Form->input('id', array('class' => 'form-control input-buscar', 'placeholder' => 'Id del embalaje', 'value' => $id)); ?>
					</div>
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Venta id</label>
						<?= $this->Form->input('venta_id', array('type' => 'text', 'class' => 'form-control input-buscar', 'placeholder' => 'Ingrese Id de la venta', 'value' => $venta_id)); ?>
					</div>
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Estado</label>
						<?= $this->Form->select('estado', $estados, array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $estado)); ?>
					</div>
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Bodega</label>
						<?= $this->Form->select('bodega_id', $bodegas, array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $bodega_id)); ?>
					</div>
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Marketplace</label>
						<?= $this->Form->select('marketplace_id', $marketplaces, array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $marketplace_id)); ?>
					</div>
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Comuna</label>
						<?= $this->Form->select('comuna_id', $comunas, array('class' => 'form-control select', 'empty' => 'Seleccione', 'data-live-search' => true, 'default' => $comuna_id)); ?>
					</div>
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Prioritario</label>
						<?= $this->Form->select('prioritario', $prioritarios, array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $prioritario)); ?>
					</div>
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Fecha (desde)</label>
						<div class="input-group" style="max-width: 100%;">
							<?=$this->Form->input('fecha_desde', array('value' => $fecha_desde, 'class' => 'form-control datepicker', 'placeholder' => 'Ej: 2021-01-10', 'autocomplete' => 'off', 'style' => 'max-width: 100%!important'))?>
							<span class="input-group-addon glyphicon glyphicon-calendar"></span>
						</div>
					</div>
					<div class="col-sm-4 col-xs-12 form-group">
						<label>Fecha (hasta)</label>
						<div class="input-group" style="max-width: 100%;">
							<?=$this->Form->input('fecha_hasta', array('value' => $fecha_hasta, 'class' => 'form-control datepicker', 'placeholder' => 'Ej: 2021-01-10', 'autocomplete' => 'off', 'style' => 'max-width: 100%!important'))?>
							<span class="input-group-addon glyphicon glyphicon-calendar"></span>
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
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Listado de Embalajes</h3>
						<div class="btn-group pull-right">
							<? $export = array(
								'action' => 'exportar'
								);

							if (isset($this->request->params['named'])) {
								$export = array_replace_recursive($export, $this->request->params['named']);
							}?>
							<!--<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', $export, array('class' => 'btn btn-primary', 'escape' => false)); ?>-->
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr class="sort">
										<th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('venta_id', 'Venta id/ref', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                        <th><?= $this->Paginator->sort('estado', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                        <th><?= $this->Paginator->sort('metodo_envio_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('bodega_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('marketplace_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('comuna_id', 'Comuna destino', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('prioritario', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('fecha_creacion', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $embalajes as $embalaje ) : ?>
									<tr>
										<td><?=$embalaje['EmbalajeWarehouse']['id'];?>&nbsp;</td>
										<td><?= h($embalaje['EmbalajeWarehouse']['venta_id']); ?><br><?= h($embalaje['Venta']['referencia']); ?>&nbsp;</td>
										<td><?= h($embalaje['EmbalajeWarehouse']['estado']); ?>&nbsp;</td>
										<td><?= h($embalaje['MetodoEnvio']['nombre']); ?>&nbsp;</td>
										<td><?= h($embalaje['Bodega']['nombre']); ?>&nbsp;</td>
										<td><?= ($embalaje['Marketplace']) ? $embalaje['Marketplace']['nombre'] : ''; ?>&nbsp;</td>
										<td><?= ($embalaje['Comuna']) ? $embalaje['Comuna']['nombre'] : ''; ?>&nbsp;</td>
										<td><?= ($embalaje['EmbalajeWarehouse']['prioritario'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= h($embalaje['EmbalajeWarehouse']['fecha_creacion']); ?>&nbsp;</td>
										
										<td>
										<? if ($permisos['view']) : ?>
											<?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $embalaje['EmbalajeWarehouse']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
										<? endif; ?>
										<? if ($permisos['edit'] && $embalaje['EmbalajeWarehouse']['estado'] == 'en_revision') : ?>
											<?= $this->Html->link('<i class="fa fa-edit"></i> Revisar', array('action' => 'review', $embalaje['EmbalajeWarehouse']['id']), array('class' => 'btn btn-xs btn-warning', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
										<? endif; ?>
										<? if ($permisos['edit'] && in_array($embalaje['EmbalajeWarehouse']['estado'], array('listo_para_embalar', 'procesando', 'en_revision'))) : ?>
											<?= $this->Html->link('<i class="fa fa-remove"></i> Cancelar', array('action' => 'cancelar', $embalaje['EmbalajeWarehouse']['id']), array('class' => 'btn btn-xs btn-danger start-loading-then-redirect', 'escape' => false)); ?>
										<? endif; ?>
										</td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
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
