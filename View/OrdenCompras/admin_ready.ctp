<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> OC Lista para envio</h2>
</div>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	
	<? if (!empty($this->request->data['OrdenCompra']['comentario_validar'])) : ?>
		
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-comments"></i> Anotación del administrador</h3>
					</div>
					<div class="panel-body">
						<?=$this->Text->autoParagraph($this->request->data['OrdenCompra']['comentario_validar']);?>
					</div>
				</div>
			</div>
		</div>

	<? endif; ?>


	<? if (!empty($this->request->data['OrdenCompra']['comentario_finanza'])) : ?>
		
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-money"></i> Anotación de finanzas</h3>
					</div>
					<div class="panel-body">
						<?=$this->Text->autoParagraph($this->request->data['OrdenCompra']['comentario_finanza']);?>
					</div>
				</div>
			</div>
		</div>

	<? endif; ?>

	<div class="row">
		<? if (!empty($this->request->data['OrdenCompra']['adjunto']['path'])) : ?>
		<div class="col-xs-12 col-sm-6">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-file"></i> Documento Finanzas</h3>
				</div>
				<div class="panel-body">
					<?= $this->Html->link(
					'<i class="fa fa-eye"></i> Comprobante de pago',
					sprintf('/img/%s', $this->request->data['OrdenCompra']['adjunto']['path']),
					array(
						'class' => 'btn btn-info btn-lg btn-block', 
						'target' => '_blank', 
						'fullbase' => true,
						'escape' => false) 
					); ?>
				</div>
			</div>
		</div>
		<? endif; ?>

		<? if (empty($this->request->data['OrdenCompra']['pdf'])) : ?>
		<div class="col-xs-12 <?= (!empty($this->request->data['OrdenCompra']['adjunto']['path'])) ? 'col-sm-6' : ''; ?>">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-file"></i> Documento OC</h3>
				</div>
				<div class="panel-body">
					<?= $this->Html->link(
					'<i class="fa fa-refresh"></i> Generar OC',
					array(
						'action' => 'generar_pdf', $this->request->data['OrdenCompra']['id']
					),
					array(
						'class' => 'btn btn-success btn-lg btn-block', 
						'escape' => false) 
					); ?>
				</div>
			</div>
		</div>
		<? else : ?>
		<div class="col-xs-12 <?= (!empty($this->request->data['OrdenCompra']['adjunto']['path'])) ? 'col-sm-6' : ''; ?>">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-file"></i> Documento OC</h3>
				</div>
				<div class="panel-body">
					<?= $this->Html->link(
					'<i class="fa fa-eye"></i> OC en PDF',
					sprintf('/Pdf/OrdenCompra/%d/%s', $this->request->data['OrdenCompra']['id'], $this->request->data['OrdenCompra']['pdf']),
					array(
						'class' => 'btn btn-success btn-lg btn-block', 
						'target' => '_blank',
						'escape' => false) 
					); ?>
				</div>
			</div>
		</div>
		<? endif; ?>
	</div>
	
	
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title text-uppercase"><i class="fa fa-file"></i> OC para <b><?=$this->request->data['Proveedor']['nombre'];?></b></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<caption>
								<p><small><b>Validada por:</b> <?=$this->request->data['OrdenCompra']['nombre_validado'];?></small></p>
								<p><small><b>Pagada por:</b> <?=$this->request->data['OrdenCompra']['nombre_pagado'];?></small></p>
							</caption>
							<tr>
								<td>
									<table class="table table-bordered">
										<tr>
											<td colspan="2"><b>Datos de la empresa</b></td>
										</tr>
										<tr>
											<td>Rut empresa: </td>
											<td><?=$this->request->data['OrdenCompra']['rut_empresa'];?></td>
										</tr>
										<tr>
											<td>Razón Social: </td>
											<td><?=$this->request->data['OrdenCompra']['razon_social_empresa'];?></td>
										</tr>
										<tr>
											<td>Giro: </td>
											<td><?=$this->request->data['OrdenCompra']['giro_empresa'];?></td>
										</tr>
										<tr>
											<td>Nombre de contacto: </td>
											<td><?=$this->request->data['OrdenCompra']['nombre_contacto_empresa']?></td>
										</tr>
										<tr>
											<td>Email: </td>
											<td><?=$this->request->data['OrdenCompra']['email_contacto_empresa'];?></td>
										</tr>
										<tr>
											<td>Teléfono: </td>
											<td><?=$this->request->data['OrdenCompra']['fono_contacto_empresa']?></td>
										</tr>
										<tr>
											<td>Dirección comercial: </td>
											<td><?=$this->request->data['OrdenCompra']['direccion_comercial_empresa'];?></td>
										</tr>
									</table>
								</td>
								<td>
									<table class="table table-bordered">
										<tr>
											<td colspan="2"><b>Despacho</b></td>
										</tr>
										<tr>
											<td>Fecha: </td>
											<td><?=$this->request->data['OrdenCompra']['fecha'];?></td>
										</tr>
										<tr>
											<td>Forma de pago: </td>
											
											<td><?=$this->request->data['Moneda']['nombre'];?></td>
											
										</tr>
										<tr>
											<td>Vendedor: </td>
											<td><?=$this->request->data['OrdenCompra']['vendedor'];?></td>
										</tr>
										<tr>
											
											<td>Descuento: </td>
											<td><?=($this->request->data['OrdenCompra']['tipo_descuento']) ? '%' : '$' ;?> <?=$this->request->data['OrdenCompra']['descuento'];?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						
						<table class="table table-bordered js-clone-wrapper">
							<thead>
								<th>Item</th>
								<th>Código</th>
								<th>Descripción</th>
								<th>Cantidad</th>
								<th>N. Unitario</th>
								<th colspan="2">Descuento*</th>
								<th>Total Neto</th>
							</thead>
							<tboby class="">
							<? foreach ($this->request->data['VentaDetalleProducto'] as $ipp => $this->request->datap) : ?>	
								
								<tr>
									<td>
										<?=$ipp;?>
									</td>
									<td><?=$this->request->datap['OrdenComprasVentaDetalleProducto']['codigo'];?></td>
									<td><?=$this->request->datap['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
									<td><?=$this->request->datap['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=CakeNumber::currency($this->request->datap['OrdenComprasVentaDetalleProducto']['precio_unitario'] , 'CLP');?></td>
									<td><?=($this->request->datap['OrdenComprasVentaDetalleProducto']['tipo_descuento']) ? '%' : '$' ;?></td>
									<td><?=CakeNumber::currency($this->request->datap['OrdenComprasVentaDetalleProducto']['descuento_producto'] , 'CLP');?></td>
									<td><?=CakeNumber::currency($this->request->datap['OrdenComprasVentaDetalleProducto']['total_neto'] , 'CLP');?></td>
								</tr>
								
							<? endforeach; ?>
							
							</tboby>
							<tfoot>
								<tr>
									<td colspan="6"></td>
									<td>Total neto</td>
									<td colspan="2"><?=CakeNumber::currency($this->request->data['OrdenCompra']['total_neto'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>Total Descuento</td>
									<td colspan="2"><?=CakeNumber::currency($this->request->data['OrdenCompra']['descuento_monto'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>IVA</td>
									<td colspan="2"><?=CakeNumber::currency($this->request->data['OrdenCompra']['iva'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>Total</td>
									<td colspan="2"><?=CakeNumber::currency($this->request->data['OrdenCompra']['total'] , 'CLP');?></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalComentario">Continuar</button>
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="modalComentarioLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalComentarioLabel"><i class="fa fa-paper-plane"></i> Enviar OC</h4>
      </div>
      <div class="modal-body">
		
		<?=$this->Form->hidden('pdf', array('value' => $this->request->data['OrdenCompra']['pdf']));?>
		<?=$this->Form->hidden('adjunto', array('value' => $this->request->data['OrdenCompra']['adjunto']['path']));?>

      	<div class="form-group col-xs-12">
      		<?=$this->Form->label('email_contacto_empresa', 'Se enviará la OC a los siguientes destinatarios:');?>

      		<ul>
      			<? foreach ($this->request->data['Proveedor']['meta_emails'] as $i => $email) : ?>
      			<? if ($email['activo']) : ?>
				<li><b><?=$email['email']; ?></b> (<?=$email['tipo']?>)</li>

				<?=$this->Form->hidden(sprintf('email_contacto_empresa.%d.email', $i), array('value' => $email['email'])); ?>
				<?=$this->Form->hidden(sprintf('email_contacto_empresa.%d.tipo', $i), array('value' => $email['tipo'])); ?>
      			
      			<? endif; ?>
      			<? endforeach; ?>
      		</ul>

      	</div>
      	<div class="form-group col-xs-12">
	        <?=$this->Form->label('mensaje_final', 'Mensaje personalizado');?></p>
	        <?=$this->Form->input('mensaje_final', array('class' => 'form-control', 'placeholder' => 'Ingrese texto...', 'value' => sprintf('Estimado/a %s se envía adjunto la orden de compra y su comprobante de pago.', $this->request->data['OrdenCompra']['nombre_contacto_empresa']) )); ?>
    	</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success esperar-carga" autocomplete="off" data-loading-text="Espera un momento..."><i class="fa fa-check"></i> Enviar OC a proveedor</button>
        <!--<button type="submit" class="btn btn-danger reject-button"><i class="fa fa-ban"></i> Rechazar OC</button>-->
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
