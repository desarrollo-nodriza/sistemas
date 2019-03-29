<div class="page-title">
	<h2><span class="fa fa-list"></span> Ordenes de compra</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<div class="widget widget-warning small-widget" style="min-height: 80px !important;">
                <div class="widget-title">OC a pagar</div>
                <div class="widget-int"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=validado].id'));?></div>
            </div>
		</div>
		<div class="col-xs-12 col-md-3">
			<div class="widget widget-success small-widget" style="min-height: 80px !important;">
                <div class="widget-title">OC a enviar</div>
                <div class="widget-int"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=pagado].id'));?></div>
            </div>
		</div>
		<div class="col-xs-12 col-md-3">
			<div class="widget widget-info small-widget" style="min-height: 80px !important;">
                <div class="widget-title">OC En revisión</div>
                <div class="widget-int"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=iniciado].id'));?></div>
            </div>
		</div>
		<div class="col-xs-12 col-md-3">
			<div class="widget widget-primary small-widget" style="min-height: 80px !important;">
                <div class="widget-title">OC enviadas</div>
                <div class="widget-int"><?=count(Hash::extract($ocs, '{n}.OrdenCompra[estado=enviado].id'));?></div>
            </div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'ordenCompras', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$id  = (isset($this->request->params['named']['id'])) ? $this->request->params['named']['id'] : '' ;
				$sta = (isset($this->request->params['named']['sta'])) ? $this->request->params['named']['sta'] : '' ;
				$dtf = (isset($this->request->params['named']['dtf'])) ? $this->request->params['named']['dtf'] : '' ;
				$dtt = (isset($this->request->params['named']['dtt'])) ? $this->request->params['named']['dtt'] : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>ID:</label>
							<?=$this->Form->input('id', array(
								'type' => 'text',
								'class' => 'form-control',
								'value' => $id
								));?>
						</div>
					</div>
					
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Estado</label>
							<?=$this->Form->select('sta', $estados,
								array(
								'class' => 'form-control select',
								'empty' => 'Seleccione Estado',
								'multiple' => true,
								'value' => $sta
								)
							);?>
						</div>
					</div>
					<div class="col-sm-4 col-xs-12">
						<label>Emitidos entre</label>
						<div class="input-group">
							<?=$this->Form->input('dtf', array(
								'class' => 'form-control datepicker',
								'type' => 'text',
								'value' => $dtf
								))?>
                            <span class="input-group-addon add-on"> - </span>
                            <?=$this->Form->input('dtt', array(
								'class' => 'form-control datepicker',
								'type' => 'text',
								'value' => $dtt
								))?>
                        </div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Filtrar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
						</div>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de ordenes de compra</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) :  ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nueva OC Ventas', array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
						<?= $this->Html->link('<i class="fa fa-hand-pointer-o"></i> Nueva OC Manual', array('action' => 'add_manual'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('administrador_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tienda_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('estado', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('parent_id', 'Cantidad OC', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('oc_manual', 'OC Manual', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $ordenCompras as $ordenCompra ) : ?>

								<? if (!empty($ordenCompra['ChildOrdenCompra']) && ($permisos['send'] || $permisos['generate'] ) ) : ?>
								<tr class="accordion-toggle"  data-toggle="collapse" data-target="#collapse<?=$ordenCompra['OrdenCompra']['id'];?>">
									<td><?= h($ordenCompra['OrdenCompra']['id']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['Administrador']['nombre']); ?></td>
									<td><?= h($ordenCompra['Tienda']['nombre']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['estado']); ?>&nbsp;</td>
									<td><?= count($ordenCompra['ChildOrdenCompra']); ?>&nbsp;</td>
									<td><?= ($ordenCompra['OrdenCompra']['oc_manual'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['created']); ?>&nbsp;</td>
									<td>
									
									<button type="button" class="btn btn-xs btn-warning"><i class="fa fa-expand"></i> Expandir</button>

									<? if ($permisos['generate'] && $ordenCompra['OrdenCompra']['estado'] == '') : ?>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($permisos['generate'] && $ordenCompra['OrdenCompra']['estado'] == 'iniciado') : ?>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Ver todo', array('action' => 'view', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($permisos['delete'] && $ordenCompra['OrdenCompra']['estado'] == '') : ?>
										<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
									<? endif; ?>

									</td>
								</tr>
								
								<tr id="collapse<?=$ordenCompra['OrdenCompra']['id'];?>" class="collapse">
									<td colspan="7">
										<table class="table table-bordered table-stripped">
											<thead>
												<th>Id padre</th>
												<th>Id</th>
												<th>Administrador</th>
												<th>Tinda</th>
												<th>Estado</th>
												<th>Fecha creación</th>
												<th>Acciones</th>
											</thead>
											<tbody>
											<? foreach ($ordenCompra['ChildOrdenCompra'] as $oc => $o) : ?>
											<tr>
												<td><?=$ordenCompra['OrdenCompra']['id'];?></td>
												<td><?= h($o['id']); ?>&nbsp;</td>
												<td><?= h($o['Administrador']['nombre']); ?></td>
												<td><?= h($o['Tienda']['nombre']); ?>&nbsp;</td>
												<td><?= h($o['estado']); ?>&nbsp;</td>
												<td><?= h($o['created']); ?>&nbsp;</td>
												<td>

												<? if ($permisos['edit'] && $o['estado'] == '') : ?>
													<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'editSingle', $o['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
												<? endif; ?>

												<? if ($permisos['generate'] && $o['estado'] == 'iniciado') : ?>
													<?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $o['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
												<? endif; ?>

												<? if ($permisos['validate'] && $o['estado'] == 'iniciado') : ?>
													<?= $this->Html->link('<i class="fa fa-pencil"></i> Revisar', array('action' => 'review', $o['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
												<? endif; ?>

												<? if ($permisos['send'] && $o['estado'] == 'pagado') : ?>
													<?= $this->Html->link('<i class="fa fa-paper-plane"></i> Enviar OC', array('action' => 'ready', $o['id']), array('class' => 'btn btn-xs btn-danger', 'rel' => 'tooltip', 'title' => 'Recepcionar OC', 'escape' => false)); ?>
												<? endif; ?>

												<? if ($o['estado'] == 'enviado' && $permisos['send']) : ?>
													<?= $this->Html->link('<i class="fa fa-undo"></i> Recepcionar', array('action' => 'reception', $o['id']), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
												<? endif; ?>

												<? if ($o['estado'] == 'incompleto' && $permisos['send']) : ?>
													<?= $this->Html->link('<i class="fa fa-edit"></i> Completar', array('action' => 'reception', $o['id']), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
												<? endif; ?>

												<? if ($permisos['delete'] && $o['estado'] == '') : ?>
													<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $o['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
												<? endif; ?>

												<? if ($o['estado'] == 'recibido') : ?>
													<?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $o['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
												<? endif; ?>

												</td>
											</tr>
											<? endforeach; ?>
											</tbody>
										</table>
									</td>
								</tr>
								<? elseif ( $ordenCompra['OrdenCompra']['oc_manual'] && ($permisos['send'] || $permisos['generate'] ) ) : ?>
								<tr>
									<td><?= h($ordenCompra['OrdenCompra']['id']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['Administrador']['nombre']); ?></td>
									<td><?= h($ordenCompra['Tienda']['nombre']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['estado']); ?>&nbsp;</td>
									<td><?= count($ordenCompra['ChildOrdenCompra']); ?>&nbsp;</td>
									<td><?= ($ordenCompra['OrdenCompra']['oc_manual'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['created']); ?>&nbsp;</td>
									<td>

									<? if ($permisos['edit'] && $ordenCompra['OrdenCompra']['estado'] == '') : ?>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'editSingle', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($permisos['generate'] && $ordenCompra['OrdenCompra']['estado'] == 'iniciado') : ?>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($permisos['validate'] && $ordenCompra['OrdenCompra']['estado'] == 'iniciado') : ?>
										<?= $this->Html->link('<i class="fa fa-pencil"></i> Revisar', array('action' => 'review', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($permisos['send'] && $ordenCompra['OrdenCompra']['estado'] == 'pagado') : ?>
										<?= $this->Html->link('<i class="fa fa-paper-plane"></i> Enviar OC', array('action' => 'ready', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-danger', 'rel' => 'tooltip', 'title' => 'Recepcionar OC', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($ordenCompra['OrdenCompra']['estado'] == 'enviado' && $permisos['send']) : ?>
										<?= $this->Html->link('<i class="fa fa-undo"></i> Recepcionar', array('action' => 'reception', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($ordenCompra['OrdenCompra']['estado'] == 'incompleto' && $permisos['send']) : ?>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Completar', array('action' => 'reception', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-success', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($permisos['delete'] && $ordenCompra['OrdenCompra']['estado'] == '') : ?>
										<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
									<? endif; ?>

									<? if ($ordenCompra['OrdenCompra']['estado'] == 'recibido') : ?>
										<?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
									<? endif; ?>

									</td>
								</tr>
								<? endif; ?>
								
								<!-- Validador -->
								<? if (empty($ordenCompra['ChildOrdenCompra']) && $ordenCompra['OrdenCompra']['estado'] == 'iniciado' && $permisos['validate']) : ?>
								<tr class="accordion-toggle"  data-toggle="collapse" data-target="#collapse<?=$ordenCompra['OrdenCompra']['id'];?>">
									<td><?= h($ordenCompra['OrdenCompra']['id']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['Administrador']['nombre']); ?></td>
									<td><?= h($ordenCompra['Tienda']['nombre']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['estado']); ?>&nbsp;</td>
									<td><?= count($ordenCompra['ChildOrdenCompra']); ?>&nbsp;</td>
									<td><?= ($ordenCompra['OrdenCompra']['oc_manual'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['created']); ?>&nbsp;</td>
									<td>

									<? if ($permisos['validate']) : ?>
										<?= $this->Html->link('<i class="fa fa-pencil"></i> Revisar', array('action' => 'review', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
									<? endif; ?>

									</td>
								</tr>
								<? endif; ?>


								<!-- Finanzas -->
								<? if (empty($ordenCompra['ChildOrdenCompra']) && $ordenCompra['OrdenCompra']['estado'] == 'validado' && $permisos['pay']) : ?>
								<tr class="accordion-toggle"  data-toggle="collapse" data-target="#collapse<?=$ordenCompra['OrdenCompra']['id'];?>">
									<td><?= h($ordenCompra['OrdenCompra']['id']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['Administrador']['nombre']); ?></td>
									<td><?= h($ordenCompra['Tienda']['nombre']); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['estado']); ?>&nbsp;</td>
									<td><?= count($ordenCompra['ChildOrdenCompra']); ?>&nbsp;</td>
									<td><?= ($ordenCompra['OrdenCompra']['oc_manual'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($ordenCompra['OrdenCompra']['created']); ?>&nbsp;</td>
									<td>

									<? if ($permisos['pay']) : ?>
										<?= $this->Html->link('<i class="fa fa-money"></i> Pagar OC', array('action' => 'pay', $ordenCompra['OrdenCompra']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Revisar este registro', 'escape' => false)); ?>
									<? endif; ?>

									</td>
								</tr>
								<? endif; ?>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="pull-right">
	<ul class="pagination">
		<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
		<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
		<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
	</ul>
</div>
