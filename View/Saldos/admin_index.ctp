<div class="page-title">
	<h2><span class="fa fa-money"></span> Saldos por proveedores</h2>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Saldo', array('url' => array('controller' => 'saldos', 'action' => 'add'), 'inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-form-saldo')); ?>
			<div class="panel panel-primary">
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-plus"></i> Crear saldos manualmente</h3></div>
				<div class="panel-body">
					<div class="table-responsive" style="overflow-x: inherit;">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Proveedor (<span class="text-danger">*</span>)</th>
									<th>Orden de compra</th>
									<th>Factura relacionada</th>
									<th>Pago relacionado</th>
									<th>Tipo</th>
									<th>Monto (<span class="text-danger">*</span>)</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?=$this->Form->select('proveedor_id', $proveedoresLista, array('class' => 'form-control not-blank', 'empty' => 'Seleccione', 'data-live-search' => 'true')); ?></td>
									<td><?=$this->Form->select('orden_compra_id', $ocs, array('class' => 'form-control select', 'empty' => 'Seleccione', 'data-live-search' => 'true')); ?></td>
									<td><?=$this->Form->select('orden_compra_factura_id', $facturas, array('class' => 'form-control select', 'empty' => 'Seleccione', 'data-live-search' => 'true')); ?></td>
									<td><?=$this->Form->select('pago_id', $pagos, array('class' => 'form-control select', 'empty' => 'Seleccione', 'data-live-search' => 'true')); ?></td>
									<td><?=$this->Form->select('tipo', $tipoSaldo, array('class' => 'form-control', 'empty' => false)); ?></td>
									<td><?=$this->Form->input('monto', array('class' => 'form-control not-blank is-number', 'placeholder' => 'Ingrese monto sin puntos')); ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Form->button('<i class="fa fa-plus" aria-hidden="true"></i> Agregar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-success btn-block')); ?>
					</div>
				</div>
			</div>	
			<?= $this->Form->end(); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Listado de Proveedores</h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered datatable">
								<thead>
									<tr class="sort">
										<th>Proveedor</th>
										<th>Rut empresa</th>
										<th>Encargado</th>
										<th>Email encargado</th>
										<th>Fono encargado</th>
										<th>Saldo</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $proveedores as $ip => $proveedor ) : ?>
									<tr>
										<td><?=$this->Html->link($proveedor['Proveedor']['nombre'], array('controller' => 'proveedores', 'action' => 'edit', $proveedor['Proveedor']['id']), array('target' => '_blank'));?></td>
										<td><?=$proveedor['Proveedor']['rut_empresa'];?></td>
										<td><?=$proveedor['Proveedor']['nombre_encargado'];?></td>
										<td><?=$proveedor['Proveedor']['email_contacto'];?></td>
										<td><?=$proveedor['Proveedor']['fono_contacto'];?></td>
										<td><?=CakeNumber::currency($proveedor['Proveedor']['saldo'], 'CLP');?></td>
										<td>
										<? if ($proveedor['Proveedor']['saldo'] <= 0) : ?>
										<?=$this->Html->link('<i class="fa fa-money"></i> Descontar', array('action' => 'usar', $proveedor['Proveedor']['id']), array('class' => 'btn btn-block btn-danger', 'disabled' => true, 'escape' => false));?>
										<? else : ?>
										<?=$this->Html->link('<i class="fa fa-money"></i> Descontar', array('action' => 'usar', $proveedor['Proveedor']['id']), array('class' => 'btn btn-block btn-primary', 'escape' => false));?>
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
	</div>
</div>
