<div class="panel-heading">
	<div class="page-title">
		<h2><span class="fa fa-industry"></span> Proveedores</h2>
		<? if ($this->request->data['Proveedor']['permitir_generar_oc']) :  ?>
			<div class="pull-right">
				<?= $this->Html->link('Crear OC automaticas', array('action' => 'crearOcsAutomaticas', $this->request->data['Proveedor']['id']), array('class' => 'btn btn-success start-loading-then-redirect')); ?>
			</div>
		<? endif; ?>
	</div>


</div>

<div class="page-content-wrap">
	<?= $this->Form->create('Proveedor', array('class' => 'form-horizontal js-validate-proveedor', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-edit" aria-hidden="true"></i> Editar Proveedor</h3>
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
							<tr >
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
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
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
							<tr>
								<th><?= $this->Form->label('oc_via_api', 'Capturar OC vía API'); ?></th>
								<td><?= $this->Form->input('oc_via_api', array('class' => 'icheckbox')); ?></td>
							</tr>
							<!-- <tr>
								<th><?= $this->Form->label('maximo_de_item_por_oc', 'Máximo de item por orden de compra'); ?></th>
								<td><?= $this->Form->input('maximo_de_item_por_oc'); ?><p class="form-control-static text-danger"><?= __('0 indica que no tiene hay limite'); ?></p></td>
							</tr> -->
							<? if ($this->request->data['Proveedor']['permitir_generar_oc']) :  ?>
								<tr>
									<th>Seleccione días permitidos para generar OCs</th>
									<td>
										<div class="table-responsive">
											<table class="table table-borderless align-middle">
												<thead>

													<th style="max-width: 1px;">Domingo</th>
													<th style="max-width: 1px;">Lunes</th>
													<th style="max-width: 1px;">Martes</th>
													<th style="max-width: 1px;">Miercoles</th>
													<th style="max-width: 1px;">Jueves</th>
													<th style="max-width: 1px;">Viernes</th>
													<th style="max-width: 1px;">Sábado</th>
												</thead>
												<tbody>
													<tr>
														<td>
															<?= $this->Form->input('sunday', array('class' => 'form-control icheckbox')); ?>
														</td>
														<td>
															<?= $this->Form->input('monday', array('class' => 'form-control icheckbox')); ?>
														</td>
														<td>
															<?= $this->Form->input('tuesday', array('class' => 'form-control icheckbox')); ?>
														</td>
														<td>
															<?= $this->Form->input('wednesday', array('class' => 'form-control icheckbox')); ?>
														</td>
														<td>
															<?= $this->Form->input('thursday', array('class' => 'form-control icheckbox')); ?>
														</td>
														<td>
															<?= $this->Form->input('friday', array('class' => 'form-control icheckbox')); ?>
														</td>
														<td>
															<?= $this->Form->input('saturday', array('class' => 'form-control icheckbox')); ?>
														</td>
													</tr>
												</tbody>
											</table>
										</div>

									</td>

								</tr>
							<? endif; ?>
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

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-envelope" aria-hidden="true"></i> <?= __('Emails de destino'); ?></h5>
					<ul class="panel-controls">
						<li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('Indique el/la/los destinatarios que resivirán las OC.'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Email'); ?></th>
									<th><?= __('Tipo'); ?></th>
									<th><?= __('Activo'); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->input('ProveedoresEmail.999.email', array('type' => 'text', 'disabled' => true, 'class' => 'form-control is-email not-blank', 'placeholder' => 'Ej: pp@email.cl')); ?>
									</td>
									<td>
										<?= $this->Form->select('ProveedoresEmail.999.tipo', $tipo_email, array('disabled' => true, 'class' => 'form-control not-blank', 'default' => 1, 'empty' => false)); ?>
									</td>
									<td>
										<?= $this->Form->input('ProveedoresEmail.999.activo', array('type' => 'checkbox', 'disabled' => true, 'class' => '', 'value' => 1)); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>

								<? if (!empty($this->request->data['Proveedor']['meta_emails'])) :  ?>
									<? foreach ($this->request->data['Proveedor']['meta_emails'] as $ip => $email) : ?>
										<tr>
											<td>
												<?= $this->Form->input(sprintf('ProveedoresEmail.%d.email', $ip), array('type' => 'text', 'class' => 'form-control is-email not-blank', 'placeholder' => 'Ej: pp@email.cl', 'value' => $email['email'])); ?>
											</td>
											<td>
												<?= $this->Form->select(sprintf('ProveedoresEmail.%d.tipo', $ip), $tipo_email, array('class' => 'form-control not-blank', 'default' => $email['tipo'], 'empty' => false)); ?>
											</td>
											<td>
												<?= $this->Form->input(sprintf('ProveedoresEmail.%d.activo', $ip), array('type' => 'checkbox', 'class' => '', 'checked' => $email['activo'])); ?>
											</td>
											<td valign="center">
												<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
											</td>
										</tr>
									<? endforeach; ?>
								<? endif; ?>

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


	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-percent" aria-hidden="true"></i> <?= __('Descuentos por medio de pago'); ?></h5>
					<ul class="panel-controls">
						<li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('Descuento en % que se aplica al total neto de la Orden de compra de éste proveedor.'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Medio de pago'); ?></th>
									<th><?= __('Descuento'); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->select('Moneda.999.moneda_id', $monedas, array('type' => 'text', 'disabled' => true, 'class' => 'form-control not-blank', 'empty' => 'seleccione')); ?>
									</td>
									<td>
										<?= $this->Form->input('Moneda.999.descuento', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 5')); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>

								<? if (!empty($this->request->data['Moneda'])) :  ?>
									<? foreach ($this->request->data['Moneda'] as $ip => $descuento) : ?>
										<tr>
											<td>
												<?= $this->Form->select(sprintf('Moneda.%d.moneda_id', $ip), $monedas, array('type' => 'text', 'class' => 'form-control not-blank', 'default' => $descuento['MonedasProveedor']['moneda_id'])); ?>
											</td>
											<td>
												<?= $this->Form->input(sprintf('Moneda.%d.descuento', $ip), array('type' => 'text', 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 55990', 'value' => $descuento['MonedasProveedor']['descuento'])); ?>
											</td>
											<td valign="center">
												<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
											</td>
										</tr>
									<? endforeach; ?>
								<? endif; ?>

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


	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list"></i> Movimientos de saldos</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-md-8">
							<div class="table-responsive">
								<table class="table table-bordered datatable">
									<caption>Valores entre paréntesis () son negativos</caption>
									<thead>
										<tr>
											<th>OC relacionada</th>
											<th>Factura relacionada</th>
											<th>Pago relacionada</th>
											<th>Monto</th>
										</tr>
									</thead>
									<tbody>
										<? foreach ($this->request->data['Saldo'] as $is => $saldo) : ?>
											<tr>
												<td><?= (!empty($saldo['orden_compra_id'])) ? '#' . $saldo['orden_compra_id'] : '--'; ?></td>
												<td><?= (!empty($saldo['orden_compra_factura_id'])) ? '#' . $saldo['orden_compra_factura_id'] : '--'; ?></td>
												<td><?= (!empty($saldo['pago_id'])) ? '#' . $saldo['pago_id'] : '--'; ?></td>
												<td><?= CakeNumber::currency($saldo['saldo'], 'CLP'); ?></td>
											</tr>
										<? endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-xs-12 col-md-4">
							<div class="widget widget-<?= ($this->request->data['Proveedor']['saldo'] < 0) ? 'danger' : 'success'; ?>">
								<div class="widget-title">Saldo disponible</div>
								<div class="widget-subtitle">bruto</div>
								<div class="widget-int"><?= ($this->request->data['Proveedor']['saldo'] < 0) ? '-' . CakeNumber::currency($this->request->data['Proveedor']['saldo'], 'CLP') : CakeNumber::currency($this->request->data['Proveedor']['saldo'], 'CLP'); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<? if ($this->request->data['Proveedor']['permitir_generar_oc']) :  ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h5 class="panel-title"><i class="fa fa-filter" aria-hidden="true"></i> <?= __('Configuración tipo entrega'); ?></h5>
						<div class="btn-group pull-right">
							<? if ($permisos['add']) : ?>
								<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo configuración', array('action' => '#'), array('class' => 'btn btn-success clone-configuracion-boton', 'escape' => false)); ?>
								<a type="button" class="btn btn-primary mb-control" data-box="#modal_alertas">Guardar cambios</a>
							<? endif; ?>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>
											Bodega
										</th>
										<th>
											Tienda
										</th>
										<th>
											Tipo de entrega
										</th>
										<th>
											Encargado del retiro
										</th>
										<th>
											Detalle del retiro (opcional)
										</th>
										<th>
											Acciones
										</th>
									</tr>
								</thead>
								<tbody>
									<? for ($i =  count($this->request->data['TipoEntregaProveedorOC']); $i < 10; $i++) : ?>
										<tr class="fila hidden clone-configuracion-tr">
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('TipoEntregaProveedorOC.%d.bodega_id', $i),
													$bodegas,
													array(
														'label' 	=> '',
														'class' 	=> 'form-control w-100',
														'required'
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('TipoEntregaProveedorOC.%d.tienda_id', $i),
													$tiendas,
													array(
														'label' 	=> '',
														'class' 	=> 'form-control w-100',
														'required'
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('TipoEntregaProveedorOC.%d.tipo_entrega', $i),
													$tipo_entrega,
													array(
														'label' 	=> '',
														'class' 	=> 'form-control w-100 js-tipo-entrega',
														'required'
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->input(
													sprintf('TipoEntregaProveedorOC.%d.receptor_informado', $i),
													array(
														'type' 		=> 'text',
														'label' 	=> '',
														'class' 	=> 'form-control w-100 js-receptor-informado',
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->input(
													sprintf('TipoEntregaProveedorOC.%d.informacion_entrega', $i),
													array(
														'type' 		=> 'text',
														'label' 	=> '',
														'class' 	=> 'form-control w-100 js-informacion-entrega',
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->input(sprintf('TipoEntregaProveedorOC.%d.id', $i), array('type' => 'text', 'label' => '', 'default' => "", 'class' => 'form-control hidden ')); ?>
												<button type="button" class="remove_tr remove-frecuencia-tr btn-danger"><i class="fa fa-minus"></i></button>
											</td>
										</tr>
									<? endfor; ?>

									<?php foreach ($this->request->data['TipoEntregaProveedorOC'] as $indice => $tipoEntrega) : ?>
										<tr id="remove-configuracion-tr-<?= $tipoEntrega['id'] ?>">
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('TipoEntregaProveedorOC.%d.bodega_id', $indice),
													$bodegas,
													array(
														'label' 	=> '',
														'default' 	=> $tipoEntrega['bodega_id'],
														'class' 	=> 'form-control w-100',
														'required'
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('TipoEntregaProveedorOC.%d.tienda_id', $indice),
													$tiendas,
													array(
														'label' 	=> '',
														'default' 	=> $tipoEntrega['tienda_id'],
														'class' 	=> 'form-control w-100',
														'required'
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('TipoEntregaProveedorOC.%d.tipo_entrega', $indice),
													$tipo_entrega,
													array(
														'label' 	=> '',
														'default' 	=> $tipoEntrega['tipo_entrega'],
														'class' 	=> 'form-control w-100 js-tipo-entrega',
														'required'
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->input(
													sprintf('TipoEntregaProveedorOC.%d.receptor_informado', $indice),
													array(
														'type' 		=> 'text',
														'label' 	=> '',
														'default' 	=> $tipoEntrega['receptor_informado'],
														'class' 	=> 'form-control w-100 js-receptor-informado',
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->input(
													sprintf('TipoEntregaProveedorOC.%d.informacion_entrega', $indice),
													array(
														'type' 		=> 'text',
														'label' 	=> '',
														'default' 	=> $tipoEntrega['informacion_entrega'],
														'class' 	=> 'form-control w-100 js-informacion-entrega',
													)
												); ?>
											</td>

											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->input(
													sprintf('TipoEntregaProveedorOC.%d.id', $indice),
													array(
														'readonly',
														'type' 		=> 'text',
														'label' 	=> '',
														'default' 	=> $tipoEntrega['id'],
														'class' 	=> 'form-control hidden',

													)
												); ?>
												<button type="button" onclick="eliminar_configuracion(<?= $tipoEntrega['id'] ?>,'<?= CakeSession::read('Auth.Administrador.token.token') ?>');" class="btn btn-danger start-loading-then-redirect"> Eliminar</button>
											</td>
										</tr>

									<?php endforeach; ?>
								</tbody>
							</table>
						</div>

					</div>

				</div>
			</div>
		</div>
	<? endif; ?>

	<? if ($this->request->data['Proveedor']['permitir_generar_oc']) :  ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h5 class="panel-title"><i class="fa fa-filter" aria-hidden="true"></i> <?= __('Frecuencias para generar Ordenes de compra'); ?></h5>
						<div class="btn-group pull-right">
							<? if ($permisos['add']) : ?>
								<? if (count($this->request->data['FrecuenciaGenerarOC']) < 3) :  ?>
									<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo frecuencia', array('action' => '#'), array('class' => 'btn btn-success clone-frecuencia-boton', 'escape' => false)); ?>
								<? endif; ?>
								<a type="button" class="btn btn-primary mb-control" data-box="#modal_alertas">Guardar cambios</a>
							<? endif; ?>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th style="width: 700px;">
											Hora de ejecucion
										</th>
										<th style="text-align: center;">Acciones</th>
									</tr>
								</thead>
								<tbody>
									<? if (count($this->request->data['FrecuenciaGenerarOC']) < 3) :  ?>
										<? for ($i =  count($this->request->data['FrecuenciaGenerarOC']); $i < 3; $i++) : ?>
											<tr class="fila hidden clone-frecuencia-tr">
												<td align="center" style="vertical-align: bottom;">
													<?= $this->Form->select(
														sprintf('FrecuenciaGenerarOC.%d.hora', $i),
														$horas,
														array(
															'label' => '',
															'class' => 'form-control mi-selector',
															'style' => "width: 700px",
															'required'
														)
													); ?>
												</td>
												<td align="center" style="vertical-align: bottom;">
													<?= $this->Form->input(sprintf('FrecuenciaGenerarOC.%d.id', $i), array('type' => 'text', 'label' => '', 'default' => "", 'class' => 'form-control hidden ')); ?>
													<button type="button" class="remove_tr remove-frecuencia-tr btn-danger"><i class="fa fa-minus"></i></button>
												</td>
											</tr>
										<? endfor; ?>
									<? endif; ?>

									<?php foreach ($this->request->data['FrecuenciaGenerarOC'] as $indice => $frecuencia) : ?>
										<tr id="remove-frecuencia-tr-<?= $frecuencia['id'] ?>">

											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('FrecuenciaGenerarOC.%d.hora', $indice),
													$horas,
													array(
														'type' 		=> 'text',
														'label' 	=> '',
														'default' 	=> $frecuencia['hora'],
														'class' 	=> 'form-control mi-selector',
														'required',
														'style' => "width: 700px",
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->input(
													sprintf('FrecuenciaGenerarOC.%d.id', $indice),
													array(
														'readonly',
														'type' 		=> 'text',
														'label' 	=> '',
														'default' 	=> $frecuencia['id'],
														'class' 	=> 'form-control hidden',

													)
												); ?>

												<button type="button" onclick="eliminar_frecuencia(<?= $frecuencia['id'] ?>,'<?= CakeSession::read('Auth.Administrador.token.token') ?>');" class="btn btn-danger start-loading-then-redirect"> Eliminar</button>
											</td>
										</tr>

									<?php endforeach; ?>
								</tbody>
							</table>
						</div>

					</div>

				</div>
			</div>
		</div>
	<? endif; ?>


	<? if ($this->request->data['Proveedor']['permitir_generar_oc']) :  ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h5 class="panel-title"><i class="fa fa-filter" aria-hidden="true"></i> <?= __('Reglas para generar Ordenes de compras'); ?></h5>
						<div class="btn-group pull-right">
							<? if ($permisos['add']) : ?>
								<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Regla', array('action' => '#'), array('class' => 'btn btn-success clone-boton', 'escape' => false)); ?>
								<a type="button" class="btn btn-primary mb-control" data-box="#modal_alertas">Guardar cambios</a>
							<? endif; ?>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th style="width: 700px;">
											Regla
										</th>
										<th style="text-align: center;">Acciones</th>
									</tr>
								</thead>
								<tbody>

									<? for ($i =  count($this->request->data['ReglasGenerarOC']); $i < count($reglasGenerarOC_2); $i++) : ?>
										<tr class="fila hidden clone-regla-tr">
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('ReglasGenerarOC.%d.regla_generar_oc_id', $i),
													$reglasGenerarOC,
													array(
														'type' 	=> 'text',
														'label' => '',
														'class' => 'form-control mi-selector',
														'style' => "width: 700px",
														'required'
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<button type="button" class="remove_tr remove-tr btn-danger"><i class="fa fa-minus"></i></button>
											</td>
										</tr>
									<? endfor; ?>

									<?php foreach ($this->request->data['ReglasGenerarOC'] as $indice => $regla) : ?>
										<tr id="remove-tr-<?= $regla['ReglasProveedor']['id'] ?>">
											<td align="center" style="vertical-align: bottom;">
												<?= $this->Form->select(
													sprintf('ReglasGenerarOC.%d.regla_generar_oc_id', $indice),
													$reglasGenerarOC_2,
													array(
														'label' 	=> '',
														'default' 	=> $regla['ReglasProveedor']['regla_generar_oc_id'],
														'class' 	=> 'mi-selector form-control',
														'style' 	=> "width: 700px",
														'required'
													)
												); ?>
											</td>
											<td align="center" style="vertical-align: bottom;">
												<button onclick="eliminar_regla(<?= $regla['ReglasProveedor']['id'] ?>,'<?= CakeSession::read('Auth.Administrador.token.token') ?>');" type="button" class="btn btn-danger start-loading-then-redirect">Eliminar</i></button>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>

					</div>

				</div>
			</div>
		</div>
	<? endif; ?>

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
								<?= $this->Form->input('actualizar_canales', array('type' => 'checkbox', 'class' => '')); ?>
								Sí, actualizar
								</li>
						</div>
					</div>

				</div>
				<div class="mb-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary btn-lg esperar-carga start-loading-when-form-is-validate" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<button class="btn btn-default btn-lg mb-control-close">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END MESSAGE BOX-->
	<?= $this->Form->end(); ?>







</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).on('click', '.clone-boton', function(e) {
		$.app.formularios.bind('#ReglaCreate');
		e.preventDefault();

		let clone_tr = document.getElementsByClassName("clone-regla-tr");

		if (clone_tr.length > 0) {
			let elementoremoveClass = clone_tr.item(0);
			elementoremoveClass.removeAttribute('class')
			const classes_2 = elementoremoveClass.classList
			classes_2.add("nuevo_elemento");
			classes_2.add("fila");
		}
	});
	$(document).on('click', '.remove-tr', function(e) {
		$.app.formularios.bind('#ReglaCreate');
		e.preventDefault();
		var $th = $(this).parents('tr').eq(0);

		$th.fadeOut('slow', function() {
			$th.remove();
			ordenar();
		});
	});

	$(document).on('click', '.clone-frecuencia-boton', function(e) {
		$.app.formularios.bind('#TiendaAdminEditForm');
		e.preventDefault();

		let clone_tr = document.getElementsByClassName("clone-frecuencia-tr");

		if (clone_tr.length > 0) {
			let elementoremoveClass = clone_tr.item(0);
			elementoremoveClass.removeAttribute('class')
			const classes_2 = elementoremoveClass.classList
			classes_2.add("nuevo_elemento");
			classes_2.add("fila");
		}
	});

	$(document).on('click', '.remove-frecuencia-tr', function(e) {
		$.app.formularios.bind('#TiendaAdminEditForm');
		e.preventDefault();
		var $th = $(this).parents('tr').eq(0);

		$th.fadeOut('slow', function() {
			$th.remove();
			ordenar();
		});
	});


	$(document).on('click', '.clone-configuracion-boton', function(e) {
		$.app.formularios.bind('#TiendaAdminEditForm');
		e.preventDefault();

		let clone_tr = document.getElementsByClassName("clone-configuracion-tr");

		if (clone_tr.length > 0) {
			let elementoremoveClass = clone_tr.item(0);
			elementoremoveClass.removeAttribute('class')
			const classes_2 = elementoremoveClass.classList
			classes_2.add("nuevo_elemento");
			classes_2.add("fila");
		}
	});

	$(document).on('click', '.remove-configuracion-tr', function(e) {
		$.app.formularios.bind('#TiendaAdminEditForm');
		e.preventDefault();
		var $th = $(this).parents('tr').eq(0);

		$th.fadeOut('slow', function() {
			$th.remove();
			ordenar();
		});
	});



	jQuery(document).ready(function($) {
		$(document).ready(function() {
			$('.mi-selector').select2();
		});
	});

	function eliminar_frecuencia(id, token) {
		$.app.formularios.bind('#TiendaAdminEditForm');
		var requestOptions = {
			method: 'GET',
			redirect: 'follow'
		};
		const {
			origin
		} = window.location;

		$respuesta = fetch(`${origin}/api/proveedor/eliminar-frecuencia/${id}.json?token=${token}`, requestOptions)
			.then(respuesta => {

				noty({
					text: respuesta.status == 200 ? 'Eliminado con Exito' : "Error al eliminar",
					layout: 'topRight',
					type: respuesta.status == 200 ? 'warning' : 'error'
				})
				if (respuesta.status == 200) {
					console.log(`remove-frecuencia-tr-${id}`)
					document.getElementById(`remove-frecuencia-tr-${id}`).remove();
				}
				$.app.loader.ocultar();
			})
			.catch(error => {
				console.log('error', error)
				$.app.loader.ocultar();
			});
	}

	function eliminar_configuracion(id, token) {
		$.app.formularios.bind('#TiendaAdminEditForm');
		var requestOptions = {
			method: 'GET',
			redirect: 'follow'
		};
		const {
			origin
		} = window.location;

		$respuesta = fetch(`${origin}/api/proveedor/eliminar-configuracion/${id}.json?token=${token}`, requestOptions)
			.then(respuesta => {

				noty({
					text: respuesta.status == 200 ? 'Eliminado con Exito' : "Error al eliminar",
					layout: 'topRight',
					type: respuesta.status == 200 ? 'warning' : 'error'
				})
				if (respuesta.status == 200) {
					console.log(`remove-configuracion-tr-${id}`)
					document.getElementById(`remove-configuracion-tr-${id}`).remove();
				}
				$.app.loader.ocultar();
			})
			.catch(error => {
				console.log('error', error)
				$.app.loader.ocultar();
			});
	}

	function eliminar_regla(id, token) {

		$.app.formularios.bind('#ReglaCreate');
		var requestOptions = {
			method: 'GET',
			redirect: 'follow'
		};
		const {
			origin
		} = window.location;

		$respuesta = fetch(`${origin}/api/proveedor/eliminar-regla/${id}.json?token=${token}`, requestOptions)
			.then(respuesta => {

				noty({
					text: respuesta.status == 200 ? 'Eliminado con Exito' : "Error al regla",
					layout: 'topRight',
					type: respuesta.status == 200 ? 'warning' : 'error'
				})

				if (respuesta.status == 200) {
					console.log(`remove-tr-${id}`)
					document.getElementById(`remove-tr-${id}`).remove();
				}
				$.app.loader.ocultar();
			})
			.catch(error => {
				console.log('error', error)
				$.app.loader.ocultar();
			});
	}

	$(document).on('change', '.js-tipo-entrega', function() {

		$.app.formularios.bind('#TiendaAdminEditForm');

		var $val = $(this).val(),
			$contexto = $(this).parents('tr').eq(0),
			$receptor = $contexto.find('.js-receptor-informado'),
			$detalleReceptor = $contexto.find('.js-informacion-entrega');

		if ($val === 'retiro') {

			$receptor.rules("add", {
				required: true,
				messages: {
					required: 'Campo requerido'
				}
			});
		} else {
			$receptor.val("");
			$detalleReceptor.val("");
			$receptor.rules('remove', 'required');
		}

	});
</script>