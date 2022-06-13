<div class="page-title">
	<h2><span class="fa fa-truck"></span> Cuenta corriente para transportista</h2>
</div>

<div class="page-content-wrap">
	<?= $this->Form->create('CuentaCorrienteTransporte', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
	<?= $this->Form->input('id'); ?>
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar cuenta corriente</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>

							<th><?= $this->Form->label('dependencia', 'Dependencia o Plugin'); ?></th>
							<td><?= $this->Form->select('dependencia', $dependencias, array('class' => 'form-control js-select-dependencia', 'empty' => 'Sin dependencia')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
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
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default js-panel-starken <?= ($this->request->data['CuentaCorrienteTransporte']['dependencia'] == 'starken') ? '' : 'hidden'; ?>">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Configuración de starken</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('starken_rut_api_rest', 'Rut usuario rest'); ?></th>
								<td><?= $this->Form->input('starken_rut_api_rest', array('placeholder' => 'Ingrese rut sin dv')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_clave_api_rest', 'Clave usuario rest'); ?></th>
								<td><?= $this->Form->input('starken_clave_api_rest', array('placeholder' => 'Ingrese la clave proporcionada por starken')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_rut_empresa_emisor', 'Rut empresa emisora'); ?></th>
								<td><?= $this->Form->input('rut_empresa_emisor', array('placeholder' => 'Ingrese valor sin dv')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_rut_usuario_emisor', 'Rut usuario emisor'); ?></th>
								<td><?= $this->Form->input('starken_rut_usuario_emisor', array('placeholder' => 'Ingrese valor sin dv')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_clave_usuario_emisor', 'Clave del usuario emisor'); ?></th>
								<td><?= $this->Form->input('starken_clave_usuario_emisor', array('placeholder' => 'Ej: 1234')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_tipo_entrega', 'Tipo de entrega'); ?></th>
								<td><?= $this->Form->select('starken_tipo_entrega', $dependenciasVars['starken']['tipo_entregas'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_tipo_pago', 'Tipo de pago'); ?></th>
								<td><?= $this->Form->select('starken_tipo_pago', $dependenciasVars['starken']['tipo_pagos'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_numero_cuenta_corriente', 'Número de cta corriente'); ?></th>
								<td><?= $this->Form->input('starken_numero_cuenta_corriente', array('placeholder' => 'Ej: 11111')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_dv_numero_cuenta_corriente', 'DV de cta corriente'); ?></th>
								<td><?= $this->Form->input('starken_dv_numero_cuenta_corriente', array('placeholder' => 'Ej: 1')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_centro_costo_cuenta_corriente', 'Centro de costo de la cta corriente'); ?></th>
								<td><?= $this->Form->input('starken_centro_costo_cuenta_corriente', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ej: 0')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('starken_tipo_servicio', 'Tipo de servicio'); ?></th>
								<td><?= $this->Form->select('starken_tipo_servicio', $dependenciasVars['starken']['tipo_servicios'], array('class' => 'form-control', 'empty' => 'Normal')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('ciudad_origen', 'Ciudad de origen de la encomienda'); ?></th>
								<td><?= $this->Form->select('ciudad_origen', $dependenciasVars['starken']['comunas'], array('empty' => 'Seleccione ciudad', 'class' => 'form-control select', 'data-live-search' => true)); ?></td>
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

			<div class="panel panel-default js-panel-conexxion <?= ($this->request->data['CuentaCorrienteTransporte']['dependencia'] == 'conexxion') ? '' : 'hidden'; ?>">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Configuración de conexxion</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('conexxion_api_key', 'Key conexxion'); ?></th>
								<td><?= $this->Form->input('conexxion_api_key', array('placeholder' => 'Ingrese api key')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('conexxion_sender_full_name', 'Nombre emisor'); ?></th>
								<td><?= $this->Form->input('conexxion_sender_full_name', array('placeholder' => 'Ingrese nombre del emisor')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('conexxion_sender_rut', 'Rut empresa emisor'); ?></th>
								<td><?= $this->Form->input('conexxion_sender_rut', array('placeholder' => 'Ingrese rut del emisor sin puntos ni dv (opcional)')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('conexxion_sender_email', 'Email de emisor'); ?></th>
								<td><?= $this->Form->input('conexxion_sender_email', array('placeholder' => 'Ingrese email del emisor')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('conexxion_sender_address', 'Dirección del emisor'); ?></th>
								<td><?= $this->Form->input('conexxion_sender_address', array('placeholder' => 'Ingrese dirección del emisor (opcional)')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('ciudad_origen', 'Ciudad de origen de la encomienda'); ?></th>
								<td><?= $this->Form->select('ciudad_origen', $dependenciasVars['conexxion']['comunas'], array('empty' => 'Seleccione ciudad', 'class' => 'form-control select', 'data-live-search' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('conexxion_sender_address_number', 'Numero Dpto/ oficina emisor (opcional)'); ?></th>
								<td><?= $this->Form->input('conexxion_sender_address_number', array('placeholder' => 'Ej: 1234, of 44')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('conexxion_has_return', 'Tipo de entrega'); ?></th>
								<td><?= $this->Form->select('conexxion_has_return', $dependenciasVars['conexxion']['tipo_retornos'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('conexxion_product', 'Tipo de producto'); ?></th>
								<td><?= $this->Form->select('conexxion_product', $dependenciasVars['conexxion']['tipo_productos'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('service', 'Número de cta corriente'); ?></th>
								<td><?= $this->Form->select('service', $dependenciasVars['conexxion']['tipo_servicios'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('conexxion_notification_type', 'Tipo de notificación'); ?></th>
								<td><?= $this->Form->select('conexxion_notification_type', $dependenciasVars['conexxion']['tipo_notificaciones'], array('class' => 'form-control', 'empty' => false)); ?></td>
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

			<div class="panel panel-default js-panel-boosmap <?= ($this->request->data['CuentaCorrienteTransporte']['dependencia'] == 'boosmap') ? '' : 'hidden'; ?>">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Configuración de boosmap</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('boosmap_token', 'Token de Boosmap'); ?></th>
								<td><?= $this->Form->input('boosmap_token', array('placeholder' => 'Ingrese su token')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('boosmap_pick_up_id', 'Punto de retiro'); ?></th>
								<td><?= $this->Form->select('boosmap_pick_up_id', $dependenciasVars['boosmap']['pickup'], array('empty' => 'Seleccione pickup', 'class' => 'form-control select', 'data-live-search' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('boosmap_service', 'Típo de servicio'); ?></th>
								<td><?= $this->Form->select('boosmap_service', $dependenciasVars['boosmap']['tipo_servicios'], array('class' => 'form-control', 'empty' => false)); ?></td>
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

			<div class="panel panel-default js-panel-blueexpress ">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Configuración de BlueExpress</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('blue_express_token', 'Token de BlueExpress'); ?></th>
								<td><?= $this->Form->input('blue_express_token', array('placeholder' => 'Ingrese su token', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('blue_express_clave', 'Clave de BlueExpress'); ?></th>
								<td><?= $this->Form->input('blue_express_clave', array('placeholder' => 'Ingrese su Clave', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('blue_express_usuario', 'Usuario de BlueExpress'); ?></th>
								<td><?= $this->Form->input('blue_express_usuario', array('placeholder' => 'Ingrese su Usuario', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('blue_express_compania', 'Código Empresa de BlueExpress'); ?></th>
								<td><?= $this->Form->input('blue_express_compania', array('placeholder' => 'Ingrese su código empresa', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('blue_express_cod_usuario', 'Código Usuario en BlueExpress'); ?></th>
								<td><?= $this->Form->input('blue_express_cod_usuario', array('placeholder' => 'Ingrese su Código Usuario', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('blue_express_cta_corriente', 'Cuenta Corriente en BlueExpress'); ?></th>
								<td><?= $this->Form->input('blue_express_cta_corriente', array('placeholder' => 'Ingrese su Cuenta Corriente', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('blue_express_tipo_servicio', 'Tipo de Servicio'); ?></th>
								<td><?= $this->Form->select('blue_express_tipo_servicio', $tipo_servicio, array('empty' => 'Seleccione Servicio', 'class' => 'form-control', 'required')); ?></td>
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
		</div>
	</div> <!-- end row -->
	<?= $this->Form->end(); ?>


</div>

<?= $this->Html->script(array(
	'/backend/js/cuenta_corriente_transporte.js?v=' . rand()
)); ?>
<?= $this->fetch('script'); ?>