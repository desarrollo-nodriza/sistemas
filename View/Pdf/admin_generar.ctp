<div class="table-responsive tabla-cotizacion">
	<table>
		<tr>
			<td colspan="2"><?=$this->Html->image($tienda['Tienda']['logo']['path'], array('class' => 'logo-cotizacion', 'fullBase' => true));?></td>
		</tr>
		<tr>
			<td style="width: 60%;">
				<table>
					<tr>
						<td colspan="2"><b style="font-size: 16px;"><?=$tienda['Tienda']['nombre_fantasia'];?></b></td>
					</tr>
					<tr>
						<td><strong>Rut:</strong></td><td><?=$tienda['Tienda']['rut'];?></td>
					</tr>
					<tr>	
						<td><strong>Dirección:</strong></td><td><?=$tienda['Tienda']['direccion'];?></td>
					</tr>
					<tr>
						<td><strong>Giro:</strong></td><td><?=$tienda['Tienda']['giro'];?></td>
					</tr>
					<tr>	
						<td><strong>Fono:</strong></td><td><?=$tienda['Tienda']['fono'];?></td>
					</tr>
					<tr>	
						<td colspan="2"><a href="http://<?=$tienda['Tienda']['url'];?>" target="_blank"><?=$tienda['Tienda']['url'];?></a></td>
					</tr>
				</table>
			</td>
			<td style="vertical-align: middle; font-size: 30px; font-weight: bold; text-align: center;">
				Cotización <?=$cotizacion['Cotizacion']['id'];?>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td style=" width: 60%;"><b>Datos de Cliente</b></td>
			<td><b>Condiciones comerciales de la oferta:</b></td>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td><strong>Email:</strong></td><td><?=$cotizacion['Cotizacion']['email_cliente'];?></td>
					</tr>
					<tr>
						<td><strong>Nombre:</strong></td><td><?=$cotizacion['Cotizacion']['nombre_cliente'];?></td>
					</tr>
					<tr>	
						<td><strong>Teléfono:</strong></td><td><?=$cotizacion['Cotizacion']['fono_cliente'];?></td>
					</tr>
					<tr>	
						<td><strong>Dirección:</strong></td><td><?=$cotizacion['Cotizacion']['direccion_cliente'];?></td>
					</tr>
					<tr>	
						<td><strong>Asunto:</strong></td><td><?=$cotizacion['Cotizacion']['asunto_cliente'];?></td>
					</tr>
					<? if (! empty($cotizacion['Cotizacion']['rut_empresa_cliente']) ) : ?>
						<tr>
							<td><strong>Rut Empresa:</strong></td><td><?=$cotizacion['Cotizacion']['rut_empresa_cliente'];?></td>
						</tr>
					<? endif; ?>
					<? if ( ! empty($cotizacion['Cotizacion']['nombre_empresa_cliente'])) : ?>
						<tr>
							<td><strong>Empresa:</strong></td><td><?=$cotizacion['Cotizacion']['nombre_empresa_cliente'];?></td>
						</tr>
					<? endif; ?>
				</table>
			</td>
			<td>
				<table>
					<tr>
						<td><strong>Fecha:</strong></td><td><?=$cotizacion['Cotizacion']['fecha_cotizacion'];?></td>
					</tr>
					<tr>
						<td><strong>Oferta válida solo:</strong></td><td><?=$cotizacion['ValidezFecha']['valor'];?></td>
					</tr>
					<tr>
						<td><strong>Forma de pago:</strong></td><td><?=$cotizacion['Moneda']['nombre'];?></td>
					</tr>
					<? if ( !empty($cotizacion['Cotizacion']['envio_cotizacion']) ) : ?>
					<tr>
						<td><strong>Envío:</strong></td><td><?=$cotizacion['Cotizacion']['envio_cotizacion'];?></td>
					</tr>
					<? endif; ?>
					<tr>
						<td><strong>Vendedor:</strong></td><td><?=$cotizacion['Cotizacion']['vendedor'];?></td>
					</tr>
					<tr>
						<td><strong>Email vendedor:</strong></td><td><?=$cotizacion['Cotizacion']['email_vendedor'];?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td><b>Código</b></td>
			<td><b>Detalles del producto</b></td>
			<td><b>Cantidad</b></td>
			<td><b>Neto</b></td>
			<td><b>Total</b></td>
		</tr>
		<? foreach ($productos as $inx => $producto) : ?>
			<tr>
				<td><?=$producto['Productotienda']['reference'];?></td>
				<td><?=$producto['Lang'][0]['ProductotiendaIdioma']['name'];?></td>
				<td><?=$producto['Productotienda']['cantidad']; ?></td>
				<td><?=$producto['Productotienda']['precio_neto']?></td>
				<td><?=$producto['Productotienda']['total_neto']; ?></td>
			</tr>								
		<? endforeach; ?>
		<tr>
			<td colspan="4"><b>Total Neto</b></td><td><?=$cotizacion['Cotizacion']['total_neto'];?></td>
		</tr>
		<tr>
			<td colspan="4"><b>Descuento</b></td><td><?=$cotizacion['Cotizacion']['descuento'];?></td>
		</tr>
		<tr>
			<td colspan="4"><b>IVA</b></td><td><?=$cotizacion['Cotizacion']['iva'];?></td>
		</tr>
		<tr>
			<td colspan="4"><b>Total</b></td><td><?=$cotizacion['Cotizacion']['total_bruto'];?></td>
		</tr>
	</table>
	
</div>