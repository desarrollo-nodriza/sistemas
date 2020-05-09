<div class="page-title">
	<h2><span class="fa fa-money"></span> Configuración de pagos</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-primary">
				<div class="panel-body text-center">
					<h2>Folio #<?=$factura['OrdenCompraFactura']['folio'];?></h2>
					<h3>OC relacionada #<?=$factura['OrdenCompra']['id']; ?></h3>
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

				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<caption>Factura</caption>
							<thead>
								<tr>
									<th>Folio</th>
									<th>Monto facturado</th>
									<th>Monto pagado</th>
								</tr>
							</thead>
							<tbody>
							<tr>
								<td><?=$factura['OrdenCompraFactura']['folio']; ?></td>
								<td><?=$this->Number->currency($factura['OrdenCompraFactura']['monto_facturado'], 'CLP'); ?></td>
								<td><?=$this->Number->currency($factura['OrdenCompraFactura']['monto_pagado'], 'CLP'); ?></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
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
							<? foreach ($factura['Pago'] as $ip => $pago) : ?>
							<tr>
								<td>
									<?= $this->Form->select(sprintf('%d.Pago.moneda_id', $ip), $monedas, array('default' => (!empty($pago['moneda_id'])) ? $pago['moneda_id'] : $factura['OrdenCompra']['moneda_id'], 'class' => 'form-control js-select-medio-pago', 'empty' => 'Seleccione', 'disabled' => ($pago['pagado']) ? true : false  )); ?>
								</td>
								<td>
									<!-- Hidden inputs-->
									<?= $this->Form->hidden(sprintf('%d.Pago.id', $ip), array('value' => $pago['id'])); ?>

									<?= $this->Form->input(sprintf('%d.Pago.identificador', $ip), array('type' => 'text','value' => $pago['identificador'], 'class' => 'form-control js-identificador-pago', 'disabled' => ($pago['pagado']) ? true : false )); ?>
								</td>
								<td>
									<?= $this->Form->select(sprintf('%d.Pago.cuenta_bancaria_id', $ip), $cuenta_bancarias, array('default' => $pago['cuenta_bancaria_id'], 'class' => 'form-control js-cuenta-pago', 'empty' => 'Seleccione cuenta', 'disabled' => ($pago['pagado']) ? true : false )); ?>
								</td>
								<td>
									<?= $this->Form->input(sprintf('%d.Pago.monto_pagado', $ip), array('type' => 'text','value' => $pago['monto_pagado'], 'class' => 'form-control not-blank is-number js-monto-pagado', 'disabled' => ($pago['pagado']) ? true : false )); ?>
								</td>
								<td>
									<?= $this->Form->input(sprintf('%d.Pago.fecha_pago', $ip), array('type' => 'text', 'value' => $pago['fecha_pago'], 'class' => 'form-control not-blank datepicker js-agendar', 'disabled' => ($pago['pagado']) ? true : false )); ?>
								</td>
								<td>
								<? if (!empty($pago['orden_compra_adjunto_id'])) : ?>
									<?= $this->Html->link('<i class="fa fa-file"></i> Ver documento', sprintf('%simg/OrdenCompraAdjunto/%d/%s', $this->webroot, $pago['orden_compra_adjunto_id'], $pago['OrdenCompraAdjunto']['adjunto'] ), array('class' => 'btn btn-xs btn-info', 'target' => '_blank', 'escape' => false)); ?>
								<? else : ?>
									--
								<? endif; ?>
								</td>
								<? if (!empty($pago['adjunto'])) : ?>
									<td>
										<?= $this->Html->link('<i class="fa fa-file"></i> Ver documento', sprintf('%simg/Pago/%d/%s', $this->webroot, $pago['id'], $pago['adjunto'] ), array('class' => 'btn btn-xs btn-info', 'target' => '_blank', 'escape' => false)); ?>
									</td>
								<? else : ?>
									<td>
										<?= $this->Form->input(sprintf('%d.Pago.adjunto', $ip), array('type' => 'file', 'class' => 'js-comprobante', 'disabled' => ($pago['pagado']) ? true : false )); ?>
									</td>
								<? endif; ?>
								<td>
									<?=$this->Form->input(sprintf('%d.Pago.pagado', $ip), array('class' => '', 'type' => 'checkbox', 'checked' => $pago['pagado'], 'disabled' => ($pago['pagado']) ? true : false)); ?>
								</td>
								<td valign="center">
									<? if (!$pago['pagado']) : ?>
									<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									<? endif; ?>
								</td>
							</tr>
							<? endforeach; ?>
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