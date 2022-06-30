<div class="page-title">
	<h2><span class="fa fa-filter"></span> Reglas Generar OT</h2>
</div>
<div class="page-content-wrap">
	<?= $this->Form->create(false, array(
		'class' => 'form-horizontal',
		'url' 	=> array('controller' => 'reglasGenerarOC', 'action' => 'regla_create',),
		'id' 	=> 'ReglaCreate'
	)); ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Listado de Reglas</h3>
						<div class="btn-group pull-right">
							<? if ($permisos['add']) : ?>
								<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Regla', array('action' => '#'), array('class' => 'btn btn-success clone-boton', 'escape' => false)); ?>
								<button type="submit" class="btn btn-danger start-loading-when-form-is-validate"><i class="fa fa-save"></i>Guardar Información</button>
							<? endif; ?>
						</div>
						<div class="panel-body">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr class="sort">
											<th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
											<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
											<th><?= $this->Paginator->sort('medio de pago', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
											<th><?= $this->Paginator->sort('mayor que', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
											<th><?= $this->Paginator->sort('menor que', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>

										<? for ($i = (count($reglasGenerarOC) + 1); $i <= (count($reglasGenerarOC) + 15); $i++) : ?>
											<tr class="fila hidden clone-tr">
												<td align="center" style="vertical-align: bottom; max-width: 100px;">
													<?= $this->Form->input(sprintf('%d.id', $i), array('type' => 'text', 'label' => '', 'default' => "", 'class' => 'form-control hidden ')); ?>
												</td>

												<td align="center" style="vertical-align: bottom;">
													<?= $this->Form->input(sprintf('%d.nombre', $i), array('type' => 'text', 'label' => '', 'class' => 'form-control nombre', 'required')); ?>
												</td>

												<td align="center" style="vertical-align: bottom;width: 400px;">
													<?= $this->Form->select(
														sprintf('%d.medio_pago_id', $i),
														$medio_de_pago,
														array(
															'class' => 'form-control mi-selector medio_pago_id',
															'style' => "width: 400px",
															'required'
														)
													); ?>
												</td>
												<td align="center" style="vertical-align: bottom;">
													<?= $this->Form->input(sprintf('%d.mayor_que', $i), array('type' => 'text', 'label' => '', 'class' => 'form-control mayor_que',)); ?>
												</td>
												<td align="center" style="vertical-align: bottom;">
													<?= $this->Form->input(sprintf('%d.menor_que', $i), array('type' => 'text', 'label' => '', 'class' => 'form-control menor_que',)); ?>
												</td>
												<td align="center" style="vertical-align: bottom;">
													<button type="button" class="remove_tr remove-tr btn-danger"><i class="fa fa-minus"></i></button>
												</td>
											</tr>
										<? endfor; ?>
										<?php foreach ($reglasGenerarOC as $indice => $regla) : ?>
											<tr>
												<td align="center" style="vertical-align: bottom; max-width: 100px;">
													<!-- <?= h($regla['ReglasGenerarOC']['id']); ?> -->
													<?= $this->Form->input(sprintf('%d.id', $indice), array('readonly', 'type' => 'text', 'label' => '', 'default' => $regla['ReglasGenerarOC']['id'], 'class' => 'form-control')); ?>
												</td>

												<td align="center" style="vertical-align: bottom;">
													<?= $this->Form->input(sprintf('%d.nombre', $indice), array('type' => 'text', 'label' => '', 'default' => $regla['ReglasGenerarOC']['nombre'], 'class' => ' nombre form-control')); ?>
												</td>

												<td align="center" style="vertical-align: bottom; width: 400px;">
													<?= $this->Form->select(
														sprintf('%d.medio_pago_id', $indice),
														$medio_de_pago,
														array(
															'style' => "width: 400px",
															'default' => $regla['ReglasGenerarOC']['medio_pago_id'],
															'class' => 'form-control hidden mi-selector medio_pago_id'
														)
													); ?>
												</td>
												<td align="center" style="vertical-align: bottom;">
													<?= $this->Form->input(sprintf('%d.mayor_que', $indice), array('type' => 'text', 'label' => '', 'default' => $regla['ReglasGenerarOC']['mayor_que'], 'class' => 'is-number form-control mayor_que', 'min' => 0)); ?>
												</td>
												<td align="center" style="vertical-align: bottom;">
													<?= $this->Form->input(sprintf('%d.menor_que', $indice), array('type' => 'text', 'label' => '', 'default' => $regla['ReglasGenerarOC']['menor_que'], 'class' => 'is-number form-control menor_que', 'min' => 0)); ?>
												</td>
												<td align="center" style="vertical-align: bottom;">
													-
												</td>
											</tr>

										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="pull-right">
					<ul class="pagination">
						<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
						<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
						<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?= $this->Form->end(); ?>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
	$.app.formularios.bind('#ReglaCreate');
	$(document).on('click', '.clone-boton', function(e) {
		e.preventDefault();

		let clone_tr = document.getElementsByClassName("clone-tr");

		if (clone_tr.length > 0) {
			let elementoremoveClass = clone_tr.item(0);
			elementoremoveClass.removeAttribute('class')
			const classes_2 = elementoremoveClass.classList
			classes_2.add("nuevo_elemento");
			classes_2.add("fila");
		}
	});
	$(document).on('click', '.remove-tr', function(e) {

		e.preventDefault();
		var $th = $(this).parents('tr').eq(0);

		$th.fadeOut('slow', function() {
			$th.remove();
			ordenar();
		});
	});

	$('.medio_pago_id').on('change', function(e) {
		cambiarNombre($(this))
	});

	$('.mayor_que').on('change', function(e) {
		cambiarNombre($(this))
	});

	$('.menor_que').on('change', function(e) {
		cambiarNombre($(this))
	});


	function cambiarNombre(base) {


		var medio_de_pago = base.closest('tr').find('.select2-selection__rendered').text();
		var mayor_que = base.closest('tr').find('.mayor_que').val();
		var nombre = base.closest('tr').find('.nombre').val();
		var menor_que = base.closest('tr').find('.menor_que').val();
		var rango = "Rango " + (menor_que.length > 0 ? menor_que : "")
		" - " + (menor_que.length > 0 ? menor_que : "")
		let _nombre = medio_de_pago + rango
		base.closest('tr').find('.nombre').val(_nombre);
		console.log({
			"_nombre": _nombre,
			"nombre": nombre,
			"medio_de_pago": medio_de_pago,
			"mayor_que": mayor_que,
			"menor_que": menor_que,
		});
	}

	jQuery(document).ready(function($) {
		$(document).ready(function() {
			$('.mi-selector').select2();
		});
	});
</script>