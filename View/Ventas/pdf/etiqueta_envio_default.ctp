<div class="table-responsive tabla-cotizacion" style="margin: 10px 0 0 0; padding: 0;">
	<table style="margin-bottom: 0; border: none;">
		<tr>
			<td style="width: 25%; border: none; padding-top: 0;" align="left" valign="middle">
				<img src="<?=$logo;?>" width="160">
			</td>
			<td style="width: 25%; border: none; padding-top: 0;" align="right" valign="middle">
				<img src="https://chart.googleapis.com/chart?chs=<?=$tamano;?>&cht=qr&chl=<?=$url;?>&choe=UTF-8" title="QR" height="100" style="max-width:100%; display: block; margin: 0 auto;"/>
			</td>
			<td style="width: 50%; border: none; padding-top: 0;" align="right" valign="middle">
				<span style="font-size: 35px; font-weight: bold;line-height: 35px; float: right;">VID #<?= $venta['Venta']['id'];?></span>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td style="width: 50%; padding: 10px; border-color: #000;">
				<table>
					<tr>
						<td style="font-size: 11px;"><strong>Destinatario</strong></td>
					</tr>
					<tr>
						<td style="font-size: 11px;">Nombre: <?=$venta['Envio'][0]['nombre_receptor']; ?></td>
					</tr>
					<tr>
						<td style="font-size: 11px;">Rut: <?=$this->Html->rut($venta['VentaCliente']['rut']); ?></td>
					</tr>
					<tr>
						<td style="font-size: 11px;">Dirección: <?=$venta['Envio'][0]['direccion_envio']; ?></td>
					</tr>
					<tr>
						<td style="font-size: 11px;">Fono: <?=$venta['Envio'][0]['fono_receptor']; ?></td>
					</tr>
				</table>

				<table>
					<tr>
						<td style="font-size: 11px;"><strong>Detalle de la compra</strong></td>
					</tr>
					<tr>
						<td style="font-size: 11px;">
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
						<td style="font-size: 11px;">Folio: <?=implode(', ', Hash::extract($venta['Dte'], '{n}.folio'));?></td>
					</tr>
					<? endif; ?>
					<tr>
						<td style="font-size: 11px;">Transportista: <?=$venta['VentaExterna']['transportista']; ?></td>
					</tr>
					<tr>
						<td style="font-size: 11px;">Medio de pago: <?=$venta['MedioPago']['nombre']; ?></td>
					</tr>
				</table>
			</td>
			<td style="width: 50%; padding: 10px; border-color: #000;">
				<table>
					<tr>
						<td style="font-size: 11px;"><strong>Mensajes de la compra</strong></td><td></td>
					</tr>
					<tr>
						<td style="font-size: 11px;">
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