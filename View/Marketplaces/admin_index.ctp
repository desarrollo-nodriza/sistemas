<div class="page-title">
	<h2><span class="fa fa-shopping-cart"></span> Marketplaces</h2>
</div>

<div class="page-content-wrap">

	<div class="row">

		<div class="col-xs-12">

			<div class="panel panel-default">

				<div class="panel-heading">

					<h3 class="panel-title">Listado de Marketplaces</h3>

					<div class="btn-group pull-right">
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Marketplace', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					</div>

				</div>

				<div class="panel-body">

					<div class="table-responsive">

						<table class="table table-striped listado-ventas">

							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('fee', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('marketplace_tipo_id', 'Tipo', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tienda_id', 'Tienda', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('porcentaje_adicional', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('agregar_despacho_costo', 'Agregar costo despacho', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('stock_automatico', 'Sincronizar stock', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('activo', 'Activa', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ( $marketplaces as $marketplace ) : ?>

									<tr>

										<td><?= h($marketplace['Marketplace']['nombre']); ?>&nbsp;</td>
										<td><?= h($marketplace['Marketplace']['fee']); ?>%&nbsp;</td>
										<td><?= h($marketplace['MarketplaceTipo']['nombre']); ?>&nbsp;</td>
										<td><?= h($marketplace['Tienda']['nombre']); ?>&nbsp;</td>
										<td><?= h($marketplace['Marketplace']['porcentaje_adicional']); ?>%&nbsp;</td>
										<td><?= ($marketplace['Marketplace']['agregar_despacho_costo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($marketplace['Marketplace']['stock_automatico'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($marketplace['Marketplace']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>

										<td>

											<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $marketplace['Marketplace']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>

											<?php
												if ($marketplace['Marketplace']['activo']) {
													echo $this->Form->postLink('<i class="fa fa-remove"></i> Desactivar', array('action' => 'desactivar', $marketplace['Marketplace']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Desactivar este registro', 'escape' => false));
												}
												else {
													echo $this->Form->postLink('<i class="fa fa-check"></i> Activar', array('action' => 'activar', $marketplace['Marketplace']['id']), array('class' => 'btn btn-xs btn-success confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Activar este registro', 'escape' => false));
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