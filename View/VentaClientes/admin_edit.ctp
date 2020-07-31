<div class="page-title">
	<h2><span class="fa fa-users"></span> <?= $this->request->data['VentaCliente']['nombre'];?> <?= $this->request->data['VentaCliente']['apellido'];?></h2>
</div>
<?= $this->Form->create('VentaCliente', array('class' => 'form-horizontal js-formulario', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>

<?= $this->Form->input('id'); ?>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-7">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-user"></i> <?= __('Información del cliente'); ?></h3>
				</div>
				<div  class="panel-body">
					<div class="table table-resposive">
						<table class="table table-bordered">	
							<tr>
								<th><?= __('Nombre'); ?></th>
								<td><?= $this->Form->input('nombre', array('class' => 'form-control not-blank', 'placeholder' => 'Ingrese nombre del cliente')); ?></td>
							</tr>
							<tr>
								<th><?= __('Apellido'); ?></th>
								<td><?= $this->Form->input('apellido', array('class' => 'form-control not-blank', 'placeholder' => 'Ingrese apellido del cliente')); ?></td>
							</tr>
							<tr>
								<th><?= __('Rut'); ?></th>
								<td><?= $this->Form->input('rut', array('class' => 'form-control not-blank is-rut', 'placeholder' => 'Ingrese rut del cliente')); ?></td>
							</tr>
							<tr>
								<th><?= __('Email'); ?></th>
								<td><?= $this->Form->input('email', array('class' => 'form-control not-blank is-email', 'placeholder' => 'Ingrese email del cliente')); ?></td>
							</tr>
							<tr>
								<th><?= __('Fono'); ?></th>
								<td><?= $this->Form->input('telefono', array('class' => 'form-control is-number', 'placeholder' => 'Ingrese fono del cliente')); ?></td>
							</tr>
							<tr>
								<th><?= __('Tipo de cliente'); ?></th>
								<td><?=$this->Form->select('tipo_cliente', array('persona' => 'Persona', 'empresa' => 'Empresa'), array('label' => false, 'class' => 'form-control js-cliente-tipo', 'default' => 'persona', 'empty' => false))?></td>
							</tr>
							<tr class="<?= (!empty($this->request->data['VentaCliente']['giro_comercial'])) ? '' : 'hidden' ;?>">
								<th><?= __('Giro Comercial'); ?></th>
								<td><?=$this->Form->input('giro_comercial', array('class' => 'form-control js-giro-comercial', 'placeholder' => 'Ingrese giro comercial'))?></td>
							</tr>						
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Form->button('<i class="fa fa-save"></i> Guardar cambios', array('type' => 'submit', 'class' => 'btn btn-primary start-loading-when-form-is-validate')); ?>
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
			<div class="panel panel-primary" id="clienteDirecciones">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-home"></i> <?= __('Direcciones'); ?></h3>
				</div>
				<div  class="panel-body">
					<div class="table table-resposive">
						<table class="table table-bordered">
							<thead>
								<th><?= __('ID');?></th>
								<th><?= __('Alias');?></th>
								<th><?= __('Calle');?></th>
								<th><?= __('Número');?></th>
								<th><?= __('Depto/Oficina');?></th>
								<th><?= __('Comuna');?></th>
								<th><?= __('Fecha creación');?></th>
								<th><?= __('Acciones');?></th>
							</thead>
							<tbody>
							<? if ( empty($this->request->data['Direccion']) ) : ?>
								<tr>
									<td colspan="7"><p><?= __('No registra información'); ?></p></td>
								</tr>
							<? endif; ?>
							
							<? foreach ($this->request->data['Direccion'] as $id => $d) : ?>
								<?=$this->element('direcciones/address-tr', array('direccion' => $d, 'no_index' => true)); ?>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-5">
			
			<a class="tile small tile-success"><?=CakeNumber::currency($this->request->data['Metricas']['total_comprado'], 'CLP');?> <p>Total comprado</p></a>

			<a class="tile small tile-warning"><?=CakeNumber::currency($this->request->data['Metricas']['total_cotizado'], 'CLP');?> <p>Total cotizado</p></a>

			<a class="tile small tile-info"><?=$this->request->data['Metricas']['cantidad_prospectos'];?> <p>Cantidad prospectos</p></a>

			<div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Compras realizadas</h3>
                </div>
                <div class="panel-body">
                	<div class="table-responsive" style="max-height: 470px;">
						<table class="table table-bordered datatable">
							<caption>Cantidades vendidas</caption>
							<thead>
								<th>Id venta</th>
								<th>Estado</th>
								<th>Fecha<br>venta</th>
								<th>Total</th>
								<th>Acciones</th>
							</thead>
							<tbody>
							<? foreach ($this->request->data['Venta'] as $iv => $venta) : ?>
								<tr>
									<td><?= $this->Html->link($venta['id'], array('controller' => 'ventas', 'action' => 'view', $venta['id']), array('target' => '_blank')); ?></td>
									<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a>&nbsp;</td>
									<td>
										<?= date_format(date_create($venta['fecha_venta']), 'd/m/Y H:i:s'); ?>
										<? if ($venta['picking_estado'] == 'no_definido' && $venta['VentaEstado']['VentaEstadoCategoria']['venta'] && !$venta['VentaEstado']['VentaEstadoCategoria']['final']) : 
										
											$retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['fecha_venta']), 'Y-m-d H:i:s'));

										if (!empty($retrasoMensaje)) : ?>
											<?=$retrasoMensaje;?>
										<?
										endif;
									  endif;?>			
									</td>
									<td><?=CakeNumber::currency($venta['total'], 'CLP');?></td>
									<td>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('controller' => 'ventas', 'action' => 'view', $venta['id']), array('target' => '_blank', 'class' => 'btn btn-info btn-xs', 'escape' => false)); ?>
									</td>
								</tr>
							<? endforeach; ?>										
							</tbody>	
						</table>
                	</div>
                </div>                             
            </div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>

<?=$this->element('direcciones/form-add', array('token' => $this->Session->read('Auth.Administrador.token.token'))); ?>

<?= $this->Html->script(array(
	'/backend/js/clientes.js?v=' . rand())); ?>
<?= $this->fetch('script'); ?>