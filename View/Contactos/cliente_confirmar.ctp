<div>
	<div class="container">
		<div class="row justify-content-center align-items-center">
			<div class="col-12 col-md-6 mt-5">
				<img src="<?= FULL_BASE_URL . '/webroot/img/' . $tienda['Tienda']['logo']['path']; ?>" class="img-fluid mt-5" style="max-width: 150px;">
			</div>
		</div>
		<div class="row justify-content-center align-items-center">
			<div class="col-12 col-md-6 mt-4">
				<div class="card">
					<div class="card-body">
                        <? if (!empty($contacto)) : ?>
                        <h5 class="card-title">Estimado/a <?=$contacto['Contacto']['nombre_contacto']; ?></h5>
                        <? endif; ?>
                        <? if (empty($error)) : ?>
                            <p class="mb-2 text-muted">Muchas gracias en ayudarnos a mejorar nuestros procesos. Ya puede cerrar esta pestaña.</p>
                            <p class="text-muted">Atte Servicio al cliente <?=$tienda['Tienda']['nombre']; ?></p>
                        <? else : ?>
                            <p class="mb-2 text-muted"><?=$error; ?></p>
                            <p class="text-muted">Atte Servicio al cliente <?=$tienda['Tienda']['nombre']; ?></p>
                        <? endif; ?>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>