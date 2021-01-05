<!-- Modal -->
<div class="modal fade modal-venta-detalle" data-id="<?=$venta['Venta']['id'];?>" id="venta<?=$venta['Venta']['id'];?>" tabindex="-1" role="dialog" aria-labelledby="venta<?=$venta['Venta']['id'];?>Label">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="venta<?=$venta['Venta']['id'];?>Label">Embalando paquetes de la venta #<?=$venta['Venta']['id']; ?> <a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a></h4>
      </div>
      <div class="modal-body">
		
			<div class="col-xs-12 col-md-4">
				<h4><i class="fa fa-qrcode" aria-hidden="true"></i> App Nodriza</h4>
				<img src="https://chart.googleapis.com/chart?chs=<?=$tamano;?>&cht=qr&chl=<?=$url;?>&choe=UTF-8" title="QR" class="img-responsive qr-code"/>
					
				<hr>
				<h4><i class="fa fa-money"></i> Detalle de la venta</h4>
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr>
							<td>Referencia</td>
							<td><?=$venta['Venta']['referencia'];?></td>
						</tr>
						<tr>
							<td>Canal de venta</td>
							<td>#<?=$venta['Venta']['id_externo']?> - <?=(empty($venta['Venta']['marketplace_id'])) ? $venta['Tienda']['nombre'] : $venta['Marketplace']['nombre'] ;?> </td>
						</tr>
					</table>
				</div>

				<hr>
				<h4><i class="fa fa-bell"></i> Nota interna</h4>
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr class="<?= (!empty($venta['Venta']['nota_interna'])) ? 'success' : '' ; ?>">
			      			<td><?=$venta['Venta']['nota_interna'];?></td>
			      		</tr>
					</table>
				</div>

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
		      			<td><?=$venta['Venta']['direccion_entrega'];?> <?=$venta['Venta']['numero_entrega'];?> <?=$venta['Venta']['otro_entrega'];?>, <?=$venta['Venta']['ciudad_entrega'];?> - <?=$venta['Venta']['comuna_entrega'];?></td>
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
		      	<? if (!empty($bultos)) : ?>
		      	<hr>
		      	<h4><i class="fa fa-cubes"></i> Bultos a armar</h4>
		      	<table class="table table-bordered">
		      	<? $i = 1; ?>
		      	<? foreach ($bultos as $ib => $b) : ?>
					<tr>
						<td style="width: 170px;">
							Bulto #<?=$i;?>
							<br>
							<b>Dimensiones aprox:</b> 
								<ul>
									<li>Largo <?=$b['paquete']['length']; ?> cm</li>
									<li>Ancho <?=$b['paquete']['width']; ?> cm</li>
									<li>Alto <?=$b['paquete']['height']; ?> cm</li>
								</ul>
							<br>
							<b>Peso aprox:</b>
								<ul>
									<li><?=$b['paquete']['weight']; ?> kg</li>
								</ul>
						</td>
						<td><ul><?= $b['items']; ?></ul></td>
					</tr>
				<? $i++; ?>
		      	<? endforeach; ?>
		      	</table>
		      	<hr>
		      	<? endif; ?>
		      	<h4><i class="fa fa-shopping-cart"></i> Productos</h4>
		      	<table class="table table-bordered">
		      		<thead>
		      			<th>Id</th>
		      			<th>Nombre</th>
		      			<th>Pendiente <br> preparación</th>
		      			<th>Stock <br> reservado</th>
		      			<th></th>
		      		</thead>
		      	<? foreach ($venta['VentaDetalle'] as $ivd => $d) : ?>
					<?=$this->element('ventas/tr-producto-modal', array('d' => $d, 'confirmar' => 1));?>
		      	<? endforeach; ?>
		      	</table>
			</div>
		
	  </div>
	  
	  <div class="modal-body js-revision-form hidden">
		<hr> 
		
		<h3>¿Deseas enviar a revisión manual esta venta?</h3>
		<p>Indique el problema para que el administrador pueda resolverlo. <b>No omitas los detalles importantes</b>.</p>
		<?= $this->Form->create('Venta', array('id' => 'VentaRevisionForm', 'class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
			<?= $this->Form->input('id', array('value' => $venta['Venta']['id'], 'type' => 'hidden'));?>
			<?= $this->Form->input('estado', array('value' => 'en_revision', 'type' => 'hidden')); ?>
			<div class="form-group">
				<?= $this->Form->textarea('picking_motivo_revision', array('class' => 'form-control not-blank', 'rows' => 4, 'placeholder' => 'Indique cuál es el problema...')); ?>
			</div>
			<div class="form-group">
				<div class="btn-group btn-group-justified">
					<div class="btn-group" role="group">
						<button type="submit" class="btn btn-warning"><i class="fa fa-exclamation"></i> Enviar a revisión</button>
					</div>
					<div class="btn-group" role="group">
						<button class="btn btn-dafult js-close-set-picking-revision">Cancelar</button>
					</div>
				</div>
			</div>
		<?= $this->Form->end(); ?>
	  </div>
      <div class="modal-footer">
      	<div class="btn-group pull-left">
		  <?=$this->Html->link('<i class="fa fa-eye"></i> Ver más', array('action' => 'view', $venta['Venta']['id']), array('class' => 'btn btn-info', 'target' => '_blank', 'escape' => false)); ?>
		  <button type="button" class="btn btn-warning js-open-set-picking-revision"><i class="fa fa-exclamation"></i> Pasar a revisión manual</button>
		</div>
		<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
      </div>
	</div>
  </div>
</div>