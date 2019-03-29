<div class="page-title">
	<h2><span class="fa fa-file-text"></span> <?=__('Invalidar DTE'); ?></h2>
</div>

<?= $this->Form->create('Dte', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('venta_id', array('type' => 'hidden', 'value' => $this->request->data['Venta']['id'])); ?>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-9">


			<!-- DTE A INVALIDAR -->
			<div class="panel panel panel-info panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-info" aria-hidden="true"></i> <?=__('DTE Origen'); ?></h3>
					<ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="table-responsive">
								<table class="table table-bordered">
									<tr>
										<th>Referencia</th>
										<td><?= $venta['Venta']['referencia']; ?></td>
									</tr>
									<tr>
										<th>ID Externo</th>
										<td><?= $venta['Venta']['id_externo']; ?></td>
									</tr>
									<tr>
										<th>Estado</th>
										<td><span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= $venta['VentaEstado']['VentaEstadoCategoria']['estilo']; ?>"><?= $venta['VentaEstado']['VentaEstadoCategoria']['nombre']; ?></span></td>
									</tr>
									<tr>
										<th>Fecha</th>
										<td><?= date_format(date_create($venta['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?></td>
									</tr>
									<tr>
										<th>Medio de Pago</th>
										<td><?= $venta['MedioPago']['nombre']; ?></td>
									</tr>
									<tr>
										<th>Tienda</th>
										<td><?= $venta['Tienda']['nombre']; ?></td>
									</tr>
									<tr>
										<th>Marketplace</th>
										<td><?php if (!empty($venta['Venta']['marketplace_id'])) {echo $venta['Marketplace']['nombre'];} ?>&nbsp;</td>
									</tr>
									<tr>
										<th>Atendida</th>
										<td><?= ($venta['Venta']['atendida'] ? "<span class='btn btn-xs btn-success'>Sí</span>" : "<span class='btn btn-xs btn-danger'>No</span>"); ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>


<?= $this->Form->end(); ?>