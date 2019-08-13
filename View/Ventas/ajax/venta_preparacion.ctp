<? if (!empty($venta['Venta'])) : ?>

	<? if ($venta['Venta']['picking_estado'] == 'empaquetar') : ?>
	<div data-id="<?=$venta['Venta']['id']; ?>" class="task-item task-<?=($venta['Venta']['prioritario']) ? 'danger' : 'primary';?> <?=($venta['Venta']['prioritario']) ? 'venta-prioritaria' : 'venta-normal';?>">                                    
	    <div class="task-text">
	    	<h3 class="pull-left">Venta #<?=$venta['Venta']['id']; ?> - <?=(empty($venta['Venta']['marketplace_id'])) ? $venta['Tienda']['nombre'] : $venta['Marketplace']['nombre'] ; ?></h3> <?=($venta['Venta']['prioritario']) ? '<label class="label label-danger pull-right label-form">Prioritario</label></b>' : '';?>
	    	<table class="table table-bordered table-sm">
				<tr>
					<td><b>Fecha venta: </b></td>
					<td><?=$venta['Venta']['fecha_venta']; ?> <?= $retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['Venta']['fecha_venta']), 'Y-m-d H:i:s')); ?></td>
				</tr>
				<tr>
					<td>Estado</td>
					<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a></td>
				</tr>
				<tr>
					<td><b>Transporte: </b></td>
					<td><?=$venta['MetodoEnvio']['nombre']; ?></td>
				</tr>
				<!--<tr>
					<td><b>Cliente: </b></td>
					<td><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></td>
				</tr>
				<tr>
					<td><b>Fono: </b></td>
					<td><?=$venta['Venta']['fono_receptor'];?></td>
				</tr>-->
	    	</table>
	    </div>
	    <div class="task-footer">
	        <div class="pull-right"><button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#venta<?=$venta['Venta']['id'];?>"><i class="fa fa-eye"></i> Ver más</button> </div>                                
	    </div>                                    
	</div>

	<!-- Modal -->
	<div class="modal fade modal-venta-detalle" data-id="<?=$venta['Venta']['id'];?>"" id="venta<?=$venta['Venta']['id'];?>" tabindex="-1" role="dialog" aria-labelledby="venta<?=$venta['Venta']['id'];?>Label">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="venta<?=$venta['Venta']['id'];?>Label">Vista rápida venta #<?=$venta['Venta']['id']; ?> <a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a></h4>
	      </div>
	      <div class="modal-body">
	      	<h4><i class="fa fa-user"></i> Cliente</h4>
	      	<table class="table table-bordered">
	      		<tr>
	      			<td>Nombre</td>
	      			<td><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></td>
	      		</tr>
	      		<tr>
	      			<td>Rut</td>
	      			<td><?=$venta['VentaCliente']['rut'];?></td>
	      		</tr>
	      		<tr>
	      			<td>Email</td>
	      			<td><?=$venta['VentaCliente']['email'];?></td>
	      		</tr>
	      		<tr>
	      			<td>Fono</td>
	      			<td><?=$venta['VentaCliente']['telefono'];?></td>
	      		</tr>
	      	</table>
	      	<hr>
	      	<h4><i class="fa fa-truck"></i> Despacho</h4>
	      	<table class="table table-bordered">
	      		<tr>
	      			<td>Dirección</td>
	      			<td><?=$venta['Venta']['direccion_entrega'];?>, <?=$venta['Venta']['comuna_entrega'];?></td>
	      		</tr>
	      		<tr>
	      			<td>Receptor informado</td>
	      			<td><?=$venta['Venta']['nombre_receptor'];?></td>
	      		</tr>
	      		<tr>
	      			<td>Método envio</td>
	      			<td><?=$venta['MetodoEnvio']['nombre'];?></td>
	      		</tr>
	      	</table>
	      	<hr>
	      	<h4><i class="fa fa-shopping-cart"></i> Productos</h4>
	      	<table class="table table-bordered">
	      		<thead>
	      			<th>Id</th>
	      			<th>Nombre</th>
	      			<th>Cantidad</th>
	      			<th>Pendiente preparación</th>
	      			<th>Stock reservado</th>
	      		</thead>
	      	<? foreach ($venta['VentaDetalle'] as $ivd => $d) : ?>
				<?=$this->element('ventas/tr-producto-modal', array('d' => $d, 'confirmar' => 0));?>
	      	<? endforeach; ?>
	      	</table>
	      </div>
	      <div class="modal-footer">
	      	<?=$this->Html->link('Ver más', array('action' => 'view', $venta['Venta']['id']), array('class' => 'btn btn-info pull-left', 'target' => '_blank')); ?>
	        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
	      </div>
	    </div>
	  </div>
	</div>
	<? endif; ?>

	<? if ($venta['Venta']['picking_estado'] == 'empaquetando') : ?>
	<div class="task-item task-warning <?=($this->Session->read('Auth.Administrador.email') != $venta['Venta']['picking_email']) ? 'no-move' : '' ; ?>" data-id="<?=$venta['Venta']['id']; ?>" >                                    
	    <div class="task-text">
	    	
	    	<h3 class="pull-left">Venta #<?=$venta['Venta']['id']; ?> - <?=(empty($venta['Venta']['marketplace_id'])) ? $venta['Tienda']['nombre'] : $venta['Marketplace']['nombre'] ; ?></h3> <?=($venta['Venta']['prioritario']) ? '<label class="label label-danger pull-right label-form">Prioritario</label></b>' : '';?>
			
	    	<table class="table table-bordered table-sm">
				<tr>
					<td><b>Procesado por:</b></td>
					<td><?=$venta['Venta']['picking_email'];?></td>
				</tr>
				<tr>
					<td><b>Fecha venta: </b></td>
					<td><?=$venta['Venta']['fecha_venta']; ?> <?= $retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['Venta']['fecha_venta']), 'Y-m-d H:i:s')); ?></td>
				</tr>
				<tr>
					<td>Estado</td>
					<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a></td>
				</tr>
				<tr>
					<td><b>Transporte: </b></td>
					<td><?=$venta['MetodoEnvio']['nombre']; ?></td>
				</tr>
				<!--<tr>
					<td><b>Canal: </b></td>
					<td><?=(empty($venta['Venta']['marketplace_id'])) ? $venta['Tienda']['nombre'] : $venta['Marketplace']['nombre'] ; ?></td>
				</tr>
				<tr>
					<td><b>Cliente: </b></td>
					<td><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></td>
				</tr>
				<tr>
					<td><b>Fono: </b></td>
					<td><?=$venta['Venta']['fono_receptor'];?></td>
				</tr>-->
	    	</table>
	    </div>
	    <div class="task-footer">
	        <div class="pull-right">
				<!-- DESCARGAR DOCUMENTOS -->
	           	<?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> Documentos.', array('controller' => 'ventas', 'action' => 'generar_documentos', $venta['Venta']['id'], true, false), array('class' => 'btn btn-success btn-xs js-generar-documentos-venta js-generar-documentos-venta-primario', 'rel' => 'tooltip', 'title' => 'Generar Documentos', 'escape' => false)); ?>

	            <!-- DESCARGAR ETIQUETA -->
	            <?= $this->Html->link('<i class="fa fa-cube"></i> Etiqueta', array('controller' => 'ventas', 'action' => 'generar_etiqueta', $venta['Venta']['id'], 1), array('class' => 'btn btn-warning btn-xs js-generar-etiqueta-venta', 'rel' => 'tooltip', 'title' => ' Generar Etiqueta', 'escape' => false)); ?>

	        	<button class="btn btn-xs btn-primary js-venta-ver-mas" data-id="<?=$venta['Venta']['id'];?>"><i class="fa fa-eye"></i> Ver más</button> 
	        </div>                                
	    </div>                                    
	</div>

	
	
	<? if ( empty($venta['Venta']['marketplace_id']) || $venta['Marketplace']['marketplace_tipo_id'] == 1 ) : ?>
	
	<!-- Modal cambiar estado -->
	<div class="modal fade modal-cambiar-estado" data-backdrop="static" id="modal-cambiar-estado-<?=$venta['Venta']['id'];?>" tabindex="-1" role="dialog" aria-labelledby="modal-cambiar-estado-<?=$venta['Venta']['id'];?>Label">
	<?= $this->Form->create('Venta', array('url' => array('controller' => 'ventas', 'action' => 'cambiar_estado'), 'inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate-oc js-form-cambiar-estado-venta', 'id' => 'FormCambiarEstadoVenta' . $venta['Venta']['id'], 'data-id' => $venta['Venta']['id'])); ?>
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="venta<?=$venta['Venta']['id'];?>Label">Cambiar estado</h4>
	      </div>
	      <div class="modal-body">
			<div class="row">
				<div class="col-xs-12">
					<h4><i class="fa fa-envelope" aria-hidden="true"></i> <?= __('Mensajes de la venta');?></h4>
					
					<ul class="list-group messages-dte-box">
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
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-bordered">
						<tr>
							<td>Método envio seleccionado</td>
							<td><?=$venta['MetodoEnvio']['nombre'];?></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<?= $this->Form->hidden('id_externo', array('value' => $venta['Venta']['id_externo'])); ?>
					<?= $this->Form->hidden('picking_estado', array('value' => 'empaquetado')); ?>
					<?= $this->Form->hidden('tienda_id', array('value' => $venta['Venta']['tienda_id'])); ?>
					<?= $this->Form->hidden('marketplace_id', array('value' => $venta['Venta']['marketplace_id'])); ?>
					<div class="form-group">
						<?= $this->Form->label('venta_estado_id', 'Nuevo estado de la venta');?>
						<?= $this->Form->select('venta_estado_id', $venta_estados, array('class' => 'form-control not-blank', 'empty' => 'Seleccione', 'default' => $venta['Venta']['venta_estado_id'])); ?>
						<span id="helpBlock" class="help-block">Se le notificará al <b>CLIENTE SOLO SI CORRESPONDE</b> el cambio de estado.</span>
					</div>
				</div>
			</div>
	      </div>
	      <div class="modal-footer">
	      	<?= $this->Form->button('<i class="fa fa-send" aria-hidden="true"></i> Confirmar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-success')); ?>
	        <button type="button" class="btn btn-primary js-cancelar-cambio-estado" data-dismiss="modal">Cancelar</button>
	      </div>
	    </div>
	  </div>
	<?= $this->Form->end(); ?>
	</div>

	<? endif; ?>

	<? endif; ?>


	<? if ($venta['Venta']['picking_estado'] == 'empaquetado') : ?>
	<div class="task-item task-success <?=($this->Session->read('Auth.Administrador.email') != $venta['Venta']['picking_email']) ? 'no-move' : '' ; ?>" data-id="<?=$venta['Venta']['id']; ?>">                                    
	    <div class="task-text">
	    	
	    	<h3 class="pull-left">Venta #<?=$venta['Venta']['id']; ?> - <?=(empty($venta['Venta']['marketplace_id'])) ? $venta['Tienda']['nombre'] : $venta['Marketplace']['nombre'] ; ?></h3> <label class="label label-success pull-right label-form">Empaquetado</label></b>

	    	<table class="table table-bordered table-sm">
				<tr>
					<td><b>Fecha venta: </b></td>
					<td><?=$venta['Venta']['fecha_venta']; ?> <?= $retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['Venta']['fecha_venta']), 'Y-m-d H:i:s')); ?></td>
				</tr>
				<tr>
					<td>Estado</td>
					<td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a></td>
				</tr>
				<tr>
					<td><b>Transporte: </b></td>
					<td><?=$venta['MetodoEnvio']['nombre']; ?></td>
				</tr>
				<!--<tr>
					<td><b>Canal: </b></td>
					<td><?=(empty($venta['Venta']['marketplace_id'])) ? $venta['Tienda']['nombre'] : $venta['Marketplace']['nombre'] ; ?></td>
				</tr>
				<tr>
					<td><b>Cliente: </b></td>
					<td><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></td>
				</tr>
				<tr>
					<td><b>Fono: </b></td>
					<td><?=$venta['Venta']['fono_receptor'];?></td>
				</tr>-->
	    	</table>
	    </div>
	    <div class="task-footer">

	        <div class="pull-right">
				
				<!-- DESCARGAR DOCUMENTOS -->
	            <?= $this->Html->link('<i class="fa fa-file-pdf-o"></i> Documentos.', array('controller' => 'ventas', 'action' => 'generar_documentos', $venta['Venta']['id'], true, false), array('class' => 'btn btn-success btn-xs js-generar-documentos-venta js-generar-documentos-venta-primario', 'rel' => 'tooltip', 'title' => 'Generar Documentos', 'escape' => false)); ?>

	            <!-- DESCARGAR ETIQUETA -->
	            <?= $this->Html->link('<i class="fa fa-cube"></i> Etiqueta', array('controller' => 'ventas', 'action' => 'generar_etiqueta', $venta['Venta']['id'], 1), array('class' => 'btn btn-warning btn-xs js-generar-etiqueta-venta', 'rel' => 'tooltip', 'title' => ' Generar Etiqueta', 'escape' => false)); ?>

	        	<button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#venta<?=$venta['Venta']['id'];?>"><i class="fa fa-eye"></i> Ver más</button> </div>                                
	    </div>                                    
	</div>

	<!-- Modal -->
	<div class="modal fade modal-venta-detalle" data-id="<?=$venta['Venta']['id'];?>"" id="venta<?=$venta['Venta']['id'];?>" tabindex="-1" role="dialog" aria-labelledby="venta<?=$venta['Venta']['id'];?>Label">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="venta<?=$venta['Venta']['id'];?>Label">Vista rápida venta #<?=$venta['Venta']['id']; ?> <a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a></h4>
	      </div>
	      <div class="modal-body">
			<div class="row">
				<div class="col-xs-12 col-md-4">
					<h4><i class="fa fa-qrcode" aria-hidden="true"></i> App Nodriza</h4>
					<img src="https://chart.googleapis.com/chart?chs=<?=$tamano;?>&cht=qr&chl=<?=$url;?>&choe=UTF-8" title="QR" class="img-responsive qr-code"/>
					<hr>

					<h4><i class="fa fa-envelope" aria-hidden="true"></i> <?= __('Mensajes de la venta');?></h4>
					
					<ul class="list-group messages-dte-box">
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
				<div class="col-xs-12 col-md-8">
					<h4><i class="fa fa-user"></i> Cliente</h4>
			      	<table class="table table-bordered">
			      		<tr>
			      			<td>Nombre</td>
			      			<td><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></td>
			      		</tr>
			      		<tr>
			      			<td>Rut</td>
			      			<td><?=$venta['VentaCliente']['rut'];?></td>
			      		</tr>
			      		<tr>
			      			<td>Email</td>
			      			<td><?=$venta['VentaCliente']['email'];?></td>
			      		</tr>
			      		<tr>
			      			<td>Fono</td>
			      			<td><?=$venta['VentaCliente']['telefono'];?></td>
			      		</tr>
			      	</table>
			      	<hr>
			      	<h4><i class="fa fa-truck"></i> Despacho</h4>
			      	<table class="table table-bordered">
			      		<tr>
			      			<td>Dirección</td>
			      			<td><?=$venta['Venta']['direccion_entrega'];?>, <?=$venta['Venta']['comuna_entrega'];?></td>
			      		</tr>
			      		<tr>
			      			<td>Receptor informado</td>
			      			<td><?=$venta['Venta']['nombre_receptor'];?></td>
			      		</tr>
			      		<tr>
			      			<td>Método envio</td>
			      			<td><?=$venta['MetodoEnvio']['nombre'];?></td>
			      		</tr>
			      	</table>
			      	<hr>
			      	<h4><i class="fa fa-shopping-cart"></i> Productos</h4>
			      	<table class="table table-bordered">
			      		<thead>
			      			<th>Id</th>
			      			<th>Nombre</th>
			      			<th>Cantidad</th>
			      			<th>Pendiente preparación</th>
			      			<th>Stock reservado</th>
			      		</thead>
				      	<? foreach ($venta['VentaDetalle'] as $ivd => $d) : ?>
							<?=$this->element('ventas/tr-producto-modal', array('d' => $d, 'confirmar' => 0));?>
				      	<? endforeach; ?>
			      	</table>
				</div>
			</div>
	      </div>
	      <div class="modal-footer">
	      	<?=$this->Html->link('Ver más', array('action' => 'view', $venta['Venta']['id']), array('class' => 'btn btn-info pull-left', 'target' => '_blank')); ?>
	        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
	      </div>
	    </div>
	  </div>
	</div>
	<? endif; ?>

<? endif; ?>