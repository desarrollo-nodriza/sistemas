<div class="lockscreen-container">
	<div class="lockscreen-box animated fadeInDown text-center">
	<?= $this->Html->image('/backend/img/logo-lr.png'); ?>

		<div class="lsb-access">
			<div class="lsb-box">
				<div class="fa fa-lock"></div>
				<div class="user animated fadeIn">
					<div class="fa fa-user" style="display: block;"></div>
				</div>
			</div>
		</div>

		<div class="lsb-form animated fadeInDown">
			<?= $this->Form->create('Administrador', array('class' => 'form-horizontal', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
				<div class="form-group">
					<div class="col-md-12">
						<div class="input-group">
							<div class="input-group-addon">
								<span class="fa fa-lock"></span>
							</div>
							<?= $this->Form->input('clave', array('type' => 'password', 'placeholder' => 'Contraseña')); ?>
						</div>
					</div>
				</div>
				<!--<div class="form-group">
					<span class="col-md-10 col-md-offset-1 label label-danger error">Datos Incorrectos</span>
				</div>-->
				<input type="submit" class="hidden">
			<?= $this->Form->end(); ?>
		</div>
		<?= $this->element('admin_alertas'); ?>
	</div>
</div>
