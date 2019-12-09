<div class="panel panel-default js-address-block" data-id="<?=$direccion['Direccion']['id']; ?>">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-home"></i> <?=$direccion['Direccion']['alias']; ?></h3>
		<ul class="panel-controls">
			<li><a href="#" class="address-select" data-id="<?=$direccion['Direccion']['id']; ?>"><i class="fa fa-plus"></i><i class="fa fa-check hidden"></i></a></li>
			<li><a href="#" class="address-edit"><span class="fa fa-pencil"></span></a></li>
		</ul>
	</div>
	<div class="panel-body list-group">
		<p class="list-group-item">Calle/Psje: <?=$direccion['Direccion']['calle'];?></p>
		<p class="list-group-item">N°: <?=$direccion['Direccion']['numero'];?></p>
		<? if (!empty($direccion['Direccion']['depto'])) : ?>
			<p class="list-group-item">Depto/Oficina: <?=$direccion['Direccion']['depto'];?></p>
		<? endif; ?>
		<p class="list-group-item">Comuna: <?=$direccion['Comuna']['nombre'];?></p>
		<? if (!empty($direccion['Direccion']['comentario'])) : ?>
		<p class="list-group-item"><?= $direccion['Direccion']['comentario']; ?></p>
	<? endif; ?>
	</div>
</div>