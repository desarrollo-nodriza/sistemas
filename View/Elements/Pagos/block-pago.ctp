<tr>
	<td>
		<input type="hidden" name="<?=sprintf('Pago[%d][pago_id]', $pago['Pago']['id']); ?>" value="<?=$pago['Pago']['id']?>">
		<input type="hidden" name="<?=sprintf('Pago[%d][monto_pagado]', $pago['Pago']['id']); ?>" value="<?=$pago['Pago']['monto_pagado']?>">
		<?=$pago['Pago']['identificador']; ?>
	</td>
	<td><?=$pago['Moneda']['nombre']; ?></td>
	<td><?=$pago['CuentaBancaria']['alias']; ?> - n° <?=$pago['CuentaBancaria']['numero_cuenta']; ?></td>
	<td><?=$pago['Pago']['fecha_pago']; ?></td>
	<td><?=CakeNumber::currency($pago['Pago']['monto_pagado'], 'CLP'); ?></td>
	<td><?= ($pago['Pago']['pagado']) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>' ; ?></td>
</tr>