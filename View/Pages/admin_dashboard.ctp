<div class="page-title">
    <div class="col-xs-12 col-sm-3">
	   <h2><span class="fa fa-tachometer"></span> Inicio</h2>
    </div>
    <div class="col-xs-12 col-sm-9">
    </div>
</div>
<div class="page-content-wrap" id="dashboard">
    <div class="row">
        <div class="col-xs-6 col-sm-4">
            <div class="widget widget-success widget-carousel">
                <div class="owl-carousel" id="ventas_del_periodo">
                	<div>                                    
                        <div class="widget-title">Total Ventas del mes</div>
                        <div class="widget-subtitle">Comercios</div>                                                                       
                        <div class="widget-int"><?=$sumaVentas;?></div>
                    </div>
                    <? foreach ($ventas as $venta) : ?>
                        <div>                                    
                            <div class="widget-title">Total Ventas de mes</div>
                            <div class="widget-subtitle"><?=$venta['tienda']?></div>                                                                       
                            <div class="widget-int"><?=$this->Number->currency($venta['Total'], 'CLP');?></div>
                        </div>
                    <? endforeach; ?>
                </div>                                                        
            </div>
        </div>
        <div class="col-xs-6 col-sm-4">
            <div class="widget widget-info widget-carousel">
                <div class="owl-carousel" id="pedidos_del_periodo">
                    <div>                                    
                        <div class="widget-title">Total Descuentos del mes</div>
                        <div class="widget-subtitle">Comercios</div>                                                                       
                        <div class="widget-int"><?=$sumaDescuentos;?></div>
                    </div>
                    <? foreach ($descuentos as $descuento) : ?>
                        <div>                                    
                            <div class="widget-title">Total Descuento del mes</div>
                            <div class="widget-subtitle"><?=$descuento['tienda']?></div>                                                                       
                            <div class="widget-int"><?=$this->Number->currency($descuento['Total'], 'CLP');?></div>
                        </div>
                    <? endforeach; ?>
                </div>                                                      
            </div>
        </div>
        <div class="col-xs-6 col-sm-4">
            <div class="widget widget-primary widget-carousel">
                <div class="owl-carousel" id="pedidos_del_periodo">
                    <div>                                    
                        <div class="widget-title">Total Pedidos del mes</div>
                        <div class="widget-subtitle">Comercios</div>                                                                       
                        <div class="widget-int"><?=$sumaPedidos;?></div>
                    </div>
                    <? foreach ($pedidos as $pedido) : ?>
                        <div>                                    
                            <div class="widget-title">Total Pedidos del mes</div>
                            <div class="widget-subtitle"><?=$pedido['tienda']?></div>                                                                       
                            <div class="widget-int"><?=$pedido['Total'];?></div>
                        </div>
                    <? endforeach; ?>
                </div>                                                      
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Histórico de Ventas</h3>
                    <?= $this->Form->create('Ventas', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
                    <ul class="panel-controls">      
                        <li><label class="control-label">Rango </label></li>
                        <li>
                            <div class="input-group">
                                <?=$this->Form->input('f_inicio', array('class' => 'form-control datepicker'));?>
                                <span class="input-group-addon add-on"> - </span>
                                <?=$this->Form->input('f_final', array('class' => 'form-control datepicker'));?>
                            </div>
                        </li>
                        <li><label class="control-label">Agrupar</label></li>
                        <li>
                            <div class="input-group">
                                <?=$this->Form->select('agrupar', array('anno' => 'Año', 'mes' => 'Mes', 'dia' => 'Día'), array('empty' => false, 'class' => 'form-control'));?>
                            </div>
                        </li>
                        <li><a id="enviarFormularioVentas" href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                    <?= $this->Form->end(); ?>
                </div>
                <div class="panel-body">
                    <div id="GraficoVentasHistorico" style="height: 300px;">
                        
                    </div>
                </div>                             
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Histórico de Descuentos</h3>
                    <?= $this->Form->create('Descuentos', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
                    <ul class="panel-controls">      
                        <li><label class="control-label">Rango </label></li>
                        <li>
                            <div class="input-group">
                                <?=$this->Form->input('f_inicio', array('class' => 'form-control datepicker'));?>
                                <span class="input-group-addon add-on"> - </span>
                                <?=$this->Form->input('f_final', array('class' => 'form-control datepicker'));?>
                            </div>
                        </li>
                        <li><label class="control-label">Agrupar</label></li>
                        <li>
                            <div class="input-group">
                                <?=$this->Form->select('agrupar', array('anno' => 'Año', 'mes' => 'Mes', 'dia' => 'Día'), array('empty' => false, 'class' => 'form-control'));?>
                            </div>
                        </li>
                        <li><a id="enviarFormularioDescuentos" href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                    <?= $this->Form->end(); ?>
                </div>
                <div class="panel-body">
                    <div id="GraficoDescuentosHistorico" style="height: 200px;">
                        
                    </div>
                </div>                             
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Histórico de Pedidos</h3>
                    <?= $this->Form->create('Pedidos', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
                    <ul class="panel-controls">      
                        <li><label class="control-label">Rango </label></li>
                        <li>
                            <div class="input-group">
                                <?=$this->Form->input('f_inicio', array('class' => 'form-control datepicker'));?>
                                <span class="input-group-addon add-on"> - </span>
                                <?=$this->Form->input('f_final', array('class' => 'form-control datepicker'));?>
                            </div>
                        </li>
                        <li><label class="control-label">Agrupar</label></li>
                        <li>
                            <div class="input-group">
                                <?=$this->Form->select('agrupar', array('anno' => 'Año', 'mes' => 'Mes', 'dia' => 'Día'), array('empty' => false, 'class' => 'form-control'));?>
                            </div>
                        </li>
                        <li><a id="enviarFormularioPedidos" href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                    <?= $this->Form->end(); ?>
                </div>
                <div class="panel-body">
                    <div id="GraficoPedidosHistorico" style="height: 200px;">
                        
                    </div>
                </div>                             
            </div>
        </div>
    </div>
</div>