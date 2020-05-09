<div class="page-title">
	<h2><span class="fa fa-money"></span> Configuracion de pagos</h2>
</div>

<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			<h3>Facturas seleccionadas</h3>
		</div>
	</div>

	<? foreach ($facturas as $if => $factura) : ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-primary panel-collapse panel-toggled">
					<div class="panel-heading">
						<h3 class="panel-title">Factura #<?=$factura['OrdenCompraFactura']['folio']; ?> - Total Facturado: <?=CakeNumber::currency( $factura['OrdenCompraFactura']['monto_facturado'], 'CLP')?> - OC relacionada: #<?=$factura['OrdenCompra']['id']; ?> - M de pago: <span class="label label-form label-success"><?=$factura['OrdenCompra']['Moneda']['nombre']; ?></span></h3>
						<ul class="panel-controls">
                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                        </ul>
					</div>
					<div class="panel-body text-center">
						<h2>Factura #<?=$factura['OrdenCompraFactura']['folio']; ?></h2>
						<h3 data-toggle="tooltip" data-placement="top" title="<?=CakeNumber::currency($factura['OrdenCompra']['total'], 'CLP')?>">OC relacionada #<?=$factura['OrdenCompra']['id']; ?></h3>
						<h1 id="total-facturado" data-total="<?=$factura['OrdenCompraFactura']['monto_facturado']; ?>">Total Facturado <?=CakeNumber::currency( $factura['OrdenCompraFactura']['monto_facturado'], 'CLP')?></h1>
						<h3 style="font-size: 18px !important;" class="label label-success">Moneda seleccionada: <?=$factura['OrdenCompra']['Moneda']['nombre']; ?></h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered">
								<caption>Información del proveedor</caption>
								<thead>
									<tr>
										<th>Nombre del proveedor</th>
										<th>Rut empresa</th>
										<th>Giro comercial</th>
										<th>Dirección comercial</th>
										<th>Fono contacto</th>
										<th>Email contacto</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?=$factura['Proveedor']['nombre']; ?></td>
										<td><?=$factura['Proveedor']['rut_empresa']; ?></td>
										<td><?=$factura['Proveedor']['giro']; ?></td>
										<td><?=$factura['Proveedor']['direccion']; ?></td>
										<td><?=$factura['Proveedor']['fono_contacto']; ?></td>
										<td><?=$factura['Proveedor']['email_contacto']; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	<? endforeach; ?>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Pago', array('data-oc' => $factura['OrdenCompra']['id'],'class' => 'form-horizontal js-validate-pago js-config-pago', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
			
			<div class="panel pane-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-money"></i> Creación & configuración de pagos</h3>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr js-dividir-montos"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Método de pago</th>
									<th>Identificador</th>
									<th>Cuenta bancaria</th>
									<th>Monto</th>
									<th>Fecha de pago</th>
									<th>Documento relacionado</th>
									<th>Documento</th>
									<th>Pago finalizado</th>
									<th>Quitar</th>
								</tr>
							</thead>
							<tbody>
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->select('999.Pago.moneda_id', $monedas, array('disabled' => true, 'class' => 'form-control js-select-medio-pago not-blank', 'empty' => 'Seleccione')); ?>
									</td>
									<td>
										<?= $this->Form->hidden('999.Pago.orden_compra_id', array('disabled' => true, 'value' => $factura['OrdenCompra']['id'])); ?>
										<?= $this->Form->input('999.Pago.identificador', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-identificador-pago', 'placeholder' => 'N° transacción/cheque')); ?>
									</td>
									<td>
										<?= $this->Form->select('999.Pago.cuenta_bancaria_id', $cuenta_bancarias, array('disabled' => true, 'class' => 'form-control js-cuenta-pago', 'empty' => 'Seleccione cuenta')); ?>
									</td>
									<td>
										<?= $this->Form->input('999.Pago.monto_pagado', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank is-number js-monto-pagado monto-modificable', 'placeholder' => 'Ingrese monto del pago')); ?>
									</td>
									<td>
										<?= $this->Form->input('999.Pago.fecha_pago', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank datepicker js-agendar', 'placeholder' => 'Fecha de pago')); ?>
									</td>
									<td>
										--
									</td>
									<td>
										<?= $this->Form->input('999.Pago.adjunto', array('disabled' => true, 'type' => 'file', 'class' => 'not-blank js-comprobante')); ?>
									</td>
									<td>
										<?= $this->Form->input('999.Pago.pagado', array('disabled' => true, 'type' => 'checkbox', 'class' => 'js-finalizar', 'checked' => true)); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
							
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<?= $this->Form->submit('Guardar cambios', array('class' => 'btn btn-success pull-right')); ?>
				</div>
			</div>
			<?= $this->Form->end(); ?>
		</div>
	</div>
</div>