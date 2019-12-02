<?= $this->Html->script(array(
	'/backend/js/plugins/smartwizard/jquery.smartWizard-2.0.min.js')); ?>
<?= $this->fetch('script'); ?>

<div class="page-title">
	<h2><span class="fa fa-money"></span> Nueva Venta #<?=$this->request->data['Venta']['referencia']; ?></h2>
</div>

<? if ($permisos['add']) : ?>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-primary">
		    	<div class="panel-body">

		    		<?= $this->Form->create('Venta', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => false))); ?>					
					<?= $this->Form->hidden('access_token', array('value' => $this->Session->read('Auth.Administrador.token.token'))); ?>
					<?= $this->Form->hidden('tienda_id'); ?>
					<?= $this->Form->hidden('venta_estado_id'); ?>
					
					<?= $this->Form->input('id'); ?>
		    		<div id="buy-steps" class="wizard show-submit wizard-validation">
                        <ul>
                            <li>
                                <a href="#step-1">
                                    <span class="stepNumber">1</span>
                                    <span class="stepDesc">Venta<br /><small>Detalle de la venta</small></span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-2">
                                    <span class="stepNumber">2</span>
                                    <span class="stepDesc">Detalles<br /><small>Prouctos y pago</small></span>
                                </a>
                            </li>                          
                        </ul>

                        <div id="step-1">   
							
							<div class="row">
								<div class="col-xs-12 col-md-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-exclamation" aria-hidden="true"></i> <?= __('Información de la venta');?></h5>
										</div>
										<div class="panel-body">
											<div class="form-group">
												<label><?=__('Cliente');?></label>
												<div class="input-group" style="max-width: 100%;">
	                                                <input type="text" id="obtener_cliente" class="form-control not-blank" placeholder="Ingrese email del cliente" value="<?= (!empty($this->request->data['Venta']['venta_cliente_id'])) ? $this->request->data['VentaCliente']['email'] : ''; ?>" >
	                                                <span class="input-group-addon link" data-toggle="modal" data-target="#modalCrearCliente"><i class="fa fa-plus"></i> Crear nuevo</span>
	                                                <?=$this->Form->hidden('venta_cliente_id'); ?>
	                                            </div>
												
											</div>
											<div class="form-group">
												<label><?=__('ID Externo (opcional)');?></label>
												<?= $this->Form->input('id_externo', array('placeholder' => 'Ej: 33432')); ?>
											</div>
											<div class="form-group">
												<label><?=__('Marketplace (opcional)');?></label>
												<?= $this->Form->input('marketplace_id', array('empty' => 'Seleccione')); ?>
											</div>
											<div class="form-group">
												<label class="col-md-2"><?=__('Venta Prioritaria');?></label>
												<div class="col-md-10">
													<label class="switch">
		                                                <?=$this->Form->input('prioritario', array('class' => '', 'value' => 0)); ?>
		                                                <span></span>
		                                            </label>
		                                        </div>
											</div>
											<div class="form-group">
												<label><?=__('Fecha de la venta');?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
	                                                <?= $this->Form->input('fecha_venta_f', array('class' => 'form-control not-blank datepicker', 'placeholder' => '2019-05-20', 'style' => 'min-width: 200px;', 'value' => date('Y-m-d'))); ?>
	                                                <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time"></i></span>
	                                                <?= $this->Form->input('fecha_venta_h', array('class' => 'form-control not-blank timepicker24', 'placeholder' => '12:00:00', 'style' => 'min-width: 200px;', 'value' => date('H:i:s'))); ?>
	                                            </div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-md-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-truck" aria-hidden="true"></i> <?= __('Método de tranporte');?></h5>
										</div>
										<div class="panel-body">
											<div class="form-group">
												<label><?=__('Método de envio');?></label>
												<?= $this->Form->input('metodo_envio_id', array('class' => 'form-control not-blank js-metodo-envios-ajax', 'empty' => 'Seleccione')); ?>
											</div>
											<div class="form-group hidden">
												<label><?=__('Dirección despacho');?></label>
												<?= $this->Form->input('direccion_entrega', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Ingrese dirección')); ?>
											</div>
											<div class="form-group hidden">
												<label><?=__('Comuna despacho');?></label>
												<?=$this->Form->select('comuna_entrega', $comunas, array('empty' => 'Seleccione', 'class' => 'form-control select', 'data-live-search' => true, 'default' => $this->request->data['Venta']['comuna_entrega'])); ?>
											</div>
											<div class="form-group hidden">
												<label><?=__('Telefono despacho');?></label>
												<?= $this->Form->input('fono_receptor'); ?>
											</div>
											<div class="form-group hidden">
												<label><?=__('Costo despacho');?></label>
												<?= $this->Form->input('costo_envio', array('type' => 'text', 'class' => 'form-control is-number not-blank', 'placeholder' => 'Ingrese costo de envio')); ?>
											</div>
										</div>
									</div>
								</div>
							</div>

                        </div>
						
						<div id="step-2">   

                            <div class="table-responsive">
								
								<table class="table table-bordered">
									<tr>
										<th valign="middle">Ingrese nombre del producto</th>
										<td><input type="text" id="obtener_producto" name="p" class="form-control" placeholder="Ingrese nombre o referencia del producto"></td>
									</tr>
								</table>

								<table class="table table-bordered">
									<thead>
										<th>ID Producto</th>
										<th>Referencia</th>
										<th>Nombre</th>
										<th>Cantidad</th>
										<th>Precio Bruto (unidad)</th>
										<th>Subtotal</th>
										<th>Opciones</th>
									</thead>
									<tbody class="js-productos-wrapper">
									<? foreach ($this->request->data['VentaDetalle'] as $ip => $p) : ?>
										<?=$this->element('ventas/tr-producto-crear-venta', array('producto' => $p, 'set' => 1))?>	
									<? endforeach; ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="5"></td>
											<td><?=__('Subtotal');?></td>
											<td>$<span id="subtotal" data-value="0">0</span></td>
										</tr>
										<tr>
											<td colspan="5"></td>
											<td><?=__('Iva');?></td>
											<td>$<span id="iva" data-value="">0</span></td>
										</tr>
										<tr>
											<td colspan="5"></td>
											<td><?=__('Transporte');?></td>
											<td>$<span id="transporte" data-value="">0</span></td>
										</tr>
										<tr>
											<td colspan="5"></td>
											<td><?=__('Descuento ($)');?></td>
											<td><?=$this->Form->input('descuento', array('type' => 'text', 'class' => 'form-control not-blank is-number', 'empty' => 'Ingresa el monto del descuento', 'data-value' => $this->request->data['Venta']['descuento'])); ?></td>
										</tr>
										<tr>
											<td colspan="5"></td>
											<td><?=__('Total');?></td>
											<td><?=$this->Form->input('total', array('type' => 'text', 'class' => 'form-control not-blank is-number', 'empty' => 'Monto a pagar', 'data-value' => $this->request->data['Venta']['total'])); ?></td>
										</tr>
									</tfoot>
								</table>
								<table class="table table-bordered">
									<tr>
										<th valign="middle"><?=__('Medio de pago');?></th>
										<td><?=$this->Form->input('medio_pago_id', array('class' => 'form-control not-blank', 'empty' => 'Seleccione')); ?></td>
									</tr>
									<tr>
										<td colspan="2"><?=__('Pagos');?></td>
									</tr>
									<tr>
										<td colspan="2">
											<table class="table table-bordered">
												<caption>Registre voucher, efectivo o n° de transacción</caption>
												<thead>
													<th><?=__('Voucher/Alias/N° transacción');?></th>
													<th><?=__('Monto');?></th>
												</thead>
												<tbody class="js-pagos-wrapper">
													<tr class="hidden clone-tr">
														<td>
															<?=$this->Form->hidden('VentaTransaccion.999.fecha', array('disabled' => true, 'value' => date('Y-m-d'))); ?>
															<?=$this->Form->input('VentaTransaccion.999.nombre', array('disabled' => true, 'class' => 'form-control not-blank', 'placeholder' => 'Ingrese Voucher/Alias/N° transacción'));?>		
														</td>
														<td><?=$this->Form->input('VentaTransaccion.999.monto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank is-number js-monto-pago', 'placeholder' => 'Ingrese monto pagado'));?></td>
														<td valign="center"><button class="pago_tr duplicate_tr btn-success"><i class="fa fa-plus"></i></button><button class="remove_tr js-recalcular-montos btn-danger"><i class="fa fa-minus"></i></button></td>
													</tr>
													<? foreach ($this->request->data['VentaTransaccion'] as $it => $t) : ?>
														<tr>
															<td>
																<?=$this->Form->hidden('VentaTransaccion.'.$it.'.id', array('disabled' => true)); ?>
																<?=$this->Form->hidden('VentaTransaccion.'.$it.'.fecha', array('disabled' => true, 'value' => date('Y-m-d'))); ?>
																<?=$this->Form->input('VentaTransaccion.'.$it.'.nombre', array('disabled' => true, 'class' => 'form-control not-blank', 'placeholder' => 'Ingrese Voucher/Alias/N° transacción'));?>		
															</td>
															<td><?=$this->Form->input('VentaTransaccion.'.$it.'.monto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank is-number js-monto-pago', 'placeholder' => 'Ingrese monto pagado'));?></td>
															<td valign="center"><button class="pago_tr duplicate_tr btn-success"><i class="fa fa-plus"></i></button></td>
														</tr>
													<? endforeach; ?>
												</tbody>
											</table>
										</td>
									</tr>
								</table>
							</div>
							
							<div class="panel panel-primary">
								<div class="panel-heading">
									<h5 class="panel-title"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?= __('Resumen');?></h5>
								</div>
								<div class="panel-body">
									
									<div class="col-xs-12 col-md-4">
										<a href="#" class="tile tile-info">
			                                $<span id="total-resumen">0</span>
			                                <p>Total a pagar</p>
		                            	</a>
									</div>
									<div class="col-xs-12 col-md-4">
										<a href="#" class="tile tile-success">
			                                $<span id="pagado-resumen">0</span>
			                                <p>Total pagado</p>
		                            	</a>
									</div>
									<div class="col-xs-12 col-md-4">
										<a href="#" class="tile tile-primary">
			                                $<span id="vuelto-resumen">0</span>
			                                <p>Vuelto</p>
		                            	</a>
									</div>	

								</div>
							</div>	

                        </div>

                        <!--<div id="step-3">   
							
							

                            

                        </div>

                        <div id="step-4">
                        	
							<div class="row mt-5">
								<div class="col-xs-12 col-md-5">
		                        	<div class="panel panel-default">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-usd" aria-hidden="true"></i> <?= __('Medio de pago');?></h5>
										</div>
										<div class="panel-body">
											<div class="form-group">
												<label><?=__('Medio de pago');?></label>
												
				                        	</div>

				                        	
											
											
										</div>
		                        	</div>
								</div>
								<div class="col-xs-12 col-md-7">
									
									<div class="panel panel-default">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-money" aria-hidden="true"></i> <?= __('Pagos');?></h5>
											<ul class="panel-controls">
						                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
						                    </ul>
										</div>
										<div class="panel-body">

											<div class="table-responsive">
												<table class="table table-bordered">
													<caption>Registre voucher, efectivo o n° de transacción</caption>
													<thead>
														<th><?=__('Voucher/Alias/N° transacción');?></th>
														<th><?=__('Monto');?></th>
													</thead>
													<tbody class="js-pagos-wrapper">
														<tr class="hidden clone-tr">
															<td>
																<?=$this->Form->hidden('VentaTransaccion.999.fecha', array('disabled' => true, 'value' => date('Y-m-d'))); ?>
																<?=$this->Form->input('VentaTransaccion.999.nombre', array('disabled' => true, 'class' => 'form-control not-blank', 'placeholder' => 'Ingrese Voucher/Alias/N° transacción'));?>		
															</td>
															<td><?=$this->Form->input('VentaTransaccion.999.monto', array('disabled' => true, 'type' => 'text', 'class' => 'form-control not-blank is-number js-monto-pago', 'placeholder' => 'Ingrese monto pagado'));?></td>
															<td valign="center"><button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									
									
									<div class="panel panel-default panel-toggled">
										<div class="panel-heading">
											<h3 class="panel-title"><i class="fa fa-envelope" aria-hidden="true"></i> ¿Deseas agregar un mensaje?</h3>
											<ul class="panel-controls">
												<li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
						                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
						                    </ul>
										</div>
										<div class="panel-body">

											<div class="table-responsive">
												<table class="table table-bordered">
													<caption>El mensaje no se enviará al cliente. Solo se guardará en el pedido.</caption>
													<thead>
														<tr>
															<th><?=__('Asunto');?></th>
															<th><?=__('Mensaje');?></th>
															<th><?=__('Fecha');?></th>
														</tr>
													</thead>
													<tbody class="">
														<tr class="hidden clone-tr">
															<td><?=$this->Form->input('VentaMensaje.999.asunto', array('disabled' => true, 'class' => 'form-control not-blank', 'placeholder' => 'Ingrese un asunto'))?></td>
															<td><?=$this->Form->input('VentaMensaje.999.mensaje', array('disabled' => true, 'class' => 'form-control not-blank', 'placeholder' => 'Ingrese mensaje'));?></td>
															<td><?=$this->Form->hidden('VentaMensaje.999.fecha', array('disabled' => true, 'class' => '', 'value' => date('Y-m-d H:i:s')));?><?=date('Y-m-d H:i:s');?></td>
															<td valign="center"><button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>

								</div>
							</div>

							<div class="row mt-5 mb-5">
								<div class="col-xs-12 col-md-4 mb-5 mt-5">
									<a href="#" class="tile tile-info">
										<p>Total a pagar</p>
		                                $<span id="total-pagar">0</span>
		                            </a>
		                        </div>
		                        <div class="col-xs-12 col-md-4 mb-5 mt-5">
									<a href="#" class="tile tile-info">
										<p>Total a pagar</p>
		                                $<span id="total-pagar">0</span>
		                            </a>
		                        </div>
                        	</div>

                        </div>

                        <div id="step-5">
							
							<div id="detalle-productos">
							</div>

							<div id="totales">
				

							</div>

                        </div>-->
                                                                                                                                
                    </div>
					
					<?= $this->Form->end(); ?>
				
				</div>
			</div>
		</div>
	</div>




