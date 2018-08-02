<div class="page-title">
	<h2><span class="fa fa-file-pdf-o"></span> PDF DTE Maestros</h2>
</div>

<div class="page-content-wrap">
	
	<? if (!empty($resultados['success']['messages']) || !empty($resultados['errors']['messages'])) : ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-cogs" aria-hidden="true"></i> Resultados de la operación</h3>
				</div>
				<div class="panel-body">
					<h3 class="text-success"><i class="fa fa-check"></i> DTE´S Procesados con éxito: <?=$resultados['success']['total'];?></h3>
					
					<? if (!empty($resultados['pdf']['result'])) : ?>
					<p><b>PDF Maestros generados:</b></p>
					<ul>
					<? foreach ($resultados['pdf']['result'] as $i => $document) : ?>
						<li><a href="<?=$document['document']?>" download><?=$document['document']?></a></li>	
					<? endforeach; ?>
					</ul>
					<? endif; ?>
				</div>
				<div class="panel-body">
					<h3 class="text-danger"><i class="fa fa-ban"></i> DTE´S con errores o incompletos: <?=$resultados['errors']['total'];?></h3>
					
					<? if (!empty($resultados['errors']['messages'])) : ?>
					<p><b>Mensajes de errores:</b></p>
					<ul>
					<? foreach ($resultados['errors']['messages'] as $im => $message) : ?>
						<li><?=$message;?></li>	
					<? endforeach; ?>
					</ul>
					<? endif; ?>
				</div>
			</div>
		</div>
	</div>
	<? endif; ?>
	
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Filtro', array('url' => array('controller' => 'dtes', 'action' => 'generarpdf'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
			<? 
				$by  = (isset($this->request->params['named']['by'])) ? $this->request->params['named']['by'] : '' ;
				$txt = (isset($this->request->params['named']['txt'])) ? $this->request->params['named']['txt'] : '' ;
				$tyd = (isset($this->request->params['named']['tyd'])) ? $this->request->params['named']['tyd'] : '' ;
				$sta = (isset($this->request->params['named']['sta'])) ? $this->request->params['named']['sta'] : '' ;
				$dtf = (isset($this->request->params['named']['dtf'])) ? $this->request->params['named']['dtf'] : '' ;
				$dtt = (isset($this->request->params['named']['dtt'])) ? $this->request->params['named']['dtt'] : '' ;
			?>
			<input type="hidden" name="data[Filtro][generarpdf]" value="1">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtar y generar</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>Filtrar por:</label>
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
							<a href="#" class="mb-control btn btn-buscar btn-success btn-block" data-box="#mb-alerta-pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Generar PDF Maestro</a>

							<div class="message-box message-box-banger animated fadeIn" data-sound="alert" id="mb-alerta-pdf">
								<div class="mb-container">
									<div class="mb-middle">
										<div class="mb-title"><span class="fa fa-floppy-o"></span>¿Generar <strong>PDF Maestro</strong>?</div>
										<div class="mb-content">
											<p>Se generará un PDF que contenga todos los DTES emitidos según el filtro.</p>
											<p><i>Deje vacio para crearlos todos.</i></p>
											<p>Para cancelar presiona No</p>
										</div>
										<div class="mb-footer">
											<div class="pull-right">
												
												<button type="submit" class="btn btn-warning btn-lg js-procesar"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Sí, Generar PDF Maestro</button>
												<button class="btn btn-default btn-lg mb-control-close">No</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar', array('action' => 'generarpdf'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
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
					<h3 class="panel-title">Listado de PDF Maestros</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr class="sort">
									<th>Nombre</th>
									<th>Fecha creación</th>
									<th>Tamaño (MB)</th>
									<th>Archivo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $archivos as $archivo ) : ?>
								<tr>
									<td>
										<?=$archivo['Nombre'];?>
									</td>
									<td>
										<?=$archivo['Modificado'];?>
									</td>
									<td>
										<?=$archivo['Tamaño'];?>
									</td>
									<td>
										<a href="<?=$archivo['Ruta_completa'];?>" class="btn btn-xs btn-primary" download><i class="fa fa-eye"></i> Ver PDF</a>
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
</div>

<script type="text/javascript">
	
	$('.js-procesar').on('click', function(){
		$(this).attr('disabled', 'disabled');
		$('.mb-control-close').attr('disabled', 'disabled');
	});

</script>