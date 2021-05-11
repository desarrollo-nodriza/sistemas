<!-- <div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> Nombre "<?=$ubicacion['Ubicacion']['nombre'];?>"</h2>
</div> -->

<?= $this->Form->create('Ubicacion', array('class' => 'form-horizontal js-validate-roles', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
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
								<?= $this->Form->input('id',['default'=>$ubicacion['Ubicacion']['id']]); ?>
								<tr>
									<th><?= $this->Form->label('zona_id','Zona'); ?></th>
									<td><?= $this->Form->input('zona_id',[
										'class' => 'form-control', 
										'multiple' => false,
										'empty' => 'Seleccione Zona',
										'default' => $ubicacion['Ubicacion']['zona_id']
										]); ?>
										
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('fila'); ?></th>
									<td><?= $this->Form->input('fila',['default'=>$ubicacion['Ubicacion']['fila']]); ?> 
										
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('columna'); ?></th>
									<td><?= $this->Form->input('columna',['default'=>$ubicacion['Ubicacion']['columna']]); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('alto'); ?></th>
									<td><?= $this->Form->input('alto',['default'=>$ubicacion['Ubicacion']['alto']]); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('ancho'); ?></th>
									<td><?= $this->Form->input('ancho',['default'=>$ubicacion['Ubicacion']['ancho']]); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('profundidad'); ?></th>
									<td><?= $this->Form->input('profundidad',['default'=>$ubicacion['Ubicacion']['profundidad']]); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('mts_cubicos'); ?></th>
									<td><?= $this->Form->input('mts_cubicos',['default'=>$ubicacion['Ubicacion']['mts_cubicos']]); ?> 
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('activo', 'Activo'); ?></th>
									<td><?= $this->Form->input('activo', ['class' => 'icheckbox','default' => $ubicacion['Ubicacion']['activo']]); ?>
										
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
