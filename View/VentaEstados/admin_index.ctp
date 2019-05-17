<div class="page-title">
	<h2><span class="fa fa-filter"></span> Estados de Ventas</h2>
</div>

<div class="page-content-wrap">

	<div class="row">

		<div class="col-xs-12">

			<div class="panel panel-default">

				<div class="panel-heading">
					<h3 class="panel-title">Listado de Estados de Ventas</h3>
				</div>

				<div class="panel-body">

					<div class="table-responsive">

						<table class="table table-striped listado-ventas">

							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('nombre', 'Estado Detallado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('venta_estado_categoria_id', 'Estado Agrupado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('notificacion_cliente', 'Enviar email', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('permitir_dte', 'DTE Habilitado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('permitir_oc', 'OC habilitada', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('permitir_retiro_oc', 'Retiro OC habilitada', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('revertir_stock', 'Devolver stock', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('marcar_atendida', 'Finalizar venta', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('prearacion', 'Preparación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('activo', 'Activa', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ( $ventaEstados as $ventaEstado ) : ?>

									<tr>

										<td><?= h($ventaEstado['VentaEstado']['nombre']); ?>&nbsp;</td>
										<td><span class="btn btn-xs btn-<?= $ventaEstado['VentaEstadoCategoria']['estilo']; ?>"><?= $ventaEstado['VentaEstadoCategoria']['nombre']; ?></span>&nbsp;</td>
										<td><?= ($ventaEstado['VentaEstado']['notificacion_cliente'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($ventaEstado['VentaEstado']['permitir_dte'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($ventaEstado['VentaEstado']['permitir_oc'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($ventaEstado['VentaEstado']['permitir_retiro_oc'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($ventaEstado['VentaEstado']['revertir_stock'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($ventaEstado['VentaEstado']['marcar_atendida'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($ventaEstado['VentaEstado']['preparacion'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($ventaEstado['VentaEstado']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>

										<td>

											<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $ventaEstado['VentaEstado']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>

											<?php
												if ($ventaEstado['VentaEstado']['activo']) {
													echo $this->Form->postLink('<i class="fa fa-remove"></i> Desactivar', array('action' => 'desactivar', $ventaEstado['VentaEstado']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Desactivar este registro', 'escape' => false));
												}
												else {
													echo $this->Form->postLink('<i class="fa fa-check"></i> Activar', array('action' => 'activar', $ventaEstado['VentaEstado']['id']), array('class' => 'btn btn-xs btn-success confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Activar este registro', 'escape' => false));
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