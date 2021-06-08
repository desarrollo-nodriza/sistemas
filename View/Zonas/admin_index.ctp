<div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> Zonas</h2>
</div>
<div class="page-content-wrap">

	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'zonas', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$inputs = $this->request->data['Filtro'] ?? null;
				$id     = (isset($inputs['id'])) 		? str_replace('%2F', '/', urldecode($inputs['id'])) : '' ;
				$bodega = (isset($inputs['bodega_id'])) ? str_replace('%2F', '/', urldecode($inputs['bodega_id'])) : '' ;
				$nombre = (isset($inputs['nombre'])) 	? str_replace('%2F', '/', urldecode($inputs['nombre'])) : '' ;
				$tipo 	= (isset($inputs['tipo'])) 		? str_replace('%2F', '/', urldecode($inputs['tipo'])) : '' ;
				$activo = (isset($inputs['activo']))	? str_replace('%2F', '/', urldecode($inputs['activo'])) : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Bodega</label>
							<?=$this->Form->select('bodega_id', $bodegas, array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'default' => $bodega
								));?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Id Zona</label>
							<?=$this->Form->input('id', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: 1, 5',
								'value' => $id
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Nombre Zona</label>
							<?=$this->Form->input('nombre', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: A, H',
								'value' => $nombre
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Tipo</label>
							<?=$this->Form->select('tipo', $tipos, array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'default' => $tipo
								));?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Activo</label>
							<?=$this->Form->select('activo', array(
									'0' => 'No',
									'1' => 'Si'
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
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Listado de Zonas</h3>
						<div class="btn-group pull-right">
						<? if ($permisos['add']) : ?>
							<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Zona', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
						<? endif; ?>
							<!-- <?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?> -->
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr class="sort">
                                        <th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('tipo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('bodega_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                        <th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('fecha_creacion', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $zonas as $zona ) : ?>
									<tr>
										<td><?= h($zona['Zona']['id']); ?>&nbsp;</td>
                                        <td><?= h($zona['Zona']['nombre']); ?>&nbsp;</td>
                                        <td><?= h($zona['Zona']['tipo']); ?>&nbsp;</td>
                                        <td><?= h($zona['Bodega']['nombre']); ?>&nbsp;</td>
										<td><?= ($zona['Zona']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= h($zona['Zona']['fecha_creacion']); ?>&nbsp;</td>
										<td>
										<? if ($permisos['edit']) : ?>
											<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $zona['Zona']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
										<? endif; ?>
										<!-- <? if ($permisos['delete']) : ?>
											<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $zona['Zona']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
										<? endif; ?> -->
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
