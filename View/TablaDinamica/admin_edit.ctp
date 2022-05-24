<div class="page-title">
	<h2><span class="fa fa-truck"></span> Tabla dinámica</h2>
</div>

<div class="page-content-wrap">

	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<?= $this->Form->create('TablaDinamica', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
				<?= $this->Form->input('id'); ?>
				<div class="panel-heading">
					<h3 class="panel-title">Crear tabla dinámica</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">

							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>

							<th><?= $this->Form->label('dependencia', 'Dependencia o Plugin'); ?></th>
							<td><?= $this->Form->select('dependencia', $dependencias, array('class' => 'form-control js-select-dependencia', 'empty' => 'Sin dependencia')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox', 'default' => true)); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary start-loading-when-form-is-validate" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Categoría</h3>
						<? $contador_categoria = $categorias; ?>
						<? foreach ($this->request->data['CategoriaTablaDinamica'] as $value) {

							unset($contador_categoria[$value['CategoriaTabla']['categoria_tabla_dinamica_id']]);
						} ?>
						<ul class="panel-controls">
							<? if (count($contador_categoria) > 0) : ?>
								<li><a href="#" class="clone-boton-2"><span class="fa fa-plus"></span></a></li>
							<? endif ?>
						</ul>
					</div>
					<?= $this->Form->create(false, array(
						'class' => 'form-horizontal',
						'url' => array('controller' => 'tablaDinamica', 'action' => 'categoria_add', $this->request->data['TablaDinamica']['id']),
						'id' => 'CategoriaAdd'
					)); ?>
					<div class="panel-body">
						<div class="table-responsive">
							<table id="sortable" class="table">
								<thead>
									<tr>
										<th>Nombre</th>
										<th style="text-align: center;"> Acciones</th>
									</tr>
								</thead>
								<tbody>
									<? foreach ($this->request->data['CategoriaTablaDinamica'] as $indice => $categoria) : ?>
										<tr class="fila-2">
											<td style="vertical-align: middle;">
												<?= $this->Form->input(sprintf('%d.id', $indice), array('type' => 'text', 'label' => '', 'default' =>  $categoria['CategoriaTabla']['id'], 'class' => 'form-control hidden ')); ?>
												<?= $this->Form->select(sprintf('%d.categoria_tabla_dinamica_id', $indice), $categorias, array('empty' => 'Seleccione Cuenta corriente', 'class' => 'form-control', 'required', 'default' => $categoria['CategoriaTabla']['categoria_tabla_dinamica_id'])); ?>
												<?= $this->Form->input(sprintf('%d.tabla_dinamica_id', $indice), array('type' => 'text', 'label' => '', 'default' =>  $categoria['CategoriaTabla']['tabla_dinamica_id'], 'class' => 'form-control hidden ')); ?>

											</td>
											<td style="vertical-align: middle;">
												<button type="button" data-toggle="modal" data-target="#modal-eliminar-categoria<?= $categoria['CategoriaTabla']['id'] ?>" class="btn btn-danger btn-block ">Eliminar</button>
											</td>
										</tr>
									<? endforeach; ?>

									<? for ($i = (count($this->request->data['CategoriaTablaDinamica']) + 1); $i <= (count($this->request->data['CategoriaTablaDinamica']) + count($categorias)); $i++) : ?>
										<tr class="fila-2 hidden clone-tr-2">

											<td style="vertical-align: middle;">
												<?= $this->Form->input(sprintf('%d.id', $i), array('type' => 'text', 'label' => '', 'default' => "", 'class' => 'form-control hidden ')); ?>
												<?= $this->Form->select(sprintf('%d.categoria_tabla_dinamica_id', $i), $categorias, array('empty' => 'Seleccione atributo', 'class' => 'form-control', 'required')); ?>
												<?= $this->Form->input(sprintf('%d.tabla_dinamica_id', $i), array('type' => 'text', 'label' => '', 'default' =>  $this->request->data['TablaDinamica']['id'], 'class' => 'form-control hidden ')); ?>
											</td>
											<td style="vertical-align: middle;">
												<button type="button" class="remove_tr remove-tr btn-danger"><i class="fa fa-minus"></i></button>
											</td>
										</tr>
									<? endfor; ?>


								</tbody>
							</table>
						</div>
					</div>

					<div id="guardar-bodega" class="row">
						<div class="col-xs-12">
							<div class="pull-right pagination">
								<button type="submit" class="btn btn-success btn-block start-loading-when-form-is-validate ">Guardar Información</button>
							</div>
						</div>
					</div>

					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6">
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Atributos</h3>
						<ul class="panel-controls">
							<li><a href="#" class="clone-boton"><span class="fa fa-plus"></span></a></li>
						</ul>
					</div>
					<?= $this->Form->create(false, array(
						'class' => 'form-horizontal',
						'url' => array('controller' => 'tablaDinamica', 'action' => 'atributo_add', $this->request->data['TablaDinamica']['id']),
						'id' => 'AtributoAdd'
					)); ?>
					<div class="panel-body">
						<div class="table-responsive" style="max-height: 600px;">
							<table id="sortable" class="table">
								<thead>
									<tr>
										<th>Id</th>
										<th>Atributo relacionado</th>
										<th>Referencia</th>
										<th>Requerido</th>
										<th style="text-align: center;"> Acciones</th>
									</tr>
								</thead>
								<tbody>

									<? foreach ($this->request->data['AtributoDinamico'] as $indice => $atributo) : ?>
										<tr class="fila">
											<td style="vertical-align: middle; width: 100px;">
												<?= $this->Form->input(sprintf('%d.id', $indice), array('type' => 'text', 'label' => '', 'default' =>  $atributo['TablaAtributo']['id'], 'class' => 'form-control hidden ')); ?>
												<?= $atributo['TablaAtributo']['id'] ?>
												<?= $this->Form->input(sprintf('%d.tabla_dinamica_id', $indice), array('type' => 'text', 'label' => '', 'default' =>  $this->request->data['TablaDinamica']['id'], 'class' => 'form-control hidden ')); ?>
											</td>

											<td style="vertical-align: middle;">
												<?= $this->Form->select(sprintf('%d.atributo_dinamico_id', $indice), $atributos, array('empty' => 'Seleccione Cuenta corriente', 'class' => 'form-control', 'required', 'default' => $atributo['TablaAtributo']['atributo_dinamico_id'])); ?>
											</td>
											<td style="vertical-align: middle; width: 300px;">
												<?= $this->Form->input(sprintf('%d.nombre_referencia', $indice), array('type' => 'text', 'label' => '',  'default' =>  $atributo['TablaAtributo']['nombre_referencia'], 'class' => 'form-control ', 'required')); ?>
											</td>
											<td align="center" style="vertical-align: middle;">
												<?= $this->Form->checkbox(sprintf('%d.requerido', $indice), array('label' => '', 'default' =>  $atributo['TablaAtributo']['requerido'])); ?>
											</td>

											<td style="vertical-align: middle;">
												<button type="button" data-toggle="modal" data-target="#modal-eliminar<?= $atributo['TablaAtributo']['id'] ?>" class="btn btn-danger btn-block ">Eliminar</button>
											</td>
										</tr>
									<? endforeach; ?>

									<? for ($i = (count($this->request->data['AtributoDinamico']) + 1); $i <= (count($this->request->data['AtributoDinamico']) + 10); $i++) : ?>
										<tr class="fila hidden clone-tr">
											<td style="vertical-align: middle; width: 100px;">
												<?= $this->Form->input(sprintf('%d.tabla_dinamica_id', $i), array('type' => 'text', 'label' => '', 'default' =>  $this->request->data['TablaDinamica']['id'], 'class' => 'form-control hidden ')); ?>
												<?= $this->Form->input(sprintf('%d.id', $i), array('type' => 'text', 'label' => '', 'default' => "", 'class' => 'form-control hidden ')); ?>
												-
											</td>

											<td style="vertical-align: middle;">
												<?= $this->Form->select(sprintf('%d.atributo_dinamico_id', $i), $atributos, array('empty' => 'Seleccione atributo', 'class' => 'form-control', 'required')); ?>
											</td>
											<td style="vertical-align: middle;">
												<?= $this->Form->input(sprintf('%d.nombre_referencia', $i), array('type' => 'text', 'label' => '', 'class' => 'form-control', 'required')); ?>
											</td>
											<td align="center" style="vertical-align: middle;">
												<?= $this->Form->checkbox(sprintf('%d.requerido', $i), array('label' => '', 'default' => 1)); ?>
											</td>
											<td style="vertical-align: middle;">
												<button type="button" class="remove_tr remove-tr btn-danger"><i class="fa fa-minus"></i></button>
											</td>
										</tr>
									<? endfor; ?>

								</tbody>
							</table>
						</div>

					</div>

					<div id="guardar-bodega" class="row">
						<div class="col-xs-12">
							<div class="pull-right pagination">
								<button type="submit" class="btn btn-success btn-block start-loading-when-form-is-validate ">Guardar Información</button>
							</div>
						</div>
					</div>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Model  -->
	<? foreach ($this->request->data['AtributoDinamico'] as $indice => $atributo) : ?>

		<div class="modal fade" id="modal-eliminar<?= $atributo['TablaAtributo']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-eliminar<?= $atributo['TablaAtributo']['id']; ?>-label">
			<div class="modal-dialog" role="document">

				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title text-center"> Esta seguro de eliminar la relación con el atributo</h4>
					</div>

					<div class="modal-body">
						<?= $this->Form->create(false, array(
							'class' => 'form-horizontal',
							'url' => array('controller' => 'tablaDinamica', 'action' => 'delete',  $atributo['TablaAtributo']['id']),
						)); ?>
						<?= $this->Form->input('id', array('type' => 'text', 'label' => '', 'default' => $this->request->data['TablaDinamica']['id'], 'class' => 'form-control hidden ')); ?>
						<div>
							<div class="col-xs-12">
								<div class="btn-group pull-right">
									<button type="submit" class="btn btn-success  start-loading-then-redirect">Continuar</button>
									<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger ">Cancelar</button>
								</div>
							</div>
						</div>
						<?= $this->Form->end(); ?>
					</div>
				</div>

			</div>
		</div>
	<? endforeach; ?>
	<!-- Model  -->

	<!-- Model eliminar Categoria-->
	<? foreach ($this->request->data['CategoriaTablaDinamica'] as $indice => $categoria) : ?>

		<div class="modal fade" id="modal-eliminar-categoria<?= $categoria['CategoriaTabla']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-eliminar-categoria<?= $categoria['CategoriaTabla']['id']; ?>-label">
			<div class="modal-dialog" role="document">

				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title text-center"> Esta seguro de eliminar la relación con la categoría</h4>
					</div>

					<div class="modal-body">
						<?= $this->Form->create(false, array(
							'class' => 'form-horizontal',
							'url' => array('controller' => 'tablaDinamica', 'action' => 'categoria_delete',   $this->request->data['TablaDinamica']['id']),
						)); ?>
						<?= $this->Form->input('id', array('type' => 'text', 'label' => '', 'default' =>  $categoria['CategoriaTabla']['id'], 'class' => 'form-control hidden ')); ?>
						<div>
							<div class="col-xs-12">
								<div class="btn-group pull-right">
									<button type="submit" class="btn btn-success  start-loading-then-redirect">Continuar</button>
									<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger ">Cancelar</button>
								</div>
							</div>
						</div>
						<?= $this->Form->end(); ?>
					</div>
				</div>

			</div>
		</div>
	<? endforeach; ?>
	<!-- Model eliminar Categoria -->


	<?= $this->fetch('script'); ?>
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(document).ready(function() {
				$('.mi-selector').select2();
			});
		});

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

		$(document).on('click', '.clone-boton-2', function(e) {
			e.preventDefault();

			let clone_tr = document.getElementsByClassName("clone-tr-2");
			if (clone_tr.length > 0) {
				let elementoremoveClass = clone_tr.item(0);
				elementoremoveClass.removeAttribute('class')
				const classes_2 = elementoremoveClass.classList
				classes_2.add("nuevo_elemento-2");
				classes_2.add("fila-2");
			}
		});
		$(document).on('click', '.remove-tr-2', function(e) {

			e.preventDefault();
			var $th = $(this).parents('tr').eq(0);

			$th.fadeOut('slow', function() {
				$th.remove();
				ordenar();
			});
		});
		$.app.formularios.bind('#AtributoAdd');
		$.app.formularios.bind('#CategoriaAdd');
	</script>