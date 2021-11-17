<div class="page-title">
	<h2><span class="fa fa-flag-checkered"></span> Ubicaciones</h2>
</div>
<div class="page-content-wrap">

<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'ubicaciones', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$inputs 	= $this->request->params['named'] ?? null;
				$id     	= (isset($inputs['id'])) 		? str_replace('%2F', '/', urldecode($inputs['id'])) : '' ;
				$zona 		= (isset($inputs['zona_id'])) ? str_replace('%2F', '/', urldecode($inputs['zona_id'])) : '' ;
				$fila 		= (isset($inputs['fila'])) 	? str_replace('%2F', '/', urldecode($inputs['fila'])) : '' ;
				$columna	= (isset($inputs['columna'])) 		? str_replace('%2F', '/', urldecode($inputs['columna'])) : '' ;
				$activo 	= (isset($inputs['activo']))	? str_replace('%2F', '/', urldecode($inputs['activo'])) : '' ;
				$bodega_id 	= (isset($inputs['bodega_id']))	? str_replace('%2F', '/', urldecode($inputs['bodega_id'])) : '' ;
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Id Ubicacion</label>
							<?=$this->Form->input('id', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: 1, 10',
								'value' => $id
								))?>
						</div>
					</div>

					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Bodega</label>
							<?=$this->Form->select('bodega_id', $bodegas, array(
								'class' => 'form-control select',
								'data-live-search' => true,
								'empty' => 'Seleccione',
								'default' => $bodega_id
								));?>
						</div>
					</div>
					
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Zona</label>
							<?=$this->Form->select('zona_id', $zonas, array(
								'class' => 'form-control select',
								'data-live-search' => true,
								'empty' => 'Seleccione',
								'default' => $zona
								));?>
						</div>
					</div>
					

					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Columna</label>
							<?=$this->Form->input('columna', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: 0, 1, 13',
								'value' => $columna
								))?>
						</div>
					</div>

					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Fila</label>
							<?=$this->Form->input('fila', array(
								'class' => 'form-control',
								'type' => 'text',
								'placeholder' => 'Ej: 2-00, 4-00',
								'value' => $fila
								))?>
						</div>
					</div>

					
					
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Activo</label>
							<?=$this->Form->select('activo', array(
									'0' => 'No',
									'1' => 'Si'
								), array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'default' => $activo
								));?>
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
			<div class="page-content-wrap">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Listado de Ubicaciones</h3>
						<div class="btn-group pull-right">
						<? if ($permisos['add']) : ?>
							<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Ubicacion', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
							<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Creación masiva', array('action' => 'creacion_masiva'), array('class' => 'btn btn-danger', 'escape' => false)); ?>
						<? endif; ?>

						<?  
						$exportar = array(
							'action' => 'exportar'
						);

						$generar_qrs = array(
							'action' => 'crear_etiqueta_qr'
						);

						if (isset($this->request->params['named'])) 
						{
							$exportar 		= array_replace_recursive($exportar, $this->request->params['named']);
							$generar_qrs = array_replace_recursive($generar_qrs, $this->request->params['named']);
						}
						
						?>
							<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', $exportar, array('class' => 'btn btn-primary', 'escape' => false)); ?>
							<?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> Generar QRs', $generar_qrs, array('class' => 'btn btn-info', 'escape' => false)); ?>
						</div>
					</div>
					<div class="panel-body">

						<?= $this->element('contador_resultados', array('col' => false)); ?>

						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr class="sort">
                                        <th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('bodega_id', 'Bodega', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('zona', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('columna', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('fila', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										
                                        <th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th><?= $this->Paginator->sort('fecha_creacion', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $ubicaciones as $ubicacion ) : ?>
									<tr>
										<td><?= h($ubicacion['Ubicacion']['id']); ?>&nbsp;</td>
                                        <td><?= h($ubicacion['Zona']['Bodega']['nombre']); ?>&nbsp;</td>
										<td><?= h($ubicacion['Zona']['nombre']); ?>&nbsp;</td>
										<td><?= h($ubicacion['Ubicacion']['columna']); ?>&nbsp;</td>
                                        <td><?= h($ubicacion['Ubicacion']['fila']); ?>&nbsp;</td>
                                      
										<td><?= ($ubicacion['Ubicacion']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= h($ubicacion['Ubicacion']['fecha_creacion']); ?>&nbsp;</td>
										<td>
										<?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> Generar Qr', array('action' => 'qr_ubicacion', $ubicacion['Ubicacion']['id'], 'ext' => 'pdf'), array('class' => 'btn btn-xs btn-primary', 'rel' => 'tooltip', 'title' => 'EGenerar qr', 'escape' => false, 'target' => '_blank')); ?>
										<? if ($permisos['edit']) : ?>
											<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $ubicacion['Ubicacion']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
										<? endif; ?>
										<!-- <? if ($permisos['delete']) : ?>
											<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $ubicacion['Ubicacion']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
										<? endif; ?> -->
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


