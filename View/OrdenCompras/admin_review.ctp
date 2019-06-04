<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Revisar OC generada por <?=$ocs['Administrador']['nombre'];?></h2>
</div>

<?= $this->Form->create('OrdenCompra', array('url' => array('controller' => 'ordenCompras', 'action' => 'review', $ocs['OrdenCompra']['id']),  'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?# $this->Form->input('id');?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title text-uppercase"><i class="fa fa-file"></i> OC para <b><?=$ocs['Proveedor']['nombre'];?></b></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<td>
									<table class="table table-bordered">
										<tr>
											<td colspan="2"><b>Datos de la empresa</b></td>
										</tr>
										<tr>
											<td>Rut empresa: </td>
											<td><?=$ocs['OrdenCompra']['rut_empresa'];?></td>
										</tr>
										<tr>
											<td>Razón Social: </td>
											<td><?=$ocs['OrdenCompra']['razon_social_empresa'];?></td>
										</tr>
										<tr>
											<td>Giro: </td>
											<td><?=$ocs['OrdenCompra']['giro_empresa'];?></td>
										</tr>
										<tr>
											<td>Nombre de contacto: </td>
											<td><?=$ocs['OrdenCompra']['nombre_contacto_empresa']?></td>
										</tr>
										<tr>
											<td>Email: </td>
											<td><?=$ocs['OrdenCompra']['email_contacto_empresa'];?></td>
										</tr>
										<tr>
											<td>Teléfono: </td>
											<td><?=$ocs['OrdenCompra']['fono_contacto_empresa']?></td>
										</tr>
										<tr>
											<td>Dirección comercial: </td>
											<td><?=$ocs['OrdenCompra']['direccion_comercial_empresa'];?></td>
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
											<td><?=$ocs['OrdenCompra']['fecha'];?></td>
										</tr>
										<tr>
											<td>Forma de pago: </td>
											
											<td><?=$ocs['Moneda']['nombre'];?></td>
											
										</tr>
										<tr>
											<td>Vendedor: </td>
											<td><?=$ocs['OrdenCompra']['vendedor'];?></td>
										</tr>
										<tr>
											
											<td>Descuento: </td>
											<td><?=$ocs['OrdenCompra']['descuento'];?>%</td>
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
								<th>Descuento ($)</th>
								<th>Costo Neto ($)</th>
								<th>Total Neto</th>
								<th></th>
							</thead>
							<tboby class="">
								
							<? foreach ($ocs['VentaDetalleProducto'] as $ipp => $ocsp) : ?>	
								
								<tr>
									<td><?=$ocsp['OrdenComprasVentaDetalleProducto']['id'];?></td>
									<td><?=$ocsp['OrdenComprasVentaDetalleProducto']['codigo'];?></td>
									<td><?=$ocsp['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
									<td><?=$ocsp['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=CakeNumber::currency($ocsp['OrdenComprasVentaDetalleProducto']['precio_unitario'] , 'CLP');?></td>
									<td><?=CakeNumber::currency($ocsp['OrdenComprasVentaDetalleProducto']['descuento_producto'] , 'CLP');?></td>
									<td><?=CakeNumber::currency(($ocsp['OrdenComprasVentaDetalleProducto']['precio_unitario'] - $ocsp['OrdenComprasVentaDetalleProducto']['descuento_producto']) , 'CLP');?></td>
									<td><?=CakeNumber::currency($ocsp['OrdenComprasVentaDetalleProducto']['total_neto'] , 'CLP');?></td>
									<td></td>
								</tr>
								
							<? endforeach; ?>
							
							</tboby>
							<tfoot>
								<tr>
									<td colspan="6"></td>
									<td>Total neto</td>
									<td colspan="2"><?=CakeNumber::currency($ocs['OrdenCompra']['total_neto'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>Total Descuento</td>
									<td colspan="2"><?=CakeNumber::currency($ocs['OrdenCompra']['descuento_monto'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>IVA</td>
									<td colspan="2"><?=CakeNumber::currency($ocs['OrdenCompra']['iva'] , 'CLP');?></td>
								</tr>
								<tr>
									<td colspan="6"></td>
									<td>Total</td>
									<td colspan="2"><?=CakeNumber::currency($ocs['OrdenCompra']['total'] , 'CLP');?></td>
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
        <h4 class="modal-title" id="modalComentarioLabel">¿Desea dejar un comentario?</h4>
      </div>
      <div class="modal-body">
      	<div class="form-group col-xs-12">
	        <?=$this->Form->label('comentario_validar', 'Déje un comentario, instrucción o sugerencia para ' . $ocs['Administrador']['nombre'] . ' (opcional)');?></p>
	        <?=$this->Form->input('comentario_validar', array('class' => 'form-control', 'placeholder' => 'Ingrese texto...')); ?>
    	</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success esperar-carga" autocomplete="off" data-loading-text="Espera un momento..."><i class="fa fa-check"></i> Validar OC</button>
        <button type="submit" class="btn btn-danger reject-button"><i class="fa fa-ban"></i> Rechazar OC</button>
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
