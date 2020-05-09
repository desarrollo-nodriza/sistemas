<tr data-id="<?=$pago['Pago']['id'];?>">

	<td>
		<!-- Hidden inputs -->
		<input type="hidden" name="data[<?=$index;?>][Pago][id]" value="<?=$pago['Pago']['id'];?>"/>
		
		<?=$this->Form->select(sprintf('%d.Pago.moneda_id', $index), $monedas ,array('default' => $pago['Pago']['moneda_id'], 'class' => 'form-control', 'empty' => 'Seleccione')); ?>
		
	</td>
	<td>
		<input type="text" class="form-control" value="<?=$pago['Pago']['identificador']; ?>">
	</td>

	<td>
		<?=$this->Form->select(sprintf('%d.Pago.cuenta_bancaria_id', $index), $cuentaBancarias ,array('default' => $pago['Pago']['cuenta_bancaria_id'], 'class' => 'form-control', 'empty' => 'Seleccione')); ?>
	</td>

	<td>
		<?=$this->Form->input(sprintf('%d.Pago.monto_pagado', $index), array('value' => $pago['Pago']['monto_pagado'], 'class' => 'form-control is-number', 'title' => 'Monto actual:'.CakeNumber::currency($pago['Pago']['monto_pagado'], 'CLP'), 'placeholder' => 'Ingrese monto sin puntos', 'type' => 'text', 'label' => false, 'div' => false)); ?>
	</td>

	<td>
		<?=$this->Form->input(sprintf('%d.Pago.fecha_pago', $index), array('value' => $pago['Pago']['fecha_pago'], 'class' => 'form-control not-blank datepicker', 'placeholder' => '2019-10-10', 'type' => 'text', 'label' => false, 'div' => false)); ?>
	</td>

	<? if (!empty($pago['Pago']['adjunto'])) : ?>
	<td>
		<a class="btn btn-xs btn-primary" target="_blank" href="<?= Router::url( '/', true );?>webroot/img/<?= $pago['Pago']['adjunto']['path'];?>"><i class="fa fa-file-pdf-o"></i> Ver documento</a>
	</td>
	<? else : ?>
	<td>
		<?=$this->Form->input(sprintf('%d.Pago.adjunto', $index), array('type' => 'file', 'label' => false)); ?>
	</td>
	<? endif; ?>
	<td>
		<?=$this->Form->input(sprintf('%d.Pago.pagado', $index), array('class' => '', 'type' => 'checkbox', 'label' => false, 'div' => false)); ?>
	</td>

	<td valign="center">
		<button class="ver-pago btn-success" data-toggle="modal" data-target="#modal-pago-detalle-<?=$pago['Pago']['id']; ?>"><i class="fa fa-eye"></i></button>
		<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>


		<div id="modal-pago-detalle-<?=$pago['Pago']['id']; ?>" class="modal fade" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Detalles del pago #<?=$pago['Pago']['id']; ?></h4>
		      </div>
		      <div class="modal-body">
		        <div class="table-responsive">
					<table class="table table-bordered">
						<caption>Factura/as relacionadas</caption>
						<th>Folio</th>
						<th>OC</th>
						<th>Proveedor</th>
						<th>Monto facturado</th>
						<th>Monto pagado</th>
						<th>Estado</th>
						<tbody>
						<? foreach ($pago['OrdenCompraFactura'] as $if => $f): ?>
							<tr>
								<td><?=$this->Html->link('#' . $f['id'], array('controller' => 'ordenCompraFacturas', 'action' => 'view', $f['id']), array('target' => '_blank')); ?></td>
								<td><?=$this->Html->link('#' . $f['OrdenCompra']['id'], array('controller' => 'ordenCompras', 'action' => 'view', $f['OrdenCompra']['id']), array('target' => '_blank')); ?></td>
								<td><?=$f['OrdenCompra']['Proveedor']['nombre']; ?><br><?=$f['OrdenCompra']['Proveedor']['rut_empresa']; ?></td>
								<td><?=CakeNumber::currency($f['monto_facturado'], 'CLP');?></td>
								<td><?=CakeNumber::currency($f['monto_pagado'], 'CLP');?></td>
								<td><?= ($f['pagada']) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>' ; ?></td>
							</tr>
						<? endforeach; ?>
						</tbody>
					</table>					
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>
		  </div>
		</div>

	</td>

</tr>

