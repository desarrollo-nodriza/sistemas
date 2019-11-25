<? if (isset($set)) : ?>
	<tr data-id="<?=$producto['VentaDetalleProducto']['id'];?>">
		<td>
			#<?=$producto['VentaDetalleProducto']['id'];?>
			<?=$this->Form->hidden(sprintf('VentaDetalle.%d.id', $producto['VentaDetalleProducto']['id']), array('value' => $producto['id'])); ?>
			<?=$this->Form->hidden(sprintf('VentaDetalle.%d.venta_detalle_producto_id', $producto['VentaDetalleProducto']['id']), array('value' => $producto['VentaDetalleProducto']['id'])); ?>
		</td>
		<td>
			<?= (!empty($producto['VentaDetalleProducto']['codigo_proveedor'])) ? $producto['VentaDetalleProducto']['codigo_proveedor'] : '--';?>
		</td>
		<td>
			<?= $producto['VentaDetalleProducto']['nombre']; ?>
		</td>
		<td>
			<?= $this->Form->input(sprintf('VentaDetalle.%d.cantidad', $producto['VentaDetalleProducto']['id']), array('type' => 'text', 'class' => 'form-control not-blank is-number js-cantidad-producto', 'placeholder' => 'Ingrese cantidad', 'label' => false, 'value' => $producto['cantidad'], 'data-value' => $producto['cantidad']))?>
		</td>
		<td>
			<?= $this->Form->input(sprintf('VentaDetalle.%d.precio_bruto', $producto['VentaDetalleProducto']['id']), array('type' => 'text', 'class' => 'form-control not-blank is-number js-precio-producto', 'placeholder' => 'Ingrese precio bruto', 'label' => false, 'value' => $producto['precio_bruto'], 'data-value' => $producto['precio_bruto'])); ?>
		</td>
		<td>$<span class="js-subtotal-producto" data-value="<?= $producto['precio_bruto'] * $producto['cantidad']; ?>"><?= $producto['precio_bruto'] * $producto['cantidad']; ?></span></td>
		<td valign="center">
	        <button class="remove_tr btn-danger js-recalcular-montos"><i class="fa fa-minus"></i></button>
	    </td>
	</tr>
<? else : ?>
	<tr data-id="<?=$producto['VentaDetalleProducto']['id'];?>">
		<td>
			#<?=$producto['VentaDetalleProducto']['id'];?>
			<?=$this->Form->hidden(sprintf('VentaDetalle.%d.venta_detalle_producto_id', $producto['VentaDetalleProducto']['id']), array('value' => $producto['VentaDetalleProducto']['id'])); ?>
		</td>
		<td>
			<?= (!empty($producto['VentaDetalleProducto']['codigo_proveedor'])) ? $producto['VentaDetalleProducto']['codigo_proveedor'] : '--';?>
		</td>
		<td>
			<?= $producto['VentaDetalleProducto']['nombre']; ?>
		</td>
		<td>
			<?= $this->Form->input(sprintf('VentaDetalle.%d.cantidad', $producto['VentaDetalleProducto']['id']), array('type' => 'text', 'class' => 'form-control not-blank is-number js-cantidad-producto', 'placeholder' => 'Ingrese cantidad', 'label' => false))?>
		</td>
		<td>
			<?= $this->Form->input(sprintf('VentaDetalle.%d.precio_bruto', $producto['VentaDetalleProducto']['id']), array('type' => 'text', 'class' => 'form-control not-blank is-number js-precio-producto', 'placeholder' => 'Ingrese precio bruto', 'label' => false))?>
		</td>
		<td>$<span class="js-subtotal-producto" data-value="0">0</span></td>
		<td valign="center">
	        <button class="remove_tr btn-danger js-recalcular-montos"><i class="fa fa-minus"></i></button>
	    </td>
	</tr>
<? endif; ?>