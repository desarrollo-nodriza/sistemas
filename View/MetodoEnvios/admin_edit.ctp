<div class="page-title">
	<h2><span class="fa fa-truck"></span> Método de envio</h2>
</div>

<div class="page-content-wrap">

	<div class="row">
		<div class="col-xs-12 col-md-6">
			<?= $this->Form->create('MetodoEnvio', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
			<?= $this->Form->input('id'); ?>
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
								<th><?= $this->Form->label('bodega_id', 'Bodega para despachar'); ?></th>
								<td><?= $this->Form->select('bodega_id', $bodegas, array('empty' => 'Seleccione Bodega', 'class' => 'form-control', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('retiro_local', 'Retiro en local'); ?></th>
								<td><?= $this->Form->input('retiro_local', array('class' => ' js-check-retiro-local')); ?></td>
							</tr>
							<!-- <tr>
								<th><?= $this->Form->label('dependencia', 'Dependencia o Plugin'); ?></th>
								<td><?= $this->Form->select('dependencia', $dependencias, array('class' => 'form-control js-select-dependencia', 'empty' => 'Sin dependencia')); ?></td>
							</tr> -->
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => '')); ?></td>
							</tr>

							<tr>
								<th><?= $this->Form->label('permitir_reservar_stock_otra_bodega', 'Permitir reservas en otras bodegas'); ?></th>
								<td><?= $this->Form->input('permitir_reservar_stock_otra_bodega', array('class' => '')); ?></td>
							<tr>
								<th><?= $this->Form->label('embalado_venta_estado_id', 'Estado de la venta por defecto'); ?></th>
								<td><?= $this->Form->select('embalado_venta_estado_id', $estados, array('class' => 'form-control mi-selector', 'style' => "width:100%;", 'empty' => 'Sin Estado por defecto', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('embalado_venta_estado_parcial_id', 'Estado de la venta parcial por defecto'); ?></th>
								<td><?= $this->Form->select('embalado_venta_estado_parcial_id', $estados, array('class' => 'form-control mi-selector', 'style' => "width:100%;", 'empty' => 'Sin Estado por defecto', 'required')); ?></td>
							</tr>

							<tr>
								<th><?= $this->Form->label('consolidacion_venta_estado_id', 'Estado de la venta en consolidación'); ?></th>
								<td><?= $this->Form->select('consolidacion_venta_estado_id', $estados, array('class' => 'form-control mi-selector', 'style' => "width:100%;", 'empty' => 'Sin Estado por defecto', 'required')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('generar_ot', 'Activar generación de OT'); ?></th>
								<td><?= $this->Form->input('generar_ot', array('class' => '')); ?></td>
							</tr>
							<tr class="cuenta_corriente_transporte_id <?= $this->request->data['MetodoEnvio']['generar_ot'] ? '' : 'hidden' ?>">
								<th><?= $this->Form->label('cuenta_corriente_transporte_id', 'Cuenta corriente'); ?></th>
								<td><?= $this->Form->select('cuenta_corriente_transporte_id', $cuentaCorrienteTransporte, array('class' => 'form-control mi-selector', 'style' => "width:100%;", 'empty' => 'Seleccione Cuenta corriente', 'required')); ?></td>
							</tr>

							<tr>
								<th><?= $this->Form->label('peso_maximo', 'Peso Máximo'); ?></th>
								<td><?= $this->Form->input('peso_maximo'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('peso_default', 'Peso por defecto'); ?></th>
								<td><?= $this->Form->input('peso_default'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('alto_default', 'Alto por defecto'); ?></th>
								<td><?= $this->Form->input('alto_default'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('ancho_default', 'Ancho por defecto'); ?></th>
								<td><?= $this->Form->input('ancho_default'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('largo_default', 'Largo por defecto'); ?></th>
								<td><?= $this->Form->input('largo_default'); ?></td>
							</tr>

							<tr>
								<th><?= $this->Form->label('volumen_maximo', 'Volumen Máximo'); ?></th>
								<td><?= $this->Form->input('volumen_maximo'); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga start-loading-when-form-is-validate" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
			<?= $this->Form->end(); ?>
		</div>
		<div class="col-xs-12 col-md-6">
			<? if ($this->request->data['MetodoEnvio']['permitir_reservar_stock_otra_bodega']) : ?>
			<?= $this->Form->create(false, array(
				'class' => 'form-horizontal',
				'url' => array('controller' => 'metodoEnvios', 'action' => 'bodega_add', $this->request->data['MetodoEnvio']['id']),
				'id' => 'BodegaAdd'
			)); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Otras bodegas para reservar stock</h3>
					<ul class="panel-controls">
						<li><a href="#" class="clone-boton"><span class="fa fa-plus"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive" style="max-height: 600px;">
						<div class="table-responsive">
							<table id="sortable" class="table">
								<thead>
									<tr>
										<th>Bodega</th>
										<th style="text-align: center;"> Prioritario</th>
										<th>Cuenta corriente transporte</th>
										<th style="text-align: center;"> Acciones</th>
									</tr>
								</thead>
								<tbody>
									<? foreach ($this->request->data['BodegasMetodoEnvio'] as $indice => $bodega) : ?>
										<? if ($this->request->data['MetodoEnvio']['bodega_id'] == $bodega['id'])
											continue;?>
										<tr class="fila">
											<?= $this->Form->input(sprintf('%d.id', $indice), array('type' => 'text', 'label' => '', 'default' =>  $bodega['id'] ?? 1, 'class' => 'form-control hidden')); ?>
											<?= $this->Form->input(sprintf('%d.metodo_envio_id', $indice), array('type' => 'text', 'label' => '', 'default' =>  $this->request->data['MetodoEnvio']['id'], 'class' => 'form-control hidden')); ?>
											<td align="center" style="vertical-align: middle;">
												<?= $this->Form->select(sprintf('%d.bodega_id', $indice), $bodegas, array('empty' => 'Seleccione Bodega', 'class' => 'form-control', 'required', 'default' => $bodega['bodega_id'])); ?>
											</td>
											<td align="center" style="vertical-align: middle; width: 100px;">
												<?= $this->Form->checkbox(sprintf('%d.prioritaria', $indice), array('label' => '', 'default' =>  $bodega['prioritaria'])); ?>
											</td>
											<td align="center" style="vertical-align: middle;">
												<?= $this->Form->select(sprintf('%d.cuenta_corriente_transporte_id', $indice), $cuentaCorrienteTransporte, array('empty' => 'Seleccione Cuenta corriente', 'class' => 'form-control',  'default' => $bodega['cuenta_corriente_transporte_id'])); ?>
											</td>
											<td class="hidden" align="center" style="vertical-align: middle; width: 100px;">
												<?= $this->Form->input(sprintf('%d.orden', $indice), array('type' => 'text', 'label' => '',  'default' =>  $bodega['orden'] ?? 1, 'class' => 'form-control orden')); ?>
											</td>
											<td align="center" style="vertical-align: middle; width: 100px;">
												<button type="button" data-toggle="modal" data-target="#modal-eliminar-bodega-<?= $bodega['id'] ?>" class="btn btn-danger btn-block ">Eliminar</button>
											</td>
										</tr>
									<? endforeach; ?>
									<? if (count($this->request->data['BodegasMetodoEnvio']) < count($bodegas)) : ?>

										<? for ($i = (count($this->request->data['BodegasMetodoEnvio']) + 1); $i <= (count($this->request->data['BodegasMetodoEnvio']) + count($bodegas)); $i++) : ?>
											<tr class="fila hidden clone-tr">
												<?= $this->Form->input(sprintf('%d.metodo_envio_id', $i), array('type' => 'text', 'label' => '', 'default' =>  $this->request->data['MetodoEnvio']['id'], 'class' => 'form-control hidden')); ?>
												<td align="center" style="vertical-align: middle;">
													<?= $this->Form->select(sprintf('%d.bodega_id', $i), $bodegas, array('empty' => 'Seleccione Bodega', 'class' => 'form-control',)); ?>
												</td>

												<td align="center" style="vertical-align: middle; width: 100px;">
													<?= $this->Form->checkbox(sprintf('%d.prioritaria', $i), array('label' => '', 'default' => 0)); ?>
												</td>
												<td align="center" style="vertical-align: middle;">
													<?= $this->Form->select(sprintf('%d.cuenta_corriente_transporte_id', $i), $cuentaCorrienteTransporte, array('empty' => 'Seleccione Cuenta corriente', 'class' => 'form-control')); ?>
												</td>
												<td class="hidden" align="center" style="vertical-align: middle">
													<?= $this->Form->input(sprintf('%d.orden', $i), array('type' => 'text', 'label' => '', 'default' => $i, 'class' => 'form-control orden')); ?>
												</td>
												<td valign="center" align="center" style="vertical-align: middle; width: 100px;" w>
													<button type="button" class="remove_tr remove-tr btn-danger"><i class="fa fa-minus"></i></button>
												</td>
											</tr>
										<? endfor; ?>
									<? endif ?>
								</tbody>
							</table>
						</div>
					</div>

				</div>

				<div id="guardar-bodega" class="row">
					<div class="col-xs-12">
						<div class="pull-right pagination">
							<button type="submit" class="btn btn-success btn-block start-loading-when-form-is-validate ">Guardar Información</button>
						</div>
					</div>
				</div>

			</div>
			<?= $this->Form->end(); ?>
			<? endif ?>

			<div class="panel panel-default js-clone-wrapper">
			<?= $this->Form->create('MetodoEnvio', array('url' => array('action' => 'retrasos_add'), 'class' => 'form-horizontal', 'id' => 'MetodoEnvioNotificaciones', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
			<?= $this->Form->input('id'); ?>
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-bell"></i> Retraso de ventas</h3>
					<ul class="panel-controls">
						<li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered table-xl">
							<tr>
								<th>¿Activar notificación a cliente?</th>
								<td><?=$this->Form->input('notificar_retraso', array('class' => ''));?></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-body">
					
					<p>Configure las reglas para el envio de notificaciones al cliente.</p>
					<div class="table-responsive">
						<table id="tabla-reglas-notificaciones" class="table table-bordered table-xl">
							<thead>
								<th>Bodega</th>
								<th>Estado de la venta</th>
								<th>Horas <i data-container="body" data-toggle="tooltip" data-placement="top" title="Se notificará al cliente cuando la venta se mantenga en el mismo estado durante las horas que usted indique." class="fa fa-info-circle"></i></th>
								<th>Activo</th>
							</thead>
							<tbody class="">
								<?=$this->element('metodoEnvios/nuevo_metodo_envio_retrasos', array('bodegas' => $bodegas, 'venta_estado_categorias' => $venta_estado_categorias));?>
								<? foreach ($this->request->data['MetodoEnvioRetraso'] as $imer => $regla_retraso) : ?>
									<?=$this->element('metodoEnvios/editar_metodo_envio_retrasos', array('bodegas' => $bodegas, 'venta_estado_categorias' => $venta_estado_categorias, 'regla_retraso' => $regla_retraso));?>
								<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga start-loading-when-form-is-validate" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
	< </div>

		<!-- Modal Eliminar Bodega -->
		<? foreach ($this->request->data['BodegasMetodoEnvio'] as $bodega) : ?>
			<div class="modal fade" id="modal-eliminar-bodega-<?= $bodega['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-eliminar-bodega-<?= $bodega['id']; ?>-label">
				<div class="modal-dialog" role="document">

					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title text-center"> <?= "Esta seguro de eliminar la relación entre el metodo '{$this->request->data['MetodoEnvio']['nombre']} ' y la bodega '{$bodega['Bodega']['nombre']}'"  ?></h4>
						</div>

						<div class="modal-body">
							<?= $this->Form->create(false, array(
								'class' => 'form-horizontal',
								'url' => array('controller' => 'metodoEnvios', 'action' => 'bodega_delete', $this->request->data['MetodoEnvio']['id']),
								'id' => 'BodegaDelete'

							)); ?>
							<?= $this->Form->input('id', array('type' => 'text', 'label' => '', 'default' =>  $bodega['id'] ?? 1, 'class' => 'form-control hidden')); ?>
							<div>
								<div class="col-xs-12">
									<div class="btn-group pull-right">
										<button type="submit" class="btn btn-success  start-loading-then-redirect">Continuar</button>
										<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger ">Cancelar</button>
									</div>
								</div>
							</div>
							<?= $this->Form->end(); ?>
						</div>
					</div>

				</div>
			</div>
		<? endforeach; ?>
		<!-- Fin modal Eliminar Bodega -->

		<?= $this->Html->script(array(
			'/backend/js/metodo_envios.js?v=' . rand()
		)); ?>
		<?= $this->fetch('script'); ?>

		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(document).ready(function() {
					$('.mi-selector').select2();
				});
			});
		</script>