<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Finalizar venta</h2>
</div>

<?= $this->Form->create('Venta', array('class' => 'form-horizontal js-validate-oc' , 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id'); ?>
<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><b><?=__('Indique los productos que retirará');?></b></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<th><?=__('Item');?></th>
								<th><?=__('Cantidad vendida'); ?></th>
								<th><?=__('Cantidad pendiente de entrega'); ?></th>
								<th><?=__('Cantidad entregada'); ?></th>
								<th><?=__('Cantidad a retirar'); ?></th>
								<th class="fecha_llegada hidden"><?=__('Fecha llegada'); ?></th>
							</thead>
							<tbody>
							<? foreach ($this->request->data['VentaDetalle'] as $iv => $detalle) : ?>
								<tr>
									<td>
										<?=$detalle['VentaDetalleProducto']['nombre']; ?>
										<?=$this->Form->hidden(sprintf('VentaDetalle.%d.id', $detalle['id']), array('value' => $detalle['id'])); ?>
										<?=$this->Form->hidden(sprintf('VentaDetalle.%d.venta_detalle_producto_id', $detalle['id']), array('value' => $detalle['venta_detalle_producto_id'])); ?>
									</td>
									<td>
										<?=$detalle['cantidad']; ?>
										<?=$this->Form->hidden(sprintf('VentaDetalle.%d.cantidad', $detalle['id']), array('value' => $detalle['cantidad'])); ?>
									</td>
									<td data-pendiente="<?=(!$detalle['cantidad_pendiente_entrega']) ? $detalle['cantidad'] : $detalle['cantidad_pendiente_entrega'] ; ?>" class="js-cantidad-pendiente">
										<?=(!$detalle['cantidad_pendiente_entrega']) ? $detalle['cantidad'] : $detalle['cantidad_pendiente_entrega'] ; ?>
									</td>
									<td>
										<?=$detalle['cantidad_entregada']; ?>
									</td>
									<td>
									<? if ($detalle['cantidad_entregada'] < $detalle['cantidad']) : ?>
										<?=$this->Form->input(sprintf('VentaDetalle.%d.cantidad_entregar', $detalle['id']), array('class' => 'form-control is-number not-blank js-cantidad-entregada', 'value' => $detalle['cantidad_pendiente_entrega'], 'max' => $detalle['cantidad_pendiente_entrega'], 'min' => 0)); ?>
									<? else : ?>
										0
									<? endif; ?>
									</td>
									<td <? ($detalle['cantidad_entregada'] <  $detalle['cantidad']); ?>class="fecha_llegada hidden">
										<?=$this->Form->input(sprintf('VentaDetalle.%d.fecha_llegada', $detalle['id']), array('class' => 'form-control datepicker not-blank', 'type' => 'text', 'value' => $detalle['fecha_llegada'])); ?>
									</td>
								</tr>	
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->Form->end(); ?>


<script type="text/javascript">
	
	$('.js-cantidad-entregada').on('change', function(){
		var $ths = $(this),
			pendiente = $ths.parents('tr').eq(0).find('.js-cantidad-pendiente').data('pendiente');

		if ($ths.val() < pendiente) {
			$('.fecha_llegada').removeClass('hidden');
			$('.fecha_llegada > input').removeAttr('disabled');
		}else{
			$('.fecha_llegada').addClass('hidden');
			$('.fecha_llegada > input').attr('disabled', 'disabled');
		}

	});

</script>
