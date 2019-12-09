<tr data-id="<?=$producto['VentaDetalleProducto']['id']; ?>">
	<td>
		<input type="hidden" name="data[VentaDetalleProducto][<?=$producto['VentaDetalleProducto']['id']; ?>][venta_detalle_producto_id]" value="<?=$producto['VentaDetalleProducto']['id']; ?>">
		<?=$producto['VentaDetalleProducto']['id']; ?>
	</td>
	<td>
		<?=$producto['VentaDetalleProducto']['codigo_proveedor']; ?>		
	</td>
	<td>
		<?=$producto['VentaDetalleProducto']['nombre']; ?>
	</td>
	<td data-precio="<?=$producto['VentaDetalleProducto']['external']['precio_venta']; ?>">
		<?= CakeNumber::currency($producto['VentaDetalleProducto']['external']['precio_venta'], 'CLP'); ?>
		<input type="hidden" name="data[VentaDetalleProducto][<?=$producto['VentaDetalleProducto']['id']; ?>][monto]" value="<?=$producto['VentaDetalleProducto']['external']['precio_venta']; ?>">	
	</td>
	<td>
		<input type="text" class="form-control not-blank is-number" name="data[VentaDetalleProducto][<?=$producto['VentaDetalleProducto']['id']; ?>][cantidad]" value="0" placeholder="">
	</td>
	<td>
		<button class="remove_tr_prospecto btn btn-danger btn-xs">Quitar</button>
	</td>
</tr>