<div class="page-title">
	<h2><span class="fa fa-users"></span> Administradores</h2>
</div>

<div class="page-content-wrap">
<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'Administradores', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$id     	= (isset($this->request->params['named']['id'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['id'])) : '' ;
				$nombre 	= (isset($this->request->params['named']['nombre'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['nombre'])) : '' ;
				$email = (isset($this->request->params['named']['email'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['email'])) : '' ;
				$rol 	= (isset($this->request->params['named']['rol'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['rol'])) : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Id</label>
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
							<label>Nombre</label>
							<?=$this->Form->input('nombre', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: Nodriza',
								'value' => $nombre
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Email</label>
							<?=$this->Form->input('email', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: juan_Peres@nodriza.cl',
								'value' => $email,
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Rol</label>
							<?=$this->Form->select('rol', $roles, array(
								'class' => 'form-control select',
								'empty' => 'Seleccione',
								'default' => $rol,
								'data-live-search' => true
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
						<h3 class="panel-title">Listado de Administradores</h3>
						<div class="btn-group pull-right">
						<? if ($permisos['add']) : ?>
							<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Administrador', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
						<? endif; ?>
						<? $export = array(
							'action' => 'exportar'
							);

						if (isset($this->request->params['named'])) {
							$export = array_replace_recursive($export, $this->request->params['named']);
						}?>
							<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', $export, array('class' => 'btn btn-primary', 'escape' => false)); ?>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr class="sort">
										<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('email', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('rol_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $administradores as $administrador ) : ?>
									<tr>
										<td><?= h($administrador['Administrador']['nombre']); ?>&nbsp;</td>
										<td><?= h($administrador['Administrador']['email']); ?>&nbsp;</td>
										<td><?= h($administrador['Rol']['nombre']); ?>&nbsp;</td>
										<td>											
											<? if ($permisos['edit']) : ?>
												<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $administrador['Administrador']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
											<? endif; ?>
											<? if ($permisos['delete']) : ?>
												<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $administrador['Administrador']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
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
