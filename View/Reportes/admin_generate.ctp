<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-body">
					Reporte generado el <span id="fecha_reporte"><?=date('Y-m-d');?> a las <?=date('H:i:s');?></span>
					<button id="generarGraficoBtn" class="btn btn-success btn-xs pull-right hide"><i class="fa fa-cogs" aria-hidden="true"></i> Re-generar Informe</button>
				</div>
			</div>
		</div>
		<div class="col-xs-6">
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="page-title">
						<h2><span class="fa fa-file-text"></span> <?=$reporte['Reporte']['nombre'];?></h2>
						<h4 class="pull-right" style="margin-top: 9px;">Periodo: <?=$data['f_inicio'];?> - <?=$data['f_final'];?></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
	<span id="reporteId" data-value="<?=$reporte['Reporte']['id'];?>"></span>
	<span id="fechaInicial" data-value="<?=$data['f_inicio'];?>"></span>
	<span id="fechaFinal" data-value="<?=$data['f_final'];?>"></span>
	<span id="graficosId" data-value='<?=$data['graficos'];?>'></span>
	
	<div class="row">
	<? if( isset($resultReporte['total_ventas_del_mes']) && !empty($resultReporte['total_ventas_del_mes']) ) : ?>
		<div class="col-xs-6 col-sm-4">
			<div class="tile tile-primary">
				<?=$resultReporte['total_ventas_del_mes'][0][0]['TotalVentas'];?>
	            <p>Total ventas del periodo</p>                            
	            <div class="informer informer-default"><span class="fa fa-shopping-cart"></span></div>
            </div>
		</div>
	<? endif; ?>
	<? if( isset($resultReporte['total_descuentos_del_periodo']) && !empty($resultReporte['total_descuentos_del_periodo']) ) : ?>
		<div class="col-xs-6 col-sm-4">
			<div class="tile tile-info">
				<?=$resultReporte['total_descuentos_del_periodo'][0][0]['TotalDescuentos'];?>
	            <p>Total descuentos del periodo</p>                            
	            <div class="informer informer-default"><span class="fa fa-money"></span></div>
            </div>
		</div>
	<? endif; ?>
	<? if( isset($resultReporte['pedidos_del_mes']) && !empty($resultReporte['pedidos_del_mes']) ) : ?>
		<div class="col-xs-6 col-sm-4">
			<div class="widget widget-warning widget-carousel">
                <div class="owl-carousel" id="pedidos_del_mes">
                	<? $contador1 = 0; ?>
                	<? foreach ($resultReporte['pedidos_del_mes'] as $pedidoItem) : ?>
                		<? if ($contador1 == 0) : ?>
                			<div>                                    
		                        <div class="widget-title">Total pedidos</div>
		                        <div class="widget-subtitle">del perido</div>                                                                       
		                        <div class="widget-int"><?=$pedidoItem[0]['Total'];?></div>
		                    </div>
                		<? endif; ?>
                		<div>                                    
	                        <div class="widget-title">Total pedidos</div>
	                        <div class="widget-subtitle"><?=$this->Html->translateMonth($pedidoItem[0]['Mes']);?></div>                                                                       
	                        <div class="widget-int"><?=$pedidoItem[0]['TotalPedidos'];?></div>
	                    </div>
	                    <? $contador1++; ?>
                	<? endforeach; ?>
				</div>                            
                <div class="widget-controls">                                
                    <a href="#" class="widget-control-right"><span class="fa fa-times"></span></a>
                </div>                             
	        </div>
		</div>
	<? endif; ?>
    </div>         
    <div class="row" id="grafics-container">
    <? if( isset($resultReporte['comparador_de_periodos']) && !empty($resultReporte['comparador_de_periodos']) ) : ?>
    	<div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Ventas por meses</h3>                                
                </div>
                <div class="panel-body">
                    <div id="comparador_de_periodos" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    <? endif;?>

    <? if( isset($resultReporte['productos_del_periodo']) && !empty($resultReporte['productos_del_periodo']) ) : ?>
    	<div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Productos más vendidos</h3>
                    <small class="description-graph">Representa el porcentaje (%) de los 10 mejores productos con respecto al universo de <span id="cant_total_product"></span>.</small>                                 
                </div>
                <div class="panel-body">
                    <div id="productos_del_periodo" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    <? endif;?>
    <? if( isset($resultReporte['categorias_del_mes']) && !empty($resultReporte['categorias_del_mes']) ) : ?>
    	<div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Categorias más vendidas</h3><br>
                    <small class="description-graph">Representa la cantidad de ventas de las 10 mayores categorias.</small>                                
                </div>
                <div class="panel-body">
                    <div id="categorias_del_mes" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    <? endif;?>
    <? if( isset($resultReporte['marcas_del_periodo']) && !empty($resultReporte['marcas_del_periodo']) ) : ?>
    	<div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Mejores marcas</h3>
                    <small class="description-graph">Representa el porcentaje (%) de los 10 mejores marcas con respecto al universo de <span id="cant_total_marcas"></span>.</small>                               
                </div>
                <div class="panel-body">
                    <div id="marcas_del_periodo" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    <? endif;?>
    <? if( isset($resultReporte['compradores_del_periodo']) && !empty($resultReporte['compradores_del_periodo']) ) : ?>
    	<div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Mejores compradores</h3>   
                    <small class="description-graph">Muestra los 10 mejores clientes del periodo.</small>                                  
                </div>
                <div class="panel-body">
                    <div id="compradores_del_periodo" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    <? endif;?>
    </div>
    
</div>