<?= $this->Form->create('Filtro', array('url' => array('controller' => 'ordenCompras', 'action' => str_replace('admin_', '', $this->request->action)), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
<? 
    $id  = (isset($this->request->params['named']['id'])) ? $this->request->params['named']['id'] : '' ;
    $venta  = (isset($this->request->params['named']['venta'])) ? $this->request->params['named']['venta'] : '' ;
    $prov = (isset($this->request->params['named']['prov'])) ? $this->request->params['named']['prov'] : '' ;
    $dtf = (isset($this->request->params['named']['dtf'])) ? $this->request->params['named']['dtf'] : '' ;
    $dtt = (isset($this->request->params['named']['dtt'])) ? $this->request->params['named']['dtt'] : '' ;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
    </div>
    <div class="panel-body">
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label>ID OC:</label>
                <?=$this->Form->input('id', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'value' => $id
                    ));?>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label>ID Venta:</label>
                <? if (!empty($venta)) : ?>
                <div class="input-group" style="max-width: 100%;">
                    <?=$this->Form->input('venta', array(
                    'type' => 'text',
                    'class' => 'form-control',
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
                    'value' => $venta
                    ));?>
                <? endif; ?>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
            <div class="form-group">
                <label>Proveedor</label>
                <?=$this->Form->select('prov', $proveedores,
                    array(
                    'class' => 'form-control select',
                    'empty' => 'Seleccione Proveedor',
                    'multiple' => true,
                    'value' => $prov
                    )
                );?>
            </div>
        </div>
        <div class="col-sm-3 col-xs-12">
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
    </div>
    <div class="panel-footer">
        <div class="col-xs-12">
            <div class="pull-right">
                <?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Filtrar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
            </div>
            <div class="pull-left">
                <?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index_enviadas'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>