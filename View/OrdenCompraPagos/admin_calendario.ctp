<div class="page-title">
	<h2><span class="fa fa-calendar"></span> Calendario de pagos pendientes</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
	<? if (!empty($pagos_retrasados)) : ?>
	<div class="col-xs-12 col-sm-4">
        <div class="widget widget-danger widget-carousel">
            <div class="owl-carousel" id="ventas_del_periodo">
                <? foreach ($pagos_retrasados as $pago) : ?>
                    <div>                                    
                        <div class="widget-title">Pago restrasado <a target="_blank" href="<?=Router::url( '/', true ) . 'ordenCompraPagos/edit/' . $pago['OrdenCompraPago']['id']; ?>">OC #<?=$pago['OrdenCompraPago']['orden_compra_id'];?></a></div>
                        <div class="widget-subtitle">Fecha de pago <?=$pago['OrdenCompraPago']['fecha_pago'];?></div>                                                              
                        <div class="widget-int"><?=$this->Number->currency($pago['OrdenCompraPago']['pago_pendiente'], 'CLP');?></div>
                    </div>
                <? endforeach; ?>
            </div>                                                        
        </div>
    </div>	
	<? endif; ?>
	<? if (!empty($pagos_dia)) : ?>
	<div class="col-xs-12 col-sm-4">
        <div class="widget widget-warning widget-carousel">
            <div class="owl-carousel" id="ventas_del_periodo">
                <? foreach ($pagos_dia as $pago) : ?>
                    <div>                                    
                        <div class="widget-title">Pago de hoy <a target="_blank" href="<?=Router::url( '/', true ) . 'ordenCompraPagos/edit/' . $pago['OrdenCompraPago']['id']; ?>">OC #<?=$pago['OrdenCompraPago']['orden_compra_id'];?></a></div>
                        <div class="widget-subtitle">Fecha de pago <?=$pago['OrdenCompraPago']['fecha_pago'];?></div>                                                              
                        <div class="widget-int"><?=$this->Number->currency($pago['OrdenCompraPago']['pago_pendiente'], 'CLP');?></div>
                    </div>
                <? endforeach; ?>
            </div>                                                        
        </div>
    </div>	
	<? endif; ?>
	<? if (!empty($pagos_mes)) : ?>
	<div class="col-xs-12 col-sm-4">
        <div class="widget widget-success widget-carousel">
            <div class="owl-carousel" id="ventas_del_periodo">
                <? foreach ($pagos_mes as $pago) : ?>
                    <div>                                    
                        <div class="widget-title">Proximo pago <a target="_blank" href="<?=Router::url( '/', true ) . 'ordenCompraPagos/edit/' . $pago['OrdenCompraPago']['id']; ?>">OC #<?=$pago['OrdenCompraPago']['orden_compra_id'];?></a></div>
                        <div class="widget-subtitle">Fecha de pago <?=$pago['OrdenCompraPago']['fecha_pago'];?></div>                                                              
                        <div class="widget-int"><?=$this->Number->currency($pago['OrdenCompraPago']['pago_pendiente'], 'CLP');?></div>
                    </div>
                <? endforeach; ?>
            </div>                                                        
        </div>
    </div>	
	<? endif; ?>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('OrdenCompraPago', array('class' => 'form-horizontal hidden js-validate-pago', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-money"></i> Procesar pagos</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<th>Metodo de pago</th>
								<th>Folio factura</th>
								<th>Identificador del pago</th>
								<th>N° cuenta usada</th>
								<th>Monto pagado</th>
								<th>Monto pendiente</th>
								<th>Fecha de pago</th>
								<th>Adjunto</th>
								<th>Acciones</th>
							</thead>
							<tbody id="pagar-masivo">
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" class="btn btn-success esperar-carga pull-right" autocomplete="off" data-loading-text="Espera un momento..."><i class="fa fa-check"></i> Actualizar pago/s</button>
				</div>
			</div>
			<?= $this->Form->end(); ?>
		</div>
	</div>
	<div class="row">
        <div class="col-md-12">
            <div id="alert_holder"></div>
            <div class="calendar">                                
                <div id="calendario_pagos"></div>                            
            </div>
        </div>
    </div>
</div>