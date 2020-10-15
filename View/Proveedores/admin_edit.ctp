<div class="page-title">
	<h2><span class="fa fa-industry"></span> Proveedores</h2>
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
								<td><?= $this->Form->input('email_contacto'); ?><p class="form-control-static text-danger"><?=__('No es utilizado para enviar la cotización');?></p></td>
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
					<h5 class="panel-title"><i class="fa fa-envelope" aria-hidden="true"></i> <?=__('Emails de destino');?></h5>
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
									<th><?= __('Email');?></th>
									<th><?= __('Tipo');?></th>
									<th><?= __('Activo');?></th>
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
								<? foreach($this->request->data['Proveedor']['meta_emails'] as $ip => $email) : ?>
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
					<h5 class="panel-title"><i class="fa fa-percent" aria-hidden="true"></i> <?=__('Descuentos por medio de pago');?></h5>
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
									<th><?= __('Medio de pago');?></th>
									<th><?= __('Descuento');?></th>
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
								<? foreach($this->request->data['Moneda'] as $ip => $descuento) : ?>
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
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-list"></i> Movimientos de saldos</h3></div>
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
									<? foreach($this->request->data['Saldo'] as $is => $saldo) : ?>
										<tr>
											<td><?=(!empty($saldo['orden_compra_id'])) ? '#' . $saldo['orden_compra_id'] : '--' ; ?></td>
											<td><?=(!empty($saldo['orden_compra_factura_id'])) ? '#' . $saldo['orden_compra_factura_id'] : '--' ; ?></td>
											<td><?=(!empty($saldo['pago_id'])) ? '#' . $saldo['pago_id'] : '--' ; ?></td>
											<td><?=CakeNumber::currency($saldo['saldo'], 'CLP'); ?></td>
										</tr>
									<? endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-xs-12 col-md-4">
							<div class="widget widget-<?=($this->request->data['Proveedor']['saldo'] < 0) ? 'danger' : 'success'; ?>">
					            <div class="widget-title">Saldo disponible</div>
					            <div class="widget-subtitle">bruto</div>
					            <div class="widget-int"><?=($this->request->data['Proveedor']['saldo'] < 0) ? '-' . CakeNumber::currency($this->request->data['Proveedor']['saldo'], 'CLP') : CakeNumber::currency($this->request->data['Proveedor']['saldo'], 'CLP');?></div>
					        </div>
						</div>
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