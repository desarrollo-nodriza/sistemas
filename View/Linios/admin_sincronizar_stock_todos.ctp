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
						<a class="btn btn-info" onclick="$('#mb-confirmar-sincronizacion-stock').css('display', 'block');"><i class="fa fa-refresh"></i> Sincronizar solo stock (TODOS)</a>
						<?= $this->Html->link('<i class="fa fa-cogs"></i> Editar Config de Tienda', array('controller' => 'tiendas', 'action' => 'edit', $tienda['Tienda']['id']), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">

					<div class="clearfix"><br /></div>

					<div class="container-fluid" style="padding-left: 0; padding-right: 0;">

						<div class="row">

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


<div id="mb-confirmar-sincronizacion-stock" class="message-box animated fadeIn" data-sound="alert">
	<div class="mb-container">
		<div class="mb-middle">
			<div class="mb-title"><i class="fa fa-refresh"></i> Sincronización</div>
			<div class="mb-content">¿Seguro desea sincronizar todos los productos?</div>
			<div class="mb-footer">
				<div class="pull-right">
					<?= $this->Html->link('<i class="fa fa-refresh"></i> Sincronizar stock', array('action' => 'sincronizar_stock_todos'), array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "$('#mb-confirmar-sincronizacion-stock').css('display', 'none'); $('#mb-sincronizando-productos').css('display', 'block');")); ?>
					<a class="btn btn-danger mb-control-close" onclick="$('#mb-confirmar-sincronizacion-stock').css('display', 'none');">Cancelar</a>
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