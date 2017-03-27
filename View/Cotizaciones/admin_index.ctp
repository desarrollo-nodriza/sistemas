<div class="page-title">
	<h2><span class="fa fa-lightbulb-o"></span> Cotizaciones</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'cotizaciones', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Buscar por</label>
							<?=$this->Form->select('findby', array(
								'id' => 'Identificador', 
								'email_cliente' => 'Email del cliente'),
								array(
								'class' => 'form-control',
								'empty' => false
								)
							);?>
						</div>
					</div>
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Ingrese valor</label>
							<?= $this->Form->input('nombre_buscar', array('class' => 'form-control input-buscar', 'placeholder' => 'Ingrese email o nombre del cliente')); ?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Buscar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
					</div>
					<?= $this->Form->end(); ?>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de Cotizaciones</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nueva Cotización', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
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
									<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('email_cliente', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('estado_cotizacion_id', 'Estado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Creado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('validez_fecha_id', 'Válido hasta', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $cotizaciones as $cotizacion ) : ?>
								<tr>
									<td><?= h($cotizacion['Cotizacion']['id']); ?>&nbsp;</td>
									<td><?= h($cotizacion['Cotizacion']['nombre']); ?>&nbsp;</td>
									<td><?= h($cotizacion['Cotizacion']['email_cliente']); ?>&nbsp;</td>
									<td><?= h($cotizacion['EstadoCotizacion']['nombre']); ?>&nbsp;</td>
									<td><?= h($cotizacion['Cotizacion']['created']); ?>&nbsp;</td>
									<td><?= h($cotizacion['ValidezFecha']['valor']); ?>&nbsp;</td>	
									<td>
									<div class="btn-group">
                                        <a href="#" data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="true"><span class="fa fa-cog"></span> Acciones</a>
                                        <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                            <li role="presentation" class="dropdown-header">Seleccione</li>
											<? if ($permisos['view']) : ?>
											<? if ( !empty($cotizacion['Cotizacion']['archivo']) ) : ?>
											<li><a href="<?=$cotizacion['Cotizacion']['archivo']?>" class="" download><i class="fa fa-file-pdf-o"></i> Descargar PDF</a></li>
											<? endif; ?>
											<li><?= $this->Html->link('<i class="fa fa-eye"></i> Ver', array('action' => 'view', $cotizacion['Cotizacion']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?></li>
											<? endif; ?>
											<? if ($permisos['generate']) : ?>
											<li><a href="#" class="mb-control " data-box="#mb-signout<?=$cotizacion['Cotizacion']['id'];?>"><i class="fa fa-paper-plane"></i> Reenviar</a></li>
											<? endif; ?>
											<? if ($permisos['edit']) : ?>
											<li><?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $cotizacion['Cotizacion']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?></li>
											<? endif; ?>
											<? if ($permisos['delete']) : ?>
											<li><?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $cotizacion['Cotizacion']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?></li>
											<? endif; ?>
										</ul>
										<div class="message-box message-box-warning animated fadeIn" data-sound="alert" id="mb-signout<?=$cotizacion['Cotizacion']['id'];?>">
											<div class="mb-container">
												<div class="mb-middle">
													<div class="mb-title"><span class="fa fa-paper-plane"></span>¿Reenviar <strong>Cotización</strong>?</div>
													<div class="mb-content">
														<p>¿Seguro que quieres reenviar esta cotización?</p>
														<p>Esta opción es válida solo sí la cotización no se envió correctamente al cliente.</p>
														<p>Para cancelar presiona No</p>
													</div>
													<div class="mb-footer">
														<div class="pull-right">
															<?= $this->Html->link('<i class="fa fa-paper-plane"></i> Sí, reenviar', array('action' => 'reenviar', $cotizacion['Cotizacion']['id']), array('class' => 'btn btn-primary btn-lg', 'rel' => 'tooltip', 'title' => 'Reenviar email', 'escape' => false)); ?>
															<button class="btn btn-default btn-lg mb-control-close">No</button>
														</div>
													</div>
												</div>
											</div>
										</div>
                                    </div>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
	<div class="row">
		<div class="col-xs-12">
			<div class="pull-right">
				<ul class="pagination">
					<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
					<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
					<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
				</ul>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
