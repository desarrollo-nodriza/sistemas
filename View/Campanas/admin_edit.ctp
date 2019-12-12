<?= $this->Html->script('/backend/js/campannas.js?v=' . rand()); ?>
<?= $this->fetch('script'); ?>

<div class="page-title">
	<h2><span class="fa fa-sitemap"></span> Campaña</h2>
</div>
<?= $this->Form->create('Campana', array('class' => 'form-horizontal js-form-campana', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id'); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nueva Campaña</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre', array('class' => 'form-control not-blank')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('categoria_id', 'Categoria principal'); ?></th>
								<td><?= $this->Form->select('categoria_id', $categorias, array('class' => 'form-control select js-select-categoria-main not-blank', 'data-live-search' => 'true')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('excluir_stockout', 'Excluir productos sin stock'); ?></th>
								<td><?= $this->Form->input('excluir_stockout', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
							</tr>
						</table>
					</div>
					<div class="table-responsive js-crear-etiquetas-campana">
						<table class="table table-bordered js-clone-wrapper" data-filas='5'>
							<thead>
								<tr>
									<th><?= __('Etiqueta');?></th>
									<th><?= __('Sub categoria');?></th>
									<th><a href="#" class="copy_tr btn btn-rounded btn-primary"><span class="fa fa-plus"></span> agregar</a></th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->input('CampanaEtiqueta.999.nombre', array('type' => 'text', 'disabled' => true, 'class' => 'form-control not-blank', 'placeholder' => 'Ej: Taladros makita, tijeras, etc.')); ?>
									</td>
									<td>
										<?= $this->Form->select('CampanaEtiqueta.999.categoria_id', $subCategorias, array('disabled' => true, 'class' => 'form-control not-blank', 'empty' => 'Seleccione')); ?>
									</td>									
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
								<? foreach ($this->request->data['CampanaEtiqueta'] as $ie => $e) : ?>
								<tr>
									<td>
										<?= $this->Form->input('CampanaEtiqueta.'.$ie.'.nombre', array('type' => 'text', 'class' => 'form-control not-blank', 'placeholder' => 'Ej: Taladros makita, tijeras, etc.', 'value' => $e['nombre'])); ?>
									</td>
									<td>
										<?= $this->Form->select('CampanaEtiqueta.'.$ie.'.categoria_id', $subCategorias, array('class' => 'form-control not-blank', 'empty' => 'Seleccione', 'default' => $e['categoria_id'])); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
								<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
<?= $this->Form->end(); ?>