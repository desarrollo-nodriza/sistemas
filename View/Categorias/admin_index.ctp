<div class="page-title">
	<h2><span class="fa fa-list-alt"></span> Categorias</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Categorías</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nueva Categoria', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
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
									<th><?= $this->Paginator->sort('descripcion', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('imagen', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('color_fondo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('color_titulo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $categorias as $categoria ) : ?>
								<tr>
									<td><?= h($categoria['Categoria']['nombre']); ?>&nbsp;</td>
									<td><?= h($categoria['Categoria']['descripcion']); ?>&nbsp;</td>
									<td><?= h($categoria['Categoria']['imagen']); ?>&nbsp;</td>
									<td><?= h($categoria['Categoria']['color_fondo']); ?>&nbsp;</td>
									<td><?= h($categoria['Categoria']['color_titulo']); ?>&nbsp;</td>
									<td>
									<? if ($permisos['edit']) : ?>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $categoria['Categoria']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
										<a href="#" class="mb-control btn btn-primary btn-xs" data-box="#mb-cat<?=$categoria['Categoria']['id'];?>"><i class="fa fa-history"></i> Restablecer</a>
										<div class="message-box message-box-warning animated fadeIn" data-sound="alert" id="mb-cat<?=$categoria['Categoria']['id'];?>">
											<div class="mb-container">
												<div class="mb-middle">
													<div class="mb-title"><span class="fa fa-history"></span>¿Restablecer <strong>categoría</strong>?</div>
													<div class="mb-content">
														<p>¿Seguro que quieres restablecer esta categoría?</p>
														<p>Se quitarán los productos asociados a esta categoría</p>
														<p>Para cancelar presiona No</p>
													</div>
													<div class="mb-footer">
														<div class="pull-right">
															<?= $this->Html->link('<i class="fa fa-history"></i> Restablecer', array('action' => 'clear', $categoria['Categoria']['id']), array('class' => 'btn btn-lg btn-primary', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
															<button class="btn btn-default btn-lg mb-control-close">No</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									<? endif; ?>
									<? if ($permisos['activate']) : ?>
										<? if ($categoria['Categoria']['activo'] == 1) { ?>
											<?= $this->Form->postLink('<i class="fa fa-eye-slash"></i> Desactivar', array('action' => 'desactivar', $categoria['Categoria']['id']), array('class' => 'btn btn-warning btn-xs', 'rel' => 'tooltip', 'title' => 'Desactivar este registro', 'escape' => false)); ?>
										<? }else{ ?>
											<?= $this->Form->postLink('<i class="fa fa-eye"></i> Activar', array('action' => 'activar', $categoria['Categoria']['id']), array('class' => 'btn btn-primary btn-xs', 'rel' => 'tooltip', 'title' => 'Activar este registro', 'escape' => false)); ?>
										<?	} ?>
									<? endif; ?>
									<? if ($permisos['delete']) : ?>
										<?= $this->Form->postLink('<i class="fa fa-trash"></i> Eliminar', array('action' => 'delete', $categoria['Categoria']['id']), array('class' => 'btn btn-danger btn-xs', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
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