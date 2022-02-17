<div class="page-title">
	<h2><span class="fa fa-truck"></span> Método de envio</h2>
</div>
<?= $this->Form->create('MetodoEnvio', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id'); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar método de envio</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tiempo_entrega_estimado', 'Tiempo de entrega estimado'); ?></th>
								<td><?= $this->Form->input('tiempo_entrega_estimado'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('retiro_local', 'Retiro en local'); ?></th>
								<td><?= $this->Form->input('retiro_local', array('class' => 'icheckbox js-check-retiro-local')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('dependencia', 'Dependencia o Plugin'); ?></th>
								<td><?= $this->Form->select('dependencia', $dependencias, array('class' => 'form-control js-select-dependencia', 'empty' => 'Sin dependencia')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr id="generar_ot" class="">
								<th><?= $this->Form->label('generar_ot', 'Activar generación de OT'); ?></th>
								<td><?= $this->Form->input('generar_ot', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('bodega_id', 'Bodega para despachar'); ?></th>
								<td><?= $this->Form->select('bodega_id', $bodegas, array('empty' => 'Seleccione Bodega', 'class' => 'form-control','required')); ?></td>
							</tr>

							<tr id="peso_maximo" class="">
								<th><?= $this->Form->label('peso_maximo', 'Peso Máximo'); ?></th>
								<td><?= $this->Form->input('peso_maximo'); ?></td>
							</tr>
							<tr id="peso_default" class="">
								<th><?= $this->Form->label('peso_default', 'Peso por defecto'); ?></th>
								<td><?= $this->Form->input('peso_default'); ?></td>
							</tr>
							<tr id="alto_default" class="">
								<th><?= $this->Form->label('alto_default', 'Alto por defecto'); ?></th>
								<td><?= $this->Form->input('alto_default'); ?></td>
							</tr>
							<tr id="ancho_default" class="">
								<th><?= $this->Form->label('ancho_default', 'Ancho por defecto'); ?></th>
								<td><?= $this->Form->input('ancho_default'); ?></td>
							</tr>
							<tr id="largo_default" class="">
								<th><?= $this->Form->label('largo_default', 'Largo por defecto'); ?></th>
								<td><?= $this->Form->input('largo_default'); ?></td>
							</tr>
							
							<tr id="volumen_maximo" class="">
								<th><?= $this->Form->label('volumen_maximo', 'Volumen Máximo'); ?></th>
								<td><?= $this->Form->input('volumen_maximo'); ?></td>
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
			<div class="panel panel-default js-panel-starken <?= ($this->request->data['MetodoEnvio']['dependencia'] == 'starken') ? '' : 'hidden' ;?>">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Configuración de starken</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('rut_api_rest', 'Rut usuario rest'); ?></th>
								<td><?= $this->Form->input('rut_api_rest', array('placeholder' => 'Ingrese rut sin dv')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('clave_api_rest', 'Clave usuario rest'); ?></th>
								<td><?= $this->Form->input('clave_api_rest', array('placeholder' => 'Ingrese la clave proporcionada por starken')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('rut_empresa_emisor', 'Rut empresa emisora'); ?></th>
								<td><?= $this->Form->input('rut_empresa_emisor', array('placeholder' => 'Ingrese valor sin dv')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('rut_usuario_emisor', 'Rut usuario emisor'); ?></th>
								<td><?= $this->Form->input('rut_usuario_emisor', array('placeholder' => 'Ingrese valor sin dv')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('clave_usuario_emisor', 'Clave del usuario emisor'); ?></th>
								<td><?= $this->Form->input('clave_usuario_emisor', array('placeholder' => 'Ej: 1234')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tipo_entrega', 'Tipo de entrega'); ?></th>
								<td><?= $this->Form->select('tipo_entrega', $dependenciasVars['starken']['tipo_entregas'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tipo_pago', 'Tipo de pago'); ?></th>
								<td><?= $this->Form->select('tipo_pago', $dependenciasVars['starken']['tipo_pagos'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('numero_cuenta_corriente', 'Número de cta corriente'); ?></th>
								<td><?= $this->Form->input('numero_cuenta_corriente', array('placeholder' => 'Ej: 11111')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('dv_numero_cuenta_corriente', 'DV de cta corriente'); ?></th>
								<td><?= $this->Form->input('dv_numero_cuenta_corriente', array('placeholder' => 'Ej: 1')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('centro_costo_cuenta_corriente', 'Centro de costo de la cta corriente'); ?></th>
								<td><?= $this->Form->input('centro_costo_cuenta_corriente', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ej: 0')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tipo_servicio', 'Tipo de servicio'); ?></th>
								<td><?= $this->Form->select('tipo_servicio', $dependenciasVars['starken']['tipo_servicios'], array('class' => 'form-control', 'empty' => 'Normal')); ?></td>
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

			<div class="panel panel-default js-panel-conexxion <?= ($this->request->data['MetodoEnvio']['dependencia'] == 'conexxion') ? '' : 'hidden' ;?>">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Configuración de conexxion</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('api_key', 'Key conexxion'); ?></th>
								<td><?= $this->Form->input('api_key', array('placeholder' => 'Ingrese api key')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('sender_full_name', 'Nombre emisor'); ?></th>
								<td><?= $this->Form->input('sender_full_name', array('placeholder' => 'Ingrese nombre del emisor')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('sender_rut', 'Rut empresa emisor'); ?></th>
								<td><?= $this->Form->input('sender_rut', array('placeholder' => 'Ingrese rut del emisor sin puntos ni dv (opcional)')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('sender_email', 'Email de emisor'); ?></th>
								<td><?= $this->Form->input('sender_email', array('placeholder' => 'Ingrese email del emisor')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('sender_address', 'Dirección del emisor'); ?></th>
								<td><?= $this->Form->input('sender_address', array('placeholder' => 'Ingrese dirección del emisor (opcional)')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('ciudad_origen', 'Ciudad de origen de la encomienda'); ?></th>
								<td><?= $this->Form->select('ciudad_origen', $dependenciasVars['conexxion']['comunas'], array('empty' => 'Seleccione ciudad', 'class' => 'form-control select', 'data-live-search' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('sender_address_number', 'Numero Dpto/ oficina emisor (opcional)'); ?></th>
								<td><?= $this->Form->input('sender_address_number', array('placeholder' => 'Ej: 1234, of 44')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('has_return', 'Tipo de entrega'); ?></th>
								<td><?= $this->Form->select('has_return', $dependenciasVars['conexxion']['tipo_retornos'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('product', 'Tipo de producto'); ?></th>
								<td><?= $this->Form->select('product', $dependenciasVars['conexxion']['tipo_productos'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('service', 'Número de cta corriente'); ?></th>
								<td><?= $this->Form->select('service', $dependenciasVars['conexxion']['tipo_servicios'], array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('notification_type', 'Tipo de notificación'); ?></th>
								<td><?= $this->Form->select('notification_type', $dependenciasVars['conexxion']['tipo_notificaciones'], array('class' => 'form-control', 'empty' => false)); ?></td>
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

			<div class="panel panel-default js-panel-boosmap <?= ($this->request->data['MetodoEnvio']['dependencia'] == 'boosmap') ? '' : 'hidden' ;?>">
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

			<div class="panel panel-default js-panel-blueexpress <?= ($this->request->data['MetodoEnvio']['dependencia'] == 'blueexpress') ? '' : 'hidden' ;?>">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Configuración de BlueExpress</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('token_blue_express', 'Token de BlueExpress'); ?></th>
								<td><?= $this->Form->input('token_blue_express', array('placeholder' => 'Ingrese su token', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('clave_blue_express', 'Clave de BlueExpress'); ?></th>
								<td><?= $this->Form->input('clave_blue_express', array('placeholder' => 'Ingrese su Clave', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('usuario_blue_express', 'Usuario de BlueExpress'); ?></th>
								<td><?= $this->Form->input('usuario_blue_express', array('placeholder' => 'Ingrese su Usuario', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('compania_blue_express', 'Código Empresa de BlueExpress'); ?></th>
								<td><?= $this->Form->input('compania_blue_express', array('placeholder' => 'Ingrese su código empresa', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cod_usuario_blue_express', 'Código Usuario en BlueExpress'); ?></th>
								<td><?= $this->Form->input('cod_usuario_blue_express', array('placeholder' => 'Ingrese su Código Usuario', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cta_corriente_blue_express', 'Cuenta Corriente en BlueExpress'); ?></th>
								<td><?= $this->Form->input('cta_corriente_blue_express', array('placeholder' => 'Ingrese su Cuenta Corriente', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tipo_servicio_blue_express', 'Tipo de Servicio'); ?></th>
								<td><?= $this->Form->select('tipo_servicio_blue_express', $tipo_servicio, array('empty' => 'Seleccione Servicio', 'class' => 'form-control','required')); ?></td>
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
</div>
<?= $this->Form->end(); ?>

<?= $this->Html->script(array(
	'/backend/js/metodo_envios.js?v=' . rand())); ?>
<?= $this->fetch('script'); ?>