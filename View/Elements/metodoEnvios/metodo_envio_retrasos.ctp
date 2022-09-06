<tr class="hidden clone-tr">
    <td>
        <?= $this->Form->select('MetodoEnvioRetraso.999.bodega_id', $bodegas, array('disabled' => true, 'class' => 'form-control not-blank', 'empty' => 'Seleccione')); ?>
    </td>
    <td>
        <?= $this->Form->select('MetodoEnvioRetraso.999.venta_estado_categoria_id', $venta_estado_categorias, array('disabled' => true, 'class' => 'form-control not-blank', 'empty' => 'Seleccione')); ?>
    </td>
    <td>
        <?= $this->Form->input('MetodoEnvioRetraso.999.horas_retraso', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-digits', 'placeholder' => 'Ej: 10, 5, 24', 'min' => 1, 'max' => 240)); ?>
    </td>
    <td>
        <?= $this->Form->input('MetodoEnvioRetraso.999.notificar_restraso', array('type' => 'checkbox', 'disabled' => true, 'class' => '', 'checked' => false)); ?>
    </td>
    <td valign="center">
        <button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
    </td>
</tr>