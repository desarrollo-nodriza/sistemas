<div id="dashboard">
	<div class="container">
		<div class="row">
			<div class="col-12 d-flex justify-content-between">
				<div class="card mb-3 metrica bg-success text-white">
					<div class="row no-gutters">
						<div class="col-md-4 d-flex justify-content-center align-items-center">
							<span class="metrica-icono"><i class="fa fa-shopping-bag"></i></span>
						</div>
						<div class="col-md-8">
							<div class="card-body">
								<h5 class="card-title">Total comprado</h5>
								<p class="card-text"><?=CakeNumber::currency($cliente['Metricas']['total_comprado'], 'CLP');?></p>
							</div>
						</div>
					</div>
				</div>
				<div class="card mb-3 metrica bg-primary text-white">
					<div class="row no-gutters">
						<div class="col-md-4 d-flex justify-content-center align-items-center">
							<span class="metrica-icono"><i class="fa fa-file-alt"></i></span>
						</div>
						<div class="col-md-8">
							<div class="card-body">
								<h5 class="card-title">Total cotizado</h5>
								<p class="card-text"><?=CakeNumber::currency($cliente['Metricas']['total_cotizado'], 'CLP');?></p>
							</div>
						</div>
					</div>
				</div>
				<div class="card mb-3 metrica bg-secondary text-white">
					<div class="row no-gutters">
						<div class="col-md-4 d-flex justify-content-center align-items-center">
							<span class="metrica-icono"><i class="fa fa-money-bill-alt"></i></span>
						</div>
						<div class="col-md-8">
							<div class="card-body">
								<h5 class="card-title">Prospectos</h5>
								<p class="card-text"><?=$cliente['Metricas']['cantidad_prospectos'];?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-5">
			<div class="col-12">
				<h3><i class="fa fa-shopping-bag mr-1"></i> Últimas compras</h3>
				<div class="table-responsive  mt-4">
					<table class="table table-striped table-hover">
						<caption>Se muestran las últimas 5 compras de <?=count($cliente['Venta']); ?> realizadas. <?=$this->Html->link('<i class="fa fa-shopping-bag mr-1"></i> Ver todo', array('controller' => 'ventas', 'action' => 'compras'), array('class' => 'btn btn-sm btn-secondary float-right', 'escape' => false));?></caption>
						<thead>
							<tr>
								<th scope="col">N° venta</th>
								<th scope="col">Referencia</th>
								<th scope="col">Monto</th>
								<th scope="col">Estado</th>
								<th scope="col" style="width: 125px;">Fecha compra</th>
								<th scope="col">Cant Productos</th>
								<th scope="col">Boleta/Factura</th>
								<th scope="col">Acciones</th>
							</tr>
						</thead>
						<tbody>
						<? if (!empty($cliente['Metricas']['ultimas_ventas'])) : ?>
						<? foreach ($cliente['Metricas']['ultimas_ventas'] as $iv => $venta) : ?>
							<tr>
								<th scope="row">#<?=$venta['id'];?></th>
								<td><?=$venta['referencia'];?></td>
								<td><?=CakeNumber::currency($venta['total'], 'CLP');?></td>
								<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-sm text-white btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a>&nbsp;</td>
								<td><?=$venta['fecha_venta'];?></td>
								<td><?=array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_anulada'));?></td>
								<td>
								<? if (!empty($venta['Dte'])) : ?>
								<? foreach ($venta['Dte'] as $dte) : ?>
									<a href="<?=$dte['public'];?>" class="btn btn-sm btn-info btn-block" target="_blank"><i class="fa fa-file-pdf"></i> Descargar</a>
								<? endforeach; ?>
								<? endif; ?>	
								</td>
								<td><?=$this->Html->link('<i class="fa fa-eye mr-1"></i> Ver detalles', array('controller' => 'ventas', 'action' => 'ver', $venta['id']), array('escape' => false, 'class' => 'btn btn-sm btn-primary')); ?></td>
							</tr>
						<? endforeach; ?>
						<? else : ?>
							<tr>
								<th scope="row" colspan="6">No tienes compras relacionadas.</th>
							</tr>
						<? endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="row mt-5">
			<div class="col-12">
				<h3><i class="fa fa-file-alt mr-1"></i> Últimas cotizaciones</h3>
				<div class="table-responsive  mt-4">
					<table class="table table-striped table-hover">
						<caption>Se muestran las últimas 5 cotizaciones. <?=$this->Html->link('<i class="fa fa-shopping-bag mr-1"></i> Ver todo', array('controller' => 'ventas', 'action' => 'compras'), array('class' => 'btn btn-sm btn-secondary float-right', 'escape' => false));?></caption>
						<thead>
							<tr>
								<th scope="col">N° cotización</th>
								<th scope="col">Fecha</th>
								<th scope="col">Monto</th>
								<th scope="col">Cant Productos</th>
								<th scope="col">Acciones</th>
							</tr>
						</thead>
						<tbody>
						<? if (!empty($cotizaciones)) : ?>
						<? foreach ($cotizaciones as $ic => $coti) : ?>
							<tr>
								<th scope="row"><?=$coti['id'];?></th>
								<td><?=$coti['created'];?></td>
								<td><?=CakeNumber::currency($coti['total_bruto'], 'CLP');?></td>
								<td><a href="<?=$coti['archivo'];?>" class="btn btn-sm btn-info btn-block" target="_blank"><i class="fa fa-file-pdf"></i> Descargar</a></td>
								<td><?=$this->Html->link('<i class="fa fa-eye mr-1"></i> Ver detalles', array('controller' => 'ventas', 'action' => 'ver', $venta['id']), array('escape' => false, 'class' => 'btn btn-sm btn-primary')); ?></td>
							</tr>
						<? endforeach; ?>
						<? else : ?>
							<tr>
								<th scope="row" colspan="6">No tienes cotizaciones relacionadas.</th>
							</tr>
						<? endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>