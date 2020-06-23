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
								<td><?= $this->Form->input('retiro_local', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
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
								<td><?= $this->Form->select('tipo_entrega', $tipoEntregas, array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tipo_pago', 'Tipo de pago'); ?></th>
								<td><?= $this->Form->select('tipo_pago', $tipoPagos, array('class' => 'form-control', 'empty' => false)); ?></td>
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
								<td><?= $this->Form->select('tipo_servicio', $tipoServicios, array('class' => 'form-control', 'empty' => 'Normal')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('ciudad_origen', 'Ciudad de origen de la encomienda'); ?></th>
								<td><?= $this->Form->select('ciudad_origen', $ciudadesStarken, array('empty' => 'Seleccione ciudad', 'class' => 'form-control select', 'data-live-search' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('generar_ot', 'Activar generación de OT'); ?></th>
								<td><?= $this->Form->input('generar_ot', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('peso_maximo', 'Peso por defecto del paquete (En caso de que no se logre calcular)'); ?></th>
								<td><?= $this->Form->input('peso_maximo', array('type' => 'text', 'class' => 'form-control')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('alto_default', 'Alto por defecto del paquete (En caso de que no se logre calcular)'); ?></th>
								<td><?= $this->Form->input('alto_default', array('type' => 'text', 'class' => 'form-control')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('ancho_default', 'Ancho por defecto del paquete (En caso de que no se logre calcular)'); ?></th>
								<td><?= $this->Form->input('ancho_default', array('type' => 'text', 'class' => 'form-control')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('largo_default', 'Largo por defecto del paquete (En caso de que no se logre calcular)'); ?></th>
								<td><?= $this->Form->input('largo_default', array('type' => 'text', 'class' => 'form-control')); ?></td>
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