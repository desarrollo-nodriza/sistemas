<div class="page-title">
	<h2><span class="fa fa-file"></span> DTE</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'dtes', 'action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$by  = (isset($this->request->params['named']['by'])) ? $this->request->params['named']['by'] : '' ;
				$txt = (isset($this->request->params['named']['txt'])) ? $this->request->params['named']['txt'] : '' ;
				$tyd = (isset($this->request->params['named']['tyd'])) ? $this->request->params['named']['tyd'] : '' ;
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
							<label>Buscar por:</label>
							<div class="form-inline">
							<?=$this->Form->select('by',
								array(
									'fol' => 'Folio', 
									'ord' => 'Pedido',
									'rut' => 'Rut Receptor'),
								array(
								'class' => 'form-control js-select-value',
								'empty' => 'Seleccione',
								'value' => $by
								)
							);?>
							<?=$this->Form->input('txt', array(
								'type' => 'text',
								'class' => 'form-control',
								'value' => $txt
								));?>
							</div>
						</div>
					</div>
					
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>Tipo de documento</label>
							<?=$this->Form->select('tyd', $this->Html->tipoDocumento,
								array(
								'class' => 'form-control',
								'empty' => 'Seleccione',
								'value' => $tyd
								)
							);?>
						</div>
					</div>
					<div class="col-sm-2 col-xs-12">
						<div class="form-group">
							<label>Estado del DTE</label>
							<?=$this->Form->select('sta', $this->Html->dteEstado('', true),
								array(
								'class' => 'form-control select',
								'empty' => 'Seleccione Estado',
								'multiple' => true,
								'value' => $sta
								)
							);?>
						</div>
					</div>
					<div class="col-sm-3 col-xs-12">
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
					<h3 class="panel-title">Listado de DTE´S</h3>
					<? if ($permisos['view']) : ?>
					<div class="btn-group pull-right">
						<? $export = array(
							'action' => 'exportar'
							);

						if (isset($this->request->params['named'])) {
							$export = array_replace_recursive($export, $this->request->params['named']);
						}?>
						<?= $this->Html->link('<i class="fa fa-file-excel-o"></i> Exportar a Excel', $export, array('class' => 'btn btn-primary', 'escape' => false)); ?>
					</div>
					<? endif; ?>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('folio', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('id_order', 'Pedido', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('Orden.payment', 'Método de pago', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('tipo_documento', 'Tipo de documento', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('rut_receptor', 'Rut receptor', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('estado', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('fecha', 'Fecha emisión', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $dtes as $dte ) : ?>
								<tr>
									<td><?= h($dte['Dte']['id']); ?>&nbsp;</td>
									<td><?= $folio = (!empty($dte['Dte']['folio'])) ? $dte['Dte']['folio'] : 'No aplica' ; ?></td>
									<td><?= h($dte['Dte']['id_order']); ?>&nbsp;</td>
									<td><?= h($dte['Orden']['payment']); ?>&nbsp;</td>
									<td><?= $this->Html->tipoDocumento[$dte['Dte']['tipo_documento']]; ?>&nbsp;</td>
									<td><?= $rut = (!empty($dte['Dte']['rut_receptor'])) ? $dte['Dte']['rut_receptor'] : 'No aplica' ; ?>&nbsp;</td>
									<td><?= $this->Html->dteEstado($dte['Dte']['estado']) ?>&nbsp;</td>
									<td><?= h($dte['Dte']['fecha']); ?>&nbsp;</td>
									<? if ($permisos['edit']) : ?>
									<td><?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('controller' => 'ordenes','action' => 'editar', $dte['Dte']['id'], $dte['Dte']['id_order']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?></td>
									<? endif; ?>
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
