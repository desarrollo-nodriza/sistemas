<? foreach ($ventas as $iv => $venta) : ?>
	<?
		$bodega_id = ($venta['Bodega']['id'] )? $venta['Bodega']['id']: $bodega_default['Bodega']['id']; 
	?>
<tr class="<?=($venta['Venta']['prioritario']) ? 'tr-prioritario' : '';?> " >
	<td><input type="checkbox" class="create_input" value="<?=$venta['Venta']['id'];?>" data-bodega="<?=$bodega_id?>" data-ordencompras="<?=count($venta['OrdenCompra']);?>" data-id="<?=$venta['Venta']['id'];?>" 
		<? if($venta['Venta']['selected']){ echo 'value="1" checked'; }?>></td>
	<td><?=$venta['Venta']['id'];?></td>
	<td><?=$venta['Venta']['id_externo'];?></td>
	<td><?=$venta['Bodega']['nombre']??$bodega_default['Bodega']['nombre'];?></td>
	<td><?=$venta['Venta']['referencia'];?></td>
	<td><label class="label label-<?=$venta['VentaEstado']['VentaEstadoCategoria']['estilo'];?>"><?=$venta['VentaEstado']['VentaEstadoCategoria']['nombre'];?></label></td>
	<td><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></td>
	<td><?=$venta['Venta']['fecha_venta'];?></td>
	<td><?=count($venta['OrdenCompra']);?></td>
	<td><?=count($venta['VentaDetalle']);?></td>
	<td><?=($venta['Venta']['prioritario']) ? '<i class="fa fa-check"></i> Si' : '<i class="fa fa-remove"></i> No'; ?></td>
</tr>
<? endforeach ?>		