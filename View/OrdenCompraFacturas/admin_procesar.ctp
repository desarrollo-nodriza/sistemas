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
	            <div class="widget-int"><?=CakeNumber::currency( array_sum(Hash::extract($oc['OrdenCompraFactura'], '{n}.monto_facturado')), 'CLP');?></div>
	        </div>
		</div>
		<div class="col-xs-12 col-md-6">
			<div class="widget widget-warning">
	            <div class="widget-title">Total pagos configurados</div>
	            <div class="widget-subtitle">bruto</div>
	            <div class="widget-int"><?=CakeNumber::currency( array_sum(Hash::extract($oc['Pago'], '{n}.monto_pagado')), 'CLP');?></div>
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
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Tipo de documento</th>
									<th>Folio</th>
									<th>Monto facturado</th>
									<th>Proveedor</th>
									<th>Pagada</th>
								</tr>
							</thead>
							<tbody>
							<? foreach ($oc['OrdenCompraFactura'] as $if => $factura) : ?>

								<?=$this->Form->input(sprintf('OrdenCompraFactura.%d.id', $if), array('value' => $factura['id'])); ?>

 								<tr>
									<td><?=$this->Html->tipoDocumento[$factura['tipo_documento']]; ?></td>
									<td><?=$factura['folio']; ?></td>
									<td><?=CakeNumber::currency($factura['monto_facturado'], 'CLP'); ?></td>
									<td><?=$oc['Proveedor']['nombre']; ?></td>
									<td><?= ($factura['pagada']) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>' ; ?></td>
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
					<h3 class="panel-title"><i class="fa fa-money"></i> Pagos disponibles para asignar</h3>
				</div>
				<div class="panel-body">
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
							<tbody>
							<? foreach ($oc['Pago'] as $ip => $pago) : ?>

								<?=$this->Form->input(sprintf('Pago.%d.id', $ip), array('value' => $pago['id'])); ?>

 								<tr>
									<td><?=$pago['identificador']; ?></td>
									<td><?=$oc['Moneda']['nombre']; ?></td>
									<td><?=$pago['CuentaBancaria']['alias']; ?> - n° <?=$pago['CuentaBancaria']['numero_cuenta']; ?></td>
									<td><?=$pago['fecha_pago']; ?></td>
									<td><?=CakeNumber::currency($pago['monto_pagado'], 'CLP'); ?></td>
									<td><?= ($pago['pagado']) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>' ; ?></td>
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
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar relaciones">
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>