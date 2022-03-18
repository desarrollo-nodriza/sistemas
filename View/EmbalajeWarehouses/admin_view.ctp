<div class="page-title">
	<h2><span class="fa fa-cube"></span> Embalaje #<?=$embalaje['EmbalajeWarehouse']['id'];?></h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-7">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Detalles del embalaje</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
                            <tr>
                                <td>Estado del embalaje</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['estado'];?></td>
                            </tr>
                            <tr>
                                <td>Bodega asignada</td>
                                <td><?=$embalaje['Bodega']['nombre'];?></td>
                            </tr>
                            <tr>
                                <td>Metodo de envio</td>
                                <td><?=$embalaje['MetodoEnvio']['nombre'];?></td>
                            </tr>
                            <? if (!empty($embalaje['Marketplace'])) : ?>
                            <tr>
                                <td>Marketplace relacionado</td>
                                <td><?=$embalaje['Marketplace']['nombre'];?></td>
                            </tr>
                            <? endif; ?>

                            <? if (!empty($embalaje['Comuna'])) : ?>
                            <tr>
                                <td>Comuna destino</td>
                                <td><?=$embalaje['Comuna']['nombre'];?></td>
                            </tr>
                            <? endif; ?>

                            <tr>
                                <td>Embalaje prioritario</td>
                                <td><?= ($embalaje['EmbalajeWarehouse']['prioritario']) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';?></td>
                            </tr>

                            <? if (!empty($embalado_por)) : ?>
                            <tr>
                                <td>Procesado por</td>
                                <td><?=$embalado_por['Administrador']['nombre'];?> - <?=$embalado_por['Administrador']['email'];?></td>
                            </tr>
                            <tr>
                                <td>Fecha procesado</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['fecha_procesando'];?></td>
                            </tr>
                            <? endif; ?>

                            <? if (!empty($finalizado_por)) : ?>
                            <tr>
                                <td>Finalizado por</td>
                                <td><?=$finalizado_por['Administrador']['nombre'];?> - <?=$finalizado_por['Administrador']['email'];?></td>
                            </tr>
                            <tr>
                                <td>Fecha finalizado</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['fecha_finalizado'];?></td>
                            </tr>
                            <? endif; ?>

                            <? if (!empty($cancelado_por)) : ?>
                            <tr>
                                <td>Cancelado por</td>
                                <td><?=$cancelado_por['Administrador']['nombre'];?> - <?=$cancelado_por['Administrador']['email'];?></td>
                            </tr>
                            <tr>
                                <td>Fecha cancelado</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['fecha_cancelado'];?></td>
                            </tr>
                            <? endif; ?>

                            <tr>
                                <td>Fecha de creación</td>
                                <td><?= $embalaje['EmbalajeWarehouse']['fecha_creacion'];?></td>
                            </tr>

                            <tr>
                                <td>Última modificación</td>
                                <td><?= $embalaje['EmbalajeWarehouse']['ultima_modifacion'];?></td>
                            </tr>

                            <? if (!empty($embalaje['EmbalajeWarehouse']['detalle_revision'])) : ?>
                            <tr>
                                <td>Motivo por el cual se envió a revisión</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['detalle_revision'];?></td>
                            </tr>
                            <? endif; ?>

                            <? if (!empty($embalaje['EmbalajeWarehouse']['solucion_revision'])) : ?>
                            <tr>
                                <td>Solución de la última revisión</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['solucion_revision'];?></td>
                            </tr>
                            <? endif; ?>

						</table>
					</div>
				</div>

                <div class="panel-body">
                    
                    <h5>Productos a embalar</h5>

					<div class="table-responsive">
						<table class="table table-bordered">
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cantidad a embalar</th>
                            <th>Cantidad embalada</th>

                            <tbody>
                            <? foreach ($embalaje['EmbalajeProductoWarehouse'] as $iemp => $producto) : ?>
                            <tr>
                                <td><?=$producto['producto_id']; ?></td>
                                <td><?=$producto['VentaDetalleProducto']['nombre']; ?></td>
                                <td><?=$producto['cantidad_a_embalar']; ?></td>
                                <td><?=$producto['cantidad_embalada']; ?></td>
                            </tr>
                            <? endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>
		</div>
        <div class="col-xs-12 col-md-5">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Venta relacionada</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
                                <td>Venta id</td>
                                <td><?=$this->Html->link($embalaje['Venta']['id'], array('controller' => 'ventas', 'action' => 'view', $embalaje['Venta']['id']), array('target' => '_blank'))?></td>
                            </tr>
                            <tr>
                                <td>Referencia</td>
                                <td><?=$this->Html->link($embalaje['Venta']['referencia'], array('controller' => 'ventas', 'action' => 'view', $embalaje['Venta']['id']), array('target' => '_blank'))?></td>
                            </tr>
                            <tr>
                                <td>Fecha de la venta</td>
                                <td><?= $embalaje['Venta']['fecha_venta']; ?></td>
                            </tr>
                            <tr>
                                <td>Estado</td>
                                <td><span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$embalaje['Venta']['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= $embalaje['Venta']['VentaEstado']['VentaEstadoCategoria']['estilo']; ?>"><?= $embalaje['Venta']['VentaEstado']['VentaEstadoCategoria']['nombre']; ?></span> <small><?=$embalaje['Venta']['venta_estado_responsable'];?></small></td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td><?= CakeNumber::currency($embalaje['Venta']['total'], 'CLP'); ?></td>
                            </tr>
						</table>
					</div>
				</div>
                <div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<th>Id</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Cantidad anulada</th>
                            <tbody>
                            <? foreach ($embalaje['Venta']['VentaDetalle'] as $detalle) : ?>
                                <tr>
                                    <td><?=$detalle['venta_detalle_producto_id']; ?></td>
                                    <td><?=$detalle['VentaDetalleProducto']['nombre']; ?></td>
                                    <td><?=$detalle['cantidad']; ?></td>
                                    <td><?=$detalle['cantidad_anulada']; ?></td>
                                </tr>
                            <? endforeach; ?>
                            </tbody>
						</table>
					</div>
				</div>
			</div>
            <div class="panel panel-info">
                <div class="panel-body">
                <h4><i class="fa fa-bell " aria-hidden="true"></i> <?= __('Notas del embalaje para warehouse');?></h4> 
						<p class="text-primary"><?= __('Las notas del embalaje se verán unicamente en este embalaje. Sí desea crear una nota global, dirígase a la venta.');?></p>
						<div class="messages">
							<? foreach ($notas_despacho['body'] as $nota) : if (empty($nota['embalajes'])) : continue; endif;?>
							<div class="item item-visible">
								<div class="text">
									<div class="heading" style="display: flex; flex-direction: column;">
										<div style="display: flex; align-items: center; justify-content: space-between;">
										<a style="width: 90%">#<?=$nota['id']; ?> - <?=$nota['nombre'];?></a>
										<? if ($nota['atendido']) : ?>
										<span class="label label-success label-form" style="margin-right: 10px; margin-left: auto; margin-top: 0;">Leída por <?=$nota['mail_usuario']; ?></span>
										<? else : ?>
										<span class="label label-default label-form" style="margin-right: 10px; margin-left: auto; margin-top: 0;">Sin atender</span>
										<? endif; ?>
										<?=$this->Form->postButton('<i class="fa fa-trash"></i> Eliminar', array('controller' => 'ventas', 'action' => 'eliminar_nota_despacho', $nota['id']), array('class' => 'btn btn-xs btn-danger pull-right', 'style' => "display: block; margin-left: auto; margin-right: 0;", 'escape' => false)); ?>
										</div>
										<span class="date" style="float: none;display: block;">Creada por: <?=$nota['nombre_usuario'];?></span>
										<span class="date" style="float: none;display: block;">Fecha de creación: <?=$this->Time->format($nota['fecha_creacion'], '%y-%m-%d %H:%M:%S');?></span>
									</div>
									<p style="display: block; width: 100%;"><?=$nota['descripcion']; ?></p>
								</div>
							</div>
							<? endforeach; ?>
						</div>
						
						
						<?= $this->Form->create('Nota', array('url' => array('controller' => 'ventas', 'action' => 'crear_nota_despacho'), 'id' => 'AgregarNota','class' => 'form-horizontal js-formulario', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?> 					
						<div class="form-group" style="margin-top: 25px;float: left;width: 100%;">
							<?=$this->Form->hidden('venta_id', array('value' => $embalaje['EmbalajeWarehouse']['venta_id'])); ?>
                            <?=$this->Form->hidden('titulo', array('value' => 'Nota desde embalaje')); ?>
                            <?=$this->Form->hidden('embalaje_id', array('value' => $embalaje['EmbalajeWarehouse']['id'])); ?>					
							<?=$this->Form->label('nota_despacho_global', 'Crear nueva nota de despacho para warehouse')?>
							<?=$this->Form->textarea('nota_despacho_global', array('class' => 'form-control not-blank', 'rows' => 4, 'placeholder' => 'Ingrese una nota para warehouse'));?>
						</div>
						
						<?=$this->Form->button('Crear nota para el embalaje', array('type' => 'submit', 'class' => 'btn btn-block btn-warning start-loading-when-form-is-validate')); ?> 

						<?= $this->Form->end(); ?> 
                </div>
            </div>
		</div>
	</div>

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-primary">
                <div class="panel-footer">
					<div class="pull-right">
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>