<div class="page-title">
    <h2><span class="fa fa-truck"></span> Tablas Dinámicas</h2>
</div>

<div class="page-content-wrap">

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Listado de tablas</h3>
                    <div class="btn-group pull-right">
                        <? if ($permisos['add']) : ?>
                            <?= $this->Html->link('<i class="fa fa-plus"></i> Nuevo tabla', array('action' => 'add'), array('class' => 'btn btn-success', 'escape' => false)); ?>
                        <? endif; ?>

                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="sort">
                                    <th><?= $this->Paginator->sort('id', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('nombre', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('activo', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th><?= $this->Paginator->sort('dependencia', null, array('title' => 'Haz click para ordenar por este criterio')); ?></th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tablasDinamicas as $tablas) : ?>
                                    <tr>
                                        <td><?= h($tablas['TablaDinamica']['id']); ?>&nbsp;</td>
                                        <td><?= h($tablas['TablaDinamica']['nombre']); ?>&nbsp;</td>
                                        <td><?= h($tablas['TablaDinamica']['activo'] ? 'Si' : 'No'); ?>&nbsp;</td>
                                        <td><?= h($tablas['TablaDinamica']['dependencia']); ?>&nbsp;</td>

                                        <td>
                                            <? if ($permisos['edit']) : ?>
                                                <?= $this->Html->link('<i class="fa fa-edit"></i> Editar', array('action' => 'edit', $tablas['TablaDinamica']['id']), array('class' => 'btn btn-xs btn-info', 'rel' => 'tooltip', 'title' => 'Editar este registro', 'escape' => false)); ?>
                                            <? endif; ?>
                                            <? if ($permisos['delete']) : ?>
                                                <?= $this->Form->postLink('<i class="fa fa-remove"></i> Eliminar', array('action' => 'delete', $tablas['TablaDinamica']['id']), array('class' => 'btn btn-xs btn-danger confirmar-eliminacion', 'rel' => 'tooltip', 'title' => 'Eliminar este registro', 'escape' => false)); ?>
                                            <? endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    <div class="row">
        <div class="col-xs-12">
            <div class="pull-right">
                <ul class="pagination">
                    <?= $this->Paginator->prev('« Anterior', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'first disabled hidden')); ?>
                    <?= $this->Paginator->numbers(array('tag' => 'li', 'currentTag' => 'a', 'modulus' => 2, 'currentClass' => 'active', 'separator' => '')); ?>
                    <?= $this->Paginator->next('Siguiente »', array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'last disabled hidden')); ?>
                </ul>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</div>