<!-- Modal crear cliente -->
<div class="modal fade" id="modalCrearCliente" tabindex="-1" role="dialog" aria-labelledby="modalCrearClienteLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?= $this->Form->create('VentaCliente', array('class' => 'form-horizontal js-formulario js-ajax-form', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => false))); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalCrearClienteLabel"><i class="fa fa-user-plus"></i> Crear cliente</h4>
      </div>
      <div class="modal-body">
      	
      		<div class="table-responsive">
				<table class="table table-bordered">	
					<tr>
						<th>Nombres</th>
						<td><?=$this->Form->input('nombre', array('class' => 'form-control not-blank', 'placeholder' => 'Ingrese nombres del cliente'))?></td>
					</tr>
					<tr>
						<th>Apellidos</th>
						<td><?=$this->Form->input('apellido', array('class' => 'form-control not-blank', 'placeholder' => 'Ingrese apellidos del cliente'))?></td>
					</tr>
					<tr>
						<th>Rut/Pasaporte</th>
						<td><?=$this->Form->input('rut', array('class' => 'form-control is-rut not-blank', 'placeholder' => 'Ingrese Rut/Pasaporte del cliente'))?></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><?=$this->Form->input('email', array('class' => 'form-control is-email not-blank', 'placeholder' => 'Ingrese email del cliente'))?></td>
					</tr>
					<tr>
						<th>Fono (opcional)</th>
						<td><?=$this->Form->input('telefono', array('class' => 'form-control is-number', 'placeholder' => 'Ingrese fono del cliente'))?></td>
					</tr>
				</table>
      		</div>

      		<div class="alert alert-danger hidden">
				<span id="error-mensaje"></span>
			</div>

			<div class="alert alert-success hidden">
				<span id="success-mensaje"></span>
			</div>
      	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Crear cliente</button>
      </div>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>
<!-- Fin modal cliente -->

<? endif; ?>