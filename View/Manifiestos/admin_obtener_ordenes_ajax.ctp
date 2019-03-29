<? foreach ($ventas as $iv => $orden) : ?>
<tr>
	<td><input type="checkbox" class="create_input" value="<?=$orden['Venta']['id'];?>"  data-manifiestos="<?=count($orden['Manifiesto']);?>" data-id="<?=$orden['Venta']['id'];?>" 
		<? if($orden['Venta']['selected']){ echo 'value="1" checked'; }?>></td>
	<td><?=$orden['Venta']['id'];?></td>
	<td><?=$orden['Venta']['referencia'];?></td>
	<td><label class="label label-<?=$orden['VentaEstado']['VentaEstadoCategoria']['estilo'];?>"><?=$orden['VentaEstado']['VentaEstadoCategoria']['nombre'];?></label></td>
	<td><?=CakeNumber::currency($orden['Venta']['total'], 'CLP');?></td>
	<td><?=$orden['VentaCliente']['nombre'];?> <?=$orden['VentaCliente']['apellido'];?></td>
	<td><?=$orden['Venta']['fecha_venta'];?></td>
	<td><?=count($orden['Manifiesto']);?></td>
	<td><?=count($orden['VentaDetalle']);?></td>
</tr>
<? endforeach ?>		