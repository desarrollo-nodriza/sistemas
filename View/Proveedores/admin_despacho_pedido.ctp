<div class="page-title">
    <h2><span class="fa fa-industry"></span> Proveedores</h2>
</div>
<?= $this->Form->create('RangoDespacho', array('class' => 'form-horizontal ', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<div class="page-content-wrap">
    <div class="row">
        <div class="col-xs-12">
            <div class="page-content-wrap">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Rangos de despacho <strong>(En días)</strong></h3>
                        <div class="btn-group pull-right">
                            <button type="submit" class="btn btn-info start-loading-when-form-is-validate"><i class="fa fa-save"></i>Guardar Información</button>
                        </div>
                    </div>
                    <div class="panel-body">

                        <? $indice = 0; ?>
                        <?php foreach ($proveedores as $indice_proveedor => $proveedor) : ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?= $proveedor['Proveedor']['nombre'] ?>
                                    <div class="btn-group pull-right">
                                        <? if ($permisos['add']) : ?>
                                            <?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo Regla', array('action' => '#'), array('class' => "btn btn-success clone-boton-$indice_proveedor", 'escape' => false)); ?>
                                        <? endif; ?>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table" style="max-height: 300px;">
                                            <thead>
                                                <tr class="sort">
                                                    <th>Identificador Proveedor</th>
                                                    <th>Desde </th>
                                                    <th>Hasta</th>
                                                    <th>Valor a asignar</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($proveedor['RangoDespacho'] as $rango_despacho) : ?>
                                                    <tr>
                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Form->input(sprintf('%d.proveedor_id', $indice), array('required', 'default' =>  $proveedor['Proveedor']['id'], 'type' => 'text', 'label' => '',  'class' => 'form-control', 'readonly' => true)); ?>
                                                        </td>

                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Form->input(sprintf('%d.rango_desde', $indice), array('required', 'default' =>  $rango_despacho['rango_desde'], 'type' => 'text', 'label' => '', 'class' => 'form-control',)); ?>

                                                        </td>
                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Form->input(sprintf('%d.rango_hasta', $indice), array('required', 'default' =>  $rango_despacho['rango_hasta'], 'type' => 'text', 'label' => '', 'class' => 'form-control',)); ?>

                                                        </td>
                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Form->input(sprintf('%d.despacho', $indice), array('required', 'default' =>  $rango_despacho['despacho'], 'type' => 'text', 'label' => '', 'class' => 'form-control',)); ?>
                                                        </td>
                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Html->link('<i class="fa fa-trash"></i> Eliminar rango', array('action' => 'delete_despacho_pedido', $rango_despacho['id']), array('class' => 'btn btn-xs btn-danger start-loading-then-redirect
', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
                                                            <?= $this->Form->input(sprintf('%d.id', $indice), array('default' =>  $rango_despacho['id'], 'type' => 'text', 'label' => '',  'class' => 'form-control hidden')); ?>
                                                        </td>

                                                    </tr>
                                                    <? $indice++; ?>
                                                <?php endforeach; ?>
                                                <? for ($i = (count($proveedor['RangoDespacho']) + 1); $i <= (count($proveedor['RangoDespacho']) + 6); $i++) : ?>
                                                    <tr class="fila hidden clone-tr-<?= $indice_proveedor; ?>">

                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Form->input(sprintf('%d.proveedor_id', $indice), array('required', 'default' =>  $proveedor['Proveedor']['id'], 'type' => 'text', 'label' => '',  'class' => 'form-control', 'readonly' => true)); ?>

                                                        </td>
                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Form->input(sprintf('%d.rango_desde', $indice), array('required', 'type' => 'text', 'label' => '', 'class' => 'form-control',)); ?>


                                                        </td>
                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Form->input(sprintf('%d.rango_hasta', $indice), array('required', 'type' => 'text', 'label' => '', 'class' => 'form-control',)); ?>

                                                        </td>
                                                        <td align="center" style="vertical-align: bottom;">
                                                            <?= $this->Form->input(sprintf('%d.despacho', $indice), array('required', 'type' => 'text', 'label' => '', 'class' => 'form-control',)); ?>

                                                        </td>
                                                        <td align="center" style="vertical-align: bottom;">
                                                            <button type="button" class="remove-tr-<?= $indice_proveedor; ?> btn-danger"><i class="fa fa-minus"></i></button>
                                                            <?= $this->Form->input(sprintf('%d.id', $indice), array('type' => 'text', 'label' => '',  'class' => 'form-control hidden')); ?>
                                                        </td>
                                                    </tr>
                                                    <? $indice++; ?>
                                                <? endfor; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $(document).on('click', '.clone-boton-' + <?= $indice_proveedor ?>, function(e) {
                                    $.app.formularios.bind('#ReglaCreate');
                                    e.preventDefault();

                                    let clone_tr = document.getElementsByClassName("clone-tr-" + <?= $indice_proveedor ?>);

                                    if (clone_tr.length > 0) {
                                        let elementoremoveClass = clone_tr.item(0);
                                        elementoremoveClass.removeAttribute('class')
                                        const classes_2 = elementoremoveClass.classList
                                        classes_2.add("nuevo_elemento");
                                        classes_2.add("fila");
                                    }
                                });
                                $(document).on('click', '.remove-tr-' + <?= $indice_proveedor ?>, function(e) {

                                    e.preventDefault();
                                    var $th = $(this).parents('tr').eq(0);

                                    $th.fadeOut('slow', function() {
                                        $th.remove();

                                    });
                                });
                            </script>
                        <?php endforeach; ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>