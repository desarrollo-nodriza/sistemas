<div class="page-title">
	<h2><span class="fa fa-truck"></span> Método de envio</h2>
</div>
<?= $this->Form->create('MetodoEnvio', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo método de envio</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('tiempo_entrega_estimado', 'Tiempo de entrega estimado'); ?></th>
								<td><?= $this->Form->input('tiempo_entrega_estimado'); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('retiro_local', 'Retiro en local'); ?></th>
								<td><?= $this->Form->input('retiro_local', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('dependencia', 'Dependencia o Plugin'); ?></th>
								<td><?= $this->Form->select('dependencia', $dependencias, array('class' => 'form-control js-select-dependencia', 'empty' => 'Sin dependencia')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('activo', 'Activo'); ?></th>
								<td><?= $this->Form->input('activo', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr id="generar_ot" class="hidden">
								<th><?= $this->Form->label('generar_ot', 'Activar generación de OT'); ?></th>
								<td><?= $this->Form->input('generar_ot', array('class' => 'icheckbox')); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('bodega_id', 'Bodega para despachar'); ?></th>
								<td><?= $this->Form->select('bodega_id', $bodegas, array('empty' => 'Seleccione Bodega', 'class' => 'form-control','required')); ?></td>
							</tr>
							<tr id="peso_maximo" class="hidden">
								<th><?= $this->Form->label('peso_maximo', 'Peso Máximo'); ?></th>
								<td><?= $this->Form->input('peso_maximo'); ?></td>
							</tr>
							<tr id="peso_default" class="hidden">
								<th><?= $this->Form->label('peso_default', 'Peso por defecto'); ?></th>
								<td><?= $this->Form->input('peso_default'); ?></td>
							</tr>
							<tr id="alto_default" class="hidden">
								<th><?= $this->Form->label('alto_default', 'Alto por defecto'); ?></th>
								<td><?= $this->Form->input('alto_default'); ?></td>
							</tr>
							<tr id="ancho_default" class="hidden">
								<th><?= $this->Form->label('ancho_default', 'Ancho por defecto'); ?></th>
								<td><?= $this->Form->input('ancho_default'); ?></td>
							</tr>
							<tr id="largo_default" class="hidden">
								<th><?= $this->Form->label('largo_default', 'Largo por defecto'); ?></th>
								<td><?= $this->Form->input('largo_default'); ?></td>
							</tr>
							
							<tr id="volumen_maximo" class="hidden">
								<th><?= $this->Form->label('volumen_maximo', 'Volumen Máximo'); ?></th>
								<td><?= $this->Form->input('volumen_maximo'); ?></td>
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

<?= $this->Html->script(array(
	'/backend/js/metodo_envios_add.js?v=' . rand())); ?>
<?= $this->fetch('script'); ?>