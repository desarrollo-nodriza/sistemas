<div class="page-title">
	<h2><span class="fa fa-users"></span> <?= $cliente['VentaCliente']['nombre'];?> <?= $cliente['VentaCliente']['apellido'];?></h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-7">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-user"></i> <?= __('Información del cliente'); ?></h3>
				</div>
				<div  class="panel-body">
					<div class="table table-resposive">
						<table class="table table-bordered">	
							<tr>
								<th><?= __('Nombre'); ?></th>
								<td><?= $cliente['VentaCliente']['nombre']; ?></td>
							</tr>
							<tr>
								<th><?= __('Apellido'); ?></th>
								<td><?= $cliente['VentaCliente']['apellido']; ?></td>
							</tr>
							<tr>
								<th><?= __('Rut'); ?></th>
								<td><?= $this->Html->rut($cliente['VentaCliente']['rut']); ?></td>
							</tr>
							<tr>
								<th><?= __('Email'); ?></th>
								<td><?= $cliente['VentaCliente']['email']; ?></td>
							</tr>
							<tr>
								<th><?= __('Fono'); ?></th>
								<td><?= $cliente['VentaCliente']['telefono']; ?></td>
							</tr>
							<tr>
								<th><?= __('Tipo de cliente'); ?></th>
								<td><?= Inflector::humanize($cliente['VentaCliente']['tipo_cliente']); ?></td>
							</tr>
							<? if (!empty($cliente['VentaCliente']['giro_comercial'])) : ?>
							<tr>
								<th><?= __('Giro Comercial'); ?></th>
								<td><?= $cliente['VentaCliente']['giro_comercial']; ?></td>
							</tr>
							<? endif; ?>							
						</table>
					</div>
				</div>
			</div>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-home"></i> <?= __('Direcciones'); ?></h3>
				</div>
				<div  class="panel-body">
					<div class="table table-resposive">
						<table class="table table-bordered">
							<thead>
								<th><?= __('ID');?></th>
								<th><?= __('Alias');?></th>
								<th><?= __('Calle');?></th>
								<th><?= __('Número');?></th>
								<th><?= __('Depto/Oficina');?></th>
								<th><?= __('Comuna');?></th>
								<th><?= __('Fecha creación');?></th>
							</thead>
							<tbody>
							<? if ( empty($cliente['Direccion']) ) : ?>
								<tr>
									<td colspan="7"><p><?= __('No registra información'); ?></p></td>
								</tr>
							<? endif; ?>
							
							<? foreach ($cliente['Direccion'] as $id => $d) : ?>
								<tr>
									<td><?=$d['id'];?></td>
									<td><?=$d['alias'];?></td>
									<td><?=$d['calle'];?></td>
									<td><?=$d['numero'];?></td>
									<td><?=$d['depto'];?></td>
									<td><?=$d['Comuna']['nombre'];?></td>
									<td><?=$d['created'];?></td>
								</tr>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-5">
			
			<a class="tile small tile-success"><?=CakeNumber::currency($cliente['Metricas']['total_comprado'], 'CLP');?> <p>Total comprado</p></a>

			<a class="tile small tile-warning"><?=CakeNumber::currency($cliente['Metricas']['total_cotizado'], 'CLP');?> <p>Total cotizado</p></a>

			<a class="tile small tile-info"><?=$cliente['Metricas']['cantidad_prospectos'];?> <p>Cantidad prospectos</p></a>

			<div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Compras realizadas</h3>
                </div>
                <div class="panel-body">
                	<div class="table-responsive" style="max-height: 470px;">
						<table class="table table-bordered datatable">
							<caption>Cantidades vendidas</caption>
							<thead>
								<th>Id venta</th>
								<th>Estado</th>
								<th>Fecha<br>venta</th>
								<th>Total</th>
								<th>Acciones</th>
							</thead>
							<tbody>
							<? foreach ($cliente['Venta'] as $iv => $venta) : ?>
								<tr>
									<td><?= $this->Html->link($venta['id'], array('controller' => 'ventas', 'action' => 'view', $venta['id']), array('target' => '_blank')); ?></td>
									<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a>&nbsp;</td>
									<td>
										<?= date_format(date_create($venta['fecha_venta']), 'd/m/Y H:i:s'); ?>
										<? if ($venta['picking_estado'] == 'no_definido' && $venta['VentaEstado']['VentaEstadoCategoria']['venta'] && !$venta['VentaEstado']['VentaEstadoCategoria']['final']) : 
										
											$retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['fecha_venta']), 'Y-m-d H:i:s'));

										if (!empty($retrasoMensaje)) : ?>
											<?=$retrasoMensaje;?>
										<?
										endif;
									  endif;?>			
									</td>
									<td><?=CakeNumber::currency($venta['total'], 'CLP');?></td>
									<td>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('controller' => 'ventas', 'action' => 'view', $venta['id']), array('target' => '_blank', 'class' => 'btn btn-info btn-xs', 'escape' => false)); ?>
									</td>
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