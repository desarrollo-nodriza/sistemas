<div class="page-title">
	<h2><span class="fa fa-shopping-basket"></span> Mercado Libre Productos</h2>
	<div class="pull-right">
		<? if (!empty($url) && ! $this->Session->check('Meli.access_token')) : ?>
			<div class="btn-group">
	            <a href="#" data-toggle="dropdown" class="btn btn-warning dropdown-toggle" aria-expanded="false">Aplicación Desconectada <span class="caret"></span></a>
	            <ul class="dropdown-menu pull-right" role="menu">
	                <li><?= $this->Html->link('Conectar aplicación', $url, array('escape' => false)); ?></li>
	            </ul>
	        </div>
		<? else : ?>
			<?= $this->Html->link('<i class="fa fa-refresh"></i> Sincronizar precios & stock', array('action' => 'actualizarPreciosStock'), array('escape' => false, 'class' => 'btn btn-info meli-loading')); ?>
			<div class="btn-group">
	            <a href="#" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">Aplicación Conectada <span class="caret"></span></a>
	            <ul class="dropdown-menu pull-right" role="menu">
	                <li><?= $this->Html->link('Ver mi cuenta', array('action' => 'usuario'), array('escape' => false)); ?></li>
	                <li><?= $this->Html->link('Desconectar aplicación', array('action' => 'desconectar'), array('escape' => false)); ?></li>                                                    
	            </ul>
	        </div>
		<? endif; ?>
	</div>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de productos</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Producto', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th style="width: 250px;"><?= $this->Paginator->sort('producto', 'Producto', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tienda_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('mercado_libre_plantilla_id', 'Plantilla', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('', 'Publicado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('estado', 'Estado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $mercadoLibres as $mercadoLibr ) : ?>
								<tr>
									<td>#<?= h($mercadoLibr['MercadoLibr']['id']); ?>&nbsp;</td>
									<td><?= h($mercadoLibr['MercadoLibr']['producto']); ?>&nbsp;</td>
									<td><?= h($mercadoLibr['Tienda']['nombre']); ?>&nbsp;</td>
									<td><?= h($mercadoLibr['MercadoLibrePlantilla']['nombre']); ?>&nbsp;</td>
									<td><?= $publicado = (!empty($mercadoLibr['MercadoLibr']['id_meli'])) ? '<i class="fa fa-check-circle text-success fa-lg"></i>' : '<i class="fa fa-times-circle text-danger fa-lg"></i>' ;?></td>
									<td>
										<? if ($mercadoLibr['MercadoLibr']['estado'] == 'closed' && !empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
											<label class="label label-danger">Cerrada</label>
										<? endif; ?>
										<? if ($mercadoLibr['MercadoLibr']['estado'] == 'paused' && !empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
											<label class="label label-warning">Pausada</label>
										<? endif; ?>
										<? if ($mercadoLibr['MercadoLibr']['estado'] == 'active' && !empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
											<label class="label label-success">Abierta</label>
										<? endif; ?>
										<? if ( empty($mercadoLibr['MercadoLibr']['id_meli']) ) : ?>
											<label class="label label-primary">No publicado</label>
										<? endif; ?>
									</td>
									<td>
									<div class="btn-group">
							            <a href="#" data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">Acciones <span class="caret"></span></a>
							            <ul class="dropdown-menu pull-right" role="menu">
							            <? if ($permisos['edit']) : ?>
											<li><?= $this->Html->link('Editar', array('action' => 'edit', $mercadoLibr['MercadoLibr']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?></li>
										<? endif; ?>
										<? if ($permisos['view']) : ?>
											<li><?= $this->Form->postLink('Ver Html', array('action' => 'view', $mercadoLibr['MercadoLibr']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
											</li>
										<? endif; ?>
										<? if (!empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
											<li><?= $this->Html->link('Ver publicación en MELI', $mercadoLibr['MercadoLibr']['url_meli'], array('class' => '', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false, 'target' => '_blank')); ?>
											</li>
											<? if ($mercadoLibr['MercadoLibr']['estado'] == 'closed') : ?>
											<li><?= $this->Html->link('Abrir', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'active'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Activar item', 'escape' => false)); ?>
											</li>
											<li><?= $this->Html->link('Pausar', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'paused'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Pausar item', 'escape' => false)); ?>
											</li>
											<? endif; ?>

											<? if ($mercadoLibr['MercadoLibr']['estado'] == 'paused') : ?>
											<li><?= $this->Html->link('Abrir', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'active'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Activar item', 'escape' => false)); ?>
											</li>
											<li><?= $this->Html->link('Cerrar', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'closed'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Cerrar item', 'escape' => false)); ?>
											</li>
											<? endif; ?>

											<? if ($mercadoLibr['MercadoLibr']['estado'] == 'active') : ?>
											<li><?= $this->Html->link('Pausar', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'paused'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Pausar item', 'escape' => false)); ?>
											</li>
											<li><?= $this->Html->link('Cerrar', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'closed'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Cerrar item', 'escape' => false)); ?>
											</li>
											<? endif; ?>
										<? endif; ?>
										<? if ($permisos['delete']) :?>
											<li><?= $this->Form->postLink('Eliminar', array('action' => 'delete', $mercadoLibr['MercadoLibr']['id']), array('class' => 'confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?></li>
										<? endif; ?>
							            </ul>
							        </div>
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
