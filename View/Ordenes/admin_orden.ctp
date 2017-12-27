<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Orden de compra #' . $this->request->data['Orden']['id_order']); ?></h2>
	<div class="pull-right">
		<?= $this->Html->link('<i class="fa fa-file"></i> Generar Dte para esta Orden', array('action' => 'generar', $this->request->data['Orden']['id_order']), array('class' => 'btn btn-warning', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
	</div>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Dte´s emitidos para esta orden</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr class="sort">
									<th><?= __('Folio'); ?></th>
									<th><?= __('Tipo de dte'); ?></th>
									<th><?= __('Rut receptor'); ?></th>
									<th><?= __('Razón Social'); ?></th>
									<th><?= __('Giro'); ?></th>
									<th><?= __('Neto'); ?></th>
									<th><?= __('Iva'); ?></th>
									<th><?= __('Total'); ?></th>
									<th><?= __('Fecha'); ?></th>
									<th><?= __('Estado'); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $this->request->data['Dte'] as $dte ) : ?>
								<tr>
									<td><?= h($dte['folio']); ?>&nbsp;</td>
									<td><?= $this->Html->tipoDocumento[$dte['tipo_documento']]; ?>&nbsp;</td>
									<td><?= h($dte['rut_receptor']); ?>&nbsp;</td>
									<td><?= h($dte['razon_social_receptor']); ?>&nbsp;</td>
									<td><?= h($dte['giro_receptor']); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($dte['neto'], 'CLP'); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($dte['iva'], 'CLP'); ?>&nbsp;</td>
									<td><?= CakeNumber::currency($dte['total'], 'CLP'); ?>&nbsp;</td>
									<td><?= h($dte['fecha']); ?>&nbsp;</td>
									<td><?= $dteestado = (isset($dte['estado'])) ? $this->Html->dteEstado($dte['estado']) : $this->Html->dteEstado() ; ?>&nbsp;</td>
									<td><?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('action' => 'editar', $dte['id'], $this->request->data['Orden']['id_order']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>