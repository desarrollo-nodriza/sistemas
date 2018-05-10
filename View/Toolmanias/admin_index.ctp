<div class="page-title">
	<h2><span class="fa fa-shopping-bag"></span> Productos de Toolmania</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Toolmania', array('action' => 'index', 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<label>Buscar por:</label>
							<?=$this->Form->select('findby', array(
								'code' => 'Código referencia', 
								'name' => 'Nombre'),
								array(
								'class' => 'form-control select',
								'empty' => false
								)
							);?>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
							<label>Ingrese código o nombre</label>
							<?= $this->Form->input('nombre_buscar', array('class' => 'form-control input-buscar', 'placeholder' => 'Ingrese nombre o código de un producto')); ?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Buscar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
					</div>
					<?= $this->Form->end(); ?>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list-ol" aria-hidden="true"></i> Listado de Productos</h3>
					<div class="btn-group pull-right">
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tbody>
								<tr>
									<?
									if ( !empty($textoBuscar) ) { ?>
										<td><?=sprintf('<b>%d Productos encontrados para "%s"</b>  ', $totalMostrados, $textoBuscar)?></td>
									<?
									}
									?>
									<td><b>Total de productos:</b> <?=sprintf('%d producto/s activos actualmente en el sitio', $total)?></td>
									
								</tr>
							</tbody>
						</table>
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('reference', 'Referencia', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('name', 'Nombre', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('price', 'Valor', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('show_price', 'Valor Vista', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('', 'Categorias', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('url_image', 'Imagen', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $productos as $producto ) : ?>
								<tr>
									<td><span class="label label-success label-form"><?= h($producto['Toolmania']['reference']); ?>&nbsp;</span></td>
									<td><?= h($producto['pl']['name']); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($producto['Toolmania']['price'], 'CLP'); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($producto['Toolmania']['show_price'], 'CLP'); ?>&nbsp;</td>
									<td><?
									foreach ($producto['Categoria'] as $categoria) : ?>
										<label class="label label-primary label-form"><?=h($categoria['nombre']); ?></label>
								<?	endforeach;
									?></td>
									<td><?= $this->Html->image($producto['0']['url_image'], array('class' => 'img-responsive', 'width' => '100px')); ?>&nbsp;</td>
									<? if ($permisos['edit']) : ?>
									<td><?= $this->Html->link(
										'Editar', array('action' => 'associate', $producto['Toolmania']['id_product']),
										array( 
											'escape' => false, 
											'class' => 'btn btn-xs btn-success btn-block'
										)
										); 
										?>
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
