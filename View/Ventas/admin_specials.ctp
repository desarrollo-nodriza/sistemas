<div class="page-title">
	<h2><span class="fa fa-money"></span> Ventas con observaciones</h2>
</div>

<div class="page-content-wrap">
	<div class="row">

		<div class="col-xs-12">

			<div class="panel panel-default">

				<div class="panel-heading">

					<h3 class="panel-title">Listado de Ventas con observaciones</h3>
					
				</div>

				<div class="panel-body">
					
					
					<?= $this->element('contador_resultados', array('col' => false)); ?>
						

					<div class="table-responsive">

						<table class="table table-hover">

							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id', 'ID', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('fecha_venta', 'Fecha', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('total', 'Total', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('venta_estado_categoria_id', 'Estado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('picking_estado', 'Picking', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('marketplace_id', 'Canal de venta', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th style="width: 120px"><?= $this->Paginator->sort('cliente_id', 'Cliente', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ( $ventas as $ix => $venta ) : ?>

									<tr>

										<td>
											ID: <strong><?= h($venta['Venta']['id']); ?></strong>
											<?php
												if (!empty($venta['Venta']['id_externo'])) {
													echo "<br />";
													echo "Ext: <strong>" .$venta['Venta']['id_externo']. "</strong>";
												}
												if (!empty($venta['Venta']['referencia'])) {
													echo "<br />";
													echo "Ref: <strong>" .$venta['Venta']['referencia']. "</strong>";
												}
											?>
											&nbsp;
										</td>

										<td>
											
											<?= date_format(date_create($venta['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?>
											<? if ($venta['Venta']['picking_estado'] == 'no_definido' && $venta['VentaEstado']['VentaEstadoCategoria']['venta'] && !$venta['VentaEstado']['VentaEstadoCategoria']['final'] ) : 
												
												$retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['Venta']['fecha_venta']), 'Y-m-d H:i:s'));

												if (!empty($retrasoMensaje)) : ?>
													<?=$retrasoMensaje;?>
												<?
												endif;
											  endif;?>	
										</td>

										<td><label class="label label-form label-<?=($venta['Venta']['total'] > 0) ? 'success' : '' ; ?>"><?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?>&nbsp;</label></td>

										<td>
											<a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a>&nbsp;
										</td>
										
										<td>
											<span class="btn btn-xs btn" style="color: #fff; background-color: <?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['color'];?>"><?=ClassRegistry::init('Venta')->picking_estado[$venta['Venta']['picking_estado']]['label'];?></span>
										</td>

										<td>
											<? if($venta['Venta']['venta_manual']) : ?>
												<?= (!empty($venta['Venta']['marketplace_id'])) ? $venta['Marketplace']['nombre'] : 'Pos de Venta' ; ?>&nbsp;</td>
											<? else : ?>
												<?= (!empty($venta['Venta']['marketplace_id'])) ? $venta['Marketplace']['nombre'] : $venta['Tienda']['nombre'] ; ?>&nbsp;</td>
											<? endif; ?>
										<td>
											<?php

												$cliente = $venta['VentaCliente']['nombre'];

												if (!empty($venta['VentaCliente']['apellido'])) {
													$cliente.= " " .$venta['VentaCliente']['apellido'];
												}
												if (!empty($venta['VentaCliente']['rut'])) {
													$cliente.= "<br />";
													$cliente.= $venta['VentaCliente']['rut'];
												}
												if (!empty($venta['VentaCliente']['email']) && empty($venta['Venta']['marketplace_id'])) {
													$cliente.= "<br />";
													$cliente.= $venta['VentaCliente']['email'];
												}
												if (!empty($venta['VentaCliente']['telefono'])) {
													$cliente.= "<br />";
													$cliente.= $venta['VentaCliente']['telefono'];
												}

												echo $cliente;

											?>
											&nbsp;
										</td>

										<td> 
											
											<?= $this->Html->link('<i class="fa fa-eye"></i> Ver Detalles', array('action' => 'view', $venta['Venta']['id']), array('class' => 'btn btn-xs btn-info btn-block', 'rel' => 'tooltip', 'title' => 'Ver detalles de este registro', 'escape' => false, 'target' => '_blank')); ?>
											<button class="btn btn-xs btn-block btn-primary btn-expandir-venta" data-toggle="collapse" data-target="#accordion<?=$venta['Venta']['id'];?>"><i class="fa fa-expand"></i> Expandir</button>						

										</td>

									</tr>

									<tr>
										<td colspan="8">
											<div id="accordion<?=$venta['Venta']['id']; ?>" class="collapse">
												<div class="row">
													<div class="col-xs-12">
														<h5><i class="fa fa-clock-o"></i> Productos en espera</h5>
													</div>
													<div class="col-xs-12">
														<div class="table-responsive">
															<table class="table table-bordered">
																<tr>
																	<th>Producto</th>
																	<th>Cantidad vendida</th>
																	<th>Cantidad reservada</th>
																	<th>Cantidad en espera</th>
																	<th>Fecha llegada</th>
																</tr>
																<? if (count(Hash::extract($venta['VentaDetalle'], '{n}[cantidad_en_espera>0].id'))) : ?>
																<? foreach ($venta['VentaDetalle'] as $ivd => $vd) : ?>
																<? if ($vd['cantidad_en_espera'] > 0) : ?>
																<tr>
																	<td><?=$vd['VentaDetalleProducto']['nombre'];?></td>
																	<td><?=$vd['cantidad']; ?></td>
																	<td><?=$vd['cantidad_reservada']; ?></td>
																	<td><?=$vd['cantidad_en_espera']; ?></td>
																	<td><?=$vd['fecha_llegada_en_espera']; ?></td>
																</tr>
																<? endif;?>
																<? endforeach; ?>
																<? else : ?>
																<tr>
																	<td colspan="5">No hay alertas de agenda</td>
																</tr>
																<? endif; ?>
															</table>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-xs-12">
														<h5><i class="fa fa-exclamation-triangle"></i> Productos con alertas de proveedor</h5>
													</div>
													<div class="col-xs-12">
														<div class="table-responsive">
															<table class="table table-bordered">
																<tr>
																	<th>OC relacionada</th>
																	<th>Producto</th>
																	<th>Cantidad solicitada</th>
																	<th>Cantidad en proveedor</th>
																	<th>Estado</th>
																	<th>Observacion</th>
																</tr>
																<? if (!empty($venta['OrdenCompraDetalle'])) : ?>
																<? foreach ($venta['OrdenCompraDetalle'] as $ipp => $p) : ?>
																<? if (empty($p['estado_proveedor']))
																	continue; ?>
																<tr>
																	<td>#<?=$p['orden_compra_id'];?></td>
																	<td><?=$p['descripcion'];?></td>
																	<td><?=$p['cantidad']; ?></td>
																	<td><?=$p['cantidad_validada_proveedor']; ?></td>
																	<td><?=$p['estado_proveedor']; ?></td>
																	<td><?=$p['nota_proveedor']; ?></td>
																</tr>
																<? endforeach; ?>
																<? else : ?>
																<tr>
																	<td colspan="4">No hay alertas de proveedor</td>
																</tr>
																<? endif;?>
															</table>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>

								<?php endforeach; ?>

							</tbody>

						</table>

					</div>

				</div>

			</div>

		</div>

	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="pull-right">
				<ul class="pagination">
					<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
					<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 10, 'currentClass' => 'active', 'separator' => '')); ?>
					<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<?= $this->Html->script(array(
	'/backend/js/venta.js?v=' . rand()
));?>
<?= $this->fetch('script'); ?>