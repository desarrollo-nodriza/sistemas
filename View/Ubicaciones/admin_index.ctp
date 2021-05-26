<div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> Ubicaciones</h2>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Listado de Ubicaciones</h3>
						<div class="btn-group pull-right">
						<? if ($permisos['add']) : ?>
							<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Ubicacion', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
							<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Creación masiva', array('action' => 'creacion_masiva'), array('class' => 'btn btn-danger', 'escape' => false)); ?>
						<? endif; ?>
							<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
							
							<?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> Generar QRs', array('action' => 'crear_etiqueta_qr'), array('class' => 'btn btn-info', 'escape' => false)); ?>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr class="sort">
                                        <th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('zona', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('fila', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('columna', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                        <th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('fecha_creacion', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $ubicaciones as $ubicacion ) : ?>
									<tr>
										<td><?= h($ubicacion['Ubicacion']['id']); ?>&nbsp;</td>
                                        <td><?= h($ubicacion['Zona']['nombre']); ?>&nbsp;</td>
                                        <td><?= h($ubicacion['Ubicacion']['fila']); ?>&nbsp;</td>
                                        <td><?= h($ubicacion['Ubicacion']['columna']); ?>&nbsp;</td>
										<td><?= ($ubicacion['Ubicacion']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= h($ubicacion['Ubicacion']['fecha_creacion']); ?>&nbsp;</td>
										<td>
										<?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> Generar Qr', array('action' => 'qr_ubicacion', $ubicacion['Ubicacion']['id'], 'ext' => 'pdf'), array('class' => 'btn btn-xs btn-primary', 'rel' => 'tooltip', 'title' => 'EGenerar qr', 'escape' => false, 'target' => '_blank')); ?>
										<? if ($permisos['edit']) : ?>
											<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $ubicacion['Ubicacion']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
										<? endif; ?>
										<!-- <? if ($permisos['delete']) : ?>
											<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $ubicacion['Ubicacion']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
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
