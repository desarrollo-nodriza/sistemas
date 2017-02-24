<div class="page-title">
	<h2><span class="fa fa-lightbulb-o"></span> Cotizaciones</h2>
</div>
<?= $this->Form->create('Cotizacion', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo Cotización</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('prospecto_id', 'Prospecto'); ?></th>
								<td><?= $this->Form->input('prospecto_id', array('class' => 'form-control select')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('descripcion', 'Descripcion'); ?></th>
								<td><?= $this->Form->input('descripcion'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('estado_cotizacion_id', 'Estado cotizacion'); ?></th>
								<td><?= $this->Form->input('estado_cotizacion_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('moneda_id', 'Moneda'); ?></th>
								<td><?= $this->Form->input('moneda_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('validez_fecha_id', 'Validez'); ?></th>
								<td><?= $this->Form->input('validez_fecha_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('descuento', 'Descuento'); ?></th>
								<td><?= $this->Form->input('descuento'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('comentarios', 'Comentarios'); ?></th>
								<td><?= $this->Form->input('comentarios'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('enviado', 'Enviado'); ?></th>
								<td><?= $this->Form->input('enviado', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('generado', 'Generado'); ?></th>
								<td><?= $this->Form->input('generado', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('archivo', 'Archivo'); ?></th>
								<td><?= $this->Form->input('archivo', array('type' => 'file')); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
<?= $this->Form->end(); ?>
