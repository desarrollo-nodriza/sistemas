<div class="page-title">
	<h2><span class="fa fa-bookmark"></span> Prospectos</h2>
</div>
</div>
<?= $this->Form->create('Prospecto', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar Prospecto</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<?= $this->Form->input('id'); ?>
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tienda_id', 'Tienda'); ?></th>
								<td><?= $this->Form->input('tienda_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('descripcion', 'Descripcion'); ?></th>
								<td><?= $this->Form->input('descripcion'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('estado_prospecto_id', 'Estado prospecto'); ?></th>
								<td><?= $this->Form->input('estado_prospecto_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('moneda_id', 'Moneda'); ?></th>
								<td><?= $this->Form->input('moneda_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('origen_id', 'Origen'); ?></th>
								<td><?= $this->Form->input('origen_id'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('comentarios', 'Comentarios'); ?></th>
								<td><?= $this->Form->input('comentarios'); ?></td>
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
