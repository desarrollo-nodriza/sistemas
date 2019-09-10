<div class="page-title">
	<h2><span class="fa fa-sitemap"></span> Campaña</h2>
</div>
<?= $this->Form->create('Campana', array('class' => 'form-horizontal js-form-campana', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nueva campaña</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('categoria_id', 'Categoria principal'); ?></th>
								<td><?= $this->Form->select('categoria_id', $categorias, array('class' => 'form-control select', 'data-live-search' => 'true')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
							</tr>
						</table>
					</div>
					<!--<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th><?= __('Etiqueta');?></th>
									<th><?= __('Sub categoria');?></th>
									<th></th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->input('OrdenCompraEtiqueta.999.nombre', array('type' => 'text', 'disabled' => true, 'class' => 'form-control', 'placeholder' => 'Ej: Taladros makita, tijeras, etc.')); ?>
									</td>
									<td>
										<?= $this->Form->select('OrdenCompraEtiqueta.999.categoria_id', $categorias, array('disabled' => true, 'class' => 'form-control not-blank', 'empty' => 'Seleccione')); ?>
									</td>									
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>-->
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
