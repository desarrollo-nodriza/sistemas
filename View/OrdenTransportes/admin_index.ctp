<div class="page-title">
	<h2><span class="fa fa-money"></span> Ordenes para transporte</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'ordenTransporte', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$id_filtro 	= (isset($this->request->params['named']['id'])) ? $this->request->params['named']['id'] : '' ;
				$ref_filtro = (isset($this->request->params['named']['ref'])) ? $this->request->params['named']['ref'] : '' ;
				$sta_filtro = (isset($this->request->params['named']['sta'])) ? $this->request->params['named']['sta'] : '' ;
				$mdp_filtro = (isset($this->request->params['named']['mdp'])) ? $this->request->params['named']['mdp'] : '' ;
				$mpa_filtro = (isset($this->request->params['named']['mpa'])) ? $this->request->params['named']['mpa'] : '' ;
				$men_filtro = (isset($this->request->params['named']['men'])) ? $this->request->params['named']['men'] : '' ;
				$mde_filtro = (isset($this->request->params['named']['mde'])) ? $this->request->params['named']['mde'] : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Ingrese Id</label>
							<?= $this->Form->input('id', array('class' => 'form-control', 'placeholder' => 'Ingrese id del pedido', 'value' => $id_filtro)); ?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Ingrese Referencia</label>
							<?= $this->Form->input('ref', array('class' => 'form-control', 'placeholder' => 'Ingrese referencia del pedido', 'value' => $ref_filtro)); ?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Estado</label>
							<?=$this->Form->select('sta', $estados,
								array(
								'class' => 'form-control',
								'empty' => 'Seleccione estado',
								'value' => $sta_filtro
								)
							);?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Medio de pago</label>
							<?=$this->Form->select('mdp', $medios_de_pago,
								array(
								'class' => 'form-control',
								'empty' => 'Seleccione medio de pago',
								'value' => $mdp_filtro
								)
							);?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<br>
							<label>Monto pagado</label>
							<?=$this->Form->select('mpa', $rangosPagado,
								array(
								'class' => 'form-control',
								'empty' => 'Seleccione rango',
								'value' => $mpa_filtro
								)
							);?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<br>
							<label>Monto envío</label>
							<?=$this->Form->select('men', $rangosEnvio,
								array(
								'class' => 'form-control',
								'empty' => 'Seleccione rango',
								'value' => $men_filtro
								)
							);?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<br>
							<label>Monto descuento</label>
							<?=$this->Form->select('mde', $rangosDescuento,
								array(
								'class' => 'form-control',
								'empty' => 'Seleccione rango',
								'value' => $mde_filtro
								)
							);?>
						</div>
					</div>
					<!--<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<br>
							<label>Estado DTE</label>
							<?=$this->Form->select('dte', $this->Html->dteEstado('', true),
								array(
								'class' => 'form-control',
								'empty' => 'Seleccione Estado',
								'value' => $dte_filtro
								)
							);?>
						</div>
					</div>-->
					<div class="col-sm-3 col-xs-12">
						<br>
						<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Filtrar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
					</div>
					<?= $this->Form->end(); ?>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
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
					<h3 class="panel-title">Listado de Ordenes <small>(<?=$totalMostrados;?> pedidos)</small></h3>
					<div class="btn-group pull-right">
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id_order', 'Id', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('reference', 'Referencia', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('name', 'Estado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('payment', 'Medio de pago', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('total_paid', 'T. Pagado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('total_shipping', 'T. Envío', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('date_add', 'Creado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('ot', 'OT', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $ordenes as $orden ) : ?>
								<tr>
									<td><?= h($orden['Orden']['id_order']); ?>&nbsp;</td>
									<td><?= h($orden['Orden']['reference']); ?>&nbsp;</td>
									<td><label class="label" style="background-color: <?=$orden['OrdenEstado']['color']?>"><?= h($orden['OrdenEstado']['Lang'][0]['OrdenEstadoIdioma']['name']); ?></label></td>
									<td><?= h($orden['Orden']['payment']); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($orden['Orden']['total_paid'], 'CLP'); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($orden['Orden']['total_shipping'], 'CLP'); ?>&nbsp;</td>
									<td><?= h($orden['Orden']['date_add']); ?>&nbsp;</td>
									<td><?= $ot = (!empty($orden['OrdenTransporte'])) ? '<label class="label label-success">' . count($orden['OrdenTransporte']) . ' OT emitida/s' . '</label>' : 'No emitido'; ?>&nbsp;</td>
									<td>
									<? if ($permisos['edit']) : ?>
										<? if (!empty($orden['OrdenTransporte'])) : ?>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Ver OT´s', array('action' => 'orden', $orden['Orden']['id_order']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Ver OT´s', 'escape' => false)); ?>
										<? else :?>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Crear OT´s', array('action' => 'orden', $orden['Orden']['id_order']), array('class' => 'btn btn-xs btn-primary', 'rel' => 'tooltip', 'title' => 'Crear OT´s', 'escape' => false)); ?>
										<? endif; ?>
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