<div id="login">
	<div class="container">
		<div class="row justify-content-center align-items-center">
			<div class="col-12 col-md-6 mt-5">
				<img src="<?= FULL_BASE_URL . '/webroot/img/' . $tienda['Tienda']['logo']['path']; ?>" class="img-fluid mt-5" style="max-width: 150px;">
			</div>
		</div>
		<div class="row justify-content-center align-items-center">
			<div class="col-12 col-md-6 mt-4">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Bienvenid@</h5>
						<h6 class="card-subtitle mb-4 text-muted">Ingresa al sistema y podrás ver el detalle y estado de tus compras.</h6>

						<?= $this->Form->create('VentaCliente', array('class' => 'form-horizontal js-validar-simple', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
						<div class="form-group">
							<?=$this->Form->label('email', 'Ingresa tu email de cliente ' . $tienda['Tienda']['nombre']);?>
							<?=$this->form->input('email', array('class' => 'form-control not-blank is-email', 'placeholder' => 'Ej: miemail@email.cl', 'label' => false, 'div' => false));?>
						</div>
						
						<?=$this->Form->button('<i class="fa fa-lock"></i> Obtener link de acceso', array('class' => 'btn btn-primary btn-block', 'escape' => false)); ?>
						<?= $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>