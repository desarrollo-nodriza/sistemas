<? if (!empty($detalle['EmbalajeProductoWarehouse'])) :

    $listo_para_embalar = false;
    $preparando = false;

    foreach ($detalle['EmbalajeProductoWarehouse'] as $iem => $em) :

        if ($em['EmbalajeWarehouse']['estado'] == 'listo_para_embalar') {
            $listo_para_embalar = true;
        }

        if (in_array($em['EmbalajeWarehouse']['estado'], ['procesando','listo_para_trasladar','en_traslado_a_bodega'])) {
            $preparando = true;
        }

    endforeach;

?>

    <? if ($listo_para_embalar) : ?>

        <button type="button" class="btn btn-info btn-xs btn-block mb-control" data-box="#alerta_liberar_stock<?= $detalle['id']; ?>" data-target="#alerta_liberar_stock<?= $detalle['id']; ?>"><i class="fa fa-ban"></i> Liberar</button>

        <!-- MESSAGE BOX-->
        <div class="message-box message-box-warning animated fadeIn" data-sound="alert" id="alerta_liberar_stock<?= $detalle['id']; ?>">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title" id="alerta_liberar_stock<?= $detalle['id']; ?>_label"><i class="fa fa-exclamation-triangle"></i> Liberar stock</div>
                    <div class="mb-content">
                        <p style="margin: 15px 0;">
                            Este producto está asignado a un embalaje en <b>Warehouse</b>.
                            Si usted quita la reserva, el embalaje se cancelará por completo.
                        </p>
                        <p style="margin: 15px 0;">
                            <b>¿Desea liberar la reserva?</b>
                        </p>
                    </div>
                    <div class="mb-footer">
                        <div class="btn-group">
                            <?= $this->Html->link('<i class="fa fa-ban"></i> Liberar', array('action' => 'liberar_stock_reservado', 0,  $venta['Venta']['id'], $detalle['id'], $detalle['venta_detalle_producto_id']), array('class' => 'btn btn-warning btn-lg', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Liberar stock')) ?>
                            <button class="btn btn-default btn-lg mb-control-close">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MESSAGE BOX-->

    <? elseif ($preparando) : ?>

        <button type="button" class="btn btn-info btn-xs btn-block mb-control" data-box="#alerta_liberar_stock<?= $detalle['id']; ?>" data-target="#alerta_liberar_stock<?= $detalle['id']; ?>"><i class="fa fa-ban"></i> Liberar</button>

        <!-- MESSAGE BOX-->
        <div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="alerta_liberar_stock<?= $detalle['id']; ?>">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title" id="alerta_liberar_stock<?= $detalle['id']; ?>_label"><i class="fa fa-exclamation-triangle"></i> Liberar stock</div>
                    <div class="mb-content">
                        <p style="margin: 15px 0;">
                       
                            Este producto está asignado un embalaje en <b>Warehouse</b> que se encuentra "<?=str_replace("_"," ",ucfirst($em['EmbalajeWarehouse']['estado']))?>".
                        </p>
                        <p style="margin: 15px 0;">
                            <b>Es imposible liberar la reserva</b>
                        </p>
                    </div>
                    <div class="mb-footer">
                        <div class="btn-group">
                            <button class="btn btn-default btn-lg mb-control-close">Volver</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MESSAGE BOX-->
    <? else : ?>
        <?= $this->Html->link('<i class="fa fa-ban"></i> Liberar', array('action' => 'liberar_stock_reservado', 0, $venta['Venta']['id'], $detalle['id'], $detalle['venta_detalle_producto_id']), array('class' => 'btn btn-warning btn-block btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Liberar stock')) ?>
    <? endif; ?>

<? else : ?>
    <?= $this->Html->link('<i class="fa fa-ban"></i> Liberar', array('action' => 'liberar_stock_reservado', 0, $venta['Venta']['id'], $detalle['id'], $detalle['venta_detalle_producto_id']), array('class' => 'btn btn-warning btn-block btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Liberar stock')) ?>
<? endif; ?>