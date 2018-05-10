<div class="page-title">
	<h2><span class="fa fa-shopping-bag"></span> Productos</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Productos</h3>
					<div class="btn-group pull-right">
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Producto', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table datatable">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('modelo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('valor', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('imagen', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Creado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $productos as $producto ) : ?>
								<tr>
									<td><?= h($producto['Producto']['nombre']); ?>&nbsp;</td>
									<td><?= h($producto['Producto']['modelo']); ?>&nbsp;</td>
									<td><?= h($producto['Producto']['valor']); ?>&nbsp;</td>
									<td><?= $this->Html->image($producto['Producto']['imagen']['mini'], array('class' => 'img-responsive')); ?>&nbsp;</td>
									<td><?= h($producto['Producto']['created']); ?>&nbsp;</td>
									<td>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $producto['Producto']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? if ($permiso) : ?>
										
										<? if ($producto['Producto']['activo'] == 1) { ?>
											<?= $this->Form->postLink('<i class="fa fa fa-eye-slash"></i> Desactivar', array('action' => 'desactivar', $producto['Producto']['id']), array('class' => 'btn btn-warning btn-xs', 'rel' => 'tooltip', 'title' => 'Desactivar este registro', 'escape' => false)); ?>
										<? }else{ ?>
											<?= $this->Form->postLink('<i class="fa fa-eye"></i> Activar', array('action' => 'activar', $producto['Producto']['id']), array('class' => 'btn btn-primary btn-xs', 'rel' => 'tooltip', 'title' => 'Activar este registro', 'escape' => false)); ?>
										<?	} ?>

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

<div class="pull-right">
	<ul class="pagination">
		<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
		<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
		<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
	</ul>
</div>
