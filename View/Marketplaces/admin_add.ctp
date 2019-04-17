<div class="page-title">
	<h2><span class="fa fa-shopping-cart"></span> Marketplaces</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo Marketplace</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?= $this->Form->create('Marketplace', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
							<table class="table">
								<tr>
									<th><?= $this->Form->label('nombre', 'Marketplace'); ?></th>
									<td><?= $this->Form->input('nombre'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('fee', 'Fee'); ?></th>
									<td>
										<div class="input-group">
											<?= $this->Form->input('fee'); ?>
											<span class="input-group-addon">%</span>
										</div>
                                    </td>
								</tr>
								<tr>
									<th><?= $this->Form->label('porcentaje_adicional', 'Porcentaje adicional'); ?></th>
									<td><?= $this->Form->input('porcentaje_adicional', array('type' => 'text')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('api_host', 'Api Host'); ?></th>
									<td><?= $this->Form->input('api_host'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('api_user', 'Api User'); ?></th>
									<td><?= $this->Form->input('api_user'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('api_key', 'Api Key'); ?></th>
									<td><?= $this->Form->input('api_key'); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('marketplace_tipo_id', 'Tipo'); ?></th>
									<td><?= $this->Form->input('marketplace_tipo_id', array('empty' => 'Seleccione Tipo')); ?></td>
								</tr>
								<tr>
									<th><?= $this->Form->label('tienda_id', 'Tienda'); ?></th>
									<td><?= $this->Form->input('tienda_id', array('empty' => 'Seleccione Tienda')); ?></td>
								</tr>
							</table>

							<div class="pull-right">
								<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar">
								<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
							</div>
						<?= $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
