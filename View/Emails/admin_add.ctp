<div class="page-title">
	<h2><span class="fa fa-envelope"></span> Newsletter</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo Newsletter</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('Email', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<tr>
									<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('titulo', 'Titulo'); ?></th>
									<td><?= $this->Form->input('titulo'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('descripcion', 'Descripcion'); ?></th>
									<td><?= $this->Form->input('descripcion'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('fecha', 'Fecha'); ?></th>
									<td><?= $this->Form->input('fecha'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('imagen', 'Imagen'); ?></th>
									<td><?= $this->Form->input('imagen', array('type' => 'file', 'class' => '')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('sitio_url', 'Url Sitio web'); ?></th>
									<td><?= $this->Form->input('sitio_url'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('plantilla_id', 'Plantilla'); ?></th>
									<td><?= $this->Form->input('plantilla_id', array(
										'class' => 'form-control select',
										'empty'	=> 'Seleccione'
										)); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('Categoria', 'Categoria'); ?></th>
									<td><?= $this->Form->input('Categoria', array(
										'empty' => 'Seleccione',
										'class' => 'select form-control', 
										'multiple' => 'multiple', 
										'data-live-search' => true)
									); ?></td>
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