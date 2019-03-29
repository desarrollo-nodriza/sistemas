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
						<?= $this->Form->create('VentaEstado', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<?=$this->Form->input('id');?>
							<table class="table">
								<tr>
									<th><?= $this->Form->label('nombre', 'Estado Detallado'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('venta_estado_categoria_id', 'Estado Agrupado'); ?></th>
									<td><?= $this->Form->input('venta_estado_categoria_id', array('empty' => 'Seleccione Categoría')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('permitir_dte', '¿Permitir DTE?'); ?></th>
									<td>
										<?= $this->Form->input('permitir_dte', array('class' => 'icheckbox')); ?>
										<span class="help-block">Activar la facturación electrónica a las ventas con este estado. Seleccione ésta opción cuando sea una venta tenga un pago realizado</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('revertir_stock', '¿Revertir stock?'); ?></th>
									<td>
										<?= $this->Form->input('revertir_stock', array('class' => 'icheckbox')); ?>
										<span class="help-block">Las ventas con este estado devolverán el stock virtual. Seleccione ésta opción cuando sea una venta con un pago cancelado, rechazado o con error.</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('marcar_atendida', 'Atendida'); ?></th>
									<td>
										<?= $this->Form->input('marcar_atendida', array('class' => 'icheckbox')); ?>
										<span class="help-block">Las ventas con este estado se marcarán como una venta finalizada.</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('permitir_oc', '¿Permitir oc?'); ?></th>
									<td>
										<?= $this->Form->input('permitir_oc', array('class' => 'icheckbox')); ?>
										<span class="help-block">Las ventas con este estado podrán generar oc´s.</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('permitir_retiro_oc', '¿Permitir salida de productos?'); ?></th>
									<td>
										<?= $this->Form->input('permitir_retiro_oc', array('class' => 'icheckbox')); ?>
										<span class="help-block">Las ventas con este estado le permite crear retiros de productos.</span>
									</td>
								</tr>
								<tr>
									<th><?= $this->Form->label('permitir_manifiesto', '¿Permitir manifiestos?'); ?></th>
									<td>
										<?= $this->Form->input('permitir_manifiesto', array('class' => 'icheckbox')); ?>
										<span class="help-block">Las ventas con este estado le permite crear manifiestos.</span>
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
