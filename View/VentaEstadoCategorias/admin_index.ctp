<div class="page-title">
	<h2><span class="fa fa-filter"></span> Categoria Estados de Ventas</h2>
</div>

<div class="page-content-wrap">

	<div class="row">

		<div class="col-xs-12">

			<div class="panel panel-default">

				<div class="panel-heading">
					<h3 class="panel-title">Listado de categorias</h3>
				</div>

				<div class="panel-body">

					<div class="table-responsive">

						<table class="table table-striped listado-ventas">

							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('nombre', 'Estado Detallado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('estilo', 'Color', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('venta', 'Es Venta', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('plantilla', 'Plantilla email', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('activo', 'Activa', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ( $ventaEstadoCategorias as $ventaEstadoCategoria ) : ?>

									<tr>

										<td><?= h($ventaEstadoCategoria['VentaEstadoCategoria']['nombre']); ?>&nbsp;</td>
										<td><span class="btn btn-xs btn-<?= $ventaEstadoCategoria['VentaEstadoCategoria']['estilo']; ?>"><?= $ventaEstadoCategoria['VentaEstadoCategoria']['nombre']; ?></span>&nbsp;</td>
										<td><?= ($ventaEstadoCategoria['VentaEstadoCategoria']['venta'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= h($ventaEstadoCategoria['VentaEstadoCategoria']['plantilla']); ?>&nbsp;</td>
										<td><?= ($ventaEstadoCategoria['VentaEstadoCategoria']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>

										<td>

											<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $ventaEstadoCategoria['VentaEstadoCategoria']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>

											<?php
												if ($ventaEstadoCategoria['VentaEstadoCategoria']['activo']) {
													echo $this->Form->postLink('<i class="fa fa-remove"></i> Desactivar', array('action' => 'desactivar', $ventaEstadoCategoria['VentaEstadoCategoria']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Desactivar este registro', 'escape' => false));
												}
												else {
													echo $this->Form->postLink('<i class="fa fa-check"></i> Activar', array('action' => 'activar', $ventaEstadoCategoria['VentaEstadoCategoria']['id']), array('class' => 'btn btn-xs btn-success confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Activar este registro', 'escape' => false));
												}
											?>

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