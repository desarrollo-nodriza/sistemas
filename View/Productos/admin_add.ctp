<div class="page-title">
	<h2><span class="fa fa-shopping-bag"></span> Productos</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo Producto</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('Producto', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<tbody>
									<tr>
										<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
										<td><?= $this->Form->input('nombre'); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('descripcion', 'Descripcion <small>(Opcional)</small>'); ?></th>
										<td><?= $this->Form->input('descripcion'); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('modelo', 'Modelo'); ?></th>
										<td><?= $this->Form->input('modelo'); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('imagen', 'Imagen'); ?></th>
										<td><?= $this->Form->input('imagen', array('type' => 'file', 'class' => '')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('valor', 'Valor'); ?></th>
										<td><?= $this->Form->input('valor'); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('porcentaje_oferta', 'Porcentaje oferta'); ?></th>
										<td><?= $this->Form->input('porcentaje_oferta'); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('url', 'URL'); ?></th>
										<td><?= $this->Form->input('url'); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('boton', 'Agregar botón'); ?></th>
										<td><?= $this->Form->input('boton', array('class' => 'icheckbox')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('activo', 'Activo'); ?></th>
										<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Categoria', 'Categorias'); ?></th>
										<td><?= $this->Form->select('Categoria', $categorias, array(
											'empty' => 'Seleccione',
											'class' => 'select form-control', 
											'multiple' => 'multiple', 
											'data-live-search' => true)
										); ?></td>
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