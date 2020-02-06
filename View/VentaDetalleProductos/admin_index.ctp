<div class="page-title">
	<h2><span class="fa fa-tags"></span> Productos</h2>
</div>

<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'ventaDetalleProductos', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$id     = (isset($this->request->params['named']['id'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['id'])) : '' ;
				$nombre = (isset($this->request->params['named']['nombre'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['nombre'])) : '' ;
				$marca = (isset($this->request->params['named']['marca'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['marca'])) : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Id del producto</label>
							<?=$this->Form->input('id', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: 33322, 34445, 56463',
								'value' => $id
								))?>
						</div>
					</div>
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Nombre del producto</label>
							<?=$this->Form->input('nombre', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: Taladro bosch, Esmeril 4344fF',
								'value' => $nombre
								))?>
						</div>
					</div>
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Marca</label>
							<?=$this->Form->select('marca', $marcas, array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'default' => $marca
								))?>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Filtrar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
						</div>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Productos</h3>

					<div class="btn-group pull-right">
					<? if ($permisos['edit']) : ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Actualización masiva', array('action' => 'edicion_masiva'), array('class' => 'btn btn-danger', 'escape' => false)); ?>
					<? endif; ?>
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Producto', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
						
						<? $export = array(
							'action' => 'exportar'
							);

						if (isset($this->request->params['named'])) {
							$export = array_replace_recursive($export, $this->request->params['named']);
						}?>
						
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', $export, array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>					
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<caption><?=$this->Paginator->counter('Página {:page} de {:pages}, mostrando {:current} registros de {:count}.');?></caption>
							<thead>
								<tr class="sort">
									<th style="width: 120px;"><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th style="max-width: 300px;"><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('codigo_proveedor', 'Ref Proveedor', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('marca_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('precio_costo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('cantidad_virtual', 'Stock fisico', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('cantidad_virtual', 'Stock virtual', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $ventadetalleproductos as $ventadetalleproducto ) : ?>
								<tr>
									<td>
										<b>Id:</b> <?= h($ventadetalleproducto['VentaDetalleProducto']['id']); ?>&nbsp;<br>
										<b>Id Ext:</b> <?= h($ventadetalleproducto['VentaDetalleProducto']['id_externo']); ?>&nbsp;
									</td>
									<td><?= h($ventadetalleproducto['VentaDetalleProducto']['nombre']); ?>&nbsp;</td>
									<td><?= h($ventadetalleproducto['VentaDetalleProducto']['codigo_proveedor']); ?>&nbsp;</td>
									<td><?= h($ventadetalleproducto['Marca']['nombre']); ?>&nbsp;</td>
									<td><?= CakeNumber::currency(h($ventadetalleproducto['VentaDetalleProducto']['costo']), 'CLP'); ?>&nbsp;</td>
									<td><?= h($ventadetalleproducto['VentaDetalleProducto']['stock']); ?>&nbsp;</td>
									<td><?= h($ventadetalleproducto['VentaDetalleProducto']['cantidad_virtual']); ?>&nbsp;</td>
									<td><?= ($ventadetalleproducto['VentaDetalleProducto']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td>
									<? if ($permisos['edit']) : ?>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $ventadetalleproducto['VentaDetalleProducto']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? endif; ?>
									<? if ($permisos['delete']) : ?>
										<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $ventadetalleproducto['VentaDetalleProducto']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
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
