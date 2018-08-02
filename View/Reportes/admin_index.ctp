<div class="page-title">
	<h2><span class="fa fa-file-text"></span> Reportes</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Generar Reportes</h3>
					<div class="btn-group pull-right">
					<? if ($permisos['edit']) : ?>
						<?= $this->Html->link('<i class="fa fa-list"></i> Todos', array('action' => 'all'), array('class' => 'btn btn-info', 'escape' => false)); ?>
					<? endif; ?>
					<? if ($permisos['add']) : ?>
						<?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Reporte', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
					<? endif; ?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', array('action' => 'exportar'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
				</div>
				<div class="panel-body">
				<?= $this->Form->create('Reporte', array('action' => 'generate', 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
					<div class="wizard show-submit wizard-validation">
                        <ul>
                            <li>
                                <a href="#step-1">
                                    <span class="stepNumber">1</span>
                                    <span class="stepDesc">Paso 1<br /><small>Seleccione reporte</small></span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-2">
                                    <span class="stepNumber">2</span>
                                    <span class="stepDesc">Paso 2<br /><small>Seleccione Perido</small></span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-3">
                                    <span class="stepNumber">3</span>
                                    <span class="stepDesc">Paso 3 <br /><small>Seleccione Gráficos</small></span>                   
                                </a>
                            </li>
                            <li>
                                <a href="#step-4">
                                    <span class="stepNumber">4</span>
                                    <span class="stepDesc">Paso 4<br /><small>Confirmación</small></span>                   
                                </a>
                            </li>
                        </ul>
                        <div id="step-1">
                        	<div class="form-group">
	                        	<label class="col-md-3 control-label">Seleccione el reporte que desea generar</label>
	                        	<div class="col-xs-12 col-sm-5">
	                            <?= $this->Form->input('id_reporte', array(
	                            	'empty' => 'Seleccione reporte',
	                            	'options' => $reportes,
	                            	'class' => 'form-control'
	                            )); ?>
	                            </div>
                            </div>
                        </div>
                        <div id="step-2">                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">Seleccione rango</label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                    	<?=$this->Form->input('f_inicio', array('class' => 'form-control datepicker'));?>
                                    	<span class="input-group-addon add-on"> - </span>
                                    	<?=$this->Form->input('f_final', array('class' => 'form-control datepicker'));?>
                                    </div>
                                </div>
                            </div>
                        </div>                      
                        <div id="step-3">
                            <div class="form-group">
	                        	<label class="col-md-3 control-label">Seleccione uno o más gráficos</label>
	                        	<div class="col-xs-12 col-sm-5">
	                            <?= $this->Form->input('id_grafico', array(
	                            	'empty' => 'Seleccione graficos',
	                            	'options' => $graficos,
	                            	'class' => 'form-control select',
	                            	'multiple'	=> 'multiple'
	                            )); ?>
	                            </div>
                            </div>
                        </div>
                        <div id="step-4">
                        	<div class="col-xs-12">
	                            <div class="form-group">
	                            	<div id="resume"></div>
	                            </div>
                            </div>
                        </div>                           
                    </div>
                <?= $this->Form->end(); ?>
					<!--<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Fecha de creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tienda_id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $reportes as $reporte ) : ?>
								<tr>
									<td><?= h($reporte['Reporte']['nombre']); ?>&nbsp;</td>
									<td><?= ($reporte['Reporte']['activo'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
									<td><?= h($reporte['Reporte']['created']); ?>&nbsp;</td>
									<td><?= h($reporte['Tienda']['nombre']); ?>&nbsp;</td>
									<td>
									<? if ($permisos['edit']) : ?>
										<?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $reporte['Reporte']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
									<? endif; ?>
									<? if ($permisos['generate']) : ?>
										<?= $this->Html->link('<i class="fa fa-cog"></i> Generar', array('action' => 'generate', $reporte['Reporte']['id']), array('class' => 'btn btn-xs btn-primary', 'rel' => 'tooltip', 'title' => 'Generar reporte', 'escape' => false)); ?>
									<? endif; ?>
									<? if ($permisos['delete']) : ?>
										<?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $reporte['Reporte']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
									<? endif; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>-->
				</div>
			</div>
		</div>
	</div>
</div>
