<div class="table-responsive">
    <table class="table table-bordered">
        <caption><?= __('El precio específico del producto sobrescribe al precio específico de la marca.'); ?></caption>
        <thead>
            <tr>
                <th><?= __('Nombre');?></th>
                <th><?= __('Tipo');?></th>
                <th><?= __('Descuento');?></th>
                <th><?= __('Compuesto');?></th>
                <th><?= __('Infinito');?></th>
                <th><?= __('Fecha Inicio');?></th>
                <th><?= __('Fecha Final');?></th>
                <th><?= __('Activo');?></th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="">
            <tr class="hidden clone-tr">
                <td>
                    <?= $this->Form->input('PrecioEspecificoProducto.999.nombre', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-nombre-producto', 'placeholder' => 'Nombre del precio especifico', 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->select('PrecioEspecificoProducto.999.tipo_descuento', array(1 => '%', 0 => '$'), array('disabled' => true, 'class' => 'form-control js-tipo-descuento', 'empty' => 'Seleccione','div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input('PrecioEspecificoProducto.999.descuento', array('type' => 'text', 'disabled' => true, 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 55990','div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input('PrecioEspecificoProducto.999.descuento_compuesto', array('type' => 'checkbox', 'disabled' => true, 'class' => 'js-compuesto', 'checked' => false, 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input('PrecioEspecificoProducto.999.descuento_infinito', array('type' => 'checkbox', 'disabled' => true, 'class' => 'js-infinito', 'checked' => false, 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input('PrecioEspecificoProducto.999.fecha_inicio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-inicio', 'placeholder' => 'Ej: 2018-12-20', 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input('PrecioEspecificoProducto.999.fecha_termino', array('type' => 'text', 'disabled' => true, 'class' => 'form-control datepicker js-f-final', 'placeholder' => 'Ej: 2019-12-20', 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input('PrecioEspecificoProducto.999.activo', array('type' => 'checkbox', 'disabled' => true, 'class' => '', 'checked' => true, 'div' => false, 'label' => false)); ?>
                </td>
                <td valign="center">
                    <button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
                </td>
            </tr>
            <? if (!empty($precios_especificos)) :  ?>
            <? foreach($precios_especificos as $ip => $precio) : ?>
            <tr>
                <td>
                    <?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.nombre', $ip), array('type' => 'text', 'class' => 'form-control js-nombre-producto', 'placeholder' => 'Nombre del precio especifico', 'value' => $precio['nombre'], 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->select(sprintf('PrecioEspecificoProducto.%d.tipo_descuento', $ip), array(1 => '%', 0 => '$'), array('class' => 'form-control js-tipo-descuento', 'empty' => 'Seleccione', 'default' => $precio['tipo_descuento'], 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.descuento', $ip), array('type' => 'text', 'class' => 'form-control js-descuento-input', 'placeholder' => 'Ej: 10, 55990', 'value' => $precio['descuento'], 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.descuento_compuesto', $ip), array('type' => 'checkbox', 'class' => 'js-compuesto', 'checked' => $precio['descuento_compuesto'], 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.descuento_infinito', $ip), array('type' => 'checkbox', 'class' => 'js-infinito', 'checked' => $precio['descuento_infinito'], 'div' => false, 'label' => false)); ?>
                </td>
                <td>
                    <?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.fecha_inicio', $ip), array('type' => 'text', 'class' => 'form-control datepicker js-f-inicio', 'placeholder' => 'Ej: 2018-12-20', 'value' => $precio['fecha_inicio'], 'readonly' => $precio['descuento_infinito'], 'div' => false, 'label' => false )); ?>
                </td>
                <td>
                    <?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.fecha_termino', $ip), array('type' => 'text', 'class' => 'form-control datepicker js-f-final', 'placeholder' => 'Ej: 2019-12-20', 'value' => $precio['fecha_termino'], 'readonly' => $precio['descuento_infinito'], 'div' => false, 'label' => false )); ?>
                </td>
                <td>
                    <?= $this->Form->input(sprintf('PrecioEspecificoProducto.%d.activo', $ip), array('type' => 'checkbox', 'class' => '', 'value' => $precio['activo'], 'div' => false, 'label' => false)); ?>
                </td>
                <td valign="center">
                    <button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
                </td>
            </tr>
            <? endforeach; ?>
            <? endif; ?>
            
        </tbody>
    </table>
</div>