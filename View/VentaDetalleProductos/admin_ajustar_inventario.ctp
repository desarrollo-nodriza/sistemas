<div class="page-title">
	<h2><span class="fa fa-cogs"></span> Ajustar inventario</h2>
</div>

<?= $this->Form->create('VentaDetalleProducto', array('inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate-producto js-formulario')); ?>
<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-cogs" aria-hidden="true"></i> Ajustar inventario</h3>
				</div>
				<div class="panel-body">
					
					<div id="wizard-ajuste" class="wizard show-submit wizard-validation">
                        <ul>
                            <li>
                                <a href="#step-1">
                                    <span class="stepNumber">1</span>
                                    <span class="stepDesc">Tipo de ajuste<br /><small>Seleccione como desea ajustar el inventario</small></span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-2">
                                    <span class="stepNumber">2</span>
                                    <span class="stepDesc">Ajustar<br /><small>Completar el ajuste de inventario</small></span>
                                </a>
                            </li>
                        </ul>

                        <div id="step-1">
							
							<h3>Seleccione una opción para continuar</h3>

                            <div class="form-group col-md-4">
                            	<div class="col-xs-12 col-md-6">
	                                <label class="check">
	                                    <input id="VentaDetalleProductoTipoAjusteAjusteNormal" type="radio" name="tipo_ajuste" value="ajuste_normal" checked="true" class="iradio">
	                                    Ajuste por pmp
	                                </label>
	                            </div>
	                            <div class="col-xs-12 col-md-6">
	                                <label class="check">
	                                    <input id="VentaDetalleProductoTipoAjusteAjustePrecio" type="radio" name="tipo_ajuste" value="ajuste_precio">
	                                    Ajustar por movimiento
	                                </label>
	                            </div>
							</div>            
							
							<div id="lista-movimientos" class="col-xs-12 hidden">
							<? if (!empty($movimientosBodega)) : ?>
					        <div class="table-responsive" style="max-height: 352px; overflow-y: auto;">
								<table class="table table-bordered table-striped datatable">
									<thead>
										<th></th>
										<th><?=__('Id');?></th>
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
											<td>
											<? if ($movimiento['io'] == 'IN') : ?>
												<input type="radio" name="movimiento_usar" class="not-blank">
											<? else : ?>
												<input type="radio" name="movimiento_usar" disabled>
											<? endif; ?>
											</td>
											<td>#<?=$movimiento['id']; ?></td>
											<td><?=$movimiento['bodega'];?></td>
											<td><?=$movimiento['io'];?></td>
											<td class="js-costo" data-value="<?=$movimiento['valor'];?>"><?=$this->Number->currency($movimiento['valor'], 'CLP');?></td>
											<td><?=$movimiento['cantidad'];?></td>
											<td><?=$this->Number->currency($movimiento['total'], 'CLP');?></td>
											<td><?=$movimiento['fecha'];?></td>
											<td><?=$movimiento['responsable'];?></td>
											<td><?=$movimiento['glosa'];?></td>
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
                        <div id="step-2">
                            <div class="table-responsive">
								<table class="table table-stripped">
									<thead>
										<th>Bodega</th>
										<th>Costo unitario</th>
										<th>Cantidad actual</th>
										<th>Nueva cantidad</th>
										<th>Glosa (opcional)</th>
									</thead>
									<tbody>
									<? foreach ($this->request->data['VentaDetalleProducto']['Total'] as $it => $cant) : ?>
										<tr>
											<td><?=$this->Form->hidden(sprintf('%d.bodega', $it), array('value' => $cant['bodega_id'])); ?><?= $cant['bodega_nombre'];?></td>
											<td>
											<div class="input-group">
                                                <?=$this->Form->input(sprintf('%d.costo', $it), array('value' => $cant['pmp'], 'class' => 'js-costo-pmp form-control not-blank is-number' )); ?>
                                                <span class="input-group-addon js-usar-pmp" data-value="<?= $cant['pmp'];?>" data-toggle="tooltip" title="PMP del producto: <?=$this->Number->currency($cant['pmp'], 'CLP');?>"><span class="fa fa-check"></span> Usar PMP</span>
                                            </div>	
											</td>
											<td><?= $cant['total'];?></td>
											<td><?=$this->Form->input(sprintf('%d.ajustar', $it), array('type'=> "text", 'class' => 'is-number form-control', 'min' => 0));?></td>
											<td><?=$this->Form->textarea(sprintf('%d.glosa', $it), array('class' => 'form-control', 'placeholder' => 'si se deja vacía, se usará la glosa por defecto.', 'row' => 2));?></td>
										</tr>
									<? endforeach; ?>
									</tbody>
								</table>
							</div>
                        </div>
                    </div>

				</div>
				
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>