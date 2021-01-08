<div class="page-title">
	<h2><span class="fa fa-list-ol"></span> Movimientos de Productos</h2>
</div>

<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'ventaDetalleProductos', 'action' => 'movimientos'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$producto = (isset($this->request->params['named']['producto'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['producto'])) : '' ;
				$bodega = (isset($this->request->params['named']['bodega'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['bodega'])) : '' ;
				$io       = (isset($this->request->params['named']['io'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['io'])) : '' ;
				$tipo     = (isset($this->request->params['named']['tipo'])) ? str_replace('%2F', '/', urldecode($this->request->params['named']['tipo'])) : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Producto</label>
							<?=$this->Form->select('producto', $productos, array(
								'class' => 'form-control select',
								'data-live-search' => true,
								'empty' => 'Seleccione',
								'default' => $producto
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Bodega</label>
							<?=$this->Form->select('bodega', $bodegas, array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'default' => $bodega
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>I/O</label>
							<?=$this->Form->select('io', $ios, array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'default' => $io
								))?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Tipo</label>
							<?=$this->Form->select('tipo', $tipos, array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'default' => $tipo
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
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'movimientos'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
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
					<h3 class="panel-title">Listado de Movimientos</h3>

					<div class="btn-group pull-right">
					<? if ($permisos['move_stock']) : ?>
						<?= $this->Html->link('<i class="fa fa-arrows"></i> Mover inventario bodegas', array('action' => 'moverInventarioMasivo'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>

					<? if ($permisos['adjust_stock']) : ?>
						<?= $this->Html->link('<i class="fa fa-cogs"></i> Ajustar inventario', array('action' => 'ajustarInventarioMasivo'), array('class' => 'btn btn-warning', 'escape' => false)); ?>
					<? endif; ?>
					
					<? if ($permisos['init_stock']) : ?>
						<?= $this->Html->link('<i class="fa fa-upload"></i> Carga inicial', array('action' => 'inventarioInicial'), array('class' => 'btn btn-danger', 'escape' => false)); ?>
					<? endif; ?>
					
					<? $export = array(
						'action' => 'exportar_movimientos'
						);

					if (isset($this->request->params['named'])) {
						$export = array_replace_recursive($export, $this->request->params['named']);
					}?>

					<a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle" aria-expanded="true"><i class="fa fa-file-excel-o"></i> Exportar <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><?= $this->Html->link( $this->Paginator->counter('<i class="fa fa-file-excel-o"></i> Exportar movimientos ({:count} registros).'), $export, array('class' => '', 'escape' => false)); ?></li>
							<li><?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar productos (formato inventario)', array('action' => 'exportar_inventario'), array('class' => '', 'escape' => false, 'target' => '_blank')); ?></li>                                             
						</ul>
					</div>					
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-striped">
							<caption><?=$this->Paginator->counter('Página {:page} de {:pages}, mostrando {:current} registros de {:count}.');?></caption>
							<thead>
								<tr class="sort">
									<th style="width: 100px;"><?= $this->Paginator->sort('venta_detalle_producto_id', __('Producto'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th style="width: 100px;"><?= $this->Paginator->sort('bodega_id', __('Bodega'), array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('io', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tipo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('cantidad', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('valor', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('total', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('fecha', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th style="width: 100px;"><?= $this->Paginator->sort('responsable', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th style="width: 100px;"><?= $this->Paginator->sort('glosa', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $movimientos as $movimiento ) : ?>
								<tr>
									<td><?= $movimiento['VentaDetalleProducto']['id'] . '<br>' . h($movimiento['VentaDetalleProducto']['nombre']); ?>&nbsp;</td>
									<td><?= h($movimiento['BodegasVentaDetalleProducto']['bodega']); ?>&nbsp;</td>
									<td><?= h($movimiento['BodegasVentaDetalleProducto']['io']); ?>&nbsp;</td>
									<td><?= h($movimiento['BodegasVentaDetalleProducto']['tipo']); ?>&nbsp;</td>
									<td><?= h($movimiento['BodegasVentaDetalleProducto']['cantidad']); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($movimiento['BodegasVentaDetalleProducto']['valor'], 'CLP'); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($movimiento['BodegasVentaDetalleProducto']['total'], 'CLP'); ?>&nbsp;</td>
									<td><?= h($movimiento['BodegasVentaDetalleProducto']['fecha']); ?>&nbsp;</td>
									<td><?= h($movimiento['BodegasVentaDetalleProducto']['responsable']); ?>&nbsp;</td>
									<td><?= h($movimiento['BodegasVentaDetalleProducto']['glosa']); ?>&nbsp;</td>
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
