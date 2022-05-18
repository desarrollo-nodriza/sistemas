<div class="page-title">
	<h2><span class="fa fa-truck"></span> Cuenta corriente para transportista</h2>
</div>

<div class="page-content-wrap">

	<div class="row">
		<?= $this->Form->create('CuentaCorrienteTransporte', array(
			'class' => 'form-horizontal',
			'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control')
		)); ?>
		<?= $this->Form->input('id'); ?>
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar cuenta corriente</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre', array('required' =>  true)); ?></td>
							</tr>

							<th><?= $this->Form->label('dependencia', 'Dependencia o Plugin'); ?></th>
							<td><?= $this->Form->select('dependencia', $dependencias, array('class' => 'form-control js-select-dependencia', 'empty' => 'Sin dependencia')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
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
			</div>
			<?= $this->Form->end(); ?>
		</div>

		<div class="col-xs-12 col-md-6">

			<? foreach ($tabla_dinamica as $key =>  $formulario) : ?>
				<?= $this->Form->create('CuentaCorrienteTransporte', array(
					'url'			=> array('controller' => 'cuentaCorrienteTransporte', 'action' => 'guardar_atributo', $this->request->data['CuentaCorrienteTransporte']['id']),
					'class' 		=> 'form-horizontal',
					'id'			=> "formularios-{$key}",
					'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control')
				)); ?>
				<div class="panel panel-default js-panel-<?= $formulario['TablaDinamica']['dependencia'] ?> <?= ($this->request->data['CuentaCorrienteTransporte']['dependencia']  == $formulario['TablaDinamica']['dependencia']) ? '' : 'hidden'; ?>">

					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-truck"></i><?= $formulario['TablaDinamica']['nombre'] ?></h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered">

								<? foreach ($formulario['AtributoDinamico'] as $input) : ?>
									<? $valor = Hash::extract($valor_tabla_dinamica, "{n}.ValorAtributoCuentaCorrienteTransporte[tabla_atributo_id={$input['TablaAtributo']['id']}].valor") ?>
									<? if (isset($dependenciasVars[$formulario['TablaDinamica']['dependencia']][$input['TablaAtributo']['nombre_referencia']])) : ?>
										<tr>
											<th><?= $this->Form->label($input['TablaAtributo']['id'], ucwords($input['nombre']) . " ({$input['TablaAtributo']['nombre_referencia']})"); ?></th>
											<td><?= $this->Form->select($input['TablaAtributo']['id'], $dependenciasVars[$formulario['TablaDinamica']['dependencia']][$input['TablaAtributo']['nombre_referencia']], array('class' => 'form-control mi-selector', 'style' => "width:100%;", 'default' => $valor, 'empty' => 'Seleccione', 'required' =>  $input['TablaAtributo']['requerido'])); ?></td>
										</tr>
									<? else : ?>
										<tr>
											<th><?= $this->Form->label($input['TablaAtributo']['id'], ucwords($input['nombre']) . " ({$input['TablaAtributo']['nombre_referencia']})"); ?></th>
											<td><?= $this->Form->input($input['TablaAtributo']['id'], array('default' => $valor, 'required' =>  $input['TablaAtributo']['requerido'])); ?></td>
										</tr>
									<? endif; ?>

								<? endforeach; ?>


							</table>
						</div>
					</div>
					<div class="panel-footer">
						<div class="pull-right">
							<input type="submit" class="btn btn-primary start-loading-when-form-is-validate" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
							<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			<? endforeach; ?>

		</div>
	</div>
</div>

<?= $this->Html->script(array(
	'/backend/js/cuenta_corriente_transporte.js?v=' . rand()
)); ?>
<?= $this->fetch('script'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(document).ready(function() {
			$('.mi-selector').select2();
		});
	});
</script>