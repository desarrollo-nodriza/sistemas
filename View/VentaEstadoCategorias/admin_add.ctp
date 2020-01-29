<div class="page-title">
	<h2><span class="fa fa-filter"></span> Estados de Ventas</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Estado de Ventas</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('VentaEstadoCategoria', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<tr>
									<th><?= $this->Form->label('nombre', 'Estado Detallado'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('estilo', 'Color'); ?></th>
									<td><?= $this->Form->select('estilo', $colores, array('class' => 'form-control', 'empty' => false, 'escape' => false)); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('venta', '¿Es Venta?'); ?></th>
									<td>
										<?= $this->Form->input('venta', array('class' => 'icheckbox')); ?>
										<span class="help-block">Las ventas que tengan ésta categoria se marcarán como una venta válida.</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('rechazo', '¿Es Venta rechazada?'); ?></th>
									<td>
										<?= $this->Form->input('rechazo', array('class' => 'icheckbox')); ?>
										<span class="help-block">Las ventas que tengan ésta categoria se marcarán como una venta cancelada o rechazada.</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('excluir_preparacion', '¿Excluir de la preparación de pedidos?'); ?></th>
									<td>
										<?= $this->Form->input('excluir_preparacion', array('class' => 'icheckbox')); ?>
										<span class="help-block">Las ventas que tengan ésta categoria no se mostrarán en el visor de preparación de pedidos.</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('envio', 'Estado de envio'); ?></th>
									<td>
										<?= $this->Form->input('envio', array('class' => 'icheckbox')); ?>
										<span class="help-block">Marca la venta como enviada</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('final', 'Estado final'); ?></th>
									<td>
										<?= $this->Form->input('final', array('class' => 'icheckbox')); ?>
										<span class="help-block">Marca la venta como finalizada</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('plantilla', 'Plantilla de email'); ?></th>
									<td>
										<?= $this->Form->select('plantilla', $plantillas, array('class' => 'form-control', 'empty' => 'Seleccione')); ?>
									</td>
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
