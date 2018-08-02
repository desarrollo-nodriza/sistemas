<div class="page-title">
	<h2><span class="fa fa-calendar"></span> Validez de la cotización</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de fechas de validez</h3>
					<div class="btn-group pull-right">
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nueva fecha de validez', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('valor', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
											<th><?= $this->Paginator->sort('comentario', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
											<th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $validezFechas as $validezFecha ) : ?>
								<tr>
									<td><?= h($validezFecha['ValidezFecha']['valor']); ?>&nbsp;</td>
									<td><?= h($validezFecha['ValidezFecha']['comentario']); ?>&nbsp;</td>
									<td><?= ($validezFecha['ValidezFecha']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td>
									<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $validezFecha['ValidezFecha']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $validezFecha['ValidezFecha']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
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
