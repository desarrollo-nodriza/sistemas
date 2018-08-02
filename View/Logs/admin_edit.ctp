<div class="page-title">
	<h2><span class="fa fa-list"></span> Logs</h2>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Editar Log</h3>
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<?= $this->Form->create('Log', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
				<table class="table">
					<?= $this->Form->input('id'); ?>
					<tr>
						<th><?= $this->Form->label('administrador', 'Administrador'); ?></th>
						<td><?= $this->Form->input('administrador'); ?></td>
					</tr>
					<tr>
						<th><?= $this->Form->label('administrador_id', 'Administrador'); ?></th>
						<td><?= $this->Form->input('administrador_id', array('class' => 'form-control select')); ?></td>
					</tr>
					<tr>
						<th><?= $this->Form->label('modulo', 'Modulo'); ?></th>
						<td><?= $this->Form->input('modulo'); ?></td>
					</tr>
					<tr>
						<th><?= $this->Form->label('modulo_accion', 'Modulo accion'); ?></th>
						<td><?= $this->Form->input('modulo_accion'); ?></td>
					</tr>
				</table>

				<div class="pull-right">
					<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
					<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
				</div>
			<?= $this->Form->end(); ?>
		</div>
	</div>
</div>
