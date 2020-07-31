<? if (isset($no_index)) : ?>
<tr data-id="<?=$direccion['id'];?>">
	<td><?=$direccion['id'];?></td>
	<td><?=$direccion['alias'];?></td>
	<td><?=$direccion['calle'];?></td>
	<td><?=$direccion['numero'];?></td>
	<td><?=$direccion['depto'];?></td>
	<td><?=$direccion['Comuna']['nombre'];?></td>
	<td><?=$direccion['created'];?></td>
<td><button class="btn btn-warning btn-xs address-edit"><i class="fa fa-edit"></i> Editar</button></td>
</tr>
<? else : ?>
<tr data-id="<?=$direccion['Direccion']['id'];?>">
	<td><?=$direccion['Direccion']['id'];?></td>
	<td><?=$direccion['Direccion']['alias'];?></td>
	<td><?=$direccion['Direccion']['calle'];?></td>
	<td><?=$direccion['Direccion']['numero'];?></td>
	<td><?=$direccion['Direccion']['depto'];?></td>
	<td><?=$direccion['Comuna']['nombre'];?></td>
	<td><?=$direccion['Direccion']['created'];?></td>
	<td><button class="btn btn-warning btn-xs address-edit"><i class="fa fa-edit"></i> Editar</button></td>
</tr>
<? endif; ?>