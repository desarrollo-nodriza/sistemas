<? foreach ($ventas as $iv => $venta) : ?>
<tr class="<?=($venta['Venta']['prioritario']) ? 'tr-prioritario' : ''; ?>">
	<td><input type="checkbox" class="create_input" value="<?=$venta['Venta']['id'];?>"  data-ordencompras="<?=count($venta['OrdenCompra']);?>" data-id="<?=$venta['Venta']['id'];?>" 
		<? if($venta['Venta']['selected']){ echo 'value="1" checked'; }?>></td>
	<td><?=$venta['Venta']['id'];?></td>
	<td><?=$venta['Venta']['id_externo'];?></td>
	<td><?=$venta['Venta']['referencia'];?></td>
	<td><label class="label label-<?=$venta['VentaEstado']['VentaEstadoCategoria']['estilo'];?>"><?=$venta['VentaEstado']['VentaEstadoCategoria']['nombre'];?></label></td>
	<td><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></td>
	<td><?=$venta['Venta']['fecha_venta'];?></td>
	<td><?=count($venta['OrdenCompra']);?></td>
	<td><?=count($venta['VentaDetalle']);?></td>
	<td><?=($venta['Venta']['prioritario']) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'; ?></td>
</tr>
<? endforeach ?>		