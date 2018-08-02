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
									<th><?= $this->Form->label('descripcion', 'Descripción'); ?></th>
									<td>
										<label>La descripción se mostrará al final del newsletter</label>
										<?= $this->Form->input('descripcion', array('class' => 'summernote')); ?></td>
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
									<th><?= $this->Form->label('utm_source', 'Origen de la campaña'); ?></th>
									<td><?= $this->Form->input('utm_source', array('placeholder' => 'newsletter')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('utm_medium', 'Medio de la campaña'); ?></th>
									<td><?= $this->Form->input('utm_medium', array('placeholder' => 'email')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('utm_campaign', 'Nombre de la campaña'); ?></th>
									<td><?= $this->Form->input('utm_campaign', array('placeholder' => 'ofertas_de_mes')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('utm_term', 'Término de la campaña'); ?></th>
									<td><?= $this->Form->select('utm_term', array(
										'nombre' => 'Nombre y apellido',
										'email' => 'Email'
										), array(
										'empty' => 'Seleccione',
										'class' => 'form-control')
									); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('utm_content', 'Etiqueta de la campaña'); ?></th>
									<td><?= $this->Form->input('utm_content', array('placeholder' => '*|EMAIL|*')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('mostrar_cuotas', 'Mostrar Cuotas'); ?></th>
									<td><?= $this->Form->input('mostrar_cuotas', array('class' => 'icheckbox')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('cuotas', 'Número de cuotas'); ?></th>
									<td><?= $this->Form->input('cuotas'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('html_cuotas', 'Html para mostrar cuotas'); ?></th>
									<td><label>Utilice el símbolo %d para agregar el número de cuotas y %s para el monto de las cuotas.</label>
										<?= $this->Form->input('html_cuotas', array('class' => 'summernote', 'value' => '<font style="display: block;line-height: 25px;color: #FB5A00;margin: 4px 0;">%d cuotas de %s</font>')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('plantilla_id', 'Plantilla'); ?></th>
									<td><?= $this->Form->input('plantilla_id', array(
										'class' => 'form-control select',
										'empty'	=> 'Seleccione'
										)); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('Categoria', 'Categorias'); ?></th>
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