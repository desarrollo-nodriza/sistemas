<div class="page-title">
	<? if ($this->Session->check('Marketplace.id')) : ?>
	<h2><span class="fa fa-shopping-basket"></span> Productos publicados en <?=$this->Session->read('Marketplace.nombre'); ?></h2>
	<? else : ?>
	<h2><span class="fa fa-shopping-basket"></span> Mercado Libre Productos</h2>
	<? endif; ?>
</div>

<div class="page-content-wrap">
	
	<? if (!empty($meliConexion)) : ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-plug" aria-hidden="true"></i> Conectar con Marketplaces</h3>
					</div>

					<div class="panel-body">
						<p>Debe conectar sus marketplaces para poder ver las ventas. Éste procedimiento se realiza sólo una vez.</p>
						<div class="btn-group" role="group" aria-label="Conectar">
							<? foreach ($meliConexion as $iac => $access) : ?>
								<a href="<?= $access['url']; ?>" class="btn btn-primary">Acceder a <?= $access['marketplace']; ?></a>
							<? endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<? elseif (!$this->Session->check('Marketplace.id')) : ?>
		<?= $this->Form->create('Filtro', array('url' => array('controller' => 'mercadoLibres', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list" aria-hidden="true"></i> Marketplaces disponibles para Meli</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12 form-group">
						<?= $this->Form->label('marketplace_id', 'Seleccione marketplace'); ?>
						<?= $this->Form->select('marketplace_id', $marketplaces, array('class' => 'form-control', 'empty' => false, 'default' => $this->Session->read('Marketplace.id'))); ?>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<div class="col-xs-12">
					<div class="pull-right">
						<?= $this->Form->button('<i class="fa fa-send" aria-hidden="true"></i> Usar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
					</div>
				</div>
			</div>
		<?= $this->Form->end(); ?>
	<? else : ?>
		<div class="row">
			<div class="col-xs-12">
				<?= $this->Form->create('Criterio', array('url' => array('controller' => 'mercadoLibres', 'action' => 'actualizarPrecioPorCriterio'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
				<div id="criteriosPanel" class="panel panel-default collapse">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-refresh" aria-hidden="true"></i> Actualizar precios por criterios</h3>
					</div>
					<div class="panel-body">
						
						<div class="table-responsive">
							<table class="table js-clon-scope" data-limit="10">
								<thead>
									<tr>
										<th>Criterio</th>
										<th>Condición</th>
										<th>Valor</th>
										<th>Lógica</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody class="js-clon-contenedor meli-custom-list">
									<tr class="js-clon-base hidden">
										<td><?= $this->Form->select('criterios.999.criterio', array('precio' => 'Precio'),array('disabled' => true, 'class' => 'form-control', 'empty' => false )); ?></td>
										<td><?= $this->Form->select('criterios.999.condicion', array(
												'<=' => '<= (Menor o igual)',
												'>=' => '>= (Mayor o igual)',
												'<'       => '< (Menor)',
												'>'       => '> (Mayor)',
												'='       => '== (Igual)',
												), array('disabled' => true, 'class' => 'form-control', 'empty' => 'Seleccione' )); ?></td>
										<td><?= $this->Form->input('criterios.999.valor', array('disabled' => true, 'class' => 'form-control', 'required' => true, 'placeholder' => '50000')); ?></td>
										<td><?= $this->Form->select('criterios.999.criterio', array('AND' => 'Y'), array('disabled' => true, 'class' => 'form-control', 'empty' => false )); ?></td>
										<td>
											<a href="#" class="btn btn-xs btn-danger js-clon-eliminar"><i class="fa fa-trash"></i> Quitar</a>
											<!--<a href="#" class="btn btn-xs btn-primary js-clon-clonar"><i class="fa fa-clone"></i> Duplicar</a>-->
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<td><label>Sumar al precio</label></td>
										<td colspan="3"><?=$this->Form->input('valor', array('class' => 'form-control', 'placeholder' => '1200'))?></td>
										<td><a href="#" class="btn btn-xs btn-success js-clon-agregar"><i class="fa fa-plus"></i> Agregar otro criterio</a></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="panel-footer">
						<div class="col-xs-12">
							<div class="pull-right">
								<?= $this->Form->button('<i class="fa fa-refresh" aria-hidden="true"></i> Actualizar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
							</div>
						</div>
					</div>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<?= $this->Form->create('Filtro', array('url' => array('controller' => 'mercadoLibres', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
				<? 
					$by  = (isset($this->request->params['named']['by'])) ? $this->request->params['named']['by'] : '' ;
					$txt = (isset($this->request->params['named']['txt'])) ? $this->request->params['named']['txt'] : '' ;
				?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
					</div>
					<div class="panel-body">
						<div class="col-sm-4 col-xs-12">
							<div class="form-group">
								<label>Buscar por:</label>
								<?=$this->Form->select('by',
									array(
										'ide' => 'ID de producto', 
										'idm' => 'ID MEli',
										'nam' => 'Nombre'),
									array(
									'class' => 'form-control js-select-value',
									'empty' => 'Seleccione',
									'value' => $by
									)
								);?>
							</div>
						</div>
						<div class="col-sm-4 col-xs-12">
							<div class="form-group">
								<label>Coincidencia:</label>
								<?=$this->Form->input('txt', array(
									'type' => 'text',
									'class' => 'form-control',
									'value' => $txt
									));?>
							</div>
						</div>
						<div class="col-sm-4 col-xs-12">
							<div class="form-group">
								<?= $this->Form->label('marketplace_id', 'Seleccione marketplace'); ?>
								<?= $this->Form->select('marketplace_id', $marketplaces, array('class' => 'form-control', 'empty' => false, 'default' => $this->Session->read('Marketplace.id'))); ?>
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
						<h3 class="panel-title">Listado de productos</h3>
						<div class="btn-group pull-right">
						<? if ($permisos['add']) : ?>
							<?= $this->Html->link('<i class="fa fa-refresh"></i> Sincronizar', array('action' => 'sincronizar'), array('class' => 'btn btn-danger start-loading-then-redirect', 'escape' => false)); ?>
							<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Producto', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
						<? endif; ?>
							<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
						</div>
					</div>
					<div class="panel-body">

						<?=$this->element('contador_resultados');?>
						
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr class="sort">
										<th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th style="width: 250px;"><?= $this->Paginator->sort('producto', 'Producto', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('tienda_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('marketplace_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
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
										<td><?= h($mercadoLibr['Marketplace']['nombre']); ?>&nbsp;</td>
										<td><?= $publicado = (!empty($mercadoLibr['MercadoLibr']['id_meli'])) ? '<i class="fa fa-check-circle text-success fa-lg"></i>' : '<i class="fa fa-times-circle text-danger fa-lg"></i>' ;?></td>
										<td>
											<? if (isset($mercadoLibr['MeliItem']['status']) && $mercadoLibr['MeliItem']['status'] == 'under_review' && !empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
												<label class="label label-default">En revisión</label>
											<? endif; ?>
											<? if (isset($mercadoLibr['MeliItem']['status']) && $mercadoLibr['MeliItem']['status'] == 'closed' && !empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
												<label class="label label-danger">Cerrada</label>
											<? endif; ?>
											<? if (isset($mercadoLibr['MeliItem']['status']) && $mercadoLibr['MeliItem']['status'] == 'paused' && !empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
												<label class="label label-warning">Pausada</label>
											<? endif; ?>
											<? if (isset($mercadoLibr['MeliItem']['status']) && $mercadoLibr['MeliItem']['status'] == 'active' && !empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
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
												<? if ($mercadoLibr['MeliItem']['status'] == 'closed') : ?>
												<li><?= $this->Html->link('Abrir', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'active'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Activar item', 'escape' => false)); ?>
												</li>
												<li><?= $this->Html->link('Pausar', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'paused'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Pausar item', 'escape' => false)); ?>
												</li>
												<? endif; ?>

												<? if ($mercadoLibr['MeliItem']['status'] == 'paused') : ?>
												<li><?= $this->Html->link('Abrir', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'active'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Activar item', 'escape' => false)); ?>
												</li>
												<? endif; ?>

												<? if ($mercadoLibr['MeliItem']['status'] == 'active') : ?>
												<li><?= $this->Html->link('Pausar', array('action' => 'cambiarEstado', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli'], 'paused'), array('class' => '', 'rel' => 'tooltip', 'title' => 'Pausar item', 'escape' => false)); ?>
												</li>
												<? endif; ?>
											<? endif; ?>
											<? if ($permisos['delete']) :?>
												<? if (isset($mercadoLibr['MeliItem']['status']) && $mercadoLibr['MeliItem']['status'] == 'paused') : ?>
												<li><?= $this->Form->postLink('Eliminar de Meli', array('action' => 'delete', $mercadoLibr['MercadoLibr']['id'], $mercadoLibr['MercadoLibr']['id_meli']), array('class' => 'confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?></li>
												<? elseif (empty($mercadoLibr['MercadoLibr']['id_meli'])) : ?>
												<li><?= $this->Form->postLink('Eliminar', array('action' => 'delete', $mercadoLibr['MercadoLibr']['id']), array('class' => 'confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?></li>
												<? endif; ?>
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
	<? endif; ?>
</div>
