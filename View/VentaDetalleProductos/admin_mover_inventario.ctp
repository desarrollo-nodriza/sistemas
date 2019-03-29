<div class="page-title">
	<h2><span class="fa fa-list-ol"></span> Movimientos de bodegas</h2>
</div>

<?= $this->Form->create('VentaDetalleProducto', array('inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate-producto')); ?>
<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-arrows" aria-hidden="true"></i> Mover inventario</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-stripped">
							<thead>
								<th>Bodega origen</th>
								<th>Cantidad</th>
								<th>Bodega destino</th>
								<th>Mover</th>
							</thead>
							<tbody>
							<? foreach ($this->request->data['VentaDetalleProducto']['Total'] as $it => $cant) : ?>
								<? if ($cant['total'] > 0) : ?>
								<tr>
									<td><?=$this->Form->hidden(sprintf('%d.bodega_origen', $it), array('value' => $cant['bodega_id'])); ?><?= $cant['bodega_nombre'];?></td>
									<td><?= $cant['total'];?></td>
									<td><?=$this->Form->select(sprintf('%d.bodega_destino', $it), $bodegas, array('empty' => 'Seleccione bodega', 'class' => 'form-control')); ?></td>
									<td><?=$this->Form->input(sprintf('%d.cantidad', $it), array('type'=> "text", 'class' => 'is-number form-control', 'max' => $cant['total'], 'min' => 1));?></td>
								</tr>
								<? endif; ?>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-send" aria-hidden="true"></i> Mover', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>