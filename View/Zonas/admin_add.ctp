<div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> Nuevo Zona</h2>
</div>

<?= $this->Form->create('Zona', array('class' => 'form-horizontal js-validate-roles', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
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
									<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
									<td><?= $this->Form->input('nombre'); ?> </td>
								</tr>
								<tr>
									<th><?= $this->Form->label('tipo'); ?></th>
									<td><?= $this->Form->input('tipo',[
										'class' => 'form-control',
										'multiple' => false,
										'empty' => 'Seleccione Tipo']
										); ?>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('bodega_id','Bodega'); ?></th>
									<td><?= $this->Form->input('bodega_id',array(
										'class' => 'form-control', 
										'multiple' => false,
										'empty' => 'Seleccione Bodega')); ?>
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
