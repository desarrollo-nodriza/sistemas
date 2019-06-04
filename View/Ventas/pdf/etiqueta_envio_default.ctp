<div class="table-responsive tabla-cotizacion" style="margin: 0; padding: 0;">
	<table style="margin-bottom: 0; border: none;">
		<tr>
			<td style="width: 50%; border: none; padding-top: 10px;" align="left" valign="top">
				<img src="<?=$logo;?>" width="350">
			</td>
			<td style="width: 50%; border: none; padding-top: 0;" align="right" valign="top">
				<span style="font-size: 90px; font-weight: bold;line-height: 90px; float: right;">VID #<?=$venta['Venta']['id'];?></span>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td style="width: 50%; padding: 10px; border-color: #000;">
				<table>
					<tr>
						<td style="font-size: 19px;"><strong>Destinatario</strong></td>
					</tr>
					<tr>
						<td style="font-size: 19px;">Nombre: <?=$venta['Envio'][0]['nombre_receptor']; ?></td>
					</tr>
					<tr>
						<td style="font-size: 19px;">Rut: <?=$this->Html->rut($venta['VentaCliente']['rut']); ?></td>
					</tr>
					<tr>
						<td style="font-size: 19px;">Dirección: <font style="font-size: 17px;"><?=$venta['Envio'][0]['direccion_envio']; ?></font></td>
					</tr>
					<tr>
						<td style="font-size: 19px;">Fono: <?=$venta['Envio'][0]['fono_receptor']; ?></td>
					</tr>
				</table>

				<table>
					<tr>
						<td style="font-size: 19px;"><strong>Detalle de la compra</strong></td>
					</tr>
					<tr>
						<td style="font-size: 19px;">
							Id externo: #<?=$venta['Venta']['id_externo']; ?> - 
							<? if (empty($venta['Venta']['marketplace_id'])) : ?>
								<?=$venta['Tienda']['nombre'];?>
							<? else : ?>
								<?=$venta['Marketplace']['nombre'];?>
							<? endif; ?>
						</td>
					</tr>
					<? if (!empty($venta['Dte'])) : ?>
					<tr>
						<td style="font-size: 19px;">Folio: <?=implode(', ', Hash::extract($venta['Dte'], '{n}.folio'));?></td>
					</tr>
					<? endif; ?>
					<tr>
						<td style="font-size: 19px;">Transportista: <font style="font-size: 17px;"><?=$venta['VentaExterna']['transportista']; ?></font></td>
					</tr>
					<tr>
						<td style="font-size: 19px;">Medio de pago: <?=$venta['MedioPago']['nombre']; ?></td>
					</tr>
				</table>

				<table>
					<tr>
						<td style="padding: 15px 0;" align="left" valign="middle">
							<img src="https://chart.googleapis.com/chart?chs=<?=$tamano;?>&cht=qr&chl=<?=$url;?>&choe=UTF-8" title="QR" height="290" />
						</td>
					</tr>
				</table>
			</td>
			<td style="width: 50%; padding: 10px; border-color: #000;">
				<table>
					<tr>
						<td style="font-size: 19px;"><strong>Mensajes de la compra</strong></td><td></td>
					</tr>
					<tr>
						<td style="font-size: 19px;">
							<? if (!empty($venta['VentaMensaje'])) : ?>
								<ul>
								<? foreach ($venta['VentaMensaje'] as $im => $mensaje) : ?>
									<li><?=$mensaje['mensaje']?></li>	
								<? endforeach; ?>
								</ul>
							<? else : ?>
								<p>No registra mensajes</p>
							<? endif; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

</div>