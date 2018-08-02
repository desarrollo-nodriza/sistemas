<div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> Roles de usuario</h2>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo rol de usuario</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('Rol', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<tr>
									<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('permisos', 'Json de permisos'); ?></th>
									<td><?= $this->Form->input('permisos'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('mostrar_dashboard', 'Mostrar dashboard'); ?></th>
									<td><?= $this->Form->input('mostrar_dashboard', array('class' => 'icheckbox')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
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
		</div>
	</div>
</div>
