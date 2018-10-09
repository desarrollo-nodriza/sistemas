<div class="table-responsive tabla-cotizacion">
	<table>
		<tr>
			<td colspan="2"><b style="font-size: 20px;"><?=$manifiesto['Transporte']['nombre']; ?></b></td>
		</tr>
	</table>
	<table>
		<tr>
			<td colspan="2"><b>Listado de paquetes</b></td>
		</tr>
	</table>
	<table style="width: 100%;">
		<tr>
			<? foreach ($campos as $cabecera) : ?>
				<th><b style="font-size: 11px;"><?=$cabecera;?></b></th>
			<? endforeach; ?>
		</tr>
		<? foreach ($datos as $inx => $item) : ?>
			<tr>
				<? foreach ($item['Manifiesto'] as $campo) : ?>
					<td style="font-size: 10px"><?=$campo;?></td>
				<? endforeach; ?>
			</tr>								
		<? endforeach; ?>
	</table>
	

	<p style="font-size: 10px; text-align: center; color: gray; margin-top: 50px;">No me imprimar a menos que sea absolutamente necesario.</p>
</div>