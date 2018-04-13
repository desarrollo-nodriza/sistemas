<div class="page-title">
	<h2><span class="fa fa-beer"></span> Editar Socio</h2>
</div>
<?= $this->Form->create('Socio', array('id' => 'formularioSocios', 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id');?>
<?= $this->Form->input('tienda_id', array('type' => 'hidden', 'value' => $this->Session->read('Tienda.id')));?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">URL de acceso para el socio</h3>
					<br>
					<br>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<p><?=Router::url('/', true);?>socio</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Datos del Socio</h3>
					<br>
					<br>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-12 form-group">
							<?= $this->Form->label('nombre', 'Nombre'); ?>
							<?= $this->Form->input('nombre'); ?>
						</div>
						<div class="col-xs-12 col-sm-12 form-group">
							<?= $this->Form->label('email', 'Email'); ?>
							<?= $this->Form->input('email'); ?>
						</div>
						<div class="col-xs-12 col-sm-12 form-group">
							<?= $this->Form->label('clave', 'Contraseña'); ?>
							<?= $this->Form->input('clave', array('type' => 'password', 'autocomplete' => 'off', 'value' => '')); ?>
						</div>
						<div class="col-xs-12 col-sm-12 form-group">
							<?= $this->Form->label('usuario', 'Usuario'); ?>
							<div class="input-group">
								<?= $this->Form->input('usuario', array('readonly' => true)); ?>
								<span class="input-group-btn">
                                    <button class="btn btn-primary generar_usuario" type="button">Generar nombre de usuario</button>
                                </span>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 form-group">
							<?= $this->Form->label('activo', 'Activo'); ?><br>
							<?= $this->Form->input('activo', array('class' => 'icheckbox')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Fabricantes asociados</h3>
					<br>
					<br>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="form-socios">
							<div class="form-group col-xs-12 col-sm-12">
								<?= $this->Form->label('Fabricante', 'Seleccione fabricantes asociados'); ?>
								<?= $this->Form->input('Fabricante', array(
										'class' => 'form-control select', 
										'multiple' => 'multiple',
										'data-live-search' => true,
										'empty' => 'Seleccione fabricante')
										); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-body">
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
