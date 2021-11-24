<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Nueva OC</h2>
</div>


<?= $this->Form->create('OrdenCompra', array('url' => array('controller' => 'ordenCompras', 'action' => 'validate'), 'class' => 'form-horizontal', 'type' => 'get', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							
	<div class="page-content-wrap">

		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-cubes"></i> Agrupar</h3>
					</div>
					<div class="panel-body row">
						
						<p>Seleccione las ventas que desea procesar</p>

						<div id="wrapper-ordenes" style="max-height: 400px;">
							<div class="table-responsive overflow-x">
								<table class="table table-bordered table-stripped ctm-datatables">
									<thead>
										<th></th>
										<th>ID</th>
										<th>ID EXTERNO</th>
										<th>BODEGA</th>
										<th>REFERENCIA</th>
										<th>ESTADO</th>
										<th>CLIENTE</th>
										<th>CREADA</th>
										<th>OC</th>
										<th>ITEMS</th>
										<th>PRIORITARIA</th>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="pull-right">
							<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Generar OCs">
							<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?= $this->Form->end(); ?>

<!-- MESSAGE BOX-->
<div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="modal_alertas">
    <div class="mb-container">
        <div class="mb-middle">
            <div class="mb-title" id="modal_alertas_label"><i class="fa fa-alert"></i> Confirmar orden</div>
            <div class="mb-content">
                <p id="mensajeModal"></p>                    
            </div>
            <div class="mb-footer">
                <div class="pull-right">
                	<button class="btn btn-primary btn-lg" id="confirmar_manifiesto">Agregar de todas formas</button>
                    <button class="btn btn-default btn-lg mb-control-close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MESSAGE BOX-->

<script type="text/javascript">
	


</script>