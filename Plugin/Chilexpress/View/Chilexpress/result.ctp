<div class="container">

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3">
			<ul class="tiendas">
				<? foreach ($tiendas as $it => $tienda) : ?>
					<li>
					<?=$this->Html->image($tienda['Tienda']['logo']['path'], array('class' => 'img-responsive img-tienda')); ?>
					</li>
				<? endforeach; ?>
			</ul>
		</div>
	</div>

	
	<div class="row">
		<div class="col-xs-12 col-sm-8 col-sm-offset-2">
			<?=$this->element('tracking_alert');?>
			<div class="panel panel-default">
				<div class="panel-body">
					<h1 class="title"><i class="fa fa-truck"></i> Informacion del seguimiento <?=$tracking_number;?></h1>
					<div class="table-responsive">
						<? if (!empty($tracking)) : ?>
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Estado</th>
									<th>Ubicación</th>
									<th>Fecha y hora</th>
								</tr>
							</thead>
							<tbody>
								<? foreach ($tracking as$it => $track) : ?>
								<tr>
									<td><?=$track[4];?></td>
									<td><?=$track[9];?></td>
									<td><?=$track[5];?></td>
								</tr>
								<? endforeach; ?>
							</tbody>
						</table>
						<? else : ?>
						<table class="table table-bordered table-striped">
							<caption>Esta OT no registra datos de seguimiento</caption>
						</table>
						<? endif; ?>
					</div>
				</div>
				<div class="panel-footer">
					<?=$this->Html->link('<i class="fa fa-repeat"></i> Buscar otro', array('plugin' => false, 'controller' => 'chilexpress', 'action' => 'tracking'), array('class' => 'center-block btn btn-send', 'escape' => false)); ?>
				</div>
			</div>
		</div>
	</div>
</div>