<div class="page-title">
	<h2><span class="fa fa-truck"></span> Métodos de envio</h2>
</div>

<div class="page-content-wrap">

<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'metodoEnvios', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$id     	= (isset($this->request->params['named']['id'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['id'])) : '' ;
				$nombre 	= (isset($this->request->params['named']['nombre'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['nombre'])) : '' ;
				$dependencia = (isset($this->request->params['named']['dependencia'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['dependencia'])) : '' ;
				$bodega 	= (isset($this->request->params['named']['bodega'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['bodega'])) : '' ;
				$activo = (isset($this->request->params['named']['activo'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['activo'])) : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Id del método de envío</label>
							<?=$this->Form->input('id', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: 33322, 34445, 56463',
								'value' => $id
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Nombre del método</label>
							<?=$this->Form->input('nombre', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: Despacho a domicilio',
								'value' => $nombre
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Dependencia</label>
							<?=$this->Form->select('dependencia', $dependencias, array(
								'class' => 'form-control select',
								'empty' => 'Seleccione',
								'default' => $dependencia,
								'data-live-search' => true
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Bodega</label>
							<?=$this->Form->select('bodega', $bodegas, array(
								'class' => 'form-control select',
								'empty' => 'Seleccione',
								'default' => $bodega,
								'data-live-search' => true
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Activo</label>
							<?=$this->Form->select('activo', array(
									'activo' => 'Si',
									'inactivo' => 'No'
								), array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'default' => $activo
								));?>
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
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de métodos de envio</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Método de envio', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tiempo_entrega_estimado', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('retiro_local', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('dependencia', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('bodega_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $metodoEnvios as $metodoEnvio ) : ?>
								<tr>
									<td><?= h($metodoEnvio['MetodoEnvio']['id']); ?>&nbsp;</td>
									<td><?= h($metodoEnvio['MetodoEnvio']['nombre']); ?>&nbsp;</td>
									<td><?= h($metodoEnvio['MetodoEnvio']['tiempo_entrega_estimado']); ?>&nbsp;</td>
									<td><?= ($metodoEnvio['MetodoEnvio']['retiro_local'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($metodoEnvio['MetodoEnvio']['dependencia']); ?>&nbsp;</td>
									<td><?= h($metodoEnvio['Bodega']['nombre']); ?>&nbsp;</td>
									<td><?= ($metodoEnvio['MetodoEnvio']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($metodoEnvio['MetodoEnvio']['created']); ?>&nbsp;</td>
									<td>
									<? if ($permisos['edit']) : ?>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $metodoEnvio['MetodoEnvio']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? endif; ?>
									<? if ($permisos['delete']) : ?>
										<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $metodoEnvio['MetodoEnvio']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
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
