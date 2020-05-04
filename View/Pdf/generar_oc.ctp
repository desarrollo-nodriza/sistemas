<div class="table-responsive tabla-cotizacion">
<table>
	<tr>
		<td colspan="2" valign="center" align="left" style="border: none;"><?=$this->Html->image(sprintf('Tienda/%d/%s', $oc['Tienda']['id'], $oc['Tienda']['logo']), array('class' => 'logo-cotizacion', 'fullBase' => true, 'width' => 150));?></td>
	</tr>
	<tr>
		<td valign="center" align="left">
			<table>
				<tr>
					<td colspan="2" style="font-size: 10px !important;"><strong><?=$oc['Tienda']['nombre_fantasia'];?></strong></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Rut:</td>
					<td style="font-size: 10px !important;"><?=$oc['Tienda']['rut'];?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Dirección:</td>
					<td style="font-size: 10px !important;"><?=$oc['Tienda']['direccion'];?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Giro:</td>
					<td style="font-size: 10px !important;"><?=$oc['Tienda']['giro'];?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Fono:</td>
					<td style="font-size: 10px !important;"><?=$oc['Tienda']['fono'];?></td>
				</tr>
			</table>
		</td>
		<td align="center" style="vertical-align: middle; font-size: 30px; font-weight: bold; text-align: center; line-height: 30px;">OC #<?= (Configure::read('debug') > 0) ? 'NO APLICA' : $oc['OrdenCompra']['id'];?></td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td colspan="2" style="font-size: 10px !important;"><b>Datos de la empresa</b></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Rut empresa: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['rut_empresa'];?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Razón Social: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['razon_social_empresa'];?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Giro: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['giro_empresa'];?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Nombre de contacto: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['nombre_contacto_empresa']?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Email: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['email_contacto_empresa'];?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Teléfono: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['fono_contacto_empresa']?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Dirección comercial: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['direccion_comercial_empresa'];?></td>
				</tr>
			</table>
		</td>
		<td>
			<table>
				<tr>
					<td colspan="2" style="font-size: 10px !important;"><b>Despacho</b></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Fecha: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['fecha'];?></td>
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Forma de pago: </td>
					
					<td style="font-size: 10px !important;"><?=$oc['Moneda']['nombre'];?></td>
					
				</tr>
				<tr>
					<td style="font-size: 10px !important;">Vendedor: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['vendedor'];?></td>
				</tr>
				<tr>
					
					<td style="font-size: 10px !important;">Descuento: </td>
					<td style="font-size: 10px !important;"><?=$oc['OrdenCompra']['descuento'];?>%</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table>
	<tr>
		<td style="font-size: 10px !important;"><b>Item</b></td>
		<td style="font-size: 10px !important;"><b>Código</b></td>
		<td style="font-size: 10px !important;"><b>Descripción</b></td>
		<td style="font-size: 10px !important;"><b>Cantidad</b></td>
		<td style="font-size: 10px !important;"><b>N. Unitario</b></td>
		<!--<td colspan="2"><b>Descuento</b></td>-->
		<td style="font-size: 10px !important;"><b>Total Neto</b></td>
	</tr>

	<? foreach ($oc['VentaDetalleProducto'] as $ipp => $ocp) : ?>	
		<? 
		if ($ocp['OrdenComprasVentaDetalleProducto']['estado_proveedor'] == 'stockout' || $ocp['OrdenComprasVentaDetalleProducto']['estado_proveedor'] == 'price_error')
			continue; 
		?>
		<tr>
			<td style="font-size: 10px !important;"><?=$ipp+1;?></td>
			<td style="font-size: 10px !important;"><?=$ocp['OrdenComprasVentaDetalleProducto']['codigo'];?></td>
			<td style="font-size: 10px !important;"><?=$ocp['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
			<td style="font-size: 10px !important;"><?=$ocp['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor'];?></td>
			<td style="font-size: 10px !important;"><?=CakeNumber::currency( ($ocp['OrdenComprasVentaDetalleProducto']['total_neto'] / $ocp['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor']) , 'CLP');?></td>
			<!--<td style="font-size: 10px !important;"><?=($ocp['OrdenComprasVentaDetalleProducto']['tipo_descuento']) ? '%' : '$' ;?></td>
			<td style="font-size: 10px !important;"><?=CakeNumber::currency( $ocp['OrdenComprasVentaDetalleProducto']['descuento_producto'] , 'CLP');?></td>-->
			<td style="font-size: 10px !important;"><?=CakeNumber::currency($ocp['OrdenComprasVentaDetalleProducto']['total_neto'] , 'CLP');?></td>
		</tr>
		
	<? endforeach; ?>
	
		<tr>
			<td colspan="4" style="font-size: 10px !important;"></td>
			<td style="font-size: 10px !important;">Total neto</td>
			<td colspan="2" style="font-size: 10px !important;"><?=CakeNumber::currency($oc['OrdenCompra']['total_neto'] , 'CLP');?></td>
		</tr>
		<tr>
			<td colspan="4" style="font-size: 10px !important;"></td>
			<td style="font-size: 10px !important;">IVA</td>
			<td colspan="2" style="font-size: 10px !important;"><?=CakeNumber::currency($oc['OrdenCompra']['iva'] , 'CLP');?></td>
		</tr>
		<tr>
			<td colspan="4" style="font-size: 10px !important;"></td>
			<td style="font-size: 10px !important;">Total Descuento</td>
			<td colspan="2" style="font-size: 10px !important;"><?=CakeNumber::currency($oc['OrdenCompra']['descuento_monto'] , 'CLP');?></td>
		</tr>
		<tr>
			<td colspan="4" style="font-size: 10px !important;"></td>
			<td style="font-size: 10px !important;">Total</td>
			<td colspan="2" style="font-size: 10px !important;"><?=CakeNumber::currency($oc['OrdenCompra']['total'] , 'CLP');?></td>
		</tr>
</table>
</div>