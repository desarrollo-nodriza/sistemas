<div class="page-title">
	<h2><span class="fa fa-pencil"></span> Modificar pago</h2>
</div>
<?= $this->Form->create('Pago', array('class' => 'form-horizontal js-validate-pago', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>	
<?= $this->Form->input('id');?>

<? 
if (!empty($this->request->data['Pago']['orden_compra_id'])) {
    $id_oc = $this->request->data['Pago']['orden_compra_id'];
}else{
    $id_oc = Hash::extract($this->request->data['OrdenCompraFactura'], '{n}.orden_compra_id')[0];
}
?>
<?= $this->Form->hidden('orden_compra_id', array('value' => $id_oc)); ?>
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
                                <td>
                                    <?= $this->Form->label('moneda_id', 'Método de pago');?>
                                </td>
                                <td>
                                    <?= $this->Form->select('moneda_id', $monedas, array('default' => $this->request->data['Pago']['moneda_id'], 'class' => 'form-control js-select-medio-pago', 'empty' => 'Seleccione', 'disabled' => ($this->request->data['Pago']['pagado']) ? true : false  )); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->label('identificador', 'Identificador del pago');?>
                                </td>
                                <td>
                                    <?= $this->Form->input('identificador', array('type' => 'text','value' => $this->request->data['Pago']['identificador'], 'class' => 'form-control js-identificador-pago', 'disabled' => ($this->request->data['Pago']['pagado']) ? true : false )); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->label('cuenta_bancaria_id', 'Cuenta bancaria');?>
                                </td>
                                <td>
                                    <?= $this->Form->select('cuenta_bancaria_id', $cuenta_bancarias, array('default' => $this->request->data['Pago']['cuenta_bancaria_id'], 'class' => 'form-control js-cuenta-pago', 'empty' => 'Seleccione cuenta', 'disabled' => ($this->request->data['Pago']['pagado']) ? true : false )); ?>
                                </td>
                            </tr>
                                <td>
                                    <?= $this->Form->label('monto_pagado', 'Monto del pago');?>
                                </td>
                                <td>
                                    <?= $this->Form->input('monto_pagado', array('type' => 'text','value' => $this->request->data['Pago']['monto_pagado'], 'class' => 'form-control not-blank is-number js-monto-pagado', 'disabled' => ($this->request->data['Pago']['pagado']) ? true : false )); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->label('fecha_pago', 'Fecha de pago');?>
                                </td>
                                <td>
                                    <?= $this->Form->input('fecha_pago', array('type' => 'text', 'value' => $this->request->data['Pago']['fecha_pago'], 'class' => 'form-control not-blank datepicker js-agendar', 'disabled' => ($this->request->data['Pago']['pagado']) ? true : false )); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->label('orden_compra_adjunto_id', 'Documento relacionado');?>
                                </td>
                                <td>
                                <? if (!empty($this->request->data['Pago']['orden_compra_adjunto_id'])) : ?>
                                    <?= $this->Html->link('<i class="fa fa-file"></i> Ver documento', sprintf('%simg/OrdenCompraAdjunto/%d/%s', $this->webroot, $this->request->data['Pago']['orden_compra_adjunto_id'], $this->request->data['OrdenCompraAdjunto']['adjunto'] ), array('class' => 'btn btn-xs btn-info', 'target' => '_blank', 'escape' => false)); ?>
                                <? else : ?>
                                    --
                                <? endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->label('adjunto', 'Documento adjunto');?>
                                </td>
                                <? if (!empty($this->request->data['Pago']['adjunto'])) : ?>
                                    <td>
                                        <?= $this->Html->link('<i class="fa fa-file"></i> Ver documento', sprintf('%simg/Pago/%d/%s', $this->webroot, $this->request->data['Pago']['id'], $this->request->data['Pago']['adjunto'] ), array('class' => 'btn btn-xs btn-info', 'target' => '_blank', 'escape' => false)); ?>
                                    </td>
                                <? else : ?>
                                    <td>
                                        <?= $this->Form->input('adjunto', array('type' => 'file', 'class' => 'js-comprobante', 'disabled' => ($this->request->data['Pago']['pagado']) ? true : false )); ?>
                                    </td>
                                <? endif; ?>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->label('pagado', 'Finalizar pago');?>
                                </td>
                                <td>
                                    <?=$this->Form->input('pagado', array('class' => '', 'type' => 'checkbox', 'checked' => $this->request->data['Pago']['pagado'], 'disabled' => ($this->request->data['Pago']['pagado']) ? true : false)); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="button-group pull-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Guardar cambios</button>
                        <?= $this->Html->link('<i class="fa fa-undo"></i> Volver', array('action' => 'index'), array('class' => 'btn btn-danger', 'escape' => false)); ?>
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
                            <? if (!empty($this->request->data['OrdenCompraFactura'])) : ?>
                            <? foreach ($this->request->data['OrdenCompraFactura'] as $factura) : ?>
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
                    <? if (!empty($this->request->data['OrdenCompraFactura'])) : ?>
                        <?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar facturas', array('action' => 'exportar_facturas', $this->request->data['Pago']['id']), array('class' => 'btn btn-xs btn-primary btn-block', 'rel' => 'tooltip', 'title' => 'Exportar facturas', 'escape' => false)); ?>
                    <? endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->Form->end(); ?>