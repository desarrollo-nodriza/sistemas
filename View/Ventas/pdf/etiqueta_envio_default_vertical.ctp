<div class="table-responsive tabla-cotizacion" style="margin: 0; padding: 0;">
	
	<table style="margin-bottom: 0; border: none;">
		<tr>
			<td style="width: 100%; border: none; padding: 0px 0 35px; text-align: center;" align="center" valign="middle">
				<img src="<?=$logo;?>" width="150">
			</td>
		</tr>
		<tr>
			<td style="width: 100%; border: none; padding: 15px;" align="center" valign="middle">
				<span style="font-size: 35px; font-weight: bold;line-height: 35px;">VID #<?=$venta['Venta']['id'];?></span>
			</td>
		</tr>
		<tr>
			<td style="width: 100%; border: none;" align="center" valign="middle">
				<img src="https://chart.googleapis.com/chart?chs=<?=$tamano;?>&cht=qr&chl=<?=$url;?>&choe=UTF-8" title="QR" height="200" />
			</td>
		</tr>
	</table>
	
	<table style="width: 100%; padding-left: 0px; padding-right: 0px; padding-top: 5px; padding-bottom: 5px;">
		<tr>
			<td colspan="2" style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px;"><strong>Destinatario</strong></td>
		</tr>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;">Nombre:</td>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;"><?=(empty($venta['Venta']['nombre_receptor'])) ? $venta['VentaCliente']['nombre'] . $venta['VentaCliente']['apellido'] : $venta['Venta']['nombre_receptor'] ; ?></td>
		</tr>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;">Rut:</td>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;"><?=$this->Html->rut($venta['VentaCliente']['rut']); ?></td>
		</tr>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;">Dirección:</td>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;"><?=$venta['Venta']['direccion_entrega']; ?> <?=$venta['Venta']['numero_entrega']; ?> <?=$venta['Venta']['otro_entrega']; ?> - <?=$venta['Venta']['comuna_entrega']; ?></td>
		</tr>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-bottom: 5px;">Fono:</td>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-bottom: 5px;"><?= (empty($venta['Venta']['fono_receptor'])) ? $venta['VentaCliente']['telefono'] : $venta['Venta']['fono_receptor'] ; ?></td>
		</tr>
	</table>

	<table style="width: 100%; padding: 15px 0px;">
		<tr>
			<td colspan="2" style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px;"><strong>Detalle de la compra</strong></td>
		</tr>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px;">
				Id externo:
			</td>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px;">
				<? if (!$venta['Venta']['venta_manual']) : ?>
					Id externo: #<?=$venta['Venta']['id_externo']; ?> - 
					<? if (empty($venta['Venta']['marketplace_id'])) : ?>
						<?=$venta['Tienda']['nombre'];?>
					<? else : ?>
						<?=$venta['Marketplace']['nombre'];?>
					<? endif; ?>
				<? else : ?>
					Pos de venta
				<? endif; ?>
			</td>
		</tr>
		<? if (!empty($venta['Dte'])) : ?>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;">Folio/s:</td>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;"><?=implode(', ', Hash::extract($venta['Dte'], '{n}.folio'));?></td>
		</tr>
		<? endif; ?>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;">Método de envio:</td>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;"><?=$venta['MetodoEnvio']['nombre']; ?></td>
		</tr>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-bottom: 5px;padding-top: 5px;">Medio de pago:</td>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-bottom: 5px;padding-top: 5px;"><?=$venta['MedioPago']['nombre']; ?></td>
		</tr>
	</table>
			
	<table style="width: 100%;padding: 15px 0px;">
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-top: 5px;"><strong>Mensajes de la compra</strong></td><td></td>
		</tr>
		<tr>
			<td style="font-size: 12px; padding-left: 5px; padding-right: 5px; padding-bottom: 5px;">
				<? if (!empty($venta['VentaMensaje'])) : ?>
					<ul>
					<? foreach ($venta['VentaMensaje'] as $im => $mensaje) : ?>
						<li><?=$mensaje['mensaje']?></li>	
					<? endforeach; ?>
					</ul>
				<? else : ?>
					No registra mensajes
				<? endif; ?>
			</td>
		</tr>
	</table>


</div>