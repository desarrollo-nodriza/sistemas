<div class="page-title">
	<h2><span class="fa fa-shopping-basket"></span> Mercado Libre Productos <small><?= $publicado = (!empty($this->request->data['MercadoLibr']['id_meli'])) ? 'Publicado' : 'No publicado' ;?></small></h2>
	<div class="pull-right">
		<? if (!empty($url) && ! $this->Session->check('Meli.access_token')) : ?>
			<div class="btn-group">
	            <a href="#" data-toggle="dropdown" class="btn btn-warning dropdown-toggle" aria-expanded="false">Aplicación Desconectada <span class="caret"></span></a>
	            <ul class="dropdown-menu pull-right" role="menu">
	                <li><?= $this->Html->link('Conectar aplicación', $url, array('escape' => false)); ?></li>
	            </ul>
	        </div>
		<? else : ?>
			<div class="btn-group">
	            <a href="#" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">Aplicación Conectada <span class="caret"></span></a>
	            <ul class="dropdown-menu pull-right" role="menu">
	                <li><?= $this->Html->link('Ver mi cuenta', array('action' => 'usuario'), array('escape' => false)); ?></li>
	                <li><?= $this->Html->link('Desconectar aplicación', array('action' => 'desconectar'), array('escape' => false)); ?></li>                                                    
	            </ul>
	        </div>
		<? endif; ?>
	</div>
</div>
<?= $this->Form->create('MercadoLibr', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id'); ?>
<?= $this->Form->input('tienda_id', array('type' => 'hidden', 'value' => $this->Session->read('Tienda.id'))); ?>
<?= $this->Form->input('id_product', array('type' => 'hidden', 'class' => 'id-product')); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar Mercado Libre producto</h3>
				</div>
				<div class="panel-body">
					<div class="col-xs-12">
						<h4>Categoría Mercadolibre</h4>
					</div>
					<div class="col-xs-12 col-sm-3 js-base">
						<?=$this->Form->select('categoria_00', $categoriasRoot, array('empty' => 'Seleccione categoria raiz', 'class' => 'form-control js-cat', 'id' => 'BaseCat', 'required' => true));?>
						<span class="help-block"></span>
					</div>
					<? if (!empty($categoriasHojas)) : ?>
					<? foreach($categoriasHojas as $index => $categoriasHoja) : ?>
						<div class="col-xs-12 col-sm-3">
							<?=$this->Form->select('categoria_0' . $index, $categoriasHoja, array('empty' => 'Seleccione categoria', 'class' => 'form-control js-cat', 'required' => true));?>
							<? if (end($categoriasHojas) == $categoriasHojas[$index]) : ?>
							<span class="help-block"><i class="fa fa-check text-success"></i> Categoría final</span>
							<? endif; ?>
						</div>
					<? endforeach; ?>
					<? endif; ?>
					<?= $this->Form->input('categoria_hoja', array('type' => 'hidden', 'id' => 'categoria_hoja')); ?>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('mercado_libre_plantilla_id', 'Mercado libre plantilla'); ?></th>
								<td><?= $this->Form->select('mercado_libre_plantilla_id', $plantillas, array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<? if (!empty($producto)) : ?>
								<tr>
									<th><label>Producto Actual</label></th>
									<td>
										<div class="input-group js-toggle-wrapper">
                                            <div class="form-control toggle-text"><?=$producto['Productotienda']['reference'] . ' - ' . $this->request->data['MercadoLibr']['producto'];?></div>
											<?= $this->Form->input('producto', array('type' => 'hidden', 'class' => 'form-control toggle-input input-productos-buscar-meli')); ?>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default toggle-button" type="button"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i> Actualizar</button>
                                            </span>
                                        </div>
									</td>
								</tr>
							<? else : ?>
								<tr>
									<th><label>Producto</label></th>
									<td><?= $this->Form->input('producto', array('type' => 'text', 'class' => 'form-control input-productos-buscar-meli')); ?></td>
								</tr>
							<? endif; ?>
							<tr>
								<th><?= $this->Form->label('precio', 'Precio en Mercado libre'); ?></th>
								<td><?= $this->Form->input('precio', array('class' => 'form-control js-precio')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('imagen_meli', 'Imagen del producto'); ?><?= $this->Form->input('imagen_meli', array('type' => 'hidden', 'class' => 'js-imagen')); ?></th>
								<td><img style="max-width:130px;" src="<?=$this->request->data['MercadoLibr']['imagen_meli'];?>" class="img-responsive img-rounded js-imagen-preview" /></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cantidad_disponible', 'Stock Mercado libre'); ?></th>
								<td><?= $this->Form->input('cantidad_disponible'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tipo_publicacion', 'Tipo de publicación'); ?></th>
								<td><?= $this->Form->select('tipo_publicacion', $tipoPublicacionesMeli, array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('condicion', 'Condición'); ?></th>
								<td><?= $this->Form->select('condicion', $condicionProducto, array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('id_video', 'Video del producto'); ?></th>
								<td><?= $this->Form->input('id_video'); ?></td>
							</tr>
							<? if(!empty($meliItem)) : ?>
							<tr>
								<th><?= $this->Form->label('fecha_finaliza', 'Fecha de finalización'); ?></th>
								<td>
									<? 	$fecha = date('Y-m-d H:i:s', strtotime($meliItem['stop_time'])); ?>
									<?= $this->Form->input('fecha_finaliza', array('type' => 'hidden', 'value' => $fecha)); ?>
									<?= $fecha; ?>
								</td>
							</tr>
							<tr>
								<th><?= $this->Form->label('estado', 'Estado de la publicación'); ?></th>
								<td>
									<?= $this->Form->input('estado', array('value' => $meliItem['status'], 'type' => 'hidden')); ?>
									<?= $meliItem['status']; ?>
								</td>
							</tr>
							<? else : ?>
								<?= $this->Form->input('id_meli', array('type' => 'hidden', 'value' => '')); ?>
							<? endif; ?>
						</table>
					</div>
					
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
<?= $this->Form->end(); ?>
<div class="loader"><i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i></div>