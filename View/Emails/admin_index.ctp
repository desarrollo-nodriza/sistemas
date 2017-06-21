<div class="page-title">
	<h2><span class="fa fa-envelope"></span> Newsletter</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Listado de newsletter</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Newsletter', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table datatable">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tienda_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('plantilla_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Creado', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $emails as $email ) : ?>
								<tr>
									<td><?= h($email['Email']['nombre']); ?>&nbsp;</td>
									<td><?= h($email['Tienda']['nombre']); ?>&nbsp;</td>
									<td><?= h($email['Plantilla']['nombre']); ?>&nbsp;</td>
									<td><?= h($email['Email']['created']); ?>&nbsp;</td>
									<td><?= ($email['Email']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td>
									<div class="btn-group">
                                        <a href="#" data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="true"><span class="fa fa-cog"></span> Acciones</a>
                                        <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                            <li role="presentation" class="dropdown-header">Seleccione</li>
											<? if ($permisos['edit']) : ?>
												<li><?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $email['Email']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?></li>
											<? endif; ?>
												<!--<li><?= $this->Form->postLink('<i class="fa fa-eye"></i> Generar', array('action' => 'generarHtml', $email['Email']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Ver', 'escape' => false)); ?></li>-->
											<? if (!empty($email['Email']['ultimo_html'])) : ?>
												<li>
													<?= $this->Html->link('<i class="fa fa-check"></i> Ver Html guardado', array('action' => 'view_html', $email['Email']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
												</li>
											<? endif; ?>
											<? if ($permisos['generate'] ) :?>
												<li><a href="#" class="mb-control " data-box="#mb-signout<?=$email['Email']['id'];?>"><i class="fa fa-cogs"></i> Generar y guardar</a></li>
											<? endif; ?>
											<? if ($permisos['activate']) : ?>
												<? if ($email['Email']['activo'] == 1) { ?>
													<li><?= $this->Form->postLink('<i class="fa fa fa-eye-slash"></i> Desactivar', array('action' => 'desactivar', $email['Email']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Desactivar este registro', 'escape' => false)); ?></li>
												<? }else{ ?>
													<li><?= $this->Form->postLink('<i class="fa fa-eye"></i> Activar', array('action' => 'activar', $email['Email']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Activar este registro', 'escape' => false)); ?></li>
												<?	} ?>
											<? endif; ?>
											<? if ($permisos['delete']) :?>
												<li><?= $this->Form->postLink('<i class="fa fa-trash"></i> Eliminar', array('action' => 'delete', $email['Email']['id']), array('class' => '', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?></li>
											<? endif; ?>
											</ul>
										</li>                                                    
                                        </ul>
                                    </div>
                                    <div class="message-box message-box-warning animated fadeIn" data-sound="alert" id="mb-signout<?=$email['Email']['id'];?>">
										<div class="mb-container">
											<div class="mb-middle">
												<div class="mb-title"><span class="fa fa-cogs"></span>¿Generar y <strong>guardar</strong>?</div>
												<div class="mb-content">
													<p>¿Seguro que quieres generar y guardar?</p>
													<p>Se guardará el Html generado sobreescribiendo el anterior.</p>
													<p>Para cancelar presiona No</p>
												</div>
												<div class="mb-footer">
													<div class="pull-right">
														<?= $this->Form->postLink('Aceptar', array('action' => 'generarHtml', $email['Email']['id'], true), array('class' => 'btn btn-primary btn-lg', 'rel' => 'tooltip', 'title' => 'Ver', 'escape' => false)); ?>
														<button class="btn btn-default btn-lg mb-control-close">No</button>
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
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="pull-right">
				<ul class="pagination">
					<?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
					<?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
					<?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
				</ul>
			</div>
		</div>
	</div>
</div>
