<!-- Modal crear cliente -->
<div class="modal fade" id="modalCrearPago" tabindex="-1" role="dialog" aria-labelledby="modalCrearPagoLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?= $this->Form->create('Pago', array('class' => 'form-horizontal js-formulario js-ajax-form', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => false))); ?>
      
		<?=$this->Form->hidden('access_token', array('value' => $token)); ?>
		<?=$this->Form->hidden('total_pagar', array('value' => $total_facturado - $total_pagado)); ?>
		
		<? foreach ($facturas as $if => $f) : ?>	
		<?=$this->Form->hidden(sprintf('OrdenCompraFactura.%d.factura_id', $if), array('value' => $f['OrdenCompraFactura']['id'])); ?>
		<? endforeach; ?>

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalCrearPagoLabel"><i class="fa fa-money"></i> Crear Pago</h4>
      </div>
      <div class="modal-body">
      	
      		<div class="table-responsive">
				<table class="table table-bordered">	
					<tr>
						<th>Medio de pago</th>
						<td>
							<?=$this->Form->select('moneda_id', $monedas, array('label' => false, 'class' => 'form-control', 'empty' => 'Seleccione', 'default' => ''))?>
							<span class="help-block">Solo se permite pagar y agendar.</span>				
						</td>
					</tr>
					<tr>
						<th>Cuenta bancaria</th>
						<td><?=$this->Form->select('cuenta_bancaria_id', $cuenta_bancarias, array('label' => false, 'class' => 'form-control', 'empty' => 'Seleccione'))?></td>
					</tr>
					<tr>
						<th>Identificador</th>
						<td><?=$this->Form->input('identificador', array('class' => 'form-control not-blank', 'placeholder' => 'Ej: N° de Cheque, N° transferencia'))?></td>
					</tr>
					<tr>
						<th>Monto a pagar</th>
						<td>
							<?=$this->Form->input('monto_pagado', array('type' => 'text', 'class' => 'form-control is-number not-blank', 'placeholder' => 'Ingrese monto pagado', 'min' => 0 ))?>
							<span class="help-block">Monto a pagar $<span class="total-a-pagar"><?=($total_facturado - $total_pagado); ?></span></span>		
						</td>
					</tr>
					<tr>
						<th>Fecha de pago</th>
						<td><?=$this->Form->input('fecha_pago', array('type' => 'text', 'class' => 'form-control datepicker not-blank', 'placeholder' => '2020-01-30'))?></td>
					</tr>
					<tr>
						<th>Comprobante</th>
						<td><?=$this->Form->input('adjunto', array('class' => '', 'type' => 'file'))?></td>
					</tr>
					<tr>
						<th>Pago finalizado</th>
						<td><?=$this->Form->input('pagado', array('class' => '', 'type' => 'checkbox')); ?></td>
					</tr>
				</table>
      		</div>

      		<div class="alert alert-danger hidden">
				<span id="error-mensaje-pago"></span>
			</div>

			<div class="alert alert-success hidden">
				<span id="success-mensaje-pago"></span>
			</div>
      	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Crear pago</button>
      </div>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>
<!-- Fin modal pago -->

<?= $this->Html->script(array(
	'/backend/js/monedas',
	'/backend/js/pagos2')); ?>
<?= $this->fetch('script'); ?>