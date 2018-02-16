<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Orden de compra #' . $this->request->data['Orden']['id_order']); ?></h2>
	<div class="pull-right">
		<?= $this->Html->link('<i class="fa fa-trucks"></i> Generar OT para esta Orden', array('action' => 'generar', $this->request->data['Orden']['id_order']), array('class' => 'btn btn-warning', 'rel' => 'tooltip', 'title' => 'Generar Ot', 'escape' => false)); ?>
	</div>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">OT´s emitidas para esta orden</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr class="sort">
									<th><?= __('Transportista'); ?></th>
									<th><?= __('Número OT'); ?></th>
									<th><?= __('Nombre Destiniatario'); ?></th>
									<th><?= __('Comuna destino'); ?></th>
									<th><?= __('Región destino'); ?></th>
									<th><?= __('Barra'); ?></th>
									<th><?= __('Sucursal de Destino'); ?></th>
									<th><?= __('Monto pagado'); ?></th>
									<th><?= __('Fecha creación'); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $this->request->data['OrdenTransporte'] as $ot ) : ?>
								<tr>
									<td><?= Inflector::humanize($ot['transporte']); ?>&nbsp;</td>
									<td><?= h($ot['numero_ot']); ?>&nbsp;</td>
									<td><?= h($ot['nombre_destinatario']); ?>&nbsp;</td>
									<td><?= h($ot['glosa_cobertura']); ?>&nbsp;</td>
									<td><?= $this->Chilexpress->obtenerRegion($ot['codigo_region']); ?>&nbsp;</td>
									<td><?= h($ot['barcode']); ?>&nbsp;</td>
									<td><?= $sd = (!empty($ot['centro_distribucion_destino'])) ? $ot['centro_distribucion_destino'] : 'A domicilio' ; ?>&nbsp;</td>
									<td><?= CakeNumber::currency($this->request->data['Orden']['total_shipping_tax_incl'], 'CLP'); ?>&nbsp;</td>
									<td><?= h($ot['fecha_impresion']); ?>&nbsp;</td>
									<td>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('action' => 'editar', $ot['id'], $this->request->data['Orden']['id_order']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
										<? if (!empty($ot['imagen_etiqueta'])) : ?>
										<?= $this->Html->link('<i class="fa fa-barcode"></i> Ver etiqueta', array('action' => 'verEtiqueta', $ot['imagen_etiqueta'], $ot['numero_ot'], $ot['barcode']), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Ver etiqueta', 'escape' => false)); ?>
										<? endif; ?>
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