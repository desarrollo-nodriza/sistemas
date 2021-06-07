<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> OC #<?=$this->request->data['OrdenCompra']['id'];?></h2>
</div>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizonta  js-oc-proveedor', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id');?>

<div class="page-content-wrap">

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-primary">
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<caption style="font-size: 14px; font-weight: 600;">Productos <button class="btn btn-danger pull-right" id="rechazar-todo"><i class="fa fa-times"></i> Rechazar todo</button></caption>
							<thead>
								<th>Código</th>
								<th>Descripción</th>
								<th>Precio unitario</th>
								<th>Cantidad solicitada</th>
								<th>Cantidad real</th>
								<th>Aceptar/Rechazar</th>
							</thead>
							<tboby>
							<? foreach ($this->request->data['VentaDetalleProducto'] as $ipp => $ocp) : ?>	

								<tr class="<?=($ocp['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor'] == $ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida'] || $ocp['OrdenComprasVentaDetalleProducto']['cantidad'] == $ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida']) ? '' : ''; ?>" data-cantidad="<?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad'];?>">
									<td>
										
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.venta_detalle_producto_id', $ipp), array('value' => $ocp['id'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.cantidad', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['cantidad'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.cantidad_recibida', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.codigo', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['codigo'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.descripcion', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['descripcion'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.precio_unitario', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['precio_unitario'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.total_neto', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['total_neto'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.descuento_producto', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['descuento_producto'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.tipo_descuento', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['tipo_descuento'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.estado_recibido', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['estado_recibido'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.diff_precio_recepcion', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['diff_precio_recepcion'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.cantidad_zonificada', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['cantidad_zonificada'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalleProducto.%d.zonificado', $ipp), array('value' => $ocp['OrdenComprasVentaDetalleProducto']['zonificado'])); ?>
										
										<?=$ocp['OrdenComprasVentaDetalleProducto']['codigo'];?>
											
									</td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
									<td><?= CakeNumber::currency( ($ocp['OrdenComprasVentaDetalleProducto']['total_neto'] / $ocp['OrdenComprasVentaDetalleProducto']['cantidad']), 'CLP'); ?></td>
									<td><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=$this->Form->input(sprintf('VentaDetalleProducto.%d.cantidad_validada_proveedor', $ipp), array('class' => 'form-control is-number not-blank js-cantidad', 'placeholder' => 'Ingrese cantidad disponible', 'min' => 0, 'max' => $ocp['OrdenComprasVentaDetalleProducto']['cantidad'], 'value' => (!empty($ocp['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor'])) ? $ocp['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor'] : $ocp['OrdenComprasVentaDetalleProducto']['cantidad'], 'readonly' => true))?></td>
									<td>
										
										<?= $this->Form->select(sprintf('VentaDetalleProducto.%d.estado_proveedor', $ipp), $estados, array('empty' => false, 'default' => $ocp['OrdenComprasVentaDetalleProducto']['estado_proveedor'], 'class' => 'form-control js-opcion'))?>
										
										<div class="hidden js-wrapper-nota">
											<?= $this->Form->label(sprintf('VentaDetalleProducto.%d.nota_proveedor', $ipp), 'Nota', array('class' => 'mt-5')); ?>
											<?= $this->Form->input(sprintf('VentaDetalleProducto.%d.nota_proveedor', $ipp), array('class' => 'form-control js-nota', 'rows' => 2, 'value' => $ocp['OrdenComprasVentaDetalleProducto']['nota_proveedor'] )); ?>
										</div>		
									</td>
								</tr>
								
							<? endforeach; ?>
							
							</tboby>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?=$this->Form->button('<i class="fa fa-check"></i> Completar', array('type' => 'submit', 'class' => 'btn btn-success', 'escape' => false)); ?>
						<?= $this->Html->link('Volver', array('action' => 'index', 'sta' => 'espera_recepcion'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<?= $this->Html->script(array(
	'/backend/js/revision_oc.js?v=' . rand()
));?>
<?= $this->fetch('script'); ?>

<?= $this->Form->end(); ?>