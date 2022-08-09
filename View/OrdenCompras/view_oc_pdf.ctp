<div class="page-content-wrap" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-offset-3 col-md-6">
				<div class="panel panel-success">
					<div class="panel-body">
						<?= $this->Html->image(sprintf('Tienda/%d/%s', $oc['Tienda']['id'], $oc['Tienda']['logo']), array('class' => 'img-responsive center-block', 'style' => "max-width: 150px; margin: 30px auto;")); ?>
						<h2 class="text-center">OC<?= $oc['OrdenCompra']['tipo_orden'] == "inventario" ? "I-" : "V-"; ?><?= $oc['OrdenCompra']['id']; ?> generada con éxito</h2>

						<a class="btn btn-primary btn-lg center-block" style="margin: 30px auto 15px;" href="<?= $url; ?>/Pdf/OrdenCompra/<?= $oc['OrdenCompra']['id']; ?>/<?= $oc['OrdenCompra']['pdf']; ?>" download><i class="fa fa-download"></i> Descargar OC</a>
					</div>
				</div>
				<p class="text-right text-muted">Recuerde descargar la OC antes de cerrar la pestaña.</p>
			</div>
		</div>
	</div>

</div>

<script type="text/javascript">
	window.onbeforeunload = function(e) {
		return 'Recuerde guardar el PDF antes de salir.';
	};
</script>