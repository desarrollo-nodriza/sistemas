<div class="page-title">
    <h2><span class="fa fa-cubes"></span> Atributos</h2>
</div>

<div class="page-content-wrap">
    <?= $this->Form->create(false, array(
        'class' => 'form-horizontal',
        'url' => array('controller' => 'atributoDinamico', 'action' => 'atributo_create',),
        'id' => 'AtributoCreate'
    )); ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Listado de atributo</h3>

                    <div class="btn-group pull-right">
                        <? if ($permisos['add']) : ?>
                            <?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo atributo', array('action' => '#'), array('class' => 'btn btn-success clone-boton', 'escape' => false)); ?>
                            <button type="submit" class="btn btn-danger start-loading-when-form-is-validate"><i class="fa fa-save"></i>Guardar Información</button>
                        <? endif; ?>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="max-height: 600px;">
                        <table class="table">
                            <thead>
                                <tr class="sort">
                                    <th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                <? for ($i = (count($AtributoDinamico) + 1); $i <= (count($AtributoDinamico) + 15); $i++) : ?>
                                    <tr class="fila hidden clone-tr">
                                        <td align="center" style="vertical-align: middle; max-width: 100px;">
                                            <?= $this->Form->input(sprintf('%d.id', $i), array('type' => 'text', 'label' => '', 'default' => "", 'class' => 'form-control hidden ')); ?>
                                        </td>

                                        <td align="center">
                                            <?= $this->Form->input(sprintf('%d.nombre', $i), array('type' => 'text', 'label' => '', 'class' => 'form-control', 'required')); ?>
                                        </td>
                                        <td align="center" style="vertical-align: middle;">
                                            <?= $this->Form->checkbox(sprintf('%d.activo', $i), array('class' => "icheckbox", 'label' => '', 'default' => true)); ?>
                                        </td>

                                        <td align="center" style="vertical-align: middle;">
                                            <button type="button" class="remove_tr remove-tr btn-danger"><i class="fa fa-minus"></i></button>
                                        </td>
                                    </tr>
                                <? endfor; ?>



                                <?php foreach ($AtributoDinamico as $indice => $atributo) : ?>
                                    <tr>
                                        <td align="center" style="vertical-align: middle; max-width: 100px;">
                                            <?= $this->Form->input(sprintf('%d.id', $indice), array('type' => 'text', 'label' => '', 'default' => $atributo['AtributoDinamico']['id'], 'class' => 'form-control hidden ')); ?>
                                            <?= h($atributo['AtributoDinamico']['id']); ?></td>
                                        <td align="center">
                                            <?= h($atributo['AtributoDinamico']['nombre']); ?>
                                        </td>
                                        <td align="center" style="vertical-align: middle;">
                                            <?= $this->Form->checkbox(sprintf('%d.activo', $indice), array('class' => "icheckbox", 'label' => '', 'default' => $atributo['AtributoDinamico']['activo'])); ?>
                                        </td>
                                        <td align="center" style="vertical-align: middle;">
                                            -
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="pull-right">
                <ul class="pagination">
                    <?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
                    <?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
                    <?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
                </ul>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</div>

<script type="text/javascript">
    $.app.formularios.bind('#AtributoCreate');
    $(document).on('click', '.clone-boton', function(e) {
        e.preventDefault();

        let clone_tr = document.getElementsByClassName("clone-tr");

        if (clone_tr.length > 0) {
            let elementoremoveClass = clone_tr.item(0);
            elementoremoveClass.removeAttribute('class')
            const classes_2 = elementoremoveClass.classList
            classes_2.add("nuevo_elemento");
            classes_2.add("fila");
        }
    });
    $(document).on('click', '.remove-tr', function(e) {

        e.preventDefault();
        var $th = $(this).parents('tr').eq(0);

        $th.fadeOut('slow', function() {
            $th.remove();
            ordenar();
        });
    });
</script>