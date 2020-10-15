<div class="page-title">
    <h2>
        <span class="fa fa-usd"></span> Pago #<?=$pago['Pago']['id'];?> 
        <?= ($pago['Pago']['pagado']) ? '<label class="label label-success label-form">PAGADO</label>' : '' ; ?> 
        <?= (!$pago['Pago']['pagado'] && !empty($pago['Pago']['fecha_pago'])) ? '<label class="label label-info label-form">AGENDADO</label>' : '' ; ?>
        <?= (!$pago['Pago']['pagado'] && empty($pago['Pago']['fecha_pago'])) ? '<label class="label label-warning label-form">PENDIENTE CONFIGURACIÓN</label>' : '' ; ?>
    </h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-primary">
            <div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list" aria-hidden="true"></i> Detalles del pago</h3>
				</div>
                <div class="panel-body">
                    <div class="table-reponsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>
                                    Método de pago
                                </th>
                                <td>
                                    <?=$pago['Moneda']['nombre'];?>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Identificador del pago
                                </th>
                                <td>
                                    <?=$pago['Pago']['identificador'];?>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Cuenta bancaria
                                </th>
                                <td>
                                    <?=$pago['CuentaBancaria']['alias'];?>
                                </td>
                            </tr>
                                <th>
                                    Monto del pago
                                </th>
                                <td>
                                    <?= CakeNumber::currency(h($pago['Pago']['monto_pagado']), 'CLP'); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Fecha de pago
                                </th>
                                <td>
                                    <?= h($pago['Pago']['fecha_pago']); ?>
                                </td>
                            </tr>
                            <? if (!empty($pago['Pago']['orden_compra_adjunto_id'])) : ?>
                            <tr>
                                <th>
                                    Documento relacionado
                                </th>
                                <td>
                                    <?= $this->Html->link('<i class="fa fa-file"></i> Ver documento', sprintf('%simg/OrdenCompraAdjunto/%d/%s', $this->webroot, $pago['Pago']['orden_compra_adjunto_id'], $pago['OrdenCompraAdjunto']['adjunto'] ), array('class' => 'btn btn-xs btn-info', 'target' => '_blank', 'escape' => false)); ?>
                                </td>
                            </tr>
                            <? endif; ?>
                            <? if (!empty($pago['Pago']['adjunto'])) : ?>
                            <tr>
                                <th>
                                    Documento adjunto
                                </th>
                                <td>
                                <?= $this->Html->link('<i class="fa fa-file"></i> Ver documento', sprintf('%simg/Pago/%d/%s', $this->webroot, $pago['Pago']['id'], $pago['Pago']['adjunto'] ), array('class' => 'btn btn-xs btn-info', 'target' => '_blank', 'escape' => false)); ?>
                                </td>
                            </tr>
                            <? endif; ?>
                            <tr>
                                <th>
                                    Estado
                                </th>
                                <td>
                                <?= ($pago['Pago']['pagado'] ? '<label class="label label-success"><i class="fa fa-check"></i> Pagado</label>' : '<label class="label label-danger"><i class="fa fa-close"></i> No pagado</label>'); ?>&nbsp;
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
			<div class="panel panel-primary">
            <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> 
                facturas relacionadas</h3>
				</div>
                <div class="panel-body">
                    <div class="table-reponsive">
                        <table class="table table-bordered table-middle">
                            <th>Folio</th>
                            <th>Monto facturado</th>
                            <th>Monto pagado</th>
                            <th>Estado</th>
                            <th>OC</th>
                            <th>Proveedor</th>
                            <th>Creada</th>

                            <tbody>
                            <? if (!empty($pago['OrdenCompraFactura'])) : ?>
                            <? foreach ($pago['OrdenCompraFactura'] as $factura) : ?>
                                <tr>
                                    <td><?=$this->Html->link($factura['folio'], array('controller' => 'ordenCompraFacturas', 'action' => 'view', $factura['id']), array('target' => '_blank')); ?></td>
                                    <td><?= CakeNumber::currency(h($factura['monto_facturado']), 'CLP'); ?></td>
                                    <td><?= CakeNumber::currency(h($factura['monto_pagado']), 'CLP'); ?></td>
                                    <td><?= ($factura['pagada'] ? '<label class="label label-success"><i class="fa fa-check"></i> Pagada</label>' : '<label class="label label-danger"><i class="fa fa-close"></i> No pagada o agendada</label>'); ?></td>
                                    <td><?=$this->Html->link($factura['orden_compra_id'], array('controller' => 'ordenCompras', 'action' => 'view', $factura['orden_compra_id']), array('target' => '_blank')); ?></td>
                                    <td><?=$factura['Proveedor']['nombre']; ?></td>
                                    <td><?=$factura['created']; ?></td>
                                </tr>
                            <? endforeach; ?>
                            <? else : ?>
                                <tr>
                                    <td colspan="6">No registra facturas asociadas</td>
                                </tr>
                            <? endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer">
                    <? if (!empty($pago['OrdenCompraFactura'])) : ?>
                        <?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar facturas', array('action' => 'exportar_facturas', $pago['Pago']['id']), array('class' => 'btn btn-xs btn-primary btn-block', 'rel' => 'tooltip', 'title' => 'Exportar facturas', 'escape' => false)); ?>
                    <? endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>