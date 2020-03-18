<div class="page-title">
	<h2 style="text-transform: capitalize;"><?= $titulo_index; ?></h2>
	<div class="btn-group pull-right">
	<? if ($permisos['add']) :  ?>
		<?= $this->Html->link('<i class="fa fa-plus"></i> Nueva OC Ventas', array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
		<?= $this->Html->link('<i class="fa fa-hand-pointer-o"></i> Nueva OC Manual', array('action' => 'add_manual'), array('class' => 'btn btn-success', 'escape' => false)); ?>
	<? endif; ?>
	</div>
</div>

<div class="page-content-wrap">
    
    <?=$this->element('ordenCompras/acceso_rapido');?>

    <div class="row">
        <div class="col-xs-12">
            <?=$this->element('ordenCompras/filtro');?>
        </div>
    </div>

	<div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Listado de ordenes de compra</h3>
                    <div class="btn-group pull-right">
                    <? if ($permisos['add']) :  ?>
                        <?= $this->Html->link('<i class="fa fa-plus"></i> Nueva OC Ventas', array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                        <?= $this->Html->link('<i class="fa fa-hand-pointer-o"></i> Nueva OC Manual', array('action' => 'add_manual'), array('class' => 'btn btn-success', 'escape' => false)); ?>
                    <? endif; ?>
                        
                    <? $export = array(
                        'action' => 'exportar'
                        );

                    if (isset($this->request->params['named'])) {
                        $export = array_replace_recursive($export, $this->request->params['named']);
                    }?>

                    <?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', $export, array('class' => 'btn btn-primary', 'escape' => false)); ?>

                    </div>
                </div>
                <div class="panel-body">
                    <?=$this->element('contador_resultados'); ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="sort">
                                    <th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('administrador_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('proveedor_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('estado', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('oc_manual', 'OC Manual', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('created', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $ordenCompras as $ordenCompra ) : ?>

                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'creada') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array('editar', 'cancelar')));?>
                                    <? endif; ?>
                
                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'validacion_comercial') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array('revisar')));?>
                                    <? endif; ?>

                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'asignacion_metodo_pago') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array('asignar_pagar')));?>
                                    <? endif; ?>

                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'validacion_externa') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array('notificar_proveedor')));?>
                                    <? endif; ?>

                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'pago_finanzas') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array('pagar')));?>
                                    <? endif; ?>

                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'espera_recepcion') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array('recepcionar', 'stock', 'retiro')));?>
                                    <? endif; ?>

                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'recepcion_incompleta') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array('completar', 'stock', 'retiro')));?>
                                    <? endif; ?>

                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'espera_dte') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array('completar')));?>
                                    <? endif; ?>

                                    <? if ($ordenCompra['OrdenCompra']['estado'] == 'recepcion_completa') : ?>
                                    <?=$this->element('ordenCompras/index_tr', array('ordenCompra' => $ordenCompra, 'accion' => array()));?>
                                    <? endif; ?>
                                
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="pull-right">
                        <ul class="pagination">
                            <?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
                            <?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 10, 'currentClass' => 'active', 'separator' => '')); ?>
                            <?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>