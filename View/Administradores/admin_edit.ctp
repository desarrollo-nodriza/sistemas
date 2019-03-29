<div class="page-title">
	<h2><span class="fa fa-users"></span> Administradores</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar Administrador <?=$this->request->data['Administrador']['nombre']; ?></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('Administrador', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<?= $this->Form->input('id'); ?>
								<tr>
									<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('email', 'Email'); ?></th>
									<td><?= $this->Form->input('email'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('clave', 'Clave'); ?></th>
									<td><?= $this->Form->input('clave', array('type' => 'password', 'autocomplete' => 'off', 'value' => '')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('repetir_clave', 'Repetir clave'); ?></th>
									<td><?= $this->Form->input('repetir_clave', array('type' => 'password', 'autocomplete' => 'off', 'value' => '')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('notificaciones', 'Json de notificaciones'); ?></th>
									<td><?= $this->Form->input('notificaciones'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
								</tr>
							<? if ( $this->Session->read('Auth.Administrador.rol_id') == 1 ) : ?>
								<tr>
									<th><?= $this->Form->label('rol_id', 'Rol de usuario'); ?></th>
									<td><?= $this->Form->input('rol_id', array('class' => 'form-control select', 'empty' => 'Seleccione Rol')); ?></td>
								</tr>
							<? endif; ?>
								<tr>
									<th><?= $this->Form->label('secret_key', 'Secreto'); ?></th>
									<td>
										<div class="input-group" style="max-width: 100%;">
		                                    <?= $this->Form->input('secret_key', array('type' => 'text', 'class' => 'form-control sectret_input', 'data-lenght' => 25)); ?>
		                                    <span class="input-group-btn">
                                                <button class="btn btn-default" id="generar_secreto" type="button"><i class="fa fa-cogs"></i> Generar</button>
                                            </span>
		                                </div>
									</td>
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
