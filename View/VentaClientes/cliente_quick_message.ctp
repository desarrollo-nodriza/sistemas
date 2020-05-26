<div id="login">
	<div class="container">
		<div class="row justify-content-center align-items-center">
			<div class="col-12 col-md-6 mt-5">
				<img src="<?= FULL_BASE_URL . '/webroot/img/' . $tienda['Tienda']['logo']['path']; ?>" class="img-fluid mt-5" style="max-width: 150px;">
			</div>
		</div>
		<div class="row justify-content-center align-items-center">
			<div class="col-12 col-md-6 mt-4">
				<? if (empty($error)) : ?>
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">¡Mensaje guardado!</h5>
						<h6 class="card-subtitle mb-4 text-muted">Estimad@ <?=$cliente['VentaCliente']['nombre'];?>. Tu requerimiento fue recibido existosamente y será procesado por nuestro equipo lo antes posible.</h6>
						<p class="mt-3 text-muted">Atentamente <?=$tienda['Tienda']['nombre'];?></p>
						<a class="btn btn-primary btn-block" href="https://<?=$tienda['Tienda']['url'];?>">Ir a la tienda</a>
					</div>
				</div>
				<? else : ?>
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">¡Ups! Ocurrió un error</h5>
						<h6 class="card-subtitle mb-4 text-muted"><?=$error;?></h6>
						
						<a class="btn btn-primary btn-block" href="https://<?=$tienda['Tienda']['url'];?>">Ir a la tienda</a>
					</div>
				</div>
				<? endif; ?>
			</div>
		</div>
	</div>
</div>