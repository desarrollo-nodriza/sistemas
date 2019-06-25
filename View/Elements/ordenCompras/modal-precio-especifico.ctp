<!-- Modal -->
<div class="modal fade fake-form" data-id="<?=$producto['id']; ?>" id="modalPrecio<?=$producto['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalPrecio<?=$producto['id']; ?>Label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalPrecio<?=$producto['id']; ?>Label"><i class="fa fa-usd"></i> Precio lista especifico para <b><?= $producto['nombre']; ?></b></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="panel">
                        <div class="panel-heading">
                            <ul class="panel-controls">
                                <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                            </ul>
                        </div>
                        <div class="panel-body js-fake-form-body">
                            <?=$this->element('ordenCompras/crear_precio_costo_especifico_producto', array('precios_especificos' => $producto['PrecioEspecificoProducto'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary js-guardar-precios-especificos">Guardar</button>
            </div>
        </div>
    </div>
</div>