<? foreach ($ordenes as $iv => $orden) : ?>
<tr>
	<td><input type="checkbox" class="create_input" value="<?=$orden['Orden']['id_order'];?>"  data-manifiestos="<?=count($orden['Manifiesto']);?>" data-id="<?=$orden['Orden']['id_order'];?>" 
		<? if($orden['Orden']['selected']){ echo 'value="1" checked'; }?>></td>
	<td><?=$orden['Orden']['id_order'];?></td>
	<td><?=$orden['Orden']['reference'];?></td>
	<td><label class="label" style="background-color: <?=$orden['OrdenEstado']['color'];?>;"><?=$orden['OrdenEstado']['Lang'][0]['OrdenEstadoIdioma']['name'];?></label></td>
	<td><?=CakeNumber::currency($orden['Orden']['total_paid_real'], 'CLP');?></td>
	<td><?=$orden['Cliente']['firstname'];?> <?=$orden['Cliente']['lastname'];?></td>
	<td><?=$orden['Orden']['date_add'];?></td>
	<td><?=count($orden['Manifiesto']);?></td>
	<td><?=count($orden['OrdenDetalle']);?></td>
</tr>
<? endforeach ?>		