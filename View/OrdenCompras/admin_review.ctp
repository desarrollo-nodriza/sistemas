<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Revisar OC generada por <?=$ocs['Administrador']['nombre'];?></h2>
</div>

<?= $this->Form->create('OrdenCompra', array('url' => array('controller' => 'ordenCompras', 'action' => 'review', $ocs['OrdenCompra']['id']),  'class' => 'form-horizontal js-validate-oc', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id', array('value' => $ocs['OrdenCompra']['id']));?>
<?= $this->Form->hidden('fecha_validado', array('value' => date('Y-m-d H:i:s')));?>
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
						
						<table class="table table-bordered js-clone-wrapper js-oc">
								<thead>
									<th>Item</th>
									<th>Código</th>
									<th>Descripción</th>
									<th>Cantidad</th>
									<th>N. Unitario</th>
									<th>Descuento ($)</th>
									<th>Total Neto</th>
									<? if ($ocs['OrdenCompra']['validado_proveedor']) : ?>
									<th>Estado proveedor</th>
									<th>Nota proveedor</th>
									<? endif; ?>
									<th><a href="#" class="copy_tr btn btn-rounded btn-primary"><span class="fa fa-plus"></span> agregar</a></th>									
								</thead>
								<tboby class="">
									<tr class="hidden clone-tr">
										<td>
											<?= $this->Form->input('VentaDetalleProducto.999.venta_detalle_producto_id', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-id-producto not-blank')); ?>
										</td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.codigo', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank js-codigo-producto')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.descripcion', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-descripcion-producto js-buscar-producto not-blank', 'style' =>'width: 200px;')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.cantidad', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-cantidad-producto not-blank js-cantidad-producto not-blank is-number')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.precio_unitario', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-precio-producto not-blank is-number')); ?></td>
										<td data-toggle="tooltip" data-placement="top" title="" class="js-descuento-valor"><?= $this->Form->input('VentaDetalleProducto.999.descuento_producto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-descuento-producto not-blank is-number')); ?></td>
										<td><?= $this->Form->input('VentaDetalleProducto.999.total_neto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control js-total-producto not-blank is-number')); ?></td>
										<? if ($ocs['OrdenCompra']['validado_proveedor']) : ?>
										<td></td>
										<td></td>
										<? endif; ?>
										<td valign="center" class="js-acciones">
											<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
											<!--<button class="habilitar-fila btn-success"><i class="fa fa-pencil"></i></button>-->
											<button class="btn-warning btn-modificar-precio-especifico" data-toggle="modal" data-target=""><i class="fa fa-usd"></i></button>
											<div class="js-modal-precio-especifico">
											</div>
										</td>
									</tr>
									<? foreach ($ocs['VentaDetalleProducto'] as $ipp => $pp) : ?>	
									<tr>
										<td>
											<?= $this->Form->input(sprintf('VentaDetalleProducto.%d.venta_detalle_producto_id', $ipp), array('value' => $pp['id'], 'type' => 'text', 'class' => 'form-control js-id-producto not-blank')); ?>
										</td>
										<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.codigo', $ipp), array('type' => 'text', 'class' => 'form-control not-blank js-codigo-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['codigo'] )); ?></td>
										<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.descripcion', $ipp), array('type' => 'text', 'class' => 'form-control not-blank js-descripcion-producto', 'value' => $pp['nombre'], 'style' =>'width: 200px;')); ?></td>
										
										<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.cantidad', $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-cantidad-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['cantidad'], 'min' => $pp['cant_minima_compra'] )); ?></td>
										
										<td><?= $this->Form->input(sprintf('VentaDetalleProducto.%d.precio_unitario', $ipp), array('readonly' => true, 'type' => 'text', 'class' => 'form-control not-blank is-number js-precio-producto', 'value' => $pp['precio_costo'])); ?></td>
										
										
										<td id="descuento-<?=$pp['id'];?>" data-toggle="tooltip" data-placement="top" title="<?= (!empty($pp['nombre_descuento'])) ? $pp['nombre_descuento'] : '' ; ?>" class="js-descuento-valor">
											<?= $this->Form->input(sprintf('VentaDetalleProducto.%d.descuento_producto', $ipp), array('readonly' => true, 'type' => 'text', 'class' => 'form-control not-blank is-number js-descuento-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['descuento_producto'], 'data-descuento' => $pp['total_descuento'])); ?>
										</td>
										
										<td>
											<?= $this->Form->input(sprintf('VentaDetalleProducto.%d.total_neto', $ipp), array('readonly' => true, 'type' => 'text', 'class' => 'form-control not-blank is-number js-total-producto', 'value' => $pp['OrdenComprasVentaDetalleProducto']['total_neto'])); ?>
										</td>
										<? if ($ocs['OrdenCompra']['validado_proveedor']) : ?>
										<td>
											<?= $estados_proveedor[$pp['OrdenComprasVentaDetalleProducto']['estado_proveedor']]; ?>
										</td>
										<td>
											<?= $pp['OrdenComprasVentaDetalleProducto']['nota_proveedor']; ?>
										</td>										
										<? endif; ?>
										<td valign="center" class="js-acciones">
											<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
											<!--<button class="habilitar-fila btn-success"><i class="fa fa-pencil"></i></button>-->
											<button class="btn-warning btn-modificar-precio-especifico" data-toggle="modal" data-target="#modalPrecio<?=$pp['id']?>"><i class="fa fa-usd"></i></button>
											
											<div class="js-modal-precio-especifico">
												<?=$this->element('ordenCompras/modal-precio-especifico', array('producto' => $pp)); ?>
											</div>

										</td>
									</tr>
									<? endforeach; ?>
								</tboby>
								<tfoot>
									<tr>
										<td colspan="<?= ($ocs['OrdenCompra']['validado_proveedor']) ? '8' : '6' ; ?>"></td>
										<td>Total neto</td>
										<td colspan="2"><?=$this->Form->input('total_neto', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-neto', 'value' => $ocs['OrdenCompra']['total_neto']) );?></td>
									</tr>
									<tr>
										<td colspan="<?= ($ocs['OrdenCompra']['validado_proveedor']) ? '8' : '6' ; ?>"></td>
										<td>Total Descuento</td>
										<td colspan="2">
											No aplicado aún.
											<!--<?=$this->Form->input('descuento_monto', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-descuento', 'value' => $ocs['OrdenCompra']['descuento_monto']) );?>--></td>
									</tr>
									<tr>
										<td colspan="<?= ($ocs['OrdenCompra']['validado_proveedor']) ? '8' : '6' ; ?>"></td>
										<td>IVA</td>
										<td colspan="2"><?=$this->Form->input('iva', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-iva', 'value' => $ocs['OrdenCompra']['iva']) );?></td>
									</tr>
									<tr>
										<td colspan="<?= ($ocs['OrdenCompra']['validado_proveedor']) ? '8' : '6' ; ?>"></td>
										<td>Total</td>
										<td colspan="2"><?=$this->Form->input('total', array('type' => 'text', 'class' => 'form-control not-blank is-number js-total-oc', 'value' => $ocs['OrdenCompra']['total']) );?></td>
									</tr>
								</tfoot>
							</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalComentario">Continuar</button>
						<?= $this->Html->link('Volver', array('action' => 'index', 'sta' => 'validacion_comercial'), array('class' => 'btn btn-danger')); ?>
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
