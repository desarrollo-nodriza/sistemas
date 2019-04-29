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
								<th><?= $this->Form->label('cantidad_real', 'Stock fisico'); ?></th>
								<td><?= $this->Form->input('cantidad_real', array('class' => 'form-control', 'type' => 'text', 'readonly' => true, 'value' => array_sum(Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[io=IN].cantidad')) - array_sum(Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[io=ED].cantidad')) )); ?></td>
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
		                    <h3 class="panel-title"><i class="fa fa-money" aria-hidden="true"></i> Ventas</h3>
		                    <ul class="panel-controls">      
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
		            </div>
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
								<th><?=__('Bodega');?></th>
								<th><?=__('I/O');?></th>
								<th><?=__('Costo');?></th>
								<th><?=__('Cantidad');?></th>
								<th><?=__('Total');?></th>
								<th><?=__('Fecha');?></th>
								<th><?=__('Responsable');?></th>
								<th><?=__('Glosa');?></th>
							</thead>
							<tbody>
							<? foreach ($movimientosBodega as $movimiento) : ?>
								<tr>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['bodega'];?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['io'];?></td>
									<td><?=$this->Number->currency($movimiento['BodegasVentaDetalleProducto']['valor'], 'CLP');?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=$this->Number->currency($movimiento['BodegasVentaDetalleProducto']['total'], 'CLP');?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['fecha'];?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['responsable'];?></td>
									<td><?=$movimiento['BodegasVentaDetalleProducto']['glosa'];?></td>
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
                        <div class="widget-int"><?=array_sum(Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[io=IN].cantidad')) - array_sum(Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));?></div>
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
					<h5 class="panel-title"><i class="fa fa-usd" aria-hidden="true"></i> <?=__('Precios específicos');?></h5>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('El precio específico del producto sobrescribe al precio específico de la marca.'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Nombre');?></th>
									<th><?= __('Tipo');?></th>
									<th><?= __('Descuento');?></th>
									<th><?= __('Compuesto');?></th>
									<th><?= __('Infinito');?></th>
									<th><?= __('Fecha Inicio');?></th>
									<th><?= __('Fecha Final');?></th>
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
										<?= $this->Form->input('PrecioEspecificoProducto.999.descuento_compuesto', array('type' => 'checkbox', 'disabled' => true, 'class' => 'js-compuesto', 'checked' => false)); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.descuento_infinito', array('type' => 'checkbox', 'disabled' => true, 'class' => 'js-infinito', 'checked' => false)); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.fecha_inicio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-inicio', 'placeholder' => 'Ej: 2018-12-20')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.fecha_termino', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-final', 'placeholder' => 'Ej: 2019-12-20')); ?>
									</td>
									<td>
										<?= $this->Form->input('PrecioEspecificoProducto.999.activo', array('type' => 'checkbox', 'disabled' => true, 'class' => '', 'checked' => true)); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>

								<? if (!empty($this->request->data['PrecioEspecificoProducto'])) :  ?>
								<? foreach($this->request->data['PrecioEspecificoProducto'] as $ip => $precio) : ?>
								<tr>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.nombre', $ip), array('type' => 'text', 'class' => 'form-control js-nombre-producto', 'placeholder' => 'Nombre del precio especifico', 'value' => $precio['nombre'])); ?>
									</td>
									<td>
										<?= $this->Form->select(sprintf('PrecioEspecificoProducto.%d.tipo_descuento', $ip), $tipoDescuento, array('class' => 'form-control js-tipo-descuento', 'empty' => 'Seleccione', 'default' => $precio['tipo_descuento'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.descuento', $ip), array('type' => 'text', 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 55990', 'value' => $precio['descuento'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.descuento_compuesto', $ip), array('type' => 'checkbox', 'class' => 'js-compuesto', 'checked' => $precio['descuento_compuesto'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.descuento_infinito', $ip), array('type' => 'checkbox', 'class' => 'js-infinito', 'checked' => $precio['descuento_infinito'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.fecha_inicio', $ip), array('type' => 'text', 'class' => 'form-control datepicker js-f-inicio', 'placeholder' => 'Ej: 2018-12-20', 'value' => $precio['fecha_inicio'], 'readonly' => $precio['descuento_infinito'] )); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.fecha_termino', $ip), array('type' => 'text', 'class' => 'form-control datepicker js-f-final', 'placeholder' => 'Ej: 2019-12-20', 'value' => $precio['fecha_termino'], 'readonly' => $precio['descuento_infinito'] )); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.activo', $ip), array('type' => 'checkbox', 'class' => '', 'value' => $precio['activo'])); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
								<? endforeach; ?>
								<? endif; ?>
								
							</tbody>
						</table>
					</div>
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