<div class="page-title">
	<h2><span class="fa fa-edit"></span> Editar "<?=$this->request->data['VentaDetalleProducto']['nombre']; ?>"</h2>
</div>
<?= $this->Form->create('VentaDetalleProducto', array('class' => 'form-horizontal js-validate-producto', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id'); ?>
<?= $this->Form->input('id_externo', array('type' => 'hidden', 'value' => $this->request->data['VentaDetalleProducto']['id_externo'])); ?>
<div class="page-content-wrap">
	<div class="row">
		<? foreach ($canales as $ic => $canal) : ?>
			<? foreach ($canal as $i => $c) : ?>
				<? if ($c['existe']) : ?>
				<div class="col-xs-12 col-md-2">
					<div class="widget small-widget widget-warning widget-carousel">
		                <div class="owl-carousel">
		                	<div>                                    
		                        <div class="widget-title"><?=$c['nombre'];?></div>
		                        <div class="widget-subtitle">Precio base</div>                                                                       
		                        <div class="widget-int"><?=$this->Number->currency($c['item']['precio'], 'CLP');?></div>
		                    </div>
		                    <div>                                    
		                        <div class="widget-title"><?=$c['nombre'];?></div>
		                        <div class="widget-subtitle">Stock</div>                                                                       
		                        <div class="widget-int"><?=$c['item']['stock_disponible'];?></div>
		                    </div>
		                    <div>                                    
		                        <div class="widget-title"><?=$c['nombre'];?></div>
		                        <div class="widget-subtitle">Status</div>                                                                       
		                        <div class="widget-int"><?=$c['item']['estado'];?></div>
		                    </div>
		                </div>                                                        
		            </div>
				</div>
				<? endif; ?>
			<? endforeach; ?>
		<? endforeach; ?>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-6">
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
								<th><?= $this->Form->label('codigo_proveedor', 'Código proveedor'); ?></th>
								<td><?= $this->Form->input('codigo_proveedor', array('class' => 'form-control not-blank', 'placeholder' => 'Ej: HTD32S')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cant_minima_compra', 'Cantidad mínima de compra'); ?></th>
								<td><?= $this->Form->input('cant_minima_compra', array('type' => 'text', 'class' => 'form-control not-blank is-number', 'placeholder' => 'Por defecto es 1', 'min' => 1, 'max' => 1000)); ?>
									<span class="help-block">Por defecto la cantidad mínima es 1.</span>
								</td>
							</tr>
							<tr>
								<th><?= $this->Form->label('marca_id', 'Marca'); ?></th>
								<td><?= $this->Form->input('marca_id', array('class' => 'form-control not-blank', 'empty' => 'Seleccione Marca', 'default' => $this->request->data['Marca']['id'])); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('precio_costo', 'Precio Lista'); ?></th>
								<td><?= $this->Form->input('precio_costo', array('class' => 'form-control not-blank is-number', 'type' => 'text')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cantidad_virtual', 'Stock virtual'); ?></th>
								<td><?= $this->Form->input('cantidad_virtual', array('class' => 'form-control', 'type' => 'text')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cantidad_real', 'Stock disponible', array('data-toggle' => 'tooltip', 'title' => 'Cantidad reservada: ' . $this->request->data['VentaDetalleProducto']['cantidad_reservada'])); ?></th>
								<td><?= $this->Form->input('cantidad_real', array('class' => 'form-control', 'type' => 'text', 'readonly' => true)); ?><span class="help-block">Unidades fisicas menos las reservadas.</span></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('cantidad_real_fisica', 'Stock fisico real'); ?></th>
								<td><?= $this->Form->input('cantidad_real_fisica', array('class' => 'form-control', 'type' => 'text', 'readonly' => true)); ?>
									<span class="help-block">Unidades aun en bodega.</span>
								</td>
							</tr>
							<tr>
								<th><?= $this->Form->label('stock_automatico', 'Sincronizar stock'); ?></th>
								<td><?= $this->Form->input('stock_automatico', array('class' => 'icheckbox')); ?></td>
							</tr>
						</table>
					</div>
				</div>
				
			</div>
		</div> <!-- end col -->


		<div class="col-xs-12 col-md-6">
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-info">
		                <div class="panel-heading">
		                    <h3 class="panel-title"><i class="fa fa-arrows" aria-hidden="true"></i> Dimensiones</h3>
		                </div>
		                <div class="panel-body">
		                	<div class="table-responsive">
								<table class="table table-bordered">
									<tr>
										<th><?= $this->Form->label('peso', 'Peso bulto (kg)'); ?></th>
										<td><?= $this->Form->input('peso', array('class' => 'form-control is-number', 'type' => 'text')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('alto', 'Alto bulto (cm)'); ?></th>
										<td><?= $this->Form->input('alto', array('class' => 'form-control is-number', 'type' => 'text')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('ancho', 'Ancho bulto (cm)'); ?></th>
										<td><?= $this->Form->input('ancho', array('class' => 'form-control is-number', 'type' => 'text')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('largo', 'Largo bulto (cm)'); ?></th>
										<td><?= $this->Form->input('largo', array('class' => 'form-control is-number', 'type' => 'text')); ?></td>
									</tr>
								</table>
		                	</div>
		                </div>                             
		            </div>
					
					<? if ($productoWarehouse) : ?>
					<div class="panel panel-info">
		                <div class="panel-heading">
		                    <h3 class="panel-title"><i class="fa fa-cubes" aria-hidden="true"></i> Warehouse</h3>
		                </div>
		                <div class="panel-body">
		                	<div class="table-responsive">
								<table class="table table-bordered">
									<tr>
										<th><?= $this->Form->label('permitir_ingreso_sin_barra', '¿Permitir ingreso sin barra?'); ?></th>
										<td><?= $this->Form->input('permitir_ingreso_sin_barra', array('type' => 'checkbox', 'class' => 'icheckbox', 'checked' => ($productoWarehouse['ProductoWarehouse']['permitir_ingreso_sin_barra']))); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('cod_barra', 'Código de barras'); ?></th>
										<td><?= $this->Form->input('cod_barra', array('class' => 'form-control is-number', 'type' => 'text', 'value' => $productoWarehouse['ProductoWarehouse']['cod_barra'])); ?></td>
									</tr>
								</table>
		                	</div>
		                </div>                             
		            </div>
					<? else : ?>
						<div class="panel panel-info">
		                <div class="panel-heading">
		                    <h3 class="panel-title"><i class="fa fa-cubes" aria-hidden="true"></i> Warehouse</h3>
		                </div>
		                <div class="panel-body">
		                	<div class="table-responsive">
								<table class="table table-bordered">
									<tr>
										<th><?= $this->Form->label('permitir_ingreso_sin_barra', '¿Permitir ingreso sin barra?'); ?></th>
										<td><?= $this->Form->input('permitir_ingreso_sin_barra', array('type' => 'checkbox', 'class' => 'icheckbox')); ?></td>
									</tr>
									<tr>
										<th><?= $this->Form->label('cod_barra', 'Código de barras'); ?></th>
										<td><?= $this->Form->input('cod_barra', array('class' => 'form-control is-number', 'type' => 'text')); ?></td>
									</tr>
								</table>
		                	</div>
		                </div>                             
		            </div>
					<? endif; ?>

		            <div class="panel panel-info">
		                <div class="panel-heading">
		                    <h3 class="panel-title"><i class="fa fa-money" aria-hidden="true"></i> Unidades vendidas</h3>
		                    <ul class="panel-controls"> 
		                    	<li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
		                    </ul>
		                </div>
		                <div class="panel-body">
		                	<div class="table-responsive" style="max-height: 470px;">
								<table class="table table-bordered datatable">
									<caption>Cantidades vendidas</caption>
									<thead>
										<th>Id venta</th>
										<th>Estado</th>
										<th>Fecha<br>venta</th>
										<th>Cantidad<br>vendida</th>
										<th>Cantidad<br>reservada</th>
										<th>Acciones</th>
									</thead>
									<tbody>
									<? foreach ($this->request->data['VentaDetalle'] as $ivd => $vd) : ?>
										<tr>
											<td><?= $this->Html->link($vd['venta_id'], array('controller' => 'ventas', 'action' => 'view', $vd['venta_id']), array('target' => '_blank')); ?></td>
											<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$vd['Venta']['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($vd['Venta']['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($vd['Venta']['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a>&nbsp;</td>
											<td>
												<?= date_format(date_create($vd['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?>
												<? if ($vd['Venta']['picking_estado'] == 'no_definido' && $vd['Venta']['VentaEstado']['VentaEstadoCategoria']['venta'] && !$vd['Venta']['VentaEstado']['VentaEstadoCategoria']['final']) : 
												
													$retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($vd['Venta']['fecha_venta']), 'Y-m-d H:i:s'));

												if (!empty($retrasoMensaje)) : ?>
													<?=$retrasoMensaje;?>
												<?
												endif;
											  endif;?>			
											</td>
											<td><?= $vd['cantidad']; ?></td>
											<td><?= $vd['cantidad_reservada']; ?></td>
											<td>
												<div class="btn-group">
												<?=$this->Html->link('<i class="fa fa-hand-paper-o"></i> Reservar', array('controller' => 'VentaDetalleProductos', 'action' => 'reservar_stock', $vd['id']), array('class' => 'btn btn-success btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Reservar stock'))?>

												<? if ($vd['cantidad_reservada'] > 0) : ?>
												<?=$this->Html->link('<i class="fa fa-ban"></i> Liberar', array('controller' => 'ventas', 'action' => 'liberar_stock_reservado', $vd['Venta']['id'], $vd['id'], $vd['cantidad_reservada']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Liberar stock'))?>
												<? endif; ?>
												</div>
											</td>
										</tr>
									<? endforeach; ?>										
									</tbody>	
								</table>
		                	</div>
		                </div>                             
		            </div>

					<div class="panel panel-info panel-toggled">
		                <div class="panel-heading">
		                    <h3 class="panel-title"><i class="fa fa-cubes" aria-hidden="true"></i> Unidades reservadas</h3>
		                    <ul class="panel-controls"> 
		                    	<li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
		                    </ul>
		                </div>
		                <div class="panel-body">
		                	<div class="table-responsive" style="max-height: 470px;">
								<table class="table table-bordered">
									<caption>Cantidades reservadas en distintas ventas</caption>
									<thead>
										<th>Id venta</th>
										<th>Estado</th>
										<th>Cantidad vendida</th>
										<th>Cantidad reservada</th>
										<th>Liberar</th>
									</thead>
									<tbody>
									<? foreach ($this->request->data['VentaDetalle'] as $ivd => $vd) : if($vd['cantidad_reservada'] < 1) { continue; } ?>
										<tr>
											<td><?= $this->Html->link($vd['venta_id'], array('controller' => 'ventas', 'action' => 'view', $vd['venta_id']), array('target' => '_blank')); ?></td>
											<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$vd['Venta']['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($vd['Venta']['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($vd['Venta']['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a>&nbsp;</td>
											<td><?= $vd['cantidad']; ?></td>
											<td><?= $vd['cantidad_reservada']; ?></td>
											<td><?=$this->Html->link('<i class="fa fa-ban"></i> Liberar', array('controller' => 'ventas', 'action' => 'liberar_stock_reservado', $vd['Venta']['id'], $vd['id'], $vd['cantidad_reservada']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Liberar stock'))?></td>
										</tr>
									<? endforeach; ?>										
									</tbody>	
								</table>
		                	</div>
		                </div>                             
		            </div>
				</div>

				
				<!--<div class="col-xs-12">
					<div class="panel panel-info panel-toggled">
		                <div class="panel-heading">
		                    <h3 class="panel-title"><i class="fa fa-money" aria-hidden="true"></i> Ventas</h3>
		                    <ul class="panel-controls"> 
		                    	<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>     
		                        <li><label class="control-label">Rango </label></li>
		                        <li>
		                            <div class="input-group">
		                                <?=$this->Form->input('f_inicio', array('class' => 'form-control datepicker', 'id' => 'f_inicial', 'value' => date('Y-01-01')));?>
		                                <span class="input-group-addon add-on"> - </span>
		                                <?=$this->Form->input('f_final', array('class' => 'form-control datepicker', 'id' => 'f_final', 'value' => date('Y-m-t')));?>
		                            </div>
		                        </li>
		                        <li><a id="enviarFormularioDescuentos" href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
		                    </ul>
		                </div>
		                <div class="panel-body">
		                    <div id="ventas" style="height: 342px;">
		                        
		                    </div>
		                </div>                             
		            </div>-->

				</div>


				<!--<div class="col-xs-12">
					<div class="panel panel-info">
						<div class="panel-heading">
							<h5 class="panel-title"><i class="fa fa-archive" aria-hidden="true"></i> <?=__('Inventario');?></h5>
						</div>
						<div class="panel-body">
							<div class="table-responsive">
								<table class="table table-bordered">
								<tr>
									<td><?=__('PMP Global');?></td>
									<td><?=$this->Number->currency($this->request->data['VentaDetalleProducto']['pmp_global'], 'CLP');?></td>
								</tr>
								<tr>
									<td><?=__('Stock Global');?></td>
									<td><?=array_sum(Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[io=IN].cantidad')) - array_sum(Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));?></td>
								</tr>
								<? if (isset($this->request->data['VentaDetalleProducto']['Inventario'])) : ?>
									<? foreach ($this->request->data['VentaDetalleProducto']['Inventario'] as $i => $inventario) : ?>
									<tr>
										<td><?=sprintf('PMP bodega %s', $inventario['bodega_nombre']);?></td>
										<td><?=$this->Number->currency($inventario['pmp'], 'CLP');?></td>
									</tr>
									<tr>
										<td><?=sprintf('Stock bodega %s', $inventario['bodega_nombre']);?></td>
										<td><?=$inventario['total'];?></td>
									</tr>
									<? endforeach; ?>
								<? endif; ?>	
								</table>
							</div>
						</div>
					</div>
				</div>-->
			</div>
		</div>
	
	</div>
	<!-- end row -->
	
	<div class="row">

		<div class="col-xs-12 col-md-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-archive" aria-hidden="true"></i> <?=__('Movimientos de bodega');?></h5>
					<div class="btn-group pull-right">
					<? if ($permisos['move_stock']) : ?>
						<?= $this->Html->link('<i class="fa fa-arrows"></i> Mover inventario', array('action' => 'moverInventario', $this->request->data['VentaDetalleProducto']['id']), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>

					<? if ($permisos['adjust_stock']) : ?>
						<?= $this->Html->link('<i class="fa fa-cogs"></i> Ajustar inventario', array('action' => 'ajustarInventario', $this->request->data['VentaDetalleProducto']['id']), array('class' => 'btn btn-warning', 'escape' => false)); ?>
					<? endif; ?>
					</div>
				</div>
				<div class="panel-body">
					<? if (!empty($movimientosBodega)) : ?>
			        <div class="table-responsive" style="max-height: 352px; overflow-y: auto;">
						<table class="table table-bordered table-striped">
							<thead>
								<th><?=__('Id');?></th>
								<th><?=__('Bodega');?></th>
								<th><?=__('I/O');?></th>
								<th><?=__('Costo');?></th>
								<th><?=__('Cantidad');?></th>
								<th><?=__('Total');?></th>
								<th><?=__('Fecha');?></th>
								<th><?=__('Responsable');?></th>
								<th><?=__('Glosa');?></th>
								<th><?=__('OC');?></th>
								<th><?=__('Venta');?></th>
							</thead>
							<tbody>
							<? foreach ($movimientosBodega as $movimiento) : ?>
								<tr>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['id'];?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['bodega'];?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['io'];?></td>
									<td><?=$this->Number->currency($movimiento['BodegasVentaDetalleProducto']['valor'], 'CLP');?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=$this->Number->currency($movimiento['BodegasVentaDetalleProducto']['total'], 'CLP');?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['fecha'];?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['responsable'];?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['glosa'];?></td>
									<td>
									<? if (!empty($movimiento['BodegasVentaDetalleProducto']['orden_compra_id'])) : ?>
										<?=$this->Html->link('#' . $movimiento['BodegasVentaDetalleProducto']['orden_compra_id'], array('controller' => 'ordenCompras', 'action' => 'view', $movimiento['BodegasVentaDetalleProducto']['orden_compra_id']), array('target' => '_blank')); ?>
									<? else : ?>
										--
									<? endif; ?>
									</td>
									<td>
									<? if (!empty($movimiento['BodegasVentaDetalleProducto']['venta_id'])) : ?>
										<?=$this->Html->link('#' . $movimiento['BodegasVentaDetalleProducto']['venta_id'], array('controller' => 'ventas', 'action' => 'view', $movimiento['BodegasVentaDetalleProducto']['venta_id']), array('target' => '_blank')); ?>
									<? else : ?>
										--
									<? endif; ?>	
									</td>
								</tr>
							<? endforeach; ?>
							</tbody>
						</table>
			        </div>
					<? else : ?>
						<p><?=__('No registra movimientos.');?></p>
					<? endif; ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-md-4">
			<div class="widget" style="background-color: #<?=random_color();?>;">
                <div class="owl-carousel">
                	<div>                                    
                        <div class="widget-title"><?=__('Precio Costo');?></div>                                                                      
                        <div class="widget-int"><?=$this->Number->currency($precio_costo_final, 'CLP');?></div>
                    </div>
                </div>                                                        
            </div>
		</div>
		<div class="col-xs-12 col-md-4">
			<div class="widget" style="background-color: #<?=random_color();?>;">
                <div class="owl-carousel">
                	<div>                                    
                        <div class="widget-title"><?=__('PMP Global');?></div>                                                                      
                        <div class="widget-int"><?=$this->Number->currency($this->request->data['VentaDetalleProducto']['pmp_global'], 'CLP');?></div>
                    </div>
                </div>                                                        
            </div>
		</div>
		<div class="col-xs-12 col-md-4">
			<div class="widget" style="background-color: #<?=random_color();?>;">
                <div class="owl-carousel">
                	<div>                                    
                        <div class="widget-title"><?=__('Stock Global');?></div>                                                                      
                        <div class="widget-int"><?=$this->request->data['VentaDetalleProducto']['cantidad_real_fisica'];?></div>
                    </div>
                </div>                                                        
            </div>
		</div>
	</div>
	<div class="row">		
		<? if (isset($this->request->data['VentaDetalleProducto']['Inventario'])) : ?>
			<? foreach ($this->request->data['VentaDetalleProducto']['Inventario'] as $i => $inventario) : ?>
			<div class="col-xs-12 col-md-2">
				<div class="widget small-widget" style="background-color: #<?=random_color();?>;">
	                <div class="owl-carousel">
	                	<div>                                    
	                        <div class="widget-title"><?=sprintf('PMP bodega %s', $inventario['bodega_nombre']);?></div>                                                                      
	                        <div class="widget-int"><?=$this->Number->currency($inventario['pmp'], 'CLP');?></div>
	                    </div>
	                </div>                                                        
	            </div>
			</div>

			<div class="col-xs-12 col-md-2">
				<div class="widget small-widget" style="background-color: #<?=random_color();?>;">
	                <div class="owl-carousel">
	                	<div>                                    
	                        <div class="widget-title"><?=sprintf('Stock bodega %s', $inventario['bodega_nombre']);?></div>                                                                      
	                        <div class="widget-int"><?=$inventario['total'];?></div>
	                    </div>
	                </div>                                                        
	            </div>
			</div>
			<? endforeach; ?>
		<? endif; ?>
	</div>

	<!-- end row -->
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-usd" aria-hidden="true"></i> <?=__('Precios específicos compra');?></h5>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<?=$this->element('ordenCompras/crear_precio_costo_especifico_producto', array('precios_especificos' => $this->request->data['PrecioEspecificoProducto'])); ?>
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

	<div class="row">

		<div class="col-xs-12 col-md-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-archive" aria-hidden="true"></i> <?=__('Movimientos de zonificaciones');?></h5>
					
					<div class="btn-group pull-right">
						<?= $this->Html->link('<i class="fa fa-arrows"></i> Reubicar stock', array('controller' => 'zonificaciones','action' => 'mover_de_ubicacion', $this->request->data['VentaDetalleProducto']['id']), array('class' => 'btn btn-success', 'escape' => false)); ?>
						<?= $this->Html->link('<i class="fa fa-cogs"></i> Reubicar stock masivamente del producto', array('controller' => 'zonificaciones','action' => 'reubicacion_masiva', $this->request->data['VentaDetalleProducto']['id']), array('class' => 'btn btn-warning', 'escape' => false)); ?>
					</div>
						
					<div class="panel-body">
						<? if (!empty($zonificaciones)) : ?>
						<div class="table-responsive" style="max-height: 352px; overflow-y: auto;">
							<table class="table table-bordered table-striped">
								<thead>
									<th><?=__('Id');?></th>
									<th><?=__('Ubicacion');?></th>
									
									<th><?=__('Cantidad');?></th>
									<th><?=__('Movimiento');?></th>
									<th><?=__('Responsable');?></th>
									<th><?=__('Embalaje');?></th>
									<th><?=__('Ubicacion nueva');?></th>
									<th><?=__('Ubicacion antiguo');?></th>
									<th><?=__('Orden de compra');?></th>
								</thead>
								<tbody>
								<? foreach ($zonificaciones as $ubicacion) : ?>
									<tr>
										<td><?=$ubicacion['Zonificacion']['id'];?></td>
										<?php
										$zona =$ubicacion['Ubicacion']['Zona'];
										?>
										<td><?="{$zona['nombre']} - {$ubicacion['Ubicacion']['columna']}".' - '."{$ubicacion['Ubicacion']['fila']}";?></td>
									
										<td><?=$ubicacion['Zonificacion']['cantidad'];?></td>
										<td><?=$ubicacion['Zonificacion']['movimiento'];?></td>
										<td><?=$ubicacion['VentaCliente']['nombre'].' '.$ubicacion['VentaCliente']['apellido'];?></td>
										<td><?=$ubicacion['Zonificacion']['embalaje_id'];?></td>
										<td><?=$ubicacion['Zonificacion']['nueva_ubicacion_id'];?></td>
										<td><?=$ubicacion['Zonificacion']['antigua_ubicacion_id'];?></td>
										<td>
											<? if (!empty($ubicacion['Zonificacion']['orden_de_compra'])) : ?>
												<?=$this->Html->link('#' . $ubicacion['Zonificacion']['orden_de_compra'], array('controller' => 'ordenCompras', 'action' => 'view', $ubicacion['Zonificacion']['orden_de_compra']), array('target' => '_blank')); ?>
											<? else : ?>
												--
											<? endif; ?>
										</td>
										
									</tr>
								<? endforeach; ?>
								</tbody>
							</table>
						</div>
						<? else : ?>
							<p><?=__('No registra movimientos.');?></p>
						<? endif; ?>
					</div>
					
				</div>
	</div>


</div>


<!-- MESSAGE BOX-->
<div class="message-box message-box-info animated fadeIn" data-sound="alert" id="modal_alertas">
    <div class="mb-container">
        <div class="mb-middle">
            <div class="mb-title" id="modal_alertas_label"><i class="fa fa-alert"></i> ¿Actualizar canales?</div>
            <div class="mb-content">
                <p id="mensajeModal">¿Desea actualizar el stock en los distintos canales de ventas disponibles?</p>
                <div class="form-group">
					<div class="checkbox">
                        <label>
                            <?=$this->Form->input('actualizar_canales', array('type' => 'checkbox', 'class' => '')); ?>
                            Sí, actualizar stock
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


<script type="text/javascript">
	
	<? 

	echo 'var id=' . $this->request->data['VentaDetalleProducto']['id'] . ';';

	?>

	$(document).ready(function(){

		function iniciarGraficoMorris($data, $xkey, $ykeys, $labels, $resize = true, $lineColors) {

			$('#ventas').html('');

			Morris.Line({
				element: 'ventas',
				data: $data,
				xkey: $xkey,
				ykeys: $ykeys,
				labels: $labels,
				resize: $resize,
				lineColors: $lineColors
			});

		}

    
      $('#enviarFormularioDescuentos').on('click', function(){

      	var f_inicial = $('#f_inicial').val(),
      		f_final   = $('#f_final').val();

      	$.ajax({
	      	url: webroot + 'ventaDetalleProductos/obtenerVentas/' + id + '/' + f_inicial + '/' + f_final,
	      })
	      .done(function(respuesta) {
	      	
	      	var res = $.parseJSON(respuesta);

	      	iniciarGraficoMorris(res.data, res.xkeys, res.ykeys, res.labels, true, res.lineColors );

	      })
	      .fail(function() {
	      	console.log("error");
	      });

      });

      $('#enviarFormularioDescuentos').trigger('click');

    });

</script>