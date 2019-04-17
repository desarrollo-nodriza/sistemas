<div class="page-title">
	<h2><span class="fa fa-shopping-basket"></span> Productos <?=$this->Session->read('Marketplace.nombre'); ?> <small><?= $publicado = (!empty($this->request->data['MercadoLibr']['id_meli'])) ? 'Publicado' : 'No publicado' ;?></small></h2>
</div>
<?= $this->Form->create('MercadoLibr', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id'); ?>
<?= $this->Form->input('tienda_id', array('type' => 'hidden', 'value' => $this->Session->read('Tienda.id'))); ?>
<?= $this->Form->input('id_product', array('type' => 'hidden', 'class' => 'id-product')); ?>
<?= $this->Form->hidden('marketplace_id', array('value' => $this->Session->read('Marketplace.id'))); ?>
<div class="page-content-wrap">
	<div class="row">
	<? if(isset($meliItem['item']['sold_quantity'])) : ?>
		<div class="col-xs-12 col-sm-3">
            <a href="#" class="tile tile-primary">
                <?= $meliItem['item']['sold_quantity']; ?>
                <p>Cantidad vendida en MELI</p>                            
                <div class="informer informer-default"><span class="fa fa-shopping-cart"></span></div>
            </a>
        </div>
    <? endif; ?>
    <? if(isset($meliItem['item']['available_quantity'])) : ?>
		<div class="col-xs-12 col-sm-3">
            <a href="#" class="tile tile-info">
                <?= $meliItem['item']['available_quantity']; ?>
                <p>Stock MELI</p>                            
                <div class="informer informer-default"><span class="fa fa-cubes"></span></div>
            </a>
        </div>
    <? endif; ?>
    <? if(isset($meliItem['item']['price'])) : ?>
		<div class="col-xs-12 col-sm-3">
            <a href="#" class="tile tile-success">
                <?= CakeNumber::currency($meliItem['item']['price'], 'CLP'); ?>
                <p>Precio MELI</p>                            
                <div class="informer informer-default"><span class="fa fa-usd"></span></div>
            </a>
        </div>
    <? endif; ?>
    <? if (isset($meliItem['item']['permalink'])) : ?>
    <div class="col-xs-12 col-sm-3">
            <a href="<?=$meliItem['item']['permalink'];?>" class="tile tile-warning" target="_blank">
                <span class="fa fa-eye"></span>              
            </a>
        </div>
    <? endif; ?>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar Mercado Libre producto</h3>
				</div>
				<div class="panel-body">
					<div class="col-xs-12">
						<h4>Categoría Mercadolibre</h4>
						<? if (!empty($this->request->data['MercadoLibr']['categoria_00']) && !empty($this->request->data['MercadoLibr']['id_meli']) ) : ?>
						<label class="label label-info">La categoria del item debe ser actualizado directamente en Mercado Libre</label>
						<br />
						<br />
						<? endif; ?>
					</div>
					<div class="col-xs-12 col-sm-2 js-base">
						<? if (!empty($this->request->data['MercadoLibr']['categoria_00']) && !empty($this->request->data['MercadoLibr']['id_meli'])) : ?>
						<?=$this->Form->select('categoria_00', $categoriasRoot, array('empty' => 'Seleccione categoria raiz', 'class' => 'form-control js-cat', 'id' => 'BaseCat', 'required' => true, 'disabled' => true));?>
						<? else : ?>
						<?=$this->Form->select('categoria_00', $categoriasRoot, array('empty' => 'Seleccione categoria raiz', 'class' => 'form-control js-cat', 'id' => 'BaseCat', 'required' => true));?>
						<? endif; ?>
						<span class="help-block"></span>
					</div>
					<? if (!empty($categoriasHojas)) : ?>
					<? foreach($categoriasHojas as $index => $categoriasHoja) : ?>
						<div class="col-xs-12 col-sm-2">
							
							<? if (!empty($this->request->data['MercadoLibr']['id_meli'])) : ?>
								<?=$this->Form->select('categoria_0' . $index, $categoriasHoja, array('empty' => 'Seleccione categoria', 'class' => 'js-cat form-control', 'required' => true, 'disabled' => true));?>
							<? else : ?>
								<?=$this->Form->select('categoria_0' . $index, $categoriasHoja, array('empty' => 'Seleccione categoria', 'class' => 'js-cat form-control', 'required' => true));?>
							<? endif; ?>
							
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
								<th><?= $this->Form->label('tienda_oficial_id', 'Tienda oficial'); ?></th>
								<td><?= $this->Form->select('tienda_oficial_id', $tiendasOficiales, array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><label>ID del Producto</label></th>
								<td><?= $this->Form->input('seller_custom_field', array('type' => 'text', 'class' => 'form-control input-productos-buscar-meli', 'placeholder' => 'ej: 12422')); ?></td>
							</tr>
							<tr>
								<th><label>Nombre del Producto</label></th>
								<td><?= $this->Form->input('producto', array('maxlength' => '60', 'minlength' =>'1', 'type' => 'text', 'class' => 'form-control js-nombre')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('precio', 'Precio en Mercado libre'); ?></th>
								<td><?= $this->Form->input('precio', array('class' => 'form-control js-precio')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('agregar_costo_envio', '¿Agregar costo envio?'); ?></th>
								<td><?= $this->Form->input('agregar_costo_envio', array('class' => 'icheckbox', 'default' => true)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('description', 'Descripción del producto'); ?></th>
								<td><?= $this->Form->input('description', array('class' => 'form-control js-description')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('imagen_meli', 'Imagen del producto'); ?><?= $this->Form->input('imagen_meli', array('type' => 'hidden', 'class' => 'js-imagen')); ?></th>
								<td><img style="max-width:130px;" src="<?=$this->request->data['MercadoLibr']['imagen_meli'];?>" class="img-responsive img-rounded js-imagen-preview" /></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cantidad_disponible', 'Stock Mercado libre'); ?></th>
								<td><?= $this->Form->input('cantidad_disponible', array('class' => 'form-control js-stock')); ?></td>
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
							<? if (!empty($envio)) : ?>
							<tr>
								<th><?= $this->Form->label('', 'Opciones de envio'); ?></th>
								<? if(isset($meliItem['item']['sold_quantity']) && $meliItem['item']['sold_quantity'] > 0) : ?>

									<label class="label label-info">Los métodos de envio no pueden ser modificadas</label>

								<? else : ?>
								<td class="shipping-container">
									<? foreach ($envio as $k => $v) : ?>
									<? if ($v['mode'] == 'custom') : ?>
									<? if (isset($v['shipping_attributes']['local_pick_up']) ) : ?>
									<? if (isset($meliItem['item']['shipping']['local_pick_up']) && $meliItem['item']['shipping']['local_pick_up'] ) : ?>
									<div class="form-group">
										<input type="checkbox" name="data[Envios][local_pick_up]" checked>
										<label> Tambien se puede retirar en persona</label>
									</div>
									<? else : ?>
									<div class="form-group">
										<input type="checkbox" name="data[Envios][local_pick_up]">
										<label> Tambien se puede retirar en persona</label>
									</div>
									<? endif; ?>
									<? endif;  ?>
									<div class="form-group">
										<? if (isset($meliItem['item']['shipping']['mode']) && $meliItem['item']['shipping']['mode'] == $v['mode']) : ?>
										<input type="checkbox" name="data[Envios][<?= $v['mode']; ?>]" id="<?= $v['mode']; ?>" class="meli-custom-shipment" checked>
										<label for="<?= $v['mode']; ?>"><?= $v['label']; ?></label>
										<? else : ?>
										<input type="checkbox" name="data[Envios][<?= $v['mode']; ?>]" id="<?= $v['mode']; ?>" class="meli-custom-shipment">
										<label for="<?= $v['mode']; ?>"><?= $v['label']; ?></label>
										<? endif; ?>
									</div>
									<div class="meli-custom-list table-responsive">
										<table class="table table-bordered js-clon-scope" data-limit="10">
											<thead>
												<th><?= __('Descripción'); ?></th>
												<th><?= __('Costo'); ?></th>
												<th><?= __('Acciones'); ?></th>
											</thead>
											<tbody class="js-clon-contenedor js-clon-blank">
												<tr class="js-clon-base hidden">
													<td>
														<?= $this->Form->input('Envios.costs.999.description', array('disabled' => true, 'class' => 'form-control'));?>
													</td>
													<td>
														<?= $this->Form->input('Envios.costs.999.cost', array('disabled' => true, 'class' => 'form-control'));?>
													</td>
													<td>
														<a href="#" class="btn btn-xs btn-danger js-clon-eliminar"><i class="fa fa-trash"></i> Quitar</a>
													</td>
												</tr>
												<? if (is_array($meliItem['item']['shipping']['options'])) : ?>
												<? foreach ($meliItem['item']['shipping']['options'] as $indice => $costo) : ?>
													<tr>
														<td>
															<?= $this->Form->input(sprintf('Envios.costs.%d.description', $indice), array('class' => 'form-control', 'value' => $costo['name'])); ?>
														</td>
														<td>
															<?= $this->Form->input(sprintf('Envios.costs.%d.cost', $indice), array('class' => 'form-control', 'value' => $costo['cost'])); ?>
														</td>
														<td>
															<a href="#" class="btn btn-xs btn-danger js-clon-eliminar"><i class="fa fa-trash"></i> Quitar</a>
														</td>
													</tr>
												<? endforeach; ?>
												<? endif; ?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="3"><a href="#" class="btn btn-xs btn-success js-clon-agregar"><i class="fa fa-plus"></i> Agregar otro</a></td>
												</tr>
											</tfoot>
										</table>
									</div>
									<? elseif ($v['mode'] == 'me2') : ?>
									<? if (isset($meliItem['item']['shipping']['mode']) && $meliItem['item']['shipping']['mode'] == $v['mode']) : ?>
									<div class="form-group">
										<input type="checkbox" name="data[Envios][<?= $v['mode']; ?>]" id="<?= $v['mode']; ?>" checked>
										<label for="<?= $v['mode']; ?>"><?= $v['label']; ?></label>
									</div>
									<? else : ?>
									<div class="form-group">
										<input type="checkbox" name="data[Envios][<?= $v['mode']; ?>]" id="<?= $v['mode']; ?>">
										<label for="<?= $v['mode']; ?>"><?= $v['label']; ?></label>
									</div>
									<? endif; ?>
									<? if (isset($v['shipping_attributes']['local_pick_up']) ) : ?>
									<? if (isset($meliItem['item']['shipping']['local_pick_up']) && $meliItem['item']['shipping']['local_pick_up'] ) : ?>
									<div class="form-group">
										<input type="checkbox" name="data[Envios][local_pick_up]" checked>
										<label> Tambien se puede retirar en persona</label>
									</div>
									<? else : ?>
									<div class="form-group">
										<input type="checkbox" name="data[Envios][local_pick_up]">
										<label> Tambien se puede retirar en persona</label>
									</div>
									<? endif; ?>
									<? endif;  ?>
									<? endif; ?>
									<? endforeach; ?>
								</td>
								<? endif; ?>
							</tr>
							<? else : ?>
							<tr>
								<th><?= $this->Form->label('', 'Opciones de envio'); ?></th>
								<td class="shipping-container"></td>
							<tr>
							<? endif; ?>
							<tr>
								<th><?= $this->Form->label('garantia', 'Garantia'); ?></th>
								<td>
									<? if(isset($meliItem['item']['sold_quantity']) && $meliItem['item']['sold_quantity'] > 0) : ?>
										<?= $this->Form->input('garantia', array('disabled' => true)); ?>
									<? else : ?>
										<?= $this->Form->input('garantia'); ?>
									<? endif; ?>
									
								</td>
							</tr>

							<? if(!empty($meliItem['item'])) : ?>
							<tr>
								<th><?= $this->Form->label('fecha_finaliza', 'Fecha de finalización'); ?></th>
								<td>
									<? 	$fecha = (isset($meliItem['item']['stop_time'])) ? date('Y-m-d H:i:s', strtotime($meliItem['item']['stop_time'])) : '0000-00-00 00:00:00'; ?>
									<?= $this->Form->input('fecha_finaliza', array('type' => 'hidden', 'value' => $fecha)); ?>
									<?= $fecha; ?>
								</td>
							</tr>
							<tr>
								<th><?= $this->Form->label('estado', 'Estado de la publicación'); ?></th>
								<td>
									<?= $this->Form->input('estado', array('value' => $meliItem['item']['status'], 'type' => 'hidden')); ?>
									<?= $meliItem['item']['status']; ?>
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