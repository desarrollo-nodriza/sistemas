<div class="page-title">
	<h2><span class="fa fa-bookmark"></span> Prospectos</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'prospectos', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Estado</label>
							<?=$this->Form->select('findby', $estadoProspectos,
								array(
								'class' => 'form-control',
								'empty' => 'No importa'
								)
							);?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<label>Creado entre</label>
                            <?=$this->Form->input('f_inicio', array('class' => 'form-control datepicker'));?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<label>y el</label>
                            <?=$this->Form->input('f_final', array('class' => 'form-control datepicker'));?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Buscar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
					</div>
					<?= $this->Form->end(); ?>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Prospectos</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Prospecto', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tienda_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('estado_prospecto_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('moneda_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Creado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $prospectos as $prospecto ) : ?>
								<tr>
									<td><?= h($prospecto['Prospecto']['nombre']); ?>&nbsp;</td>
									<td><?= h($prospecto['Tienda']['nombre']); ?>&nbsp;</td>
									<td><?= h($prospecto['EstadoProspecto']['nombre']); ?>&nbsp;</td>
									<td><?= h($prospecto['Moneda']['nombre']); ?>&nbsp;</td>
									<td><?= h($prospecto['Prospecto']['created']); ?>&nbsp;</td>
									<td>
									<? if ($permisos['edit'] && $prospecto['EstadoProspecto']['nombre'] != 'Finalizada') : ?>
									<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $prospecto['Prospecto']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? endif; ?>
									<? if ($permisos['delete'] && $prospecto['EstadoProspecto']['nombre'] != 'Finalizada') : ?>
									<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $prospecto['Prospecto']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
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

<? 	
	if ( ! empty( $this->request->params['named']['findby'] ) ) :
		echo "<script type='text/javascript'>";
		echo "$('#FiltroFindby').val('" . $this->request->params['named']['findby'] . "');";
		echo "</script> ";
	endif;

	if ( ! empty( $this->request->params['named']['f_inicio'] ) ) :
		echo "<script type='text/javascript'>";
		echo "$('#FiltroFInicio').val('" . $this->request->params['named']['f_inicio'] . "');";
		echo "</script> ";
	endif;


	if ( ! empty( $this->request->params['named']['f_final'] ) ) :
		echo "<script type='text/javascript'>";
		echo "$('#FiltroFFinal').val('" . $this->request->params['named']['f_final'] . "');";
		echo "</script> ";
	endif;

?>
