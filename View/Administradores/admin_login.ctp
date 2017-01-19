<div class="login-box animated fadeInDown">
	<div class="login-logo"></div>
	<?= $this->element('admin_alertas'); ?>
	<div class="login-body">
		<div class="login-title text-center"><strong>Bienvenido</strong></div>
		<div class="login-title text-center">Para iniciar sesión debes identificarte.</div>
		<?= $this->Form->create('Administrador', array('class' => 'form-horizontal', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
			<div class="form-group">
				<div class="col-md-12">
					<?= $this->Form->input('email', array('placeholder' => 'Email')); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<?= $this->Form->input('clave', array('type' => 'password', 'placeholder' => 'Contraseña')); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<button type="submit" class="btn btn-info btn-block">Entrar</button>
				</div>
			</div>
			<div class="login-or">O</div>
			<div class="form-group">
				<div class="login-title text-center">
					<?= $this->Html->link(
						$this->Html->image('/backend/img/login_google.png'),
						$authUrl,
						array('escape' => false)
					); ?>
				</div>
			</div>
		<?= $this->Form->end(); ?>
	</div>
	<div class="login-footer">
		<div class="pull-left">
			&copy; <?=date('Y');?> Nodriza Spa
		</div>
	</div>
</div>
