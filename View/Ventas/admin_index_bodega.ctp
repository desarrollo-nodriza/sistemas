

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

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-primary panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de ventas por empaquetar</h3>
					<ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="form-group col-xs-12 col-md-2">
							<label for="filtro-venta-id">Id de la venta</label>
							<input type="text" class="form-control" id="filtro-venta-id" placeholder="ID de la venta">
						</div>
						<div class="form-group col-xs-12 col-md-3">
							<label for="filtro-venta-id">Comunas</label>
							<select class="form-control select" data-live-search="true" id="filtro-venta-comuna">
								<?=$this->Html->crear_opciones_por_arraglo($comunas); ?>
							</select>
						</div>
						<div class="form-group col-xs-12 col-md-3">
							<label for="filtro-venta-id">Metodo de envio</label>
							<select class="form-control" id="filtro-venta-envio">
								<?=$this->Html->crear_opciones_por_arraglo($metodo_envios); ?>
							</select>
						</div>
						<div class="form-group col-xs-12 col-md-2">
							<label for="filtro-venta-id">Marketplace</label>
							<select class="form-control" id="filtro-venta-marketplace">
								<?=$this->Html->crear_opciones_por_arraglo($canales); ?>
							</select>
						</div>
						<div class="form-group col-xs-12 col-md-2">
							<label for="filtro-venta-id">Tienda</label>
							<select class="form-control" id="filtro-venta-tienda">
								<?=$this->Html->crear_opciones_por_arraglo($tiendas); ?>
							</select>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtros', array('action' => 'index_bodega'), array('class' => 'btn btn-primary btn-block', 'escape' => false)); ?>
						</div>
						<div class="pull-right">
							<button class="btn btn-success" id="filtro-venta-btn"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>

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

<?= $this->Html->script(array(
	'/backend/js/venta.js?v=' . rand()
));?>
<?= $this->fetch('script'); ?>