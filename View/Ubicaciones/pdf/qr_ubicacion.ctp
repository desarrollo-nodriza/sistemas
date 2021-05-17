<div class="table-responsive tabla-cotizacion" style="margin: 10px 0 0 0; padding: 0; width: 100%;">
	<table class="table-border" style="width: 100%;">
		<tr>
			<td align="center" style="border: none; padding-top: 0;" align="left" valign="middle">
				<img src="<?=$logo;?>" width="160" style="margin-bottom: 40px;">
			</td>
		</tr>
		<tr>
			<td align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: bold; text-transform: uppercase; padding: 10px;">bodega: <?=$ubicacion['Zona']['Bodega']['nombre']; ?></td>
		</tr>
		<tr>
			<td align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 30px; font-weight: bold; text-transform: uppercase; padding: 10px;">zona: <?=$ubicacion['Zona']['nombre']; ?></td>
		</tr>
		<tr>
            <td align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 40px; font-weight: bold; text-transform: uppercase; padding: 10px;">ubicación: <?=$ubicacion['Ubicacion']['fila']; ?>-<?=$ubicacion['Ubicacion']['columna']; ?></td>
		</tr>
		<tr>
			<td align="center" valign="center" style="width: 100%; padding: 10px; border-color: #000;">
                <img src="https://chart.googleapis.com/chart?chs=<?=$tamano;?>&cht=qr&chl=<?=$ubicacion['Ubicacion']['id'];?>&choe=UTF-8" height="700">
            </td>
		</tr>
	</table>
</div>