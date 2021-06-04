<div class="page-title">
	<h2><span class="fa fa-cube"></span> Revisión del embalaje #<?=$embalaje['EmbalajeWarehouse']['id'];?></h2>
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
                                <td><?=$embalado_por['Administrador']['nombre'];?> - <<?=$embalado_por['Administrador']['email'];?>></td>
                            </tr>
                            <tr>
                                <td>Fecha procesado</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['fecha_procesando'];?>></td>
                            </tr>
                            <? endif; ?>

                            <? if (!empty($finalizado_por)) : ?>
                            <tr>
                                <td>Finalizado por</td>
                                <td><?=$finalizado_por['Administrador']['nombre'];?> - <<?=$finalizado_por['Administrador']['email'];?>></td>
                            </tr>
                            <tr>
                                <td>Fecha finalizado</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['fecha_finalizado'];?>></td>
                            </tr>
                            <? endif; ?>

                            <? if (!empty($cancelado_por)) : ?>
                            <tr>
                                <td>Cancelado por</td>
                                <td><?=$cancelado_por['Administrador']['nombre'];?> - <<?=$cancelado_por['Administrador']['email'];?>></td>
                            </tr>
                            <tr>
                                <td>Fecha cancelado</td>
                                <td><?=$embalaje['EmbalajeWarehouse']['fecha_cancelado'];?>></td>
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
		</div>
	</div>
    <?= $this->Form->create('EmbalajeWarehouse', array('class' => 'form-horizontal js-formulario', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
    <?= $this->Form->input('id', array('type' => 'hidden', 'value' => $embalaje['EmbalajeWarehouse']['id']));?>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-warning">
                <div class="panel-heading">
					<h3 class="panel-title">Revisar embalaje</h3>
				</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <?=$this->Form->label('detalle_revision', 'Motivo por el cual se envió a revisión');?>
                            <?=$this->Text->autoParagraph($embalaje['EmbalajeWarehouse']['detalle_revision']); ?>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <?=$this->Form->label('solucion_revision', 'Solución propuesta');?>
                            <?=$this->Form->input('solucion_revision', array('class' => 'not-blank form-control', 'rows' => 3, 'placeholder' => 'Describa cómo solucionó el problema'));?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="pull-right">
						<?= $this->Form->button('Enviar a listo para embalar', array('type' => 'submit', 'class' => 'btn btn-primary start-loading-when-form-is-validate')); ?>
                        <?= $this->Html->link('Cancelar embalaje', array('action' => 'cancelar', $embalaje['EmbalajeWarehouse']['id']), array('class' => 'btn btn-danger start-loading-then-redirect')); ?>
					</div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>

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