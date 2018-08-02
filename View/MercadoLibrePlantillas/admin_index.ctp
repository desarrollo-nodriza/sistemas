<div class="page-title">
	<h2><span class="fa fa-file-text"></span> Mercado Libre Plantillas</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Mercado Libre Plantillas</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Mercado Libre Pantilla', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
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
									<th><?= $this->Paginator->sort('html', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $mercadoLibrePlantillas as $mercadoLibrePlantilla ) : ?>
								<tr>
									<td><?= h($mercadoLibrePlantilla['MercadoLibrePlantilla']['nombre']); ?>&nbsp;</td>
									<td><?= $this->Text->truncate(h($mercadoLibrePlantilla['MercadoLibrePlantilla']['html']), 40); ?>&nbsp;</td>
									<td><?= ($mercadoLibrePlantilla['MercadoLibrePlantilla']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($mercadoLibrePlantilla['MercadoLibrePlantilla']['created']); ?>&nbsp;</td>
									<td>
									<? if ($permisos['edit']) : ?>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $mercadoLibrePlantilla['MercadoLibrePlantilla']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? endif; ?>
									<? if ($permisos['activate']) : ?>
										<? if ($mercadoLibrePlantilla['MercadoLibrePlantilla']['activo'] == 1) { ?>
											<?= $this->Form->postLink('<i class="fa fa fa-eye-slash"></i> Desactivar', array('action' => 'desactivar', $mercadoLibrePlantilla['MercadoLibrePlantilla']['id']), array('class' => 'btn btn-xs btn-primary', 'rel' => 'tooltip', 'title' => 'Desactivar este registro', 'escape' => false)); ?>
										<? }else{ ?>
											<li><?= $this->Form->postLink('<i class="fa fa-eye"></i> Activar', array('action' => 'activar', $mercadoLibrePlantilla['MercadoLibrePlantilla']['id']), array('class' => 'btn btn-xs btn-default', 'rel' => 'tooltip', 'title' => 'Activar este registro', 'escape' => false)); ?></li>
										<?	} ?>
									<? endif; ?>
									<? if ($permisos['delete']) :?>
										<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $mercadoLibrePlantilla['MercadoLibrePlantilla']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
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
