<div class="page-title">
	<h2><span class="fa fa-list"></span> Resumen órden de compra</h2>
	<div class="btn-group pull-right">
	<? if ($permisos['add']) :  ?>
		<?= $this->Html->link('<i class="fa fa-plus"></i> Nueva OC Ventas', array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
		<?= $this->Html->link('<i class="fa fa-hand-pointer-o"></i> Nueva OC Manual', array('action' => 'add_manual'), array('class' => 'btn btn-success', 'escape' => false)); ?>
	<? endif; ?>
	</div>
</div>

<div class="page-content-wrap">
	
	<?=$this->element('link_ordencompras');?>

	<div class="row">

		<div class="col-xs-12">

			<div class="panel panel-info">
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped datatable">
							<thead>
								<tr>
									<th>Proveedor / Estado</th>
								<?  foreach ($estados as $slug => $estado) : ?>
									<th><a class="label label-form label-<?=$this->Html->colorOc($slug);?>"><?=$estado; ?></a></th>
								<? endforeach; ?>
								</tr>
							</thead>
							<tbody>
							<?  foreach ($matriz['proveedor'] as $x) : ?>
								<tr>
									<td><?=$x['nombre']; ?></td>
								<? foreach ($x['total'] as $slug => $y) : ?>
									<td><span class="center-block text-center text-<?=$this->Html->colorOc($slug);?>"><?=$y;?></span></td>
								<? endforeach; ?>
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