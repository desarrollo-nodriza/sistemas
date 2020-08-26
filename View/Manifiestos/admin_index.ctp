<div class="page-title">
	<h2><i class="fa fa-table" aria-hidden="true"></i> Manifiestos</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Manifiestos</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Manifiesto', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
					<? if ($permisos['generate']) : ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					<? endif; ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id', __('N°'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tienda_id', __('Tienda'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('administrador_id', __('Autor'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('transporte_id', __('Transportista'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('entregado', __('Entregado'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('fecha_entregado', __('Fecha entregado'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('impreso', __('Impreso'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('modified', __('Modificado'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($manifiestos as $manifiesto): ?>
									<tr>
										<td><?= h($manifiesto['Manifiesto']['id']); ?>&nbsp;</td>
										<td><?= h($manifiesto['Tienda']['nombre']); ?></td>
										<td><?= h($manifiesto['Administrador']['nombre']);?></td>
										<td><?= h($manifiesto['Transporte']['nombre']);?></td>
										<td><?= ($manifiesto['Manifiesto']['entregado'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-remove text-danger"></i>'); ?>&nbsp;</td>
										<td><?= h($manifiesto['Manifiesto']['fecha_entregado']); ?>&nbsp;</td>
										<td><?= ($manifiesto['Manifiesto']['impreso'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-remove text-danger"></i>'); ?>&nbsp;</td>
										<td><?= h($manifiesto['Manifiesto']['modified']); ?>&nbsp;</td>
										<td class="actions">
											<? if ($permisos['edit']) : ?>
												
												<? if (!$manifiesto['Manifiesto']['entregado']) : ?>
													
													<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $manifiesto['Manifiesto']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>

													<?= $this->Html->link('<i class="fa fa-truck" aria-hidden="true"></i> Finalizar', array('action' => 'finish', $manifiesto['Manifiesto']['id']), array('class' => 'btn btn-xs btn-warning', 'rel' => 'tooltip', 'title' => 'Finalizar este registro', 'escape' => false)); ?>
												
												<? else : ?>
													
													<?= $this->Html->link('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Ver Excel', array('action' => 'view', $manifiesto['Manifiesto']['id']), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>

													<?= $this->Html->link('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Ver Excel Conexxion', array('action' => 'view_conexxion', $manifiesto['Manifiesto']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>

													<?= $this->Html->link('<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver Pdf', array('action' => 'view_pdf', $manifiesto['Manifiesto']['id']), array('class' => 'btn btn-xs btn-primary', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>

												<? endif; ?>
											<? endif; ?>

											<? if ($permisos['delete']) : ?>
												<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $manifiesto['Manifiesto']['id']), array('confirm' => __('¿Seguro deseas eliminar el manifiesto # %s?', $manifiesto['Manifiesto']['id']), 'class' => 'btn btn-xs btn-danger', 'escape' => false)); ?>
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
