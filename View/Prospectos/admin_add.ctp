<div class="page-title">
	<h2><span class="fa fa-bookmark"></span> Nuevo Prospecto</h2>
</div>
<?= $this->Form->create('Prospecto', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Información del prospecto</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre del prospecto (*)'); ?></th>
								<td><?= $this->Form->input('nombre', array('placeholder' => 'Prospecto para Ejemplo')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tienda_id', 'Tienda del prospecto (*)'); ?></th>
								<td><?= $this->Form->input('tienda_id', array('class' => 'form-control js-tienda')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('descripcion', 'Descripcion del prospecto (*)'); ?></th>
								<td><?= $this->Form->input('descripcion', array('placeholder' => 'Ingrese una descripción para el prospecto')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('estado_prospecto_id', 'Estado del prospecto (*)'); ?></th>
								<td><?= $this->Form->input('estado_prospecto_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('moneda_id', 'Moneda a utilizar (*)'); ?></th>
								<td><?= $this->Form->input('moneda_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('origen_id', 'Origen del contacto (*)'); ?></th>
								<td><?= $this->Form->input('origen_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('comentarios', 'Comentarios adicionales'); ?></th>
								<td><?= $this->Form->input('comentarios', array('placeholder' => 'Ingrese comentarios adicionales.')); ?></td>
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
		</div> <!-- end col -->
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Información del cliente</h3>
				</div>
				<div class="panel-body">
					<div class="loader"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('existente', '¿Cliente existente?'); ?></th>
								<td><label class="switch switch-small"><?= $this->Form->checkbox('existente'); ?><span></span></label></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('ape', 'Tipo de cliente'); ?></th>
								<td><?=$this->Form->select('Cliente.1.ape', array(
									'1' => 'Persona',
									'2' => 'Empresa'
								), array('class' => 'form-control js-tipo-cliente', 'empty' => 'Seleccione tipo de cliente'));?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('email', 'Email del cliente'); ?></th>
								<td><?= $this->Form->input('Cliente.1.email', array('class' => 'form-control input-clientes-buscar email', 'placeholder' => 'ejemplo@email.cl')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('siret', 'Rut de la empresa'); ?></th>
								<td><?= $this->Form->input('Cliente.1.siret', array('class' => 'form-control rut js-rut-empresa', 'placeholder' => '11.111.111-1')); ?></td>
							</tr>
							<tr class="nuevo-cliente">
								<th><?= $this->Form->label('id_gender', 'Sexo'); ?></th>
								<td><?=$this->Form->select('Cliente.1.id_gender', array(
									'0' => 'No especifica',
									'1' => 'Hombre',
									'2' => 'Mujer'
								), array('class' => 'form-control', 'empty' => 'Seleccione sexo'));?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('firstname', 'Nombre del cliente'); ?></th>
								<td><?= $this->Form->input('Cliente.1.firstname', array('placeholder' => 'Ingrese nombre del cliente')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('lastname', 'Apellido del cliente'); ?></th>
								<td><?= $this->Form->input('Cliente.1.lastname', array('placeholder' => 'Ingrese apellido del cliente')); ?></td>
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
		</div> <!-- end col -->
		<div class="col-xs-12 js-clon-parent">
			<div class="table-responsive js-clon-contenedor">
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					<div class="panel panel-default js-clon-base hidden">
						<div class="panel-heading" role="tab">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="false" class="btn-toggle"></a>
							</h4>
							<ul class="panel-controls">
                                <li><label class="control-label">Utilizar esta dirección </label></li>
                                <li>
                                	<label class="switch"><?= $this->Form->checkbox('Direccioncliente.999.utilizar_check', array('class' => 'js-direccion-utilizar', 'disabled' => true)); ?><span></span></label>
                                </li>
                            </ul>
						</div>
						<div class="panel-collapse collapse" role="tabpanel">
							<div class="panel-body">
								<table class="table">
									<tr>
										<th><?= $this->Form->label('alias', 'Alias de la dirección (*)'); ?></th>
										<td>
											<?= $this->Form->input('Direccioncliente.999.id_address', array('class' => 'form-control js-direccion-id', 'type' => 'hidden')); ?>
											<?= $this->Form->input('Direccioncliente.999.alias', array('class' => 'form-control js-direccion-alias', 'placeholder' => 'Mi dirección', 'disabled' => true)); ?>
										</td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.company', 'Empresa'); ?></th>
										<td><?= $this->Form->input('Direccioncliente.999.company', array('class' => 'form-control js-direccion-empresa', 'disabled' => true, 'placeholder' => 'Ejemplo S.A')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.vat_number', 'Rut Empresa'); ?></th>
										<td><?= $this->Form->input('Direccioncliente.999.vat_number', array('class' => 'form-control js-direccion-empresa-rut rut', 'disabled' => true, 'placeholder' => '11.111.111-1')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.firstname', 'Nombre del cliente'); ?></th>
										<td><?= $this->Form->input('Direccioncliente.999.firstname', array('class' => 'form-control js-direccion-nombre', 'disabled' => true, 'placeholder' => 'Ingrese nombre del cliente')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.lastname', 'Apellido'); ?></th>
										<td><?= $this->Form->input('Direccioncliente.999.lastname', array('class' => 'form-control js-direccion-apellido', 'disabled' => true, 'placeholder' => 'Ingrese apellido del cliente')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.address1', 'Dirección principal'); ?></th>
										<td><?= $this->Form->textarea('Direccioncliente.999.address1', array('class' => 'form-control js-direccion-direccion1', 'disabled' => true, 'placeholder' => 'Ingrese dirección principal')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.address2', 'Dirección 2'); ?></th>
										<td><?= $this->Form->textarea('Direccioncliente.999.address2', array('class' => 'form-control js-direccion-direccion2', 'disabled' => true, 'placeholder' => 'Ingrese dirección 2')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.city', 'Ciudad (*)'); ?></th>
										<td><?= $this->Form->input('Direccioncliente.999.city', array('class' => 'form-control js-direccion-ciudad', 'disabled' => true, 'placeholder' => 'Ingrese ciudad del cliente')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.id_country', 'Pais'); ?></th>
										<td><?= $this->Form->select('Direccioncliente.999.id_country', array(), array('class' => 'form-control js-pais js-direccion-pais', 'disabled' => true)); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.id_state', 'Región'); ?></th>
										<td><?= $this->Form->select('Direccioncliente.999.id_state', array(), array('class' => 'form-control js-region js-direccion-region', 'disabled' => true)); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.phone', 'Teléfono fijo'); ?></th>
										<td><?= $this->Form->input('Direccioncliente.999.phone', array('class' => 'mascara_fono form-control js-direccion-fono', 'placeholder' => 'xxxxx-xxxx', 'disabled' => true)); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.phone_mobile', 'Celular'); ?></th>
										<td><?= $this->Form->input('Direccioncliente.999.phone_mobile', array('class' => 'mascara_fono form-control js-direccion-celular', 'placeholder' => 'xxxxx-xxxx', 'disabled' => true)); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('Direccioncliente.999.other', 'Comentarios de la dirección'); ?></th>
										<td><?= $this->Form->textarea('Direccioncliente.999.other', array('class' => 'form-control js-direccion-otro', 'disabled' => true, 'placeholder' => 'Ingrese comentarios adicionales o de referencia')); ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pull-right">
				<button class="js-clon-agregar btn btn-success"><span class="fa fa-plus"></span> Agregar otra Dirección</button>
			</div>
		</div> <!-- end col -->
		<div class="col-xs-12">
			<div class="pull-right seccion-botones">
				<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
				<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
<?= $this->Form->end(); ?>
<div class="loader"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>