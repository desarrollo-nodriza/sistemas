<div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> Nombre "<?=$zona['Zona']['nombre'];?>"</h2>
</div>

<?= $this->Form->create('Zona', array('class' => 'form-horizontal js-validate-roles', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Editar</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
							<table class="table">
								<?= $this->Form->input('id',['default'=>$zona['Zona']['id']]); ?>
								<tr>
									<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
									<td><?= $this->Form->input('nombre',['default'=>$zona['Zona']['nombre']]); ?> 
										
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('tipo'); ?></th>
									<td><?= $this->Form->input('tipo',[
										'class' => 'form-control',
										'multiple' => false,
										'default'=>$zona['Zona']['tipo']]); ?>
										
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('bodega_id','Bodega'); ?></th>
									<td><?= $this->Form->input('bodega_id',array(
										'class' => 'form-control', 
										'multiple' => false,
										'empty' => 'Seleccione Bodega',
										'default' => $zona['Zona']['bodega_id'])); ?>
										
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', ['class' => 'icheckbox','default' => $zona['Zona']['activo']]); ?>
										
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
			<button type="submit" class="btn btn-primary">Guardar cambios</button>
			<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
		</div>
	</div>


	
</div>
<?= $this->Form->end(); ?>
