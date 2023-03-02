<div class="page-title">
	<h2><span class="fa fa-list"></span> Tiendas</h2>
</div>

<?= $this->Form->create('Tienda', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id'); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-cog"></i> Configuración de la tienda</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('url', 'URL'); ?></th>
								<td><?= $this->Form->input('url'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('configuracion', 'Configuracion BD'); ?></th>
								<td><?= $this->Form->input('configuracion'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('prefijo', 'Prefijo de las tablas'); ?></th>
								<td><?= $this->Form->input('prefijo'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('principal', 'Tienda principal'); ?></th>
								<td><?= $this->Form->input('principal', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tema', 'Tema de la tienda'); ?></th>
								<td><?= $this->Form->select('tema', array(
										'dark-head-light' => 'Dark Head Light',
										'dark' => 'Dark',
										'default-head-light' => 'Default Head Light',
										'default' => 'Default',
										'forest-head-light' => 'Forest Head light',
										'forest' => 'Forest',
										'light' => 'Light',
										'night-head-light' => 'Night Head Light',
										'night' => 'Night',
										'nodriza' => 'Nodriza',
										'serenity-head-light' => 'Serenity Head Light',
										'serenity' => 'Serenity'
									), array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('email_remitente', 'Email remitente'); ?></th>
								<td><?= $this->Form->input('email_remitente'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('emails_bcc', 'Email para <br> Copia oculta'); ?></th>
								<td><?= $this->Form->input('emails_bcc', array('placeholder' => 'Emails separados por coma (,)')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tiempo_despacho', 'Tiempo despacho por defecto'); ?></th>
								<td><?= $this->Form->input('tiempo_despacho', array('class' => 'is_number')); ?></td>
							</tr>
						</table>
						<div class="pull-right">
							<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
							<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-info"></i> Información del Comercio</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre_fantasia', 'Nombre Comercio'); ?></th>
								<td><?= $this->Form->input('nombre_fantasia'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('rut', 'Rut'); ?></th>
								<td><?= $this->Form->input('rut'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('direccion', 'Dirección'); ?></th>
								<td><?= $this->Form->input('direccion'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('giro', 'Giro comercial'); ?></th>
								<td><?= $this->Form->input('giro'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('detalle_cuenta', 'Información para transferencias'); ?></th>
								<td><?= $this->Form->input('detalle_cuenta'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('fono', 'Teléfono'); ?></th>
								<td><?= $this->Form->input('fono', array('class' => 'form-control js-fono')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('whatsapp_numero', 'Número whatsapp'); ?></th>
								<td><?= $this->Form->input('whatsapp_numero', array('class' => 'form-control js-fono')); ?></td>
							</tr>
							<? if (!empty($this->request->data['Tienda']['logo'])) : ?>
								<tr>
									<th><?= $this->Form->label('', 'Logo actual'); ?></th>
									<td><?= $this->Html->image($this->request->data['Tienda']['logo']['path'], array('class' => 'img-responsive', 'style' => 'max-width: 100px')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('logo', 'Actualiza (300x135)'); ?></th>
									<td><?= $this->Form->input('logo', array('class' => '', 'type' => 'file')); ?></td>
								</tr>
							<? else : ?>
								<tr>
									<th><?= $this->Form->label('logo', 'Logo (300x135)'); ?></th>
									<td><?= $this->Form->input('logo', array('class' => '', 'type' => 'file')); ?></td>
								</tr>
							<? endif; ?>
						</table>
						<div class="pull-right">
							<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
							<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">

			<h2><i class="fa fa-bug"></i> Integraciones & Extensiones</h2>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-bell"></i> Notificar retraso a cliente</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">

					<p>Activa la notificación de ventas retrasadas para los cliente de la tienda (excluye Marketplaces).</p>

					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('notificacion_retraso_venta', 'Activar notificación'); ?></th>
								<td><?= $this->Form->input('notificacion_retraso_venta', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('notificacion_retraso_venta_dias', 'Comenzar a notificar al día'); ?></td>
								<td>
									<div class="input-group">
										<?= $this->Form->input('notificacion_retraso_venta_dias'); ?>
										<span class="input-group-addon add-on">de retraso</span>
									</div>
								</td>
							</tr>
							<tr>
								<td><?= $this->Form->label('notificacion_retraso_venta_limite', 'Limite inferior de ventas'); ?></td>
								<td>
									<div class="input-group">
										<?= $this->Form->input('notificacion_retraso_venta_limite'); ?>
										<span class="input-group-addon add-on">días</span>
									</div>
								</td>
							</tr>
							<tr>
								<td><?= $this->Form->label('notificacion_retraso_venta_repetir', 'Repetir notificación cada'); ?></td>
								<td>
									<div class="input-group">
										<?= $this->Form->input('notificacion_retraso_venta_repetir'); ?>
										<span class="input-group-addon add-on">días</span>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-refresh"></i> Stock sincronizado</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">

					<p>Activa la sincronización de stock de todos los canales de venta como prestashop, linio y mercadolibre. Recuerda que debes mantener el "stock virtual" de tus productos actualizado. Así evitas que los productos se desactiven en los distintos canales. </p>

					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('stock_automatico', 'Stock sincronizado'); ?></th>
								<td><?= $this->Form->input('stock_automatico', array('class' => 'icheckbox')); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>


			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-hdd-o"></i> Almacenamiento externo</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">

					<p>Agrega el Endpoint de tu Bucket S3 para tomar las imagenes de los productos desde ahí (Solo prestashop). </p>

					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('url_almaceamiento_externo', 'S3 endpoint'); ?></th>
								<td><?= $this->Form->input('url_almaceamiento_externo'); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>


			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-shopping-bag"></i> Prestashop</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('apiurl_prestashop', 'Api Url Prestashop'); ?></th>
								<td><?= $this->Form->input('apiurl_prestashop'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('apikey_prestashop', 'Api Key Prestashop'); ?></th>
								<td><?= $this->Form->input('apikey_prestashop'); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-shopping-bag"></i> Linio <small>(No disponible en la siguente versión)</small></h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('apiurl_linio', 'Api Url Linio'); ?></th>
								<td><?= $this->Form->input('apiurl_linio'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('apiuser_linio', 'Api User Linio'); ?></th>
								<td><?= $this->Form->input('apiuser_linio'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('apikey_linio', 'Api Key Linio'); ?></th>
								<td><?= $this->Form->input('apikey_linio'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('sincronizacion_automatica_linio', 'Sincronización Automática de Linio'); ?></th>
								<td><?= $this->Form->input('sincronizacion_automatica_linio', array('class' => 'icheckbox')); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-book"></i> Libredte</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th><?= $this->Form->label('facturacion_apikey', 'Api Key Libredte'); ?></th>
								<td><?= $this->Form->input('facturacion_apikey'); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-bell"></i> Notificaciones Push</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td><?= $this->Form->label('activar_notificaciones', '¿Activar notificciones Push?'); ?></td>
								<td><?= $this->Form->input('activar_notificaciones', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('notificacion_apikey', 'Api Key Pushalert'); ?></td>
								<td><?= $this->Form->input('notificacion_apikey'); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Envíame</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive" style="overflow: unset;">
						<table class="table table-bordered">
							<tr>
								<td><?= $this->Form->label('activo_enviame', '¿Activar integración con Envíame?'); ?></td>
								<td><?= $this->Form->input('activo_enviame', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('apihost_enviame', 'Api Host Envíame'); ?></td>
								<td><?= $this->Form->input('apihost_enviame', array('placeholder' => 'Ej: https://stage.api.enviame.io/api')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('apikey_enviame', 'Api Key Envíame'); ?></td>
								<td><?= $this->Form->input('apikey_enviame'); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('company_enviame', 'ID de la empresa en Envíame'); ?></td>
								<td><?= $this->Form->input('company_enviame'); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('bodega_enviame', 'Bodega de la empresa en Envíame'); ?></td>
								<td><?= $this->Form->input('bodega_enviame'); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('peso_enviame', 'Descativar si el peso del bulto es mayor a'); ?></td>
								<td>
									<div class="input-group">
										<?= $this->Form->input('peso_enviame'); ?>
										<span class="input-group-addon add-on">KG</span>
									</div>
								</td>
							</tr>
							<tr>
								<td><?= $this->Form->label('volumen_enviame', 'Volumen máximo de un bulto'); ?></td>
								<td>
									<div class="input-group">
										<?= $this->Form->input('volumen_enviame'); ?>
										<span class="input-group-addon add-on">M3</span>
									</div>
								</td>
							</tr>
							<tr>
								<td><?= $this->Form->label('meta_ids_enviame', 'Métodos de envio usados en Envíame (multiple)'); ?></td>
								<td><?= $this->Form->select('meta_ids_enviame', $metodo_envios, array('class' => 'form-control', 'multiple' => true, 'empty' => false, 'style' => 'min-height: 150px')); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-envelope"></i> Envio de correos con Mandrill</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td><?= $this->Form->label('mandrill_apikey', 'Api Key Mandrill'); ?></td>
								<td><?= $this->Form->input('mandrill_apikey'); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list"></i> Conexión SII</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td><?= $this->Form->label('sii_rut', 'Rut Empresa'); ?></td>
								<td><?= $this->Form->input('sii_rut', array('placeholder' => 'Ingrese Rut sin puntos')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('sii_clave', 'Clave SII'); ?></td>
								<td><?= $this->Form->input('sii_clave'); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('libredte_token', 'Token obtenido desde https://api.libredte.cl/'); ?></td>
								<td><?= $this->Form->input('libredte_token'); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('sincronizar_compras', '¿Activar conexión SII?'); ?></td>
								<td><?= $this->Form->input('sincronizar_compras', array('class' => 'icheckbox')); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>


			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-truck"></i> Api Starken</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<p>Conexión para servicios web de Starken como tarificación y tracking.</p>
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td><?= $this->Form->label('starken_rut', 'Rut Empresa'); ?></td>
								<td><?= $this->Form->input('starken_rut', array('placeholder' => 'Ingrese Rut sin puntos ni dv')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('starken_clave', 'Clave starken'); ?></td>
								<td><?= $this->Form->input('starken_clave', array('placeholder' => 'Ingrese clave de api starken')); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-refresh"></i> Api Onestock</h3>
					<ul class="panel-controls">
						<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<p>Conexión para consultar stock de productos.</p>
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td><?= $this->Form->label('apiurl_onestock', 'URL de Api Onestock'); ?></td>
								<td><?= $this->Form->input('apiurl_onestock', array('placeholder' => 'Ingrese URL')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('token_onestock', 'Token de acceso'); ?></td>
								<td><?= $this->Form->input('token_onestock', array('placeholder' => 'Ingrese su token valido')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('cliente_id_onestock', 'Identificador del cliente'); ?></td>
								<td><?= $this->Form->input('cliente_id_onestock', array('placeholder' => 'Ingrese su identifacodor')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('onestock_correo', 'Correo asociado a Onestock'); ?></td>
								<td><?= $this->Form->input('onestock_correo', array('placeholder' => 'Ingrese correo')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('onestock_clave', 'Clave asociado a Onestock'); ?></td>
								<td><?= $this->Form->input('onestock_clave', array('placeholder' => 'Ingrese clave')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->label('stock_default', 'Stock por defecto cuando proveedor entrega información binaria'); ?></td>
								<td><?= $this->Form->input('stock_default', array('placeholder' => 'Ingrese un número')); ?></td>
							</tr>
						</table>
					</div>
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>