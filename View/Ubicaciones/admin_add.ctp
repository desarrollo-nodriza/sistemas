<div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> Nuevo Ubicacion</h2>
</div>

<?= $this->Form->create('Ubicacion', array('class' => 'form-horizontal js-validate-roles', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Crear</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
							<table class="table">
							<tr>
									<th><?= $this->Form->label('zona_id','Zona'); ?></th>
									<td><?= $this->Form->input('zona_id',[
										'class' => 'form-control', 
										'multiple' => false,
										'empty' => 'Seleccione Zona',
										
										]); ?>
										
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('fila'); ?></th>
									<td><?= $this->Form->input('fila'); ?> 
										
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('columna'); ?></th>
									<td><?= $this->Form->input('columna'); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('alto'); ?></th>
									<td><?= $this->Form->input('alto'); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('ancho'); ?></th>
									<td><?= $this->Form->input('ancho'); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('profundidad'); ?></th>
									<td><?= $this->Form->input('profundidad'); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('mts_cubicos'); ?></th>
									<td><?= $this->Form->input('mts_cubicos'); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', ['class' => 'icheckbox']); ?>
										
									</td>
								</tr>
							</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel-footer">
		<div class="pull-right">
			<button type="submit" class="btn btn-primary">Guardar</button>
			<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>
