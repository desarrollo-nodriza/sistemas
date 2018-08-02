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

							<?php //-------------------------------------------------- Configuración Prestashop -------------------------------------------------- ?>
							<div class="col-md-6 col-sm-12 col-xs-12">
						    	
					            <div class="form-horizontal">

						            <div class="panel panel-primary" style="height: 255px;">

						                <div class="panel-heading">
						                    <h3 class="panel-title"><span class="fa fa-cogs"></span> Config Actual Prestashop</h3>
						                </div>

						                <div class="panel-body">

						                	<table class="table">
												<tr>
													<th><label>Api Url</label></th>
													<td><?= $tienda['Tienda']['apiurl_prestashop']; ?></td>
												</tr>
												<tr>
													<th><label>Api Key</label></th>
													<td><?= $tienda['Tienda']['apikey_prestashop']; ?></td>
												</tr>
											</table>

						                </div>

						            </div>

					            </div>

							</div>

							<?php //-------------------------------------------------- Configuración Linio -------------------------------------------------- ?>
							<div class="col-md-6 col-sm-12 col-xs-12">
						    	
					            <div class="form-horizontal">

						            <div class="panel panel-primary">

						                <div class="panel-heading">
						                    <h3 class="panel-title"><span class="fa fa-cogs"></span> Config Actual Linio</h3>
						                </div>

						                <div class="panel-body">

						                	<table class="table">
												<tr>
													<th><label>Api Url</label></th>
													<td><?= $tienda['Tienda']['apiurl_linio']; ?></td>
												</tr>
												<tr>
													<th><label>Api User</label></th>
													<td><?= $tienda['Tienda']['apiuser_linio']; ?></td>
												</tr>
												<tr>
													<th><label>Api Key</label></th>
													<td><?= $tienda['Tienda']['apikey_linio']; ?></td>
												</tr>
												<tr>
													<th><label>Sincronización Automática</label></th>
													<td><span class="btn btn-xs btn-<?php if (!empty($tienda['Tienda']['sincronizacion_automatica_linio'])) {echo "success";} else {echo "danger";} ?>"><?php if (!empty($tienda['Tienda']['sincronizacion_automatica_linio'])) {echo "Activa";} else {echo "Inactiva";} ?></span></td>
												</tr>
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