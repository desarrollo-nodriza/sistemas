<div class="page-title">
	<h2><span class="fa fa-list"></span> Ordenes de compra</h2>
	<div class="btn-group pull-right">
	<? if ($permisos['add']) :  ?>
		<?= $this->Html->link('<i class="fa fa-plus"></i> Nueva OC Ventas', array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
		<?= $this->Html->link('<i class="fa fa-hand-pointer-o"></i> Nueva OC Manual', array('action' => 'add_manual'), array('class' => 'btn btn-success', 'escape' => false)); ?>
	<? endif; ?>
	</div>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-4">
			<a href="<?=Router::url('/', false);?>ordenCompras/index_no_procesadas">
            <div class="widget widget-danger widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-ban"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=$sin_iniciar;?></div>
                    <div class="widget-title">Sin procesar</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
		</div>
		<div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_revision">
			<div class="widget widget-warning widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-pencil-square-o"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=iniciado].id'));?></div>
                    <div class="widget-title">En revisión</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
		</div>
        <div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_asignacion_moneda">
            <div class="widget widget-primary widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-user"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=validado].id'));?></div>
                    <div class="widget-title">Asignación de m. de pago</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
        </div>
        <div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_validadas">
            <div class="widget widget-info widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-money"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=asignacion_moneda].id'));?></div>
                    <div class="widget-title">Espera de validación</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
        </div>
        <div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_validada_proveedores">
            <div class="widget widget-primary widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-user"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=validado_proveedor].id'));?></div>
                    <div class="widget-title">Espera de pago</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
        </div>
		<div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_pagadas">
			<div class="widget widget-success widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-envelope"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=pagado].id'));?></div>
                    <div class="widget-title">Listas para envio</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
		</div>
		<div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_enviadas">
			<div class="widget widget-primary widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-truck"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=enviado].id'));?></div>
                    <div class="widget-title">Enviadas</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
		</div>	
		<div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_incompletas">
			<div class="widget widget-danger widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-meh-o"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=incompleto].id'));?></div>
                    <div class="widget-title">Incompletas</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
		</div>		

        <div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_pendiente_facturas">
            <div class="widget widget-danger widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-exclamation-circle"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=pendiente_factura].id'));?></div>
                    <div class="widget-title">Pendiente Factura</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
        </div>
        <div class="col-xs-12 col-md-4">
            <a href="<?=Router::url('/', false);?>ordenCompras/index_finalizadas">
            <div class="widget widget-success widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-smile-o"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=recibido].id'));?></div>
                    <div class="widget-title">Completas</div>
                    <div class="widget-subtitle">Pincha AQUÍ para ver</div>
                </div>                         
            </div>
            </a>
        </div>
    </div>
    
	<br>
	<hr>
	<br>
</div>