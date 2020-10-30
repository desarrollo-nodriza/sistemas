<div class="page-title">
	<h2><span class="fa fa-users"></span> Editar <?=$this->request->data['Administrador']['nombre']; ?></h2>
</div>

<?= $this->Form->create('Administrador', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-pencil"></i> Información</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
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
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-bell-o" aria-hidden="true"></i> Notificaciones</h3>
				</div>
				<div class="panel-body">
					<table class="table">
						<tr>
							<th><?= $this->Form->label('notificacion_ventas', 'Ventas'); ?></th>
							<td><?= $this->Form->input('notificacion_ventas', array('class' => 'icheckbox')); ?></td>
						</tr>
						<tr>
							<th><?= $this->Form->label('notificacion_revision_oc', 'Revisar OC'); ?></th>
							<td><?= $this->Form->input('notificacion_revision_oc', array('class' => 'icheckbox')); ?></td>
						</tr>
						<tr>
							<th><?= $this->Form->label('notificacion_pagar_oc', 'Pagar OC'); ?></th>
							<td><?= $this->Form->input('notificacion_pagar_oc', array('class' => 'icheckbox')); ?></td>
						</tr>
						<tr>
							<th><?= $this->Form->label('notificacion_bodegas', 'Bodegas'); ?></th>
							<td><?= $this->Form->input('notificacion_bodegas', array('class' => 'icheckbox')); ?></td>
						</tr>
						<tr>
							<th><?= $this->Form->label('notificacion_contactos', 'Contactos'); ?></th>
							<td><?= $this->Form->input('notificacion_contactos', array('class' => 'icheckbox')); ?></td>
						</tr>
					</table>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>
