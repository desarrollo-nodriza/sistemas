<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-body">
					Reporte generado el <span id="fecha_reporte"></span>
					<button id="generarGraficoBtn" class="btn btn-success btn-xs pull-right"><i class="fa fa-cogs" aria-hidden="true"></i> Re-generar Informe</button>
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

	<? if( !empty($resultReporte['Total ventas del mes']) ) : ?>

	<div class="row">
		<div class="col-xs-4">
			<div class="tile tile-primary">
				<?=$resultReporte['Total ventas del mes'][0][0]['TotalVentas'];?>
	            <p>Total ventas del periodo</p>                            
	            <div class="informer informer-default"><span class="fa fa-shopping-cart"></span></div>
            </div>
		</div>
	</div>
	<? endif; ?>
                    
    <div class="row" id="grafics-container">
        
    </div>
    
</div>