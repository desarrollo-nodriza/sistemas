<?= $this->Form->create('OrdenCompra', array('inputDefaults' => array('div' => false, 'label' => false))); ?>
<? 
    $id  = (isset($this->request->params['named']['id'])) ? $this->request->params['named']['id'] : '' ;
    $venta  = (isset($this->request->params['named']['venta'])) ? $this->request->params['named']['venta'] : '' ;
    $prod  = (isset($this->request->params['named']['prod'])) ? $this->request->params['named']['prod'] : '' ;
    $prov = (isset($this->request->params['named']['prov'])) ? $this->request->params['named']['prov'] : '' ;
    $sta = (isset($this->request->params['named']['sta'])) ? $this->request->params['named']['sta'] : '' ;
    $dtf = (isset($this->request->params['named']['dtf'])) ? $this->request->params['named']['dtf'] : '' ;
    $dtt = (isset($this->request->params['named']['dtt'])) ? $this->request->params['named']['dtt'] : '' ;
    $ret = (isset($this->request->params['named']['ret'])) ? $this->request->params['named']['ret'] : '' ;
    $bodega_id = (isset($this->request->params['named']['bodega_id'])) ? $this->request->params['named']['bodega_id'] : '' ;

    echo $this->Form->hidden('sta', array('value' => $sta));

?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
    </div>
    <div class="panel-body">
        <div class="form-group col-sm-3 col-xs-12">
                <label>ID OC:</label>
                <?=$this->Form->input('id', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese ID de OC',
                    'value' => $id
                    ));?>
        </div>
        <div class="form-group col-sm-3 col-xs-12">
                <label>ID Venta:</label>
                <? if (!empty($venta)) : ?>
                <div class="input-group" style="max-width: 100%;">
                    <?=$this->Form->input('venta', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese ID de venta',
                    'value' => $venta
                    ));?>
                    <span class="input-group-addon add-on">
                    <?=$this->Html->link('<span class="fa fa-eye"></span>', array('controller' => 'ventas', 'action' => 'view', $venta), array('escape' => false, 'target' => '_blank', 'style' => 'color:#fff !important'));?>
                    </span>
                </div>
                
                <? else : ?>
                <?=$this->Form->input('venta', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'value' => $venta,
                    'placeholder' => 'Ingrese ID de venta'
                    ));?>
                <? endif; ?>
        </div>
        <div class="form-group col-sm-3 col-xs-12">
                <label>ID Producto:</label>
                <? if (!empty($prod)) : ?>
                <div class="input-group" style="max-width: 100%;">
                    <?=$this->Form->input('prod', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese ID de producto',
                    'value' => $prod
                    ));?>
                    <span class="input-group-addon add-on">
                    <?=$this->Html->link('<span class="fa fa-eye"></span>', array('controller' => 'ventaDetalleProductos', 'action' => 'edit', $prod), array('escape' => false, 'target' => '_blank', 'style' => 'color:#fff !important'));?>
                    </span>
                </div>
                
                <? else : ?>
                <?=$this->Form->input('prod', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'value' => $prod,
                    'placeholder' => 'Ingrese ID de producto'
                    ));?>
                <? endif; ?>
        </div>
        <div class="form-group col-sm-3 col-xs-12">
                <label>Proveedor</label>
                <?=$this->Form->select('prov', $proveedores,
                    array(
                    'class' => 'form-control select',
                    'empty' => 'Seleccione Proveedor',
                    'multiple' => true,
                    'value' => $prov,
                    'data-live-search' => true
                    )
                );?>
        </div>
        <div class="form-group col-sm-3 col-xs-12">
                <label>Bodega</label>
                <?=$this->Form->select('bodega_id', $bodegas,
                    array(
                    'class' => 'form-control select',
                    'empty' => 'Seleccione Bodega',
                    'multiple' => true,
                    'value' => $bodega_id,
                    'data-live-search' => true
                    )
                );?>
        </div>
        <div class="form-group col-sm-3 col-xs-12">
                <label>Retirar OC:</label>
                <?=$this->Form->select('ret', array(
                        'si' => 'Retiro marcado',
                        'no' => 'Retiro no marcado'
                    ), array(
                    'class' => 'form-control',
                    'empty' => 'Seleccione',
                    'default' => $ret
                    ));?>
        </div>
        <div class="form-group col-sm-3 col-xs-12">
            <label>Emitidos entre</label>
            <div class="input-group">
                <?=$this->Form->input('dtf', array(
                    'class' => 'form-control datepicker',
                    'type' => 'text',
                    'value' => $dtf
                    ))?>
                <span class="input-group-addon add-on"> - </span>
                <?=$this->Form->input('dtt', array(
                    'class' => 'form-control datepicker',
                    'type' => 'text',
                    'value' => $dtt
                    ))?>
        </div>
    </div>
    <div class="panel-footer">
        <div class="col-xs-12">
            <div class="pull-right">
                <?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Filtrar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
            </div>
            <div class="pull-left">
                <?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>