<div class="page-title">
	<h2><span class="fa fa-edit"></span> Editar "<?=$this->request->data['VentaDetalleProducto']['nombre']; ?>"</h2>
</div>
<?= $this->Form->create('VentaDetalleProducto', array('class' => 'form-horizontal js-validate-producto', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-7">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-info"></i> Información del producto</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive overflow-x">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<!--<tr>
								<th><?= $this->Form->label('Bodega', 'Bodega'); ?></th>
								<td><?= $this->Form->input('Bodega', array(
										'class' => 'form-control select', 
										'multiple' => 'multiple',
										'empty' => 'Seleccione Bodega')
										); ?></td>
							</tr>-->
							<tr>
								<th><?= $this->Form->label('Proveedor', 'Proveedor'); ?></th>
								<td><?= $this->Form->input('Proveedor', array(
										'class' => 'form-control', 
										'multiple' => false,
										'empty' => 'Seleccione Proveedor')
										); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('precio_costo', 'Costo'); ?></th>
								<td><?= $this->Form->input('precio_costo', array('class' => 'form-control', 'type' => 'text')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cantidad_virtual', 'Stock virtual'); ?></th>
								<td><?= $this->Form->input('cantidad_virtual', array('class' => 'form-control', 'type' => 'text')); ?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
		<div class="col-xs-12 col-md-5">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-archive" aria-hidden="true"></i> <?=__('Bodegaje');?></h5>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th><?= __('Bodega');?></th>
									<th><?= __('Cantidad');?></th>
									<th></th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->select('Bodega.999.bodega_id', $bodegas, array('disabled' => true, 'class' => 'form-control js-bodega-id', 'empty' => 'Seleccione bodega')); ?>
									</td>
									<td>
										<?= $this->Form->input('Bodega.999.cantidad', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-bodega-cantidad', 'placeholder' => 'Ej: 10, 20, 5, 100')); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end row -->
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
							<caption><?= __('Éste precio específico del producto sobrescribe al precio específico del proveedor.'); ?></caption>
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
										<?= $this->Form->input('PrecioEspecificoProducto.999.nombre', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-nombre-producto', 'placeholder' => 'Nombre del precio especifico')); ?>
									</td>
									<td>
										<?= $this->Form->select('PrecioEspecificoProducto.999.tipo_descuento', $tipoDescuento, array('disabled' => true, 'class' => 'form-control js-tipo-descuento', 'empty' => 'Seleccione')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.descuento', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 55990')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.fecha_inicio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-inicio', 'placeholder' => 'Ej: 2018-12-20')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.hora_inicio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control timepicker24 js-h-inicio', 'placeholder' => 'Ej: 00:00:00')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.fecha_termino', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-final', 'placeholder' => 'Ej: 2019-12-20')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.hora_termino', array('type' => 'text', 'disabled' => true, 'class' => 'form-control timepicker24 js-h-final', 'placeholder' => 'Ej: 00:00:00')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.activo', array('type' => 'checkbox', 'disabled' => true, 'class' => '', 'checked' => true)); ?>
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
						
						<!-- MESSAGE BOX-->
						<div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="modal_alertas">
						    <div class="mb-container">
						        <div class="mb-middle">
						            <div class="mb-title" id="modal_alertas_label"><i class="fa fa-alert"></i> ¿Actualizar canales?</div>
						            <div class="mb-content">
						                <p id="mensajeModal">¿Desea actualizar el stock en los distintos canales de ventas disponibles?</p>
										<div class="checkbox">
                                            <label>
                                                <?=$this->Form->input('actualizar_canales', array('type' => 'checkbox', 'class' => '')); ?>
                                                Sí, actualizar precios
                                            </label>
                                        </div>
						                       
						            </div>
						            <div class="mb-footer">
						                <div class="pull-right">
						                	<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						                	<button class="btn btn-default btn-lg mb-control-close">Cerrar</button>
						                </div>
						            </div>
						        </div>
						    </div>
						</div>
						<!-- END MESSAGE BOX-->


						<a type="button" class="btn btn-primary mb-control" data-box="#modal_alertas">Guardar cambios</a>
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>
