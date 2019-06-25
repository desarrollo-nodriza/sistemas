<div class="page-title">
	<h2><span class="fa fa-users"></span> Clientes</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'ventaClientes', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			
			<? 
				$findby  = (isset($this->request->params['named']['findby'])) ? $this->request->params['named']['findby'] : '' ;
				$buscar = (isset($this->request->params['named']['nombre_buscar'])) ? $this->request->params['named']['nombre_buscar'] : '' ;
			?>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Buscar por:</label>
							<?=$this->Form->select('findby', array(
								'email' => 'Email', 
								'nombre' => 'Nombre'),
								array(
								'class' => 'form-control',
								'empty' => 'No importa',
								'default' => $findby
								)
							);?>
						</div>
					</div>
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Ingrese email o nombre</label>
							<?= $this->Form->input('nombre_buscar', array('class' => 'form-control input-buscar', 'placeholder' => 'Ingrese email o nombre del cliente', 'value' => $buscar)); ?>
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
					<h3 class="panel-title"><i class="fa fa-list-ol" aria-hidden="true"></i> Listado de Clientes</h3>
					<div class="btn-group pull-right">
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					
					<?= $this->element('contador_resultados'); ?>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th style="width: 50px;"><?= $this->Paginator->sort('email', 'Email', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('nombre', 'Nombre', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('apellido', 'Apellidos', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('rut', 'Rut', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('telefono', 'Fono', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Registrado el', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $ventaClientes as $cliente ) : ?>
								<tr>
									<td><?= h($cliente['VentaCliente']['email']); ?>&nbsp;</td>
									<td><?= h($cliente['VentaCliente']['nombre']); ?>&nbsp;</td>
									<td><?= h($cliente['VentaCliente']['apellido']); ?>&nbsp;</td>
									<td><?= h($cliente['VentaCliente']['rut']); ?>&nbsp;</td>
									<td><?= h($cliente['VentaCliente']['telefono']); ?>&nbsp;</td>
									<td><?= h($cliente['VentaCliente']['created']); ?>&nbsp;</td>
									<td>
										<?= $this->Html->link( 'Ver', array('action' => 'view', $cliente['VentaCliente']['id']),
										array( 'escape' => false, 'class' => 'btn btn-xs btn-info btn-block' )
										);  ?>
									<? if ($permisos['edit']) : ?>
										<?= $this->Html->link( 'Editar', array('action' => 'edit', $cliente['VentaCliente']['id']),
										array( 'escape' => false, 'class' => 'btn btn-xs btn-success btn-block' )
										);  ?>
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
					<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 10, 'currentClass' => 'active', 'separator' => '')); ?>
					<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
				</ul>
			</div>
		</div>
	</div>

</div>