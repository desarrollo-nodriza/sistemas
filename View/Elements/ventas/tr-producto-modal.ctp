<tr data-id="<?=$d['id'];?>" data-cantidad="<?=$d['cantidad_reservada'];?>">
	<td>
		<? if ($d['confirmado_app']) : ?>
		<i class="fa fa-mobile text-success"></i>
		<? endif; ?>
		<?=$d['venta_detalle_producto_id'];?></td>
	<td data-toggle="tooltip" title="<?=$d['VentaDetalleProducto']['nombre'];?>" class="td-producto">
		<? if (!empty($d['VentaDetalleProducto']['imagenes'])) : ?>
		<img src="<?=Hash::extract($d['VentaDetalleProducto']['imagenes'], '{n}[principal=1].url')[0]; ?>" class="img-responsive producto-td-imagen">
		<? endif; ?>
		<?= $d['VentaDetalleProducto']['nombre']; ?>
	</td>
	<td><?=$d['cantidad'];?></td>
	<td><?=$d['cantidad_pendiente_entrega'];?></td>
	<td><?=$d['cantidad_reservada'];?></td>
	<? if ($confirmar) : ?>
	<td><button class="btn btn-xs btn-success btn-block js-confirmar-detalle" <?=($d['confirmado_app']) ? 'disabled' : '' ; ?>><i class="fa fa-check"></i> Confirmar</td>
	<? endif; ?>
</tr>