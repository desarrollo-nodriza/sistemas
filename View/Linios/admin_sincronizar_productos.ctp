<style type="text/css">
	.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate {padding: 10px;}
</style>

<div class="page-title">
	<h2><span class="fa fa-shopping-bag"></span> Linio</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Sincronización de Productos Prestashop - Linio</h3>
					<div class="btn-group pull-right">
						<?= $this->Html->link('<i class="fa fa-refresh"></i> Sincronizar Productos', array('action' => 'sincronizar_productos'), array('class' => 'btn btn-success', 'escape' => false)); ?>
						<a class="btn btn-info" onclick="$('#mb-confirmar-sincronizacion').css('display', 'block');"><i class="fa fa-refresh"></i> Sincronizar Productos (TODOS)</a>
						<?= $this->Html->link('<i class="fa fa-cogs"></i> Editar Config de Tienda', array('controller' => 'tiendas', 'action' => 'edit', $tienda['Tienda']['id']), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">

					<div class="clearfix"><br /></div>

					<div class="container-fluid" style="padding-left: 0; padding-right: 0;">

						<div class="row">

							<?php if (!empty($ResultadoSincronizacion)) { //si se realizó la sincronozación ?>

								<?php //-------------------------------------------------- Resultados de Sincronización -------------------------------------------------- ?>
								<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
							    	
						            <div class="form-horizontal">

							            <div class="panel panel-primary">

							                <div class="panel-heading">
							                    <h3 class="panel-title"><span class="fa fa-refresh"></span> Reporte de Sincronización</h3>
							                </div>

							                <div class="panel-body">

							                	<table class="table">

													<tr>
														<th><label>Resultado</label></th>
														<td>
															<?php if ($ResultadoSincronizacion['resultado']) { ?>
																<span class="btn btn-success">Sincronización Exitosa</span>
															<?php } else { ?>
																<span class="btn btn-danger">Error en Sincronización</span>
															<?php } ?>
														</td>
													</tr>

													<?php if ($ResultadoSincronizacion['resultado']) { ?>

														<tr>
															<th><label>Total Productos</label></th>
															<td><?= $ResultadoSincronizacion['total']; ?> producto<?php if ($ResultadoSincronizacion['total'] != 1) {echo "s";} ?></td>
														</tr>

														<tr>
															<th><label>Coincidencias</label></th>
															<td><?= $ResultadoSincronizacion['coincidencias']; ?> producto<?php if ($ResultadoSincronizacion['coincidencias'] != 1) {echo "s";} ?></td>
														</tr>

														<tr>
															<th><label>Actualizados</label></th>
															<td><?= $ResultadoSincronizacion['actualizados']; ?> producto<?php if ($ResultadoSincronizacion['actualizados'] != 1) {echo "s";} ?></td>
														</tr>

													<?php } else { ?>

														<tr>
															<th><label>Detalles</label></th>
															<td><?= $ResultadoSincronizacion['error']; ?></td>
														</tr>

													<?php } ?>

												</table>

							                </div>

							            </div>

						            </div>

								</div>

							<?php } else { //si se carga el listado de productos de linio para seleccionar los que se quieren sincronizar ?>

								<?php if ($ResultadoConsultaLinio) { //si la consulta a linio fue correcta ?>

									<div class="col-md-12 col-sm-12 col-xs-12">

										<form method="POST" action="">

											<div class="panel panel-default">

												<div class="panel-heading">
													<div class="panel-title-box">
														<h3>PRODUCTOS CARGADOS EN LINIO</h3>
														<span>Seleccione los productos que desea sincronizar</span>
													</div>
													<div class="btn-group pull-right">
														<input type="submit" class="btn btn-success esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Sincronizar" onclick="$('#mb-sincronizando-productos').css('display', 'block');" />
													</div>
												</div>

												<div class="panel-body panel-body-table">

													<div class="table-responsive">

														<table class="table table-striped datatable">

															<thead>
																<tr>
																	<th width="46px" align="center">
																		<?php /*
																		<a class="btn btn-xs btn-success btn-marcar" style="width: 28px; padding: 0; text-align: center;" onclick="ProductosMarcarTodos();"><i class="fa fa-check" style="margin: 0;"></i></a>
																		<a class="btn btn-xs btn-danger hidden btn-desmarcar" style="width: 28px; padding: 0; text-align: center;" onclick="ProductosDesmarcarTodos();"><i class="fa fa-remove" style="margin: 0;"></i></a>
																		*/ ?>
																	</th>
																	<th>REFERENCIA</th>
																	<th>SKU LINIO</th>
																	<th>NOMBRE</th>
																	<th>PRECIO</th>
																	<th>STOCK</th>
																</tr>
															</thead>

															<tbody class="lista-productos">

																<?php 

																	$i = -1; foreach ($ListaProductos as $producto) { $i++; ?>

																	<tr>

																		<td align="center">

																			<input type="checkbox" class="icheckbox" name="data[Linio][<?= $i; ?>][seleccionado]" />
																			
																			<input type="hidden" name="data[Linio][<?= $i; ?>][Product][SellerSku]" value="<?= $producto['SellerSku']; ?>" />

																			<input type="hidden" name="data[Linio][<?= $i; ?>][Product][ProductData][Model]" value="<?= $producto['ProductData']['Model']; ?>" />

																			<input type="hidden" name="data[Linio][<?= $i; ?>][Product][Name]" value="<?= $producto['Name']; ?>" />

																			<input type="hidden" name="data[Linio][<?= $i; ?>][Product][Price]" value="<?= $producto['Price']; ?>" />

																			<input type="hidden" name="data[Linio][<?= $i; ?>][Product][Quantity]" value="<?= $producto['Quantity']; ?>" />

																		</td>

																		<td><?= $producto['ProductData']['Model']; ?></td>
																		<td><?= $producto['SellerSku']; ?></td>
																		<td><?= $producto['Name']; ?></td>
																		<td>$<?= number_format(intval($producto['Price']), 0, ".", "."); ?></td>
																		<td><?= $producto['Quantity']; ?></td>
																	</tr>
																	
																<?php } ?>

															</tbody>

														</table>

													</div>

												</div>

												<div class="panel-heading">
													<div class="btn-group pull-right">
														<input type="submit" class="btn btn-success esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Sincronizar" onclick="$('#mb-sincronizando-productos').css('display', 'block');" />
													</div>
												</div>

											</div>

										</form>

									</div>

								<?php } else { //si hubo un error leyendo los productos de linio ?>

									<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
							            <div class="form-horizontal">
								            <div class="panel panel-primary">
								                <div class="panel-heading">
								                    <h3 class="panel-title"><span class="fa fa-warning"></span> Error en consulta a Linio</h3>
								                </div>
								                <div class="panel-body">
								                	<table class="table">
														<tr>
															<td>Ocurrió al intentar obtener los productos de Linio.</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
									</div>

								<?php } //fin resultado de consulta a linio ?>

							<?php } //fin carga de productos de linio para seleccionar los que se quieren sincronizar ?>

						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<div id="mb-confirmar-sincronizacion" class="message-box animated fadeIn" data-sound="alert">
	<div class="mb-container">
		<div class="mb-middle">
			<div class="mb-title"><i class="fa fa-refresh"></i> Sincronización</div>
			<div class="mb-content">¿Seguro desea sincronizar todos los productos?</div>
			<div class="mb-footer">
				<div class="pull-right">
					<?= $this->Html->link('<i class="fa fa-refresh"></i> Sincronizar', array('action' => 'sincronizar_productos_todos'), array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "$('#mb-confirmar-sincronizacion').css('display', 'none'); $('#mb-sincronizando-productos').css('display', 'block');")); ?>
					<a class="btn btn-danger mb-control-close" onclick="$('#mb-confirmar-sincronizacion').css('display', 'none');">Cancelar</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="mb-sincronizando-productos" class="message-box animated fadeIn" data-sound="alert">
	<div class="mb-container">
		<div class="mb-middle">
			<div class="mb-title"><i class="fa fa-refresh"></i> Sincronizando</div>
			<div class="mb-content">Los productos se están sincronizando...</div>
			<div class="mb-footer">
				<div class="pull-right">
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	function ProductosMarcarTodos () {

		$(".btn-marcar").addClass("hidden");
		$(".btn-desmarcar").removeClass("hidden");

		var i = 0;
		for (i = 0; i < $(".icheckbox_flat-red").length; i++) {
			if(!$(".icheckbox_flat-red").eq(i).hasClass("checked")) {
				$(".icheckbox_flat-red").eq(i).find(".iCheck-helper").click();
			}
		}

		$("input:checkbox").prop('checked', true);

	}

	function ProductosDesmarcarTodos () {
		$(".btn-desmarcar").addClass("hidden");
		$(".btn-marcar").removeClass("hidden");

		var i = 0;
		for (i = 0; i < $(".icheckbox_flat-red").length; i++) {
			if($(".icheckbox_flat-red").eq(i).hasClass("checked")) {
				$(".icheckbox_flat-red").eq(i).find(".iCheck-helper").click();
			}
		}

		$("input:checkbox").prop('checked', false);

	}

	function IniciarCheckboxes () {

		$('input.icheckbox').iCheck({
			checkboxClass	: 'icheckbox_flat-red',
			radioClass		: 'iradio_flat-red',
			increaseArea	: '20%'
		});

	}

	$(document).ready(function() {
	    
	    $(".dataTables_length select").change(function() {
			IniciarCheckboxes ();
		});

		$(".dataTables_filter label input").keyup(function() {
			IniciarCheckboxes ();
		});

		$(".paginate_button").click(function() {
			IniciarCheckboxes ();
		});

	});

</script>