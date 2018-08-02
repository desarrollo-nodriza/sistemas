<div class="page-title">
	<h2><span class="fa fa-cubes"></span> Modulos</h2>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo Modulo</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('Modulo', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<tr>
									<th><?= $this->Form->label('parent_id', 'Modulo padre'); ?></th>
									<td><?= $this->Form->input('parent_id', array(
										    'options' => $parentModulos,
										    'empty' => 'Sin categoría padre',
										    'class' => 'form-control select'
										)); ?>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('url', 'URL'); ?></th>
									<?  
										$options = array(); 

									 	$controladores		=  array_map(function($controlador) {

											return str_replace('Controller', '', $controlador);

										}, App::objects('controller'));

										   foreach ( $controladores as $controlador ) : 
										   if ( $controlador === 'App' ) continue; 

											 $options[lcfirst($controlador)] = $controlador; 

										   endforeach; ?>
									
									<td><?= $this->Form->select('url', $options,array('empty' => 'Seleccione', 'class' => 'form-control select', 'data-live-search' => 'true')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('icono', 'Icono'); ?></th>
									<td>
										<div class="input-group">
											<span class="input-group-addon add-on select-icon" data-toggle="modal" data-target="#modal_iconos" >
												<span class="fa fa-fort-awesome"></span>
											</span>
											<?= $this->Form->input('icono'); ?>
										</div>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('Rol', 'Rol'); ?></th>
									<td><?= $this->Form->input('Rol', array(
										'class' => 'form-control select', 
										'multiple' => 'multiple',
										'empty' => 'Seleccione Rol')
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
<?=$this->element('iconos');?>