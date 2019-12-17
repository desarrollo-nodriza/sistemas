<div class="page-title">
	<h2><span class="fa fa-tags"></span> Marcas</h2>
</div>

<div class="page-content-wrap">
	<?= $this->Form->create('Marca', array('class' => 'form-horizontal js-validate-marca', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Editar Marca</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('descuento_base', 'Descuento base'); ?></th>
								<td>
									<div class="input-group" style="max-width: 100%;">
                                        <?= $this->Form->input('descuento_base', array('type' => 'text')); ?>
                                        <span class="input-group-addon">%</span>
                                    </div>
								</td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tiempo_entrega_maximo', 'Tiempo máximo de entrega'); ?></th>
								<td>
									<div class="input-group" style="max-width: 100%;">
                                        <?= $this->Form->input('tiempo_entrega_maximo', array('type' => 'text')); ?>
                                        <span class="input-group-addon">Días</span>
                                    </div>
								</td>
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
					<h5 class="panel-title"><i class="fa fa-usd" aria-hidden="true"></i> <?=__('Precios específicos');?></h5>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('El precio específico del producto sobrescribe al precio específico de la marca.'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Nombre');?></th>
									<th><?= __('Tipo');?></th>
									<th><?= __('Descuento');?></th>
									<th><?= __('Compuesto');?></th>
									<th><?= __('Infinito');?></th>
									<th><?= __('Fecha Inicio');?></th>
									<th><?= __('Fecha Final');?></th>
									<th><?= __('Activo');?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->input('PrecioEspecificoMarca.999.nombre', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-nombre-producto', 'placeholder' => 'Nombre del precio especifico')); ?>
									</td>
									<td>
										<?= $this->Form->select('PrecioEspecificoMarca.999.tipo_descuento', $tipoDescuento, array('disabled' => true, 'class' => 'form-control js-tipo-descuento', 'empty' => 'Seleccione')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoMarca.999.descuento', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 55990')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoMarca.999.descuento_compuesto', array('type' => 'checkbox', 'disabled' => true, 'class' => 'js-compuesto', 'checked' => false)); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoMarca.999.descuento_infinito', array('type' => 'checkbox', 'disabled' => true, 'class' => 'js-infinito', 'checked' => false)); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoMarca.999.fecha_inicio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-inicio', 'placeholder' => 'Ej: 2018-12-20')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoMarca.999.fecha_termino', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-final', 'placeholder' => 'Ej: 2019-12-20')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoMarca.999.activo', array('type' => 'checkbox', 'disabled' => true, 'class' => '', 'checked' => true)); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>

								<? if (!empty($this->request->data['PrecioEspecificoMarca'])) :  ?>
								<? foreach($this->request->data['PrecioEspecificoMarca'] as $ip => $precio) : ?>
								<tr>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoMarca.%d.nombre', $ip), array('type' => 'text', 'class' => 'form-control js-nombre-producto', 'placeholder' => 'Nombre del precio especifico', 'value' => $precio['nombre'])); ?>
									</td>
									<td>
										<?= $this->Form->select(sprintf('PrecioEspecificoMarca.%d.tipo_descuento', $ip), $tipoDescuento, array('class' => 'form-control js-tipo-descuento', 'empty' => 'Seleccione', 'default' => $precio['tipo_descuento'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoMarca.%d.descuento', $ip), array('type' => 'text', 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 55990', 'value' => $precio['descuento'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoMarca.%d.descuento_compuesto', $ip), array('type' => 'checkbox', 'class' => 'js-compuesto', 'checked' => $precio['descuento_compuesto'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoMarca.%d.descuento_infinito', $ip), array('type' => 'checkbox', 'class' => 'js-infinito', 'checked' => $precio['descuento_infinito'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoMarca.%d.fecha_inicio', $ip), array('type' => 'text', 'class' => 'form-control datepicker js-f-inicio', 'placeholder' => 'Ej: 2018-12-20', 'value' => $precio['fecha_inicio'], 'readonly' => $precio['descuento_infinito'] )); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoMarca.%d.fecha_termino', $ip), array('type' => 'text', 'class' => 'form-control datepicker js-f-final', 'placeholder' => 'Ej: 2019-12-20', 'value' => $precio['fecha_termino'], 'readonly' => $precio['descuento_infinito'] )); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoMarca.%d.activo', $ip), array('type' => 'checkbox', 'class' => '', 'value' => $precio['activo'])); ?>
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
	
	<!-- MESSAGE BOX-->
	<div class="message-box message-box-info animated fadeIn" data-sound="alert" id="modal_alertas">
	    <div class="mb-container">
	        <div class="mb-middle">
	            <div class="mb-title" id="modal_alertas_label"><i class="fa fa-alert"></i> ¿Actualizar Prestashop?</div>
	            <div class="mb-content">
	                <p id="mensajeModal">¿Desea actualizar la marca en prestashop?</p>
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