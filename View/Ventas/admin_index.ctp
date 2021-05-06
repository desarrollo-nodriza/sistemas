<?= $this->Html->css(array('../backend/css/jquery-ui.css'), array('inline' => false)); ?>
<?= $this->Html->script(array('../backend/js/jquery-ui.min.js'), array('inline' => false)); ?>

<style type="text/css">
	.listado-ventas td {
		vertical-align: middle !important;
	}
</style>

<div class="page-title">
	<h2><span class="fa fa-money"></span> Ventas</h2>
</div>

<div class="page-content-wrap">

	<? if (!empty($meliConexion)) : ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-plug" aria-hidden="true"></i> Conectar con Marketplaces</h3>
					</div>

					<div class="panel-body">
						<p>Debe conectar sus marketplaces para poder ver las ventas. Éste procedimiento se realiza sólo una vez.</p>
						<div class="btn-group" role="group" aria-label="Conectar">
							<? foreach ($meliConexion as $iac => $access) : ?>
								<a href="<?= $access['url']; ?>" class="btn btn-primary">Acceder a <?= $access['marketplace']; ?></a>
							<? endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<? endif; ?>

	<div class="row">

		<div class="col-xs-12">

			<?= $this->Form->create('Venta', array('url' => array('controller' => 'ventas', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
				
				<div class="panel panel-default">

					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtros de búsqueda</h3>
					</div>

					<div class="panel-body">

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<label>Venta</label>
								<input name="data[Venta][filtroventa]" class="form-control" value="<?= $FiltroVenta; ?>" id="VentaFiltro" type="text" placeholder="ID, Ref" />
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<label>Cliente</label>
								<input name="data[Venta][filtrocliente]" class="form-control" value="<?= $FiltroCliente; ?>" id="VentaCliente" type="text" placeholder="Nombre, rut, email, telefono" />
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<label>Tienda</label>
								<?= $this->Form->input('tienda_id', array('class' => 'form-control', 'empty' => 'Tienda', 'required' => false, 'default' => $FiltroTienda)); ?>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<label>Canal de venta</label>
								<?= $this->Form->input('marketplace_id', array('class' => 'form-control', 'empty' => 'Seleccione canal', 'required' => false, 'default' => $FiltroMarketplace)); ?>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<label>Estado</label>
								<?= $this->Form->input('venta_estado_categoria_id', array('class' => 'form-control', 'empty' => 'Estado', 'required' => false, 'default' => $FiltroVentaEstadoCategoria)); ?>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<label>Origen de la venta</label>
								<?= $this->Form->select('origen_venta_manual', $this->Html->origen_venta_manual(), array('class' => 'form-control', 'empty' => 'Origen', 'required' => false, 'default' => $FiltroVentaOrigen)); ?>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<br />
								<label>Medio de Pago</label>
								<?= $this->Form->input('medio_pago_id', array('class' => 'form-control', 'empty' => 'Medio de Pago', 'required' => false, 'default' => $FiltroMedioPago)); ?>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<br />
								<label>Venta urgente</label>
								<?= $this->Form->input('prioritario', array('class' => 'form-control', 'empty' => 'Seleccione', 'options' => array('1' => 'Atención urgente', '0' => 'Atencion normal'), 'required' => false, 'default' => $FiltroPrioritario)); ?>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<br />
								<label>Facturadas</label>
								<?= $this->Form->input('facturado', array('class' => 'form-control', 'empty' => 'Seleccione', 'options' => array('1' => 'Facturadas', '2' => 'Mal facturadas', '0' => 'No facturadas'), 'required' => false, 'default' => $FiltroDte)); ?>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<br />
							<label>Estado picking</label>
							<?= $this->Form->select('picking_estado', $picking, array('class' => 'form-control', 'empty' => 'Seleccione', 'required' => false, 'default' => $FiltroPicking)); ?>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<br />
								<label>Fecha (desde)</label>
								<div class="input-group" style="width: 100%; max-width: 100%;">
									<input name="data[Venta][FechaDesde]" class="form-control" value="<?= $FiltroFechaDesde; ?>" id="FechaDesde" type="text" placeholder="Fecha (desde)" />
									<span class="input-group-addon glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<br />
								<label>Fecha (hasta)</label>
								<div class="input-group" style="width: 100%; max-width: 100%;">
									<input name="data[Venta][FechaHasta]" class="form-control" value="<?= $FiltroFechaHasta; ?>" id="FechaHasta" type="text" placeholder="Fecha (hasta)" />
									<span class="input-group-addon glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>		

						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<br />
								<label>Monto Total (desde)</label>
								<input name="data[Venta][MontoDesde]" class="form-control" value="<?= $FiltroMontoDesde; ?>" id="MontoDesde" type="number" min="1" placeholder="5000, 9000" />
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<br />
								<label>Monto Total (hasta)</label>
								<input name="data[Venta][MontoHasta]" class="form-control" value="<?= $FiltroMontoHasta; ?>" id="MontoHasta" type="number" min="1" placeholder="90000, 100000" />
							</div>
						</div>						

					</div>

					<div class="panel-footer">
						<div class="col-xs-12">
							<div class="pull-left">
								<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtros', array('action' => 'index'), array('class' => 'btn btn-primary btn-block', 'escape' => false)); ?>
							</div>
							<div class="pull-right">
								<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Filtrar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-success btn-block')); ?>
							</div>
						</div>
					</div>

				</div>

			<?= $this->Form->end(); ?>

		</div>

	</div>

	<div class="row">

		<div class="col-xs-12">

			<div class="panel panel-default">

				<div class="panel-heading">

					<h3 class="panel-title">Listado de Ventas</h3>
					
					<?= $this->Form->create('Venta', array('url' => array('controller' => 'ventas', 'action' => 'facturacion_masiva'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
					<?= $this->Form->hidden('return_url', array('value' => Router::url( $this->here, true )));?>
					<div class="btn-group pull-right">
						<? if ($permisos['generate']) : ?>
						
						<?= $this->Form->button('<i class="fa fa-file" aria-hidden="true"></i> Facturar seleccionados (<span id="ventas-seleccionadas">0</span>)', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-warning btn-facturacion-masiva', 'disabled' => true)); ?>
						

						<!--<a class="btn btn-success" onclick="$('#mb-confirmar-actualizacion').css('display', 'block');"><i class="fa fa-refresh"></i> Actualizar Ventas</a> -->
						<? endif; ?>
						<a class="btn btn-primary" onclick="VentasExportarExcel();"><i class="fa fa-file-excel-o"></i> Exportar a Excel</a>

						<? if ($permisos['edit']) : ?>
						<a class="btn btn-info" data-toggle="modal" data-target="#modal-venta-manual"><i class="fa fa-money"></i> Obtener venta desde canal</a>
						<? endif; ?>
					</div>
					<?= $this->Form->end(); ?>

				</div>

				<div class="panel-body">
					
					
					<?= $this->element('contador_resultados', array('col' => false)); ?>
						

					<div class="table-responsive">

						<table class="table table-striped listado-ventas">

							<thead>
								<tr class="sort">
									<th></th>
									<th><?= $this->Paginator->sort('id', 'ID', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('fecha_venta', 'Fecha', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('total', 'Total', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th style="width: 120px"><?= $this->Paginator->sort('medio_pago_id', 'Medio de Pago', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('venta_estado_categoria_id', 'Estado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('picking_estado', 'Picking', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('marketplace_id', 'Canal de venta', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th style="width: 120px"><?= $this->Paginator->sort('cliente_id', 'Cliente', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('Dte.id', 'Dtes', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<!--<th><?= $this->Paginator->sort('atendida', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>-->
									<!--<th><?= $this->Paginator->sort('permitir_dte', 'DTE habilitado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>-->
									<!--<th><?= $this->Paginator->sort('activo', 'Activa', array('title' => 'Haz click para ordenar por este criterio')); ?></th>-->
									<th>Acciones</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ( $ventas as $ix => $venta ) : ?>

									<tr class="<?=($venta['Venta']['prioritario']) ? 'tr-prioritario' : ''; ?>">

										<td style="position: relative;">
										<? if ($venta['Venta']['prioritario']) : ?>
											<div class="prioritario-btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Prioritario">
												<i class="fa fa-exclamation" aria-hidden="true"></i>
											</div>
										<? endif; ?>
										
											<input type="checkbox" class="facturacion_masiva" name="data[Venta][<?=$ix;?>][id]" value="<?=$venta['Venta']['id'];?>" data-id="<?=$venta['Venta']['id'];?>" <?= (count(Hash::extract($venta['Dte'], '{n}[estado=dte_real_emitido].id')) > 0 || !$venta['VentaEstado']['permitir_dte']) ? 'disabled="disabled"' : '' ; ?>  > 

										</td>

										<td>
											ID: <strong><?= h($venta['Venta']['id']); ?></strong>
											<?php
												if (!empty($venta['Venta']['id_externo'])) {
													echo "<br />";
													echo "Ext: <strong>" .$venta['Venta']['id_externo']. "</strong>";
												}
												if (!empty($venta['Venta']['referencia'])) {
													echo "<br />";
													echo "Ref: <strong>" .$venta['Venta']['referencia']. "</strong>";
												}
											?>
											&nbsp;
										</td>

										<td>
											
											<?= date_format(date_create($venta['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?>
											<? if ($venta['Venta']['picking_estado'] == 'no_definido' && $venta['VentaEstado']['VentaEstadoCategoria']['venta'] && !$venta['VentaEstado']['VentaEstadoCategoria']['final'] ) : 
												
												$retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['Venta']['fecha_venta']), 'Y-m-d H:i:s'));

												if (!empty($retrasoMensaje)) : ?>
													<?=$retrasoMensaje;?>
												<?
												endif;
											  endif;?>	
										</td>

										<td><label class="label label-form label-<?=($venta['Venta']['total'] > 0) ? 'success' : '' ; ?>"><?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?>&nbsp;</label></td>

										<td><?= h($venta['MedioPago']['nombre']); ?>&nbsp;</td>

										<td>
											<a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a>&nbsp;
										</td>
										
										<td>
											<span class="btn btn-xs btn" style="color: #fff; background-color: <?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['color'];?>"><?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['label'];?></span>
										</td>

										<!--<td><?= h($venta['Tienda']['nombre']); ?>&nbsp;</td>-->

										<td>
											<? if($venta['Venta']['venta_manual']) : ?>
												<?= (!empty($venta['Venta']['marketplace_id'])) ? $venta['Marketplace']['nombre'] : 'Pos de Venta' ; ?>&nbsp;</td>
											<? else : ?>
												<?= (!empty($venta['Venta']['marketplace_id'])) ? $venta['Marketplace']['nombre'] : $venta['Tienda']['nombre'] ; ?>&nbsp;</td>
											<? endif; ?>
										<td>
											<?php

												$cliente = $venta['VentaCliente']['nombre'];

												if (!empty($venta['VentaCliente']['apellido'])) {
													$cliente.= " " .$venta['VentaCliente']['apellido'];
												}
												if (!empty($venta['VentaCliente']['rut'])) {
													$cliente.= "<br />";
													$cliente.= $venta['VentaCliente']['rut'];
												}
												if (!empty($venta['VentaCliente']['email']) && empty($venta['Venta']['marketplace_id'])) {
													$cliente.= "<br />";
													$cliente.= $venta['VentaCliente']['email'];
												}
												if (!empty($venta['VentaCliente']['telefono'])) {
													$cliente.= "<br />";
													$cliente.= $venta['VentaCliente']['telefono'];
												}

												echo $cliente;

											?>
											&nbsp;
										</td>

										<td>
											<label class="label label-form label-primary"><?= count(Hash::extract($venta['Dte'], '{n}[estado=dte_real_emitido].id')); ?></label>
										</td>

										<td> 
											
											<?= $this->Html->link('<i class="fa fa-eye"></i> Ver Detalles', array('action' => 'view', $venta['Venta']['id']), array('class' => 'btn btn-xs btn-info btn-block', 'rel' => 'tooltip', 'title' => 'Ver detalles de este registro', 'escape' => false, 'target' => '_blank')); ?>
											
											<? if ($permisos['delete'] && $venta['Venta']['total'] == 0 ) : ?>
												<?=$this->Html->link('<i class="fa fa-trash"></i> Eliminar venta', array('controller' => 'ventas', 'action' => 'eliminar', $venta['Venta']['id']), array('class' => 'btn btn-danger btn-xs btn-block', 'escape' => false) );?>
											<? endif; ?>

											<? if ($permisos['storage'] && $venta['VentaEstado']['permitir_retiro_oc']) : ?>
											
											<?= $this->Html->link('<i class="fa fa-ban"></i> Procesar', array('action' => 'procesar_ventas', $venta['Venta']['id']), array('class' => 'btn btn-xs btn-warning btn-block', 'rel' => 'tooltip', 'title' => 'Procesar este registro', 'escape' => false)); ?>

											<? endif; ?>

											<? if (!empty($venta['VentaEstado']) && $venta['VentaEstado']['notificacion_cliente']) : ?>

											<?=$this->Html->link('<i class="fa fa-send"></i> Re-enviar email', array('controller' => 'ventas', 'action' => 'enviar_email_estado', $venta['Venta']['id']), array('class' => 'btn btn-success btn-xs btn-block', 'escape' => false) );?>

											<? endif; ?>

											<? if (!$venta['Venta']['prioritario'] && $permisos['edit']) : ?>
											<?= $this->Form->postLink('<i class="fa fa-check"></i> Marcar como prioritaria', array('action' => 'marcar_prioritaria', $venta['Venta']['id']), array('class' => 'btn btn-xs btn-primary btn-block mt-5', 'rel' => 'tooltip', 'title' => 'Marcar Venta como Prioritaria', 'escape' => false));?>
											<? elseif ($permisos['edit']) : ?>

												<?= $this->Form->postLink('<i class="fa fa-remove"></i> Marcar no prioritaria', array('action' => 'marcar_no_prioritaria', $venta['Venta']['id']), array('class' => 'btn btn-xs btn-default btn-block mt-5', 'rel' => 'tooltip', 'title' => 'Marcar Venta como Prioritaria', 'escape' => false));?>
											<? endif; ?>
											<?php
												
												if ($venta['Venta']['atendida']) {
													echo $this->Form->postLink('<i class="fa fa-remove"></i> Marcar como No Atendida', array('action' => 'marcar_no_atendida', $venta['Venta']['id']), array('class' => 'btn btn-xs btn-danger btn-block mt-5', 'rel' => 'tooltip', 'title' => 'Marcar Venta como No Atendida', 'escape' => false));
												}
												else {
													#echo $this->Form->postLink('<i class="fa fa-check"></i> Marcar como Atendida', array('action' => 'marcar_atendida', $venta['Venta']['id']), array('class' => 'btn btn-xs btn-success btn-block', 'rel' => 'tooltip', 'title' => 'Marcar Venta como Atendida', 'escape' => false));
												}
											?>											

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

	<div class="row">
		<div class="col-xs-12">
			<div class="pull-right">
				<ul class="pagination">
					<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
					<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 10, 'currentClass' => 'active', 'separator' => '')); ?>
					<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
				</ul>
			</div>
		</div>
	</div>

</div>

<div id="mb-confirmar-actualizacion" class="message-box animated fadeIn" data-sound="alert">
	<div class="mb-container">
		<div class="mb-middle">
			<div class="mb-title"><i class="fa fa-refresh"></i> Actualización</div>
			<div class="mb-content">¿Seguro desea actualizar las ventas?</div>
			<div class="mb-footer">
				<div class="pull-right">
					<?= $this->Html->link('<i class="fa fa-refresh"></i> Actualizar', array('action' => 'actualizar_ventas'), array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "$('#mb-confirmar-actualizacion').css('display', 'none'); $('#mb-actualizando-ventas').css('display', 'block');")); ?>
					<a class="btn btn-danger mb-control-close" onclick="$('#mb-confirmar-actualizacion').css('display', 'none');">Cancelar</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="mb-actualizando-ventas" class="message-box animated fadeIn" data-sound="alert">
	<div class="mb-container">
		<div class="mb-middle">
			<div class="mb-title"><i class="fa fa-refresh fa-spin"></i> Actualizando</div>
			<div class="mb-content">Las Ventas se están actualizando...</div>
			<div class="mb-footer">
				<div class="pull-right">
				</div>
			</div>
		</div>
	</div>
</div>


<?= $this->element('ventas/modal-venta-manual', array('tiendas' => $tiendas, 'marketplaces' => $marketplaces))?>

<?= $this->Html->script(array(
	'/backend/js/venta.js?v=' . rand()
));?>
<?= $this->fetch('script'); ?>

<script type="text/javascript">

	$(document).ready(function() {

        $(function() {

            $("input#FechaDesde").datepicker({

                dateFormat: "dd/mm/yy",
                defaultDate: "+0w",
                maxDate: '+0w +0w',
                changeMonth: false,
                numberOfMonths: 1,
                monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"],
                prevText: "Anterior",
                nextText: "Siguiente",
                onClose: function(selectedDate) {
                    $("input#FechaHasta").datepicker("option", "minDate", selectedDate);
                }

            });

            $("input#FechaHasta").datepicker({

                dateFormat: "dd/mm/yy",
                defaultDate: "+0w",
                maxDate: '+0w +0w',
                changeMonth: false,
                numberOfMonths: 1,
                monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"],
                prevText: "Anterior",
                nextText: "Siguiente",
                onClose: function(selectedDate) {
                    $("input#FechaDesde").datepicker("option", "maxDate", selectedDate);
                }

            });

        });
	
	});

	function VentasExportarExcel () {

		var accion = $("#VentaIndexForm").attr("action");

		$("#VentaIndexForm").attr("action", accion + "/exportar");

		$("#VentaIndexForm").submit();

		$("#VentaIndexForm").attr("action", accion);

	}
	
</script>