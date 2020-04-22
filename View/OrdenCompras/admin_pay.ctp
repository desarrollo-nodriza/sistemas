<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Pagar OC <small class="text-mutted">(Validada por <?=$ocs['OrdenCompra']['nombre_validado'];?>)</small></h2>
</div>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizontal form-pay', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'), 'data-id' => $ocs['OrdenCompra']['id'])); ?>

	<?=$this->Form->input('id', array('value' => $ocs['OrdenCompra']['id']));?>
	<?=$this->Form->hidden('fecha_pagado', array('value' => date('Y-m-d H:i:s')));?>
	<?=$this->Form->hidden('descuento', array('value' => $ocs['OrdenCompra']['descuento']));?>
	<?=$this->Form->hidden('descuento_monto', array('value' => $ocs['OrdenCompra']['descuento_monto']));?>
	<?=$this->Form->hidden('total', array('value' => $ocs['OrdenCompra']['total']));?>

<div class="page-content-wrap">
	
	<? if (!empty($ocs['OrdenCompra']['comentario_validar'])) : ?>
		
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-comments"></i> Anotación del administrador</h3>
					</div>
					<div class="panel-body">
						<?=$this->Text->autoParagraph($ocs['OrdenCompra']['comentario_validar']);?>
					</div>
				</div>
			</div>
		</div>

	<? endif; ?>
	
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title text-uppercase">
						<b>Proveedor:</b> <?=$ocs['Proveedor']['nombre'];?><br>
						<b>Rut:</b> <?=$ocs['Proveedor']['rut_empresa'];?><br>
					</h3>
				</div>
				<div class="panel-body">
					
				    <div class="row">
						<div class="col-xs-12 form-group text-center">
							<h1>OC N°<?=$ocs['OrdenCompra']['id'];?></h1>
							<h2>Monto a pagar: <span id="total_bruto"><?=CakeNumber::currency($ocs['OrdenCompra']['total'] , 'CLP');?></span></h2>
							<h3>Descuento aplicado: <span id="descuento_aplicado"><?= (empty($ocs['OrdenCompra']['descuento'])) ? 0 : $ocs['OrdenCompra']['descuento']; ?></span>%</h3>
							<!--<h3>Pendiente de pago: <span id="total_pendiente"><?=CakeNumber::currency($ocs['OrdenCompra']['pendiente_pago'], 'CLP');?></span></h3>-->
						</div>
					</div>
				</div>

				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 form-group">
                            <?= $this->Form->label('moneda_id', 'Medio de pago (requerido)');?></p>
                            <?= $this->Form->input('moneda_id', array('class' => 'form-control not-blank js-select-moneda', 'empty' => 'Seleccione', 'default' => $ocs['OrdenCompra']['moneda_id'], 'disabled' => true )); ?>
							<span class="help-block">El medio de pago usado, se heredará a las facturas recibidas para ésta OC</span>
						</div>
						<div class="col-xs-12 js-adjuntos">
							<div class="table-responsive">
								<table class="table table-bordered js-clone-wrapper" data-filas="10">
									<thead>
										<th>N° identificador</th>
										<th>Documento</th>
										<th>Incluir en email</th>
										<th><a href="#" class="copy_tr btn btn-rounded btn-primary"><span class="fa fa-plus"></span> agregar</a></th>
									</thead>
									<tbody>
										<tr class="hidden clone-tr">
											<td>
												<?= $this->Form->input('OrdenCompraAdjunto.999.identificador', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank', 'placeholder' => 'Ingrese N° cheque/transferencia')); ?>		
											</td>
											<td>
												<?= $this->Form->input('OrdenCompraAdjunto.999.adjunto', array('disabled' => true, 'type' => 'file', 'class' => 'not-blank')); ?>		
											</td>
											<td>
												<?= $this->Form->input('OrdenCompraAdjunto.999.incluir_email', array('disabled' => true, 'type' => 'checkbox', 'class' => '', 'checked' => true)); ?>		
											</td>
											<td valign="center">
												<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-xs-12 form-group">
							<?=$this->Form->label('comentario_finanza', 'Anotación (opcional)');?></p>
		        			<?=$this->Form->input('comentario_finanza', array('class' => 'form-control', 'placeholder' => 'Ingrese una anotación...')); ?>
						</div>
					</div>
				</div>
				
			</div>

			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-users"></i> Destinatarios</h3>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<?=$this->Form->label('email_contacto_empresa', 'Se enviará la OC a los siguientes destinatarios:');?>
						<ul>
							<? foreach ($ocs['Proveedor']['meta_emails'] as $i => $email) : ?>
							<? if ($email['activo']) : ?>
								<li><b><?=$email['email']; ?></b> (<?=$email['tipo']?>)</li>
								<?=$this->Form->hidden(sprintf('email_contacto_empresa.%d.email', $i), array('value' => $email['email'])); ?>
								<?=$this->Form->hidden(sprintf('email_contacto_empresa.%d.tipo', $i), array('value' => $email['tipo'])); ?>
							<? endif; ?>
							<? endforeach; ?>
						</ul>
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" class="btn btn-success esperar-carga" autocomplete="off" data-loading-text="Espera un momento..."><i class="fa fa-check"></i> Pagar y enviar a Proveedor</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->Form->end(); ?>

<script type="text/javascript">
	$('.reject-button').on('click', function(e){
		e.preventDefault();

		var input = '<input type="hidden" name="data[OrdenCompra][estado]" value="rechazado">';

		$('form').append(input);
		$('form').submit();
	});

</script>
