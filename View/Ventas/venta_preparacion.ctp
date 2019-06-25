<? if (!empty($venta)) : ?>

	<? if ($venta['subestado'] == 'empaquetar') : ?>
	<div class="task-item task-primary">                                    
	    <div class="task-text">
	    	<ul>
	    		<li><b>Venta: </b><?=$venta['Venta']['id']; ?></li>
	    		<li><b>Fecha venta: </b><?=$venta['Venta']['fecha_venta']; ?></li>
	    		<li><b>Transporte: </b><?=$venta['MetodoEnvio']['nombre']; ?></li>
	    		<li><b>Total items: </b><?=array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')); ?></li>
	    		<li><b>Nombre del cliente: </b><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></li>
	    		<li><b>Fono del cliente: </b><?=$venta['Venta']['fono_receptor'];?></li>
	    	</ul>
	    </div>
	    <div class="task-footer">
	        <div class="pull-left"><span class="fa fa-clock-o"></span> <?= $retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['Venta']['fecha_venta']), 'Y-m-d H:i:s')); ?></div>
	        <div class="pull-right"><button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#venta<?=$iv;?>"><i class="fa fa-eye"></i> Ver más</button> </div>                                
	    </div>                                    
	</div>

	<!-- Modal -->
	<div class="modal fade" id="venta<?=$iv;?>" tabindex="-1" role="dialog" aria-labelledby="venta<?=$iv;?>Label">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="venta<?=$iv;?>Label">Vista rápida venta #<?=$venta['Venta']['id']; ?></h4>
	      </div>
	      <div class="modal-body">
	      	<h4><i class="fa fa-user"></i> Cliente</h4>
	      	<table class="table">
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
	      	<table class="table">
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
	      	<h4><i class="fa fa-cart"></i> Productos</h4>
	      	<table class="table">
	      		<thead>
	      			<th>Id</th>
	      			<th>Referencia</th>
	      			<th>Nombre</th>
	      			<th>Cantidad</th>
	      			<th>Pendiente entrega</th>
	      		</thead>
	      	<? foreach ($venta['VentaDetalle'] as $ivd => $d) : ?>
				<tr>
					<td><?=$d['venta_detalle_producto_id'];?></td>
					<td><?=$d['VentaDetalleProducto']['referencia'];?></td>
					<td><?=$d['VentaDetalleProducto']['nombre'];?></td>
					<td><?=$d['cantidad'];?></td>
					<td><?=$d['cantidad_pendiente_entrega'];?></td>
				</tr>
	      	<? endforeach; ?>
	      	</table>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Save changes</button>
	      </div>
	    </div>
	  </div>
	</div>
	<? endif; ?>

	<? if ($venta['subestado'] == 'empaquetar') : ?>
	<div class="task-item task-primary">                                    
	    <div class="task-text">
	    	<ul>
	    		<li><b>Venta: </b><?=$venta['Venta']['id']; ?></li>
	    		<li><b>Fecha venta: </b><?=$venta['Venta']['fecha_venta']; ?></li>
	    		<li><b>Transporte: </b><?=$venta['MetodoEnvio']['nombre']; ?></li>
	    		<li><b>Total items: </b><?=array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')); ?></li>
	    		<li><b>Nombre del cliente: </b><?=$venta['VentaCliente']['nombre'];?> <?=$venta['VentaCliente']['apellido'];?></li>
	    		<li><b>Fono del cliente: </b><?=$venta['Venta']['fono_receptor'];?></li>
	    	</ul>
	    </div>
	    <div class="task-footer">
	        <div class="pull-left"><span class="fa fa-clock-o"></span> <?= $retrasoMensaje = $this->Html->calcular_retraso(date_format(date_create($venta['Venta']['fecha_venta']), 'Y-m-d H:i:s')); ?></div>
	        <div class="pull-right"><button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#venta<?=$iv;?>"><i class="fa fa-eye"></i> Ver más</button> </div>                                
	    </div>                                    
	</div>

	<!-- Modal -->
	<div class="modal fade" id="venta<?=$iv;?>" tabindex="-1" role="dialog" aria-labelledby="venta<?=$iv;?>Label">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="venta<?=$iv;?>Label">Vista rápida venta #<?=$venta['Venta']['id']; ?></h4>
	      </div>
	      <div class="modal-body">

			<img src="https://chart.googleapis.com/chart?chs=<?=$tamano;?>&cht=qr&chl=<?=$url;?>&choe=UTF-8" title="QR" />

	      	<h4><i class="fa fa-user"></i> Cliente</h4>
	      	<table class="table">
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
	      	<table class="table">
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
	      	<h4><i class="fa fa-cart"></i> Productos</h4>
	      	<table class="table">
	      		<thead>
	      			<th>Id</th>
	      			<th>Referencia</th>
	      			<th>Nombre</th>
	      			<th>Cantidad</th>
	      			<th>Pendiente entrega</th>
	      		</thead>
	      	<? foreach ($venta['VentaDetalle'] as $ivd => $d) : ?>
				<tr>
					<td><?=$d['venta_detalle_producto_id'];?></td>
					<td><?=$d['VentaDetalleProducto']['referencia'];?></td>
					<td><?=$d['VentaDetalleProducto']['nombre'];?></td>
					<td><?=$d['cantidad'];?></td>
					<td><?=$d['cantidad_pendiente_entrega'];?></td>
				</tr>
	      	<? endforeach; ?>
	      	</table>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Save changes</button>
	      </div>
	    </div>
	  </div>
	</div>
	<? endif; ?>

<? endif; ?>