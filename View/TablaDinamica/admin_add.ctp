<div class="page-title">
	<h2><span class="fa fa-truck"></span> Crear tabla dinámica</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<?= $this->Form->create('TablaDinamica', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>

				<div class="panel-heading">
					<h3 class="panel-title">Crear tabla dinámica</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>

							<th><?= $this->Form->label('dependencia', 'Dependencia o Plugin'); ?></th>
							<td><?= $this->Form->select('dependencia', $dependencias, array('class' => 'form-control js-select-dependencia', 'empty' => 'Sin dependencia')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox', 'default' => true)); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary start-loading-when-form-is-validate" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>

		</div>
	</div>


</div>

<?= $this->Html->script(array(
	'/backend/js/cuenta_corriente_transporte.js?v=' . rand()
)); ?>
<?= $this->fetch('script'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(document).ready(function() {
			$('.mi-selector').select2();
		});
	});
</script>