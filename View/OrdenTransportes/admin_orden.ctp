<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Orden de compra #' . $this->request->data['Orden']['id_order']); ?></h2>
	<div class="pull-right">
		<? if (empty($this->request->data['OrdenTransporte'])) : ?>
		<?= $this->Html->link('<i class="fa fa-trucks"></i> Generar OT para esta Orden', array('action' => 'generar_chilexpress', $this->request->data['Orden']['id_order']), array('class' => 'btn btn-warning', 'rel' => 'tooltip', 'title' => 'Generar Ot Chilexpress', 'escape' => false)); ?>
		<? endif; ?>
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
									<td><?= h($ot['r_numero_ot']); ?>&nbsp;</td>
									<td><?= h($ot['r_nombre_destinatario']); ?>&nbsp;</td>
									<td><?= h($ot['r_glosa_cobertura']); ?>&nbsp;</td>
									<td><?= $this->Chilexpress->obtenerRegion($ot['r_codigo_region']); ?>&nbsp;</td>
									<td><?= $sd = (!empty($ot['r_centro_distribucion_destino'])) ? $ot['r_centro_distribucion_destino'] : 'A domicilio' ; ?>&nbsp;</td>
									<td><?= CakeNumber::currency($this->request->data['Orden']['total_shipping_tax_incl'], 'CLP'); ?>&nbsp;</td>
									<td><?= h($ot['r_fecha_impresion']); ?>&nbsp;</td>
									<td>
									<? if ($permisos['edit']) : ?>
									<div class="btn-group">
                                        <a href="#" data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="true"><span class="fa fa-cog"></span> Acciones</a>
                                        <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                            <li role="presentation" class="dropdown-header">Seleccione</li>
                                            <? if (!empty($ot['pdf'])) : ?>
                                            <li>
											<a class="btn-imprimir-ot" data-etiqueta="<?= $ot['pdf']; ?>"><i class="fa fa-print"></i> Imprimir etiqueta</a>
											</li>
											<li>
												<a class="btn-imprimir-ot" target="_blank" href="<?= $ot['pdf']; ?>"><i class="fa fa-tag"></i> Ver etiqueta</a>
											</li>
											<? else : ?>
											<li>
												<?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> Generar PDF', array('action' => 'generar_pdf', $this->request->data['Orden']['id_order']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Generar Pdf', 'escape' => false)); ?>
											</li>
											<? endif; ?>
                                            <li><?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('action' => 'view_chilexpress', $this->request->data['Orden']['id_order']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?></li>
                                        </ul>
                                    </div>
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