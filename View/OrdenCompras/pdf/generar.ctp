<table class="table">
	<tr>
		<td>
			<table class="table table-bordered">
				<tr>
					<td colspan="2"><b>Datos de la empresa</b></td>
				</tr>
				<tr>
					<td>Rut empresa: </td>
					<td><?=$oc['OrdenCompra']['rut_empresa'];?></td>
				</tr>
				<tr>
					<td>Razón Social: </td>
					<td><?=$oc['OrdenCompra']['razon_social_empresa'];?></td>
				</tr>
				<tr>
					<td>Giro: </td>
					<td><?=$oc['OrdenCompra']['giro_empresa'];?></td>
				</tr>
				<tr>
					<td>Nombre de contacto: </td>
					<td><?=$oc['OrdenCompra']['nombre_contacto_empresa']?></td>
				</tr>
				<tr>
					<td>Email: </td>
					<td><?=$oc['OrdenCompra']['email_contacto_empresa'];?></td>
				</tr>
				<tr>
					<td>Teléfono: </td>
					<td><?=$oc['OrdenCompra']['fono_contacto_empresa']?></td>
				</tr>
				<tr>
					<td>Dirección comercial: </td>
					<td><?=$oc['OrdenCompra']['direccion_comercial_empresa'];?></td>
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
					<td><?=$oc['OrdenCompra']['fecha'];?></td>
				</tr>
				<tr>
					<td>Forma de pago: </td>
					
					<td><?=$oc['Moneda']['nombre'];?></td>
					
				</tr>
				<tr>
					<td>Vendedor: </td>
					<td><?=$oc['OrdenCompra']['vendedor'];?></td>
				</tr>
				<tr>
					
					<td>Descuento: </td>
					<td><?=($oc['OrdenCompra']['tipo_descuento']) ? '%' : '$' ;?> <?=$oc['OrdenCompra']['descuento'];?></td>
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
	<? foreach ($oc['VentaDetalleProducto'] as $ipp => $ocp) : ?>	
		
		<tr>
			<td>
				<?=$ipp;?>
			</td>
			<td><?=$ocp['OrdenComprasVentaDetalleProducto']['codigo'];?></td>
			<td><?=$ocp['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
			<td><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
			<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['precio_unitario'] , 'CLP');?></td>
			<td><?=($ocp['OrdenComprasVentaDetalleProducto']['tipo_descuento']) ? '%' : '$' ;?></td>
			<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['descuento_producto'] , 'CLP');?></td>
			<td><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['total_neto'] , 'CLP');?></td>
		</tr>
		
	<? endforeach; ?>
	
	</tboby>
	<tfoot>
		<tr>
			<td colspan="6"></td>
			<td>Total neto</td>
			<td colspan="2"><?=CakeNumber::currency($oc['OrdenCompra']['total_neto'] , 'CLP');?></td>
		</tr>
		<tr>
			<td colspan="6"></td>
			<td>Total Descuento</td>
			<td colspan="2"><?=CakeNumber::currency($oc['OrdenCompra']['descuento_monto'] , 'CLP');?></td>
		</tr>
		<tr>
			<td colspan="6"></td>
			<td>IVA</td>
			<td colspan="2"><?=CakeNumber::currency($oc['OrdenCompra']['iva'] , 'CLP');?></td>
		</tr>
		<tr>
			<td colspan="6"></td>
			<td>Total</td>
			<td colspan="2"><?=CakeNumber::currency($oc['OrdenCompra']['total'] , 'CLP');?></td>
		</tr>
	</tfoot>
</table>