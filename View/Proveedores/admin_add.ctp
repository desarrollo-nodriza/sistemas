<div class="page-title">
	<h2><span class="fa fa-industry"></span> Proveedores</h2>
</div>

<div class="page-content-wrap">
	<?= $this->Form->create('Proveedor', array('class' => 'form-horizontal js-validate-proveedor', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Editar Proveedor</h3>
				</div>
				<div class="panel-body">
				<div class="table-responsive">
						<table class="table">
							<?= $this->Form->input('id'); ?>
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<!--<tr>
								<th><?= $this->Form->label('descuento_base', 'Descuento base'); ?></th>
								<td>
									<div class="input-group" style="max-width: 100%;">
                                        <?= $this->Form->input('descuento_base', array('type' => 'text')); ?>
                                        <span class="input-group-addon">%</span>
                                    </div>
								</td>
							</tr>-->
							<tr>
								<th><?= $this->Form->label('giro', 'Giro empresa'); ?></th>
								<td><?= $this->Form->input('giro'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('direccion', 'Dirección comercial'); ?></th>
								<td><?= $this->Form->input('direccion'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('email_contacto', 'Email de contacto'); ?></th>
								<td><?= $this->Form->input('email_contacto'); ?><p class="form-control-static text-danger"><?= __('No es utilizado para enviar la cotización'); ?></p>
								</td>
							</tr>
							<tr>
								<th><?= $this->Form->label('fono_contacto', 'Fono de contacto'); ?></th>
								<td><?= $this->Form->input('fono_contacto'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('rut_empresa', 'Rut empresa'); ?></th>
								<td><?= $this->Form->input('rut_empresa'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cuenta_bancaria', 'Cta bancaria'); ?></th>
								<td><?= $this->Form->input('cuenta_bancaria'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('codigo_banco', 'Código banco'); ?></th>
								<td><?= $this->Form->input('codigo_banco'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('nombre_encargado', 'Nombre de encargado'); ?></th>
								<td><?= $this->Form->input('nombre_encargado'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox','default'=>true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('permitir_generar_oc', 'Permitir Generar OC'); ?></th>
								<td><?= $this->Form->input('permitir_generar_oc', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('aceptar_dte', 'Aceptar facturas de compra automática'); ?></th>
								<td><?= $this->Form->input('aceptar_dte', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('margen_aceptar_dte', 'Margen para aceptar facturas de compra'); ?></th>
								<td><?= $this->Form->input('margen_aceptar_dte', array('class' => 'form-control select')); ?></td>
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
					<h5 class="panel-title"><i class="fa fa-usd" aria-hidden="true"></i> <?=__('Precios específicos Proveedor');?></h5>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('El precio específico del producto sobrescribe al precio específico del proveedor.'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Nombre');?></th>
									<th><?= __('Tipo');?></th>
									<th><?= __('Descuento');?></th>
									<th colspan="2"><?= __('Fecha y hora Inicio');?></th>
									<th colspan="2"><?= __('Fecha y hora Final');?></th>
									<th><?= __('Activo');?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->input('PrecioEspecificoProveedor.999.nombre', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-nombre-producto', 'placeholder' => 'Nombre del precio especifico')); ?>
									</td>
									<td>
										<?= $this->Form->select('PrecioEspecificoProveedor.999.tipo_descuento', $tipoDescuento, array('disabled' => true, 'class' => 'form-control js-tipo-descuento', 'empty' => 'Seleccione')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProveedor.999.descuento', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 55990')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProveedor.999.fecha_inicio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-inicio', 'placeholder' => 'Ej: 2018-12-20')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProveedor.999.hora_inicio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control timepicker24 js-h-inicio', 'placeholder' => 'Ej: 00:00:00')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProveedor.999.fecha_termino', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-final', 'placeholder' => 'Ej: 2019-12-20')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProveedor.999.hora_termino', array('type' => 'text', 'disabled' => true, 'class' => 'form-control timepicker24 js-h-final', 'placeholder' => 'Ej: 00:00:00')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProveedor.999.activo', array('type' => 'checkbox', 'disabled' => true, 'class' => '', 'checked' => true)); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<a type="button" class="btn btn-primary mb-control" data-box="#modal_alertas">Guardar cambios</a>
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- MESSAGE BOX-->
	<div class="message-box message-box-info animated fadeIn" data-sound="alert" id="modal_alertas">
	    <div class="mb-container">
	        <div class="mb-middle">
	            <div class="mb-title" id="modal_alertas_label"><i class="fa fa-alert"></i> ¿Actualizar Prestashop?</div>
	            <div class="mb-content">
	                <p id="mensajeModal">¿Desea actualizar el proveedor en prestashop?</p>
	                <div class="form-group">
						<div class="checkbox">
	                        <label>
	                            <?=$this->Form->input('actualizar_canales', array('type' => 'checkbox', 'class' => '')); ?>
	                            Sí, actualizar
	                        </label>
	                    </div>
	                </div>
	                       
	            </div>
	            <div class="mb-footer">
	                <div class="pull-right">
	                	<input type="submit" class="btn btn-primary btn-lg esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
	                	<button class="btn btn-default btn-lg mb-control-close">Cerrar</button>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	<!-- END MESSAGE BOX-->

	<?= $this->Form->end(); ?>
</div>