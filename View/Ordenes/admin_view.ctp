<div class="page-title">
	<h2><span class="fa fa-money"></span> <?=__('Venta #' . $venta['Venta']['id']); ?></h2>
</div>

<? 

$TotalProductos = 0; 

foreach (Hash::extract($venta['Dte'], '{n}.DteDetalle.{n}') as $indice => $detalle) : 

	$TotalProductos = $TotalProductos + ($detalle['PrcItem'] * $detalle['QtyItem']);

endforeach;

$venta['Venta']['total'] = $venta['Venta']['total'] = ($TotalProductos * 1.19);

?>


<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-9">


			<!-- INFORMACIÓN DE LA VENTA -->
			<div class="panel panel panel-success panel-toggled">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-info" aria-hidden="true"></i> <?=__('Información de la venta'); ?></h3>
					<ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="table-responsive">
								<table class="table table-bordered">
									<tr>
										<th>Referencia</th>
										<td><?= $venta['Venta']['referencia']; ?></td>
									</tr>
									<tr>
										<th>ID Externo</th>
										<td><?= $venta['Venta']['id_externo']; ?></td>
									</tr>
									<tr>
										<th>Estado</th>
										<td><span class="btn btn-xs btn-<?= $venta['VentaEstado']['VentaEstadoCategoria']['estilo']; ?>"><?= $venta['VentaEstado']['VentaEstadoCategoria']['nombre']; ?></span></td>
									</tr>
									<tr>
										<th>Fecha</th>
										<td><?= date_format(date_create($venta['Venta']['fecha_venta']), 'd/m/Y H:i:s'); ?></td>
									</tr>
									<tr>
										<th>Medio de Pago</th>
										<td><?= $venta['MedioPago']['nombre']; ?></td>
									</tr>
									<tr>
										<th>Tienda</th>
										<td><?= $venta['Tienda']['nombre']; ?></td>
									</tr>
									<tr>
										<th>Marketplace</th>
										<td><?php if (!empty($venta['Venta']['marketplace_id'])) {echo $venta['Marketplace']['nombre'];} ?>&nbsp;</td>
									</tr>
									<tr>
										<th>Atendida</th>
										<td><?= ($venta['Venta']['atendida'] ? "<span class='btn btn-xs btn-success'>Sí</span>" : "<span class='btn btn-xs btn-danger'>No</span>"); ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- DTE -->
			<div class="panel panel-success">
				<div class="panel-heading">
					<h3 id="dte" class="panel-title"><i class="fa fa-file-text" aria-hidden="true"></i> <?=__('Descargar DTE'); ?></h3>
					<ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<?= $this->Html->link(
								'<i class="fa fa-file"></i> Ver ' . $this->Html->tipoDocumento[$venta['Dte'][0]['tipo_documento']],
								sprintf('/Dte/%d/%d/%s', $venta['Venta']['id'], $venta['Dte'][0]['id'], $venta['Dte'][0]['pdf']),
								array(
									'class' => 'btn btn-success btn-block btn-lg', 
									'target' => '_blank', 
									'fullbase' => true,
									'escape' => false) 
								); ?>
						</div>
					</div>
				</div>			

				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Html->link('Volver', array('controller' => 'ventas', 'action' => 'view', $venta['Venta']['id']), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>

			<!-- Enviar DTE por email -->
			<? if ( $venta['Dte'][0]['estado'] == 'dte_real_emitido' && !empty($venta['Dte'][0]['pdf']) ) : ?>
			<?= $this->Form->create('Orden', array(
				'url' => array(
					'action' => 'enviarDteViaEmail',
				), 'method' => 'post' ,'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
				<?= $this->Form->input('id_dte', array('type' => 'hidden', 'value' => $venta['Dte'][0]['id'])); ?>
				<?= $this->Form->input('venta_id', array('type' => 'hidden', 'value' => $venta['Dte'][0]['venta_id'])); ?>
				<?= $this->Form->input('dte', array('type' => 'hidden', 'value' => $venta['Dte'][0]['tipo_documento'])); ?>
				<?= $this->Form->input('folio', array('type' => 'hidden', 'value' => $venta['Dte'][0]['folio'])); ?>
				<?= $this->Form->input('emisor', array('type' => 'hidden', 'value' => $venta['Dte'][0]['emisor'])); ?>
				
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-envelope" aria-hidden="true"></i> <?=__('Enviar DTE vía email'); ?></h3>
						<ul class="panel-controls">
	                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-up"></span></a></li>
	                    </ul>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table">
								<tr>
									<th><?=__('Asunto');?></th>
									<td><?=$this->Form->input('asunto', array('value' => sprintf('Su %s ha sido emitida.', $this->Html->tipoDocumento[$venta['Dte'][0]['tipo_documento']]), 'placeholder' => 'Agregue un asunto para el email'));?></td>
								</tr>
								<tr>
									<th><?=__('Mensaje');?></th>
									<td><?=$this->Form->textarea('mensaje', array('value' => sprintf('Estimado/a %s. Hemos emitido su %s exitosamente para su compra referencia #%s. El documento los encontrará adjunto a este email. Por favor NO RESPONDA ESTE EMAIL ya que es generado automáticamente.', $venta['VentaCliente']['nombre'], $this->Html->tipoDocumento[$venta['Dte'][0]['tipo_documento']], $venta['Venta']['referencia']), 
										'class' => 'form-control', 
										'placeholder' => 'Agregue un mensaje personalizado para este email.'));?></td>
								</tr>
								<tr>
									<th><?=__('Emails');?></th>
									<td><?= $this->Form->input('emails', array('value' => $venta['VentaCliente']['email'], 'class' => 'form-control', 'placeholder' => 'Email para enviar')); ?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="panel-footer">
						<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-paper-plane" aria-hidden="true"></i> Enviar Email</button>
					</div>
				</div>
					
			<?= $this->Form->end(); ?>
			<? endif; ?>

		</div> <!-- end col -->
		

		
		<div class="col-xs-12 col-sm-3">

			<!-- TOTAL VENTA -->
			<a class="tile tile-primary">
                <?= CakeNumber::currency($venta['Venta']['total'], 'CLP'); ?>
                <p><?=__('Total documento');?></p>
            </a>
			

			<!-- CLIENTE -->

			<div class="panel panel-default">
				<div class="panel-body profile bg-info">

					<div class="profile-image">
						<img src="https://picsum.photos/200/200/?random">
					</div>
					<div class="profile-data">
					<div class="profile-data-name"><?= $venta['VentaCliente']['nombre']; ?> <?= $venta['VentaCliente']['apellido']; ?></div>
					<div class="profile-data-title text-primary"><?= __('Cliente'); ?></div>
					</div>

				</div>
				<ul class="panel-body list-group">
					
					<li class="list-group-item"><span class="fa fa-user"></span> <?= (!empty($venta['VentaCliente']['rut'])) ? $venta['VentaCliente']['rut'] : 'xxxxxxxx-x'; ?></li>
					
					<li class="list-group-item"><span class="fa fa-phone"></span> <?= (!empty($venta['VentaCliente']['telefono'])) ? $venta['VentaCliente']['telefono'] : 'x xxxx xxxx'; ?></li>

					<li class="list-group-item"><span class="fa fa-envelope"></span> <?= (!empty($venta['VentaCliente']['email'])) ? $venta['VentaCliente']['email'] : 'xxxxx@xxxx.xx'; ?></li>

				</ul>                            
			</div>


			<!-- MENSAJES -->

			<div class="panel panel-default">
				<div class="panel-body">
					<h4><i class="fa fa-envelope" aria-hidden="true"></i> <?= __('Mensajes de la venta');?></h4>
				</div>
				<ul class="panel-body list-group messages-dte-box">
				<? 
				if (!empty($venta['VentaMensaje'])) :

					foreach ($venta['VentaMensaje'] as $mensaje) : ?>
					<li class="list-group-item">
						<span class="message-subject">
							<?= (!empty($mensaje['asunto'])) ? $mensaje['asunto'] : 'Sin Asunto'; ?>
						</span>
						<span class="message-message">
							<?= $mensaje['mensaje']; ?>
						</span>
						<span class="message-date">
							<?= $mensaje['fecha']; ?>
						</span>
					</li>
					<?
					endforeach;
				else : ?>
					
					<li class="list-group-item text-mutted">
						<?= __('No registra mensajes.'); ?>
					</li>

				<?	
				endif; ?>

				</ul>  
				
			</div>

		</div>

	</div> <!-- end row -->
	
</div>