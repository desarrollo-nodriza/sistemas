<div class="page-title">
	<h2><span class="fa fa-bar-chart-o"></span> Graficos</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo Grafico</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<label>Etiquetas disponibles:</label>
							<ul>
								<li><b>[*PREFIX*]</b> = Selector de Prefijo</li>
								<li><b>[*START_DATE*]</b> = Selector de fecha inicial</li>
								<li><b>[*FINISH_DATE*]</b> = Selector de fecha final</li>
								<li><b>[*LIMIT*]</b> = Selector limite de registros</li>
							</ul>
						</div>
					</div>
					<div class="table-responsive">
						<?= $this->Form->create('Grafico', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<tr>
									<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('tipo_grafico', 'Tipo de gráfico'); ?></th>
									<td><?= $this->Form->input('tipo_grafico', array(
										'empty' => 'Seleccione',
										'options' => array(1 => 'Línea', 2 => 'Barra', 3 => 'Área', 4 => 'Donuts', 5 => 'Recuadro', 6 => 'Carrusel'),
										'class' => 'form-control select'
									)); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('descipcion', 'SQL'); ?></th>
									<td><?= $this->Form->input('descipcion'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
								</tr>
							</table>

							<div class="pull-right">
								<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
								<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
							</div>
						<?= $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
