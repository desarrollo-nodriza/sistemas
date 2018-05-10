<div class="page-title">
	<h2><span class="fa fa-shopping-bag"></span> Producto Tienda <?=$tienda['Tienda']['nombre'];?></h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Asignar categorías</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('Productotienda', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<?=$this->Form->input('id_product', array('value' => $producto['Productotienda']['id_product'], 'type' => 'hidden') );?>
								<tbody>
									<tr>
										<th>Nombre</th>
										<td><?= $producto['pl']['name']; ?></td>
									</tr>
									<tr>
										<th>Cód referencia</th>
										<td><?= $producto['Productotienda']['reference']; ?></td>
									</tr>
									<tr>
										<th>Imagen</th>
										<td><?= $this->Html->image($producto['0']['url_image'], array('class' => 'img-responsive thumbnail', 'style' => 'max-width:200px;')); ?></td>
									</tr>
									<tr>
										<th>Precio sin IVA</th>
										<td><?= CakeNumber::currency($producto['Productotienda']['price'], 'CLP'); ?>&nbsp;</td>
									</tr>
									<tr>
										<th>Precio con IVA</th>
										<td><?= CakeNumber::currency($producto['Productotienda']['valor_iva'], 'CLP'); ?>&nbsp;</td>
									</tr>
									<tr>
										<th>Precio Final</th>
										<td>
											<?= CakeNumber::currency($producto['Productotienda']['valor_final'], 'CLP'); ?>&nbsp; 
											<small><?= $descuento = ( !empty($producto['Productotienda']['descuento']) ) ? 'Descuento de un <b>'  . $producto['Productotienda']['descuento'] . '%</b>' : '' ; ?></small>
										</td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Categoria', 'Categorias'); ?></th>
										<td><?=$this->Form->input('Categoria', array(
											'class' => 'form-control', 
											'multiple' => 'multiple',
											'empty' => 'Sin categoría' ));?></td>
									</tr>
								</tbody>
							</table>

							<div class="pull-right">
								<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
								<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
							</div>
						<?= $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 hidden">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Precios específicos</h3>
					<small>Los precios especificos estan ordenados por </small>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('Productotienda', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<?=$this->Form->input('id_product', array('value' => $producto['Productotienda']['id_product'], 'type' => 'hidden') );?>
								<tbody>
									<tr>
										<th>Nombre</th>
										<td><?= $producto['pl']['name']; ?></td>
									</tr>
									<tr>
										<th>Cód referencia</th>
										<td><?= $producto['Productotienda']['reference']; ?></td>
									</tr>
									<tr>
										<th>Imagen</th>
										<td><?= $this->Html->image($producto['0']['url_image'], array('class' => 'img-responsive thumbnail', 'style' => 'max-width:200px;')); ?></td>
									</tr>
									<tr>
										<th>Precio sin IVA</th>
										<td><?= CakeNumber::currency($producto['Productotienda']['price'], 'CLP'); ?>&nbsp;</td>
									</tr>
									<tr>
										<th>Precio con IVA</th>
										<td><?= CakeNumber::currency($producto['Productotienda']['valor_iva'], 'CLP'); ?>&nbsp;</td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Categoria', 'Categorias'); ?></th>
										<td><?=$this->Form->input('Categoria', array('class' => 'form-control', 'multiple' => 'multiple' ));?></td>
									</tr>
								</tbody>
							</table>

							<div class="pull-right">
								<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
								<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
							</div>
						<?= $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<? 
	if (count($producto['Categoria']) > 0) {

       $ListaCategorias = "";

       for ($i = 0; $i < count($producto['Categoria']); $i++) {
           if ($ListaCategorias != "") $ListaCategorias .= ", ";
           $ListaCategorias .= $producto['Categoria'][$i]['id'];
       }
    ?>
		<script type="text/javascript">
           $(document).ready(function() {
           		$("#CategoriaCategoria").val([<?= $ListaCategorias; ?>]);
           });
       </script>
	<?php
	}
?>