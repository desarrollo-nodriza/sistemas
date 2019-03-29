<div class="page-title">
	<h2><span class="fa fa-cogs"></span> Ajustar inventario</h2>
</div>

<?= $this->Form->create('VentaDetalleProducto', array('inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate-producto')); ?>
<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-cogs" aria-hidden="true"></i> Ajustar inventario</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-stripped">
							<thead>
								<th>Bodega</th>
								<th>Cantidad actual</th>
								<th>Nueva cantidad</th>
							</thead>
							<tbody>
							<? foreach ($this->request->data['VentaDetalleProducto']['Total'] as $it => $cant) : ?>
								<tr>
									<td><?=$this->Form->hidden(sprintf('%d.bodega', $it), array('value' => $cant['bodega_id'])); ?><?= $cant['bodega_nombre'];?></td>
									<td><?= $cant['total'];?></td>
									<td><?=$this->Form->input(sprintf('%d.ajustar', $it), array('type'=> "text", 'class' => 'is-number form-control', 'min' => 0));?></td>
								</tr>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-send" aria-hidden="true"></i> Ajustar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>