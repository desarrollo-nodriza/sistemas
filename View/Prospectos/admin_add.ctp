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
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tienda_id', 'Tienda'); ?></th>
								<td><?= $this->Form->input('tienda_id', array('class' => 'form-control js-tienda')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('descripcion', 'Descripcion'); ?></th>
								<td><?= $this->Form->input('descripcion'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('estado_prospecto_id', 'Estado prospecto'); ?></th>
								<td><?= $this->Form->input('estado_prospecto_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('moneda_id', 'Moneda'); ?></th>
								<td><?= $this->Form->input('moneda_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('origen_id', 'Origen'); ?></th>
								<td><?= $this->Form->input('origen_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('comentarios', 'Comentarios'); ?></th>
								<td><?= $this->Form->input('comentarios'); ?></td>
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
								<th><?= $this->Form->label('existente', 'Cliente Existente'); ?></th>
								<td><label class="switch switch-small"><?= $this->Form->checkbox('existente'); ?><span></span></label></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('email', 'Email'); ?></th>
								<td><?= $this->Form->input('Cliente..email', array('class' => 'form-control input-clientes-buscar')); ?></td>
							</tr>
							<tr class="nuevo-cliente">
								<th><?= $this->Form->label('id_gender', 'Genero'); ?></th>
								<td><?=$this->Form->select('Cliente..id_gender', array(
									'0' => 'No especifica',
									'1' => 'Hombre',
									'2' => 'Mujer'
								), array('class' => 'form-control', 'empty' => 'Seleccione'));?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('firstname', 'Nombre'); ?></th>
								<td><?= $this->Form->input('Cliente..firstname'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('lastname', 'Apellido'); ?></th>
								<td><?= $this->Form->input('Cliente..lastname'); ?></td>
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
					<h3 class="panel-title">Direcciones del cliente</h3>
				</div>
				<div class="panel-body">
					<div class="loader"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>
					<div class="table-responsive js-clon-contenedor">
						<table class="table js-clon-base hidden">
							<tr>
								<th><?= $this->Form->label('alias', 'Alias de la dirección (*)'); ?></th>
								<td>
									<?= $this->Form->input('Direccioncliente.999.id_address', array('class' => 'form-control js-direccion-id', 'type' => 'hidden')); ?>
									<?= $this->Form->input('Direccioncliente.999.alias', array('class' => 'form-control js-direccion-alias')); ?>
								</td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.company', 'Empresa'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.company', array('class' => 'form-control js-direccion-empresa', 'disabled' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.vat_number', 'Rut Empresa'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.vat_number', array('class' => 'form-control js-direccion-empresa-rut', 'disabled' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.firstname', 'Nombre (*)'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.firstname', array('class' => 'form-control js-direccion-nombre', 'disabled' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.lastname', 'Apellido (*)'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.lastname', array('class' => 'form-control js-direccion-apellido', 'disabled' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.address1', 'Dirección (*)'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.address1', array('class' => 'form-control js-direccion-direccion1', 'disabled' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.address2', 'Dirección 2'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.address2', array('class' => 'form-control js-direccion-direccion2', 'disabled' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.city', 'Ciudad (*)'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.city', array('class' => 'form-control js-direccion-ciudad', 'disabled' => true)); ?></td>
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
								<td><?= $this->Form->input('Direccioncliente.999.phone', array('class' => 'form-control js-direccion-fono', 'disabled' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.phone_mobile', 'Celular'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.phone_mobile', array('class' => 'form-control js-direccion-celular', 'disabled' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('Direccioncliente.999.other', 'Observaciones'); ?></th>
								<td><?= $this->Form->input('Direccioncliente.999.other', array('class' => 'form-control js-direccion-otro', 'disabled' => true)); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<button class="js-clon-agregar btn btn-success"><span class="fa fa-plus"></span> Agregar otra</button>
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
	</div> <!-- end row -->
</div>
<?= $this->Form->end(); ?>
