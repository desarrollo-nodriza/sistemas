<tr>
    <td><?= h($ordenCompra['OrdenCompra']['id']); ?>&nbsp;</td>
    <td><?= h($ordenCompra['Administrador']['nombre']); ?></td>
    <td><?= (!empty($ordenCompra['Proveedor'])) ? $ordenCompra['Proveedor']['nombre'] : 'Sin especificar' ; ?>&nbsp;</td>
    <td><?= h($ordenCompra['Bodega']['nombre']); ?>&nbsp;</td>
    <td><?= h($estados[$ordenCompra['OrdenCompra']['estado']]); ?>&nbsp;</td>
    <td><?= ($ordenCompra['OrdenCompra']['oc_manual'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
    <td><?= h($ordenCompra['OrdenCompra']['created']); ?>&nbsp;</td>
    <td>
 
    <? if (in_array('editar', $accion) && $permisos['edit']) : ?>
        <?= $this->Html->link('<i class="fa fa-edit"></i> Editar y enviar', array('action' => 'editsingle', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-warning', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('pagar', $accion) && $permisos['pay']) : ?>
        <?= $this->Html->link('<i class="fa fa-money"></i> Pagar OC', array('action' => 'pay', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-primary', 'rel' => 'tooltip', 'title' => 'Pagar', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('asignar_pagar', $accion) && $permisos['pay']) : ?>
        <?= $this->Html->link('<i class="fa fa-money"></i> Asignar metodo de pago', array('action' => 'asignar_moneda', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-primary', 'rel' => 'tooltip', 'title' => 'Pagar', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('revisar', $accion) && $permisos['validate']) : ?>
        <?= $this->Html->link('<i class="fa fa-pencil"></i> Revisar', array('action' => 'review', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-primary', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('enviar', $accion) && $permisos['send']) : ?>
        <?= $this->Html->link('<i class="fa fa-paper-plane"></i> Enviar OC', array('action' => 'ready', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-danger', 'rel' => 'tooltip', 'title' => 'Recepcionar OC', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('completar', $accion) && $permisos['send']) : ?>
        <?= $this->Html->link('<i class="fa fa-edit"></i> Completar', array('action' => 'reception', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-success', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('recepcionar', $accion) && $permisos['send']) : ?>
        <?= $this->Html->link('<i class="fa fa-undo"></i> Recepcionar', array('action' => 'reception', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-success', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('notificar_proveedor', $accion) && $permisos['send']) : ?>
        <?= $this->Html->link('<i class="fa fa-envelope"></i> Notificar', array('action' => 'notificar_proveedor', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-warning', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('stock', $accion) && $permisos['send']) : ?>
        <?= $this->Html->link('<i class="fa fa-pencil"></i> Validar', array('action' => 'validar_stock_manual', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-warning', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('retiro', $accion) && $ordenCompra['OrdenCompra']['retiro']) : ?>
        <?= $this->Html->link('<i class="fa fa-truck"></i> Quitar retiro', array('action' => 'estado_retiro', $ordenCompra['OrdenCompra']['id'], 0), array('class' => 'btn btn-xs btn-block btn-danger', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
    <? endif; ?>

    <? if (in_array('retiro', $accion) && !$ordenCompra['OrdenCompra']['retiro']) : ?>
        <?= $this->Html->link('<i class="fa fa-truck"></i> Retiro', array('action' => 'estado_retiro', $ordenCompra['OrdenCompra']['id'], 1), array('class' => 'btn btn-xs btn-block btn-primary', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
    <? endif; ?>

    <?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
    
    <? if (in_array('cancelar', $accion) && $permisos['edit']) : ?>
        <button class="btn btn-danger btn-block btn-xs mt-5" data-toggle="modal" data-target="#modal-cancelar-oc-<?=$ordenCompra['OrdenCompra']['id'];?>"><i class="fa fa-trash"></i> Cancelar</button>
        

        <div class="modal fade" id="modal-cancelar-oc-<?=$ordenCompra['OrdenCompra']['id'];?>" tabindex="-1" role="dialog" aria-labelledby="#modal-cancelar-oc-<?=$ordenCompra['OrdenCompra']['id'];?>-label">
          <div class="modal-dialog" role="document">
            <?= $this->Form->create('OrdenCompra', array('url' => array('controller' => 'ordenCompras','action' => 'cancelar', $ordenCompra['OrdenCompra']['id']), 'method' => 'post', 'class' => 'form-horizontal js-validate-oc', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-cancelar-oc-<?=$ordenCompra['OrdenCompra']['id'];?>-label">¿Desea cancelar ésta orden de compra?</h4>
              </div>
              <div class="modal-body">
                <p>Está intentando cancelar la orden de compra #<?=$ordenCompra['OrdenCompra']['id']; ?>. Por favor indique el motivo de la cancelación a continuación:</p>
                <div class="form-group">
                    <?= $this->Form->hidden('id', array('value' => $ordenCompra['OrdenCompra']['id']));?>
                    <?= $this->Form->hidden('estado', array('value' => 'cancelada')); ?>
                    <?= $this->Form->label('razon_cancelada', 'Razón/Motivo (obligatorio)');?>
                    <?= $this->Form->input('razon_cancelada', array('rows' => 3, 'placeholder' => 'Ingrese razón de la cancelación', 'class' => 'form-control not-blank')); ?>
                </div>        
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Volver</button>
                <?= $this->Form->button('Cancelar oc', array('type' => 'submit', 'class' => 'btn btn-success')); ?>
              </div>
            </div>
            <?= $this->Form->end(); ?>
          </div>
        </div>

    <? endif; ?>

    </td>
</tr>