<tr class="">
    <td>   
        <?= $this->Form->hidden(sprintf('MetodoEnvioRetraso.%d.id', $regla_retraso['id']), array('value' => $regla_retraso['id'])); ?>
        <?= $this->Form->select(sprintf('MetodoEnvioRetraso.%d.bodega_id', $regla_retraso['id']), $bodegas, array('class' => 'form-control not-blank js-bodega-regla-noti', 'empty' => 'Seleccione', 'default' => $regla_retraso['bodega_id'])); ?>
    </td>
    <td>
        <?= $this->Form->select(sprintf('MetodoEnvioRetraso.%d.venta_estado_categoria_id', $regla_retraso['id']), $venta_estado_categorias, array('class' => 'form-control not-blank js-estado-regla-noti', 'empty' => 'Seleccione', 'default' => $regla_retraso['venta_estado_categoria_id'])); ?>
    </td>
    <td>
        <?= $this->Form->input(sprintf('MetodoEnvioRetraso.%d.horas_retraso', $regla_retraso['id']), array('type' => 'text', 'class' => 'form-control js-digits js-hora-regla-noti', 'placeholder' => 'Ej: 10, 5, 24', 'min' => 1, 'max' => 240, 'value' => $regla_retraso['horas_retraso'])); ?>
    </td>
    <td>
        <?= $this->Form->input(sprintf('MetodoEnvioRetraso.%d.notificar_retraso', $regla_retraso['id']), array('type' => 'checkbox', 'class' => '', 'checked' => $regla_retraso['notificar_retraso'])); ?>
    </td>
    <td valign="center">
        <button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
    </td>
</tr>