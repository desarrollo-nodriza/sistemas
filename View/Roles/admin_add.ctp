<div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> <?=$this->request->data['Rol']['nombre'];?></h2>
</div>

<?= $this->Form->create('Rol', array('class' => 'form-horizontal js-validate-roles', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
							<table class="table">
							
								<tr>
									<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<!--<tr>
									<th><?= $this->Form->label('permisos', 'Json de permisos'); ?></th>
									<td><?= $this->Form->input('permisos'); ?></td>
								</tr>-->
								<tr>
									<th><?= $this->Form->label('mostrar_dashboard', 'Mostrar dashboard'); ?></th>
									<td><?= $this->Form->input('mostrar_dashboard', array('class' => 'icheckbox')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
								</tr>
							</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-file" aria-hidden="true"></i> Facturación</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('permitir_boleta', 'Activar boleta'); ?></th>
								<td><?= $this->Form->input('permitir_boleta', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('permitir_factura', 'Activar factura'); ?></th>
								<td><?= $this->Form->input('permitir_factura', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('permitir_ndc', 'Activar nota de crédito'); ?></th>
								<td><?= $this->Form->input('permitir_ndc', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('permitir_ndd', 'Activar nota de débito'); ?></th>
								<td><?= $this->Form->input('permitir_ndd', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('permitir_gdd', 'Activar guia de despacho'); ?></th>
								<td><?= $this->Form->input('permitir_gdd', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('permitir_fc', 'Activar factura de compra'); ?></th>
								<td><?= $this->Form->input('permitir_fc', array('class' => 'icheckbox')); ?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?=__('Permisos');?></h5>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th><?= __('Controlador');?></th>
									<th><?= __('Permisos');?></th>
									<th></th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->input('Permisos.999.controlador', array('type' => 'text', 'disabled' => true, 'class' => 'form-control', 'placeholder' => 'Ej: roles')); ?>
									</td>
									<td>
										<?= $this->Form->input('Permisos.999.json', array('type' => 'textarea', 'class' => 'form-control summernote-permisos', 'rows' => 2, 'placeholder' => 'Ingrese en formato json')); ?>
									</td>									
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>

								<? if (!empty($this->request->data['Rol']['permisos'])) :  $cont = 0 ;?>
								<? foreach($this->request->data['Rol']['permisos'] as $ip => $permiso) : ?>
								<tr>
									<td>
										<?= $this->Form->input(sprintf('Permisos.%d.controlador', $cont), array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ej: roles', 'value' => $ip)); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('Permisos.%d.json', $cont), array('type' => 'textarea', 'class' => 'form-control summernote-permisos', 'rows' => 2, 'placeholder' => 'Ingrese en formato json', 'value' => json_encode($permiso, true) )); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
								<? $cont++; endforeach; ?>
								<? endif; ?>
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<button type="submit" class="btn btn-primary">Guardar cambios</button>
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>
