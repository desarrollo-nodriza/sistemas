<div class="page-title">
	<h2><span class="fa fa-list"></span> Ordenes de compra canceladas</h2>
</div>

<div class="page-content-wrap">

	<?=$this->element('link_ordencompras');?>

	<div class="row">
		<div class="col-xs-12">
			<?=$this->element('ordenCompras/filtro');?>
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de ordenes de compra</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) :  ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nueva OC Ventas', array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
						<?= $this->Html->link('<i class="fa fa-hand-pointer-o"></i> Nueva OC Manual', array('action' => 'add_manual'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<?=$this->element('contador_resultados'); ?>
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('administrador_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('proveedor_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('estado', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('oc_manual', 'OC Manual', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $ordenCompras as $ordenCompra ) : ?>

								<tr>
									<td><?= h($ordenCompra['OrdenCompra']['id']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['Administrador']['nombre']); ?></td>
									<td><?= (!empty($ordenCompra['Proveedor'])) ? $ordenCompra['Proveedor']['nombre'] : 'Sin especificar' ; ?>&nbsp;</td>
									<td><?= h($estados[$ordenCompra['OrdenCompra']['estado']]); ?>&nbsp;</td>
									<td><?= ($ordenCompra['OrdenCompra']['oc_manual'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['created']); ?>&nbsp;</td>
									<td>

									<?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>

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
		<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 10, 'currentClass' => 'active', 'separator' => '')); ?>
		<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
	</ul>
</div>
