

<style type="text/css">
	.listado-ventas td {
		vertical-align: middle !important;
	}
</style>

<div class="page-title">
	<h2><span class="fa fa-money"></span> Ventas para procesar <a class="btn btn-xs btn-circulo btn-primary" id="refrescar_manualmente" title="Refrescar manualmente"><i class="fa fa-refresh"></i></a> <small class="text-muted">Actualización en: <span id="actualizacion-contdown">60 segundos</span></small></h2>
</div>

<div class="divider"></div>

<div class="page-content-wrap" id="preparacion_index">

	<div class="row mb-20 mt-20">
		<div class="col-xs-12">
			<h3 class="text-center text-muted"><i class="fa fa-arrows"></i> Arrastra las ventas por las distintas columnas para cambiar sus estados</h3>
		</div>
	</div>

	<? if (!empty($meliConexion)) : ?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-plug" aria-hidden="true"></i> Conectar con Marketplaces</h3>
					</div>

					<div class="panel-body">
						<p>Debe conectar sus marketplaces para poder ver las ventas. Éste procedimiento se realiza sólo una vez.</p>
						<div class="btn-group" role="group" aria-label="Conectar">
							<? foreach ($meliConexion as $iac => $access) : ?>
								<a href="<?= $access['url']; ?>" class="btn btn-primary">Acceder a <?= $access['marketplace']; ?></a>
							<? endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<? endif; ?>

	<div class="row" style="display: flex;">
		
		<div class="col-md-4" style="display: flex; flex-direction: column;">
                                
            <h3 class="mb-5">Listos para empaquetar <small id="contador-listos">(0)</small></h3>
            
            <div class="tasks" id="tasks" style="display: flex; flex-direction: column; height: 100%;;">
                
                <div class="task-drop push-down-10">
		            <span class="fa fa-cloud"></span>
		            Arrastra la venta aquí para reiniciar el proceso
		        </div>
            </div>                            

        </div>
        <div class="col-md-4" style="display: flex; flex-direction: column;">
            <h3 class="mb-5">En progreso <small id="contador-preparacion">(0)</small></h3>
            <div class="tasks" id="tasks_progreess" style="display: flex; flex-direction: column; height: 100%;">

				<div class="task-drop push-down-10">
		            <span class="fa fa-cloud"></span>
		            Arrastra la venta aquí para comenzar a procesar
		        </div>                
            </div>
        </div>
        <div class="col-md-4" style="display: flex; flex-direction: column;">
            <h3 class="mb-5">Completos <small id="contador-completos">(0)</small></h3>
            <div class="tasks" id="tasks_completed" style="display: flex; flex-direction: column; height: 100%;">
                <div class="task-drop push-down-10">
		            <span class="fa fa-cloud"></span>
		            Arrastra la venta aquí para finalizar su prepración
		        </div>                                                
            </div>
        </div>

	</div>

</div>

<!-- Modal detalle venta -->
<div id="wrapper-modal-venta-ver-mas">
	
</div>
<!-- Fin modal -->