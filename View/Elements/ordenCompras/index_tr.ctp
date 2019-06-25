<tr>
    <td><?= h($ordenCompra['OrdenCompra']['id']); ?>&nbsp;</td>
    <td><?= h($ordenCompra['Administrador']['nombre']); ?></td>
    <td><?= (!empty($ordenCompra['Proveedor'])) ? $ordenCompra['Proveedor']['nombre'] : 'Sin especificar' ; ?>&nbsp;</td>
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

    <?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-block btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
    
    </td>
</tr>