<div class="page-title">
	<h2><span class="fa fa-users"></span> Clientes</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'clientes', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
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
								'empty' => 'No importa'
								)
							);?>
						</div>
					</div>
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Ingrese email o nombre</label>
							<?= $this->Form->input('nombre_buscar', array('class' => 'form-control input-buscar', 'placeholder' => 'Ingrese email o nombre del cliente')); ?>
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
	
<? if(isset($clientes)) : ?>
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
					<div class="table-responsive">
						<table class="table">
							<tbody>
								<tr>
									<td><b>Total clientes:</b> <?=sprintf('%d clientes registrados en el sitio', $total)?></td>
								</tr>
								<tr>
								<? if ( !empty($textoBuscar) ) : ?>
										<td><?=sprintf('<b>%d Clientes encontrados para "%s"</b>  ', $totalMostrados, $textoBuscar)?></td>
								<? endif; ?>
								</tr>
							</tbody>
						</table>
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('email', 'Email', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('firstname', 'Nombre', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('lastname', 'Apellidos', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('date_add', 'Registrado el', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $clientes as $cliente ) : ?>
								<tr>
									<td><?= h($cliente['Cliente']['email']); ?>&nbsp;</td>
									<td><?= h($cliente['Cliente']['firstname']); ?>&nbsp;</td>
									<td><?= h($cliente['Cliente']['lastname']); ?>&nbsp;</td>
									<td><?= h($cliente['Cliente']['date_add']); ?>&nbsp;</td>
									<? if ($permisos['view']) : ?>
									<td><?= $this->Html->link( 'Ver', array('action' => 'view', $cliente['Cliente']['id_customer']),
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
					<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
					<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
				</ul>
			</div>
		</div>
	</div>
<? endif; ?>
</div>

<? 	
	if ( ! empty( $this->request->params['named']['tienda'] ) ) :
		echo "<script type='text/javascript'>";
		echo "$('#FiltroTienda').val('" . $this->request->params['named']['tienda'] . "');";
		echo "</script> ";
	endif;

	if ( ! empty( $this->request->params['named']['findby'] ) ) :
		echo "<script type='text/javascript'>";
		echo "$('#FiltroFindby').val('" . $this->request->params['named']['findby'] . "');";
		echo "</script> ";
	endif;


	if ( ! empty( $this->request->params['named']['nombre_buscar'] ) ) :
		echo "<script type='text/javascript'>";
		echo "$('#FiltroNombreBuscar').val('" . $this->request->params['named']['nombre_buscar'] . "');";
		echo "$('#FiltroNombreBuscar').removeAttr('disabled');";
		echo "</script> ";
	endif;

?>
