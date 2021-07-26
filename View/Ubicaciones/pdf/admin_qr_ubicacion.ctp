<div class="table-responsive tabla-cotizacion" style="margin: 10px 0 0 0; padding: 0; width: 100%;">
	<table class="table-border" style="width: 100%; border: none;">
		<tr>
			<td align="center" style="border: none; padding: 30px;" align="left" valign="middle">
				<img src="<?=$logo;?>" width="160">
			</td>
		</tr>
		<tr>
			<td align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 30px; font-weight: bold; text-transform: uppercase; padding: 10px;">bodega: <?=$ubicacion['Zona']['Bodega']['nombre']; ?></td>
		</tr>
		<tr>
			<td align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 40px; font-weight: bold; text-transform: uppercase; padding: 10px;">zona: <?=$ubicacion['Zona']['nombre']; ?></td>
		</tr>
		<tr>
            <td align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 60px; font-weight: bold; text-transform: uppercase; padding: 10px;">ubicación: <?=$ubicacion['Ubicacion']['columna']; ?>-<?=$ubicacion['Ubicacion']['fila']; ?></td>
		</tr>
		<tr>
			<td align="center" valign="center" style="width: 100%; padding: 10px; border-color: #000;">
                <img src="https://chart.googleapis.com/chart?chs=<?=$tamano;?>&cht=qr&chl=<?=$ubicacion['Ubicacion']['id'];?>&choe=UTF-8" height="700">
            </td>
		</tr>
		<tr>
            <td align="center" style="font-family: Arial, Helvetica, sans-serif; font-size: 55px; font-weight: bold; text-transform: uppercase; padding: 10px;">UID: <?=$ubicacion['Ubicacion']['id']; ?></td>
		</tr>
	</table>
</div>