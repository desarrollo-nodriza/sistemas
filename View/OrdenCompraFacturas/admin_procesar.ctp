<div class="page-title">
	<h2><span class="fa fa-money"></span> Relacionar facturas - pagos</h2>
</div>

<?= $this->Form->create('OrdenCompraFactura', array('url' => array('controller' => 'ordenCompraFacturas', 'action' => 'relacionar_facturas_pagos'), 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="widget widget-danger">
	            <div class="widget-title">Total facturado</div>
	            <div class="widget-subtitle">bruto</div>
	            <div class="widget-int"><?=CakeNumber::currency( array_sum(Hash::extract($facturas, '{n}.OrdenCompraFactura.monto_facturado')), 'CLP');?></div>
	        </div>
		</div>
		<div class="col-xs-12 col-md-6">
			<div class="widget widget-warning">
	            <div class="widget-title">Total pagos configurados</div>
	            <div class="widget-subtitle">bruto</div>
	            <div class="widget-int" id="monto-pagado-text" data-pagado="<?=array_sum(Hash::extract($pagos, '{n}.Pago.monto_pagado'));?>"><?=CakeNumber::currency( array_sum(Hash::extract($pagos, '{n}.Pago.monto_pagado')), 'CLP');?></div>
	        </div>
		</div>
	</div>
	<div class="row" style="display: flex;">
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-file"></i> Facturas seleccionadas</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered" id="facturas">
							<thead>
								<tr>
									<th>Folio</th>
									<th>Monto facturado</th>
									<th>Proveedor</th>
									<th>OC - Medio de pago</th>
									<th>Pagada</th>
								</tr>
							</thead>
							<tbody>
							<? foreach ($facturas as $if => $factura) : ?>

								<?=$this->Form->input(sprintf('OrdenCompraFactura.%d.id', $if), array('value' => $factura['OrdenCompraFactura']['id'])); ?>

 								<tr data-id="<?=$factura['OrdenCompraFactura']['id']; ?>">
									<td><?=$factura['OrdenCompraFactura']['folio']; ?></td>
									<td><?=CakeNumber::currency($factura['OrdenCompraFactura']['monto_facturado'], 'CLP'); ?></td>
									<td><?=$factura['Proveedor']['nombre']; ?></td>
									<td>#<?=$factura['OrdenCompra']['id'];?> - <?=$factura['OrdenCompra']['Moneda']['nombre']; ?></td>
									<td><?= ($factura['OrdenCompraFactura']['pagada']) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>' ; ?></td>
								</tr>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-money"></i> Pagos relacionados</h3>
					<? if (array_sum(Hash::extract($pagos, '{n}.Pago.monto_pagado')) < array_sum(Hash::extract($facturas, '{n}.OrdenCompraFactura.monto_facturado'))) : ?>
					<ul class="panel-controls">
						<li style="color: #000; line-height: 30px; margin-right: 15px;">Crear pago</li>
						<li><a href="#" data-toggle="modal" data-target="#modalCrearPago"><span class="fa fa-plus"></span></a></li>
					</ul>
					<? endif; ?>
				</div>
				<div class="panel-body">

					<? if (empty($pagos)) : ?>
					<div id="alerta-no-pagos" class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <strong>¡Ups!</strong> No tiene pagos disponibles para estas facturas.
                    </div>
					<? endif; ?>

					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Identificador</th>
									<th>Método de pago</th>
									<th>Cuenta bancaria</th>
									<th>Fecha de pago</th>
									<th>Monto del pago</th>
									<th>Pagado</th>
								</tr>
							</thead>
							<tbody id="pago-wrapper">
							<? foreach ($pagos as $ip => $pago) : ?>
								<?=$this->Form->hidden(sprintf('Pago.%d.pago_id', $pago['Pago']['id']), array('value' => $pago['Pago']['id'])); ?>
								<?=$this->Form->hidden(sprintf('Pago.%d.monto_pagado', $pago['Pago']['id']), array('value' => $pago['Pago']['monto_pagado'])); ?>
 								<tr>
									<td><?=$pago['Pago']['identificador']; ?></td>
									<td><?=$pago['Pago']['Moneda']['nombre']; ?></td>
									<td><?=$pago['Pago']['CuentaBancaria']['alias']; ?> - n° <?=$pago['Pago']['CuentaBancaria']['numero_cuenta']; ?></td>
									<td><?=$pago['Pago']['fecha_pago']; ?></td>
									<td><?=CakeNumber::currency($pago['Pago']['monto_pagado'], 'CLP'); ?></td>
									<td><?= ($pago['Pago']['pagado']) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>' ; ?></td>
								</tr>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-primary">
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Continuar">
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>

<?=$this->element('Pagos/form-add', array('token' => $this->Session->read('Auth.Administrador.token.token'), 'monedas' => $monedas, 'cuenta_bancarias' => $cuenta_bancarias, 'total_facturado' => array_sum(Hash::extract($facturas, '{n}.OrdenCompraFactura.monto_facturado')), 'total_pagado' => array_sum(Hash::extract($pagos, '{n}.Pago.monto_pagado')), 'facturas' => $facturas)); ?>
