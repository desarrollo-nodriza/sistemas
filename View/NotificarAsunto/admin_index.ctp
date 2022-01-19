<?= $this->Html->script(array(
    '/backend/js/plugins/smartwizard/jquery.smartWizard-2.0.min.js',
    '/backend/js/asuntos_responsables.js?v=' . rand()
)); ?>
<div class="page-title">
    <h2><span class="fa fa-flag-checkered"></span> Notificar Asunto</h2>
</div>
<div class="page-content-wrap">

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="page-content-wrap">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Asuntos</h3>
                        <ul class="panel-controls">
                            <li><a href="#" class="boton clone-boton-asunto"><span class="fa fa-plus"></span></a></li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive" style="max-height: 600px;">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?= 'Nombre' ?></th>
                                            <th><?= 'Descripcion' ?></th>
                                            <th style="text-align: center;"><?= 'Activo' ?></th>
                                            <th style="text-align: center;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $this->Form->create(false, array(
                                            'class' => 'form-horizontal',
                                            'url' => array('controller' => 'notificarAsunto', 'action' => 'asuntos_add'),
                                            'id' => 'AsuntosAdd'
                                        )); ?>
                                        <? for ($i = 0; $i <= 20; $i++) : ?>
                                            <tr class="hidden clone-tr-asunto">
                                                <td align="center">
                                                    <?= $this->Form->input(sprintf('%d.nombre', $i), array('class' => 'form-control')); ?>
                                                </td>
                                                <td align="center">
                                                    <?= $this->Form->input(sprintf('%d.descripcion', $i), array('class' => 'form-control')); ?>
                                                </td>
                                                <td align="center" style="vertical-align: middle; width: 100px;">
                                                    <?= $this->Form->checkbox(sprintf('%d.activo', $i), array('label' => '', 'default' => 1)); ?>
                                                </td>
                                                <td valign="center" align="center" style="vertical-align: middle; width: 100px;" w>
                                                    <button type="button" class="remove_tr remove-tr-asunto btn-danger"><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
                                        <? endfor; ?>
                                        <?php foreach ($asuntos as $asunto) : ?>
                                            <tr>
                                                <td style="vertical-align: middle;"><?= $asunto['Asunto']['nombre'] ?></td>
                                                <td style="vertical-align: middle;"><?= $asunto['Asunto']['descripcion'] ?></td>
                                                <td align="center" style="vertical-align: middle; width: 100px;"><?= $asunto['Asunto']['activo'] ? 'Si' : 'No' ?></td>
                                                <td style="vertical-align: middle; width: 100px;">
                                                    <button type="button" data-toggle="modal" data-target="#modal-editar-asunto-<?= $asunto['Asunto']['id'] ?>" class="btn btn-info btn-block ">Editar</button>
                                                    <button type="button" data-toggle="modal" data-target="#modal-eliminar-asunto-<?= $asunto['Asunto']['id'] ?>" class="btn btn-danger btn-block ">Eliminar</button>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>


                    <div id="guardar-asunto" class="row hidden">
                        <div class="col-xs-12">
                            <div class="pull-right pagination">
                                <button type="submit" class="btn btn-success btn-block start-loading-then-redirect ">Guardar nuevos asuntos</button>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="page-content-wrap">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Notificar Correo</h3>
                        <ul class="panel-controls">
                            <li><a href="#" class="clone-boton-responsable "><span class="fa fa-plus"></span></a></li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive" style="max-height: 600px;">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?= 'Nombre' ?></th>
                                            <th><?= 'Correo' ?></th>
                                            <th style="text-align: center;"> <?= 'Activo' ?></th>
                                            <th style="text-align: center;"> <?= 'Atencion por Defecto' ?></th>
                                            <th style="text-align: center;"> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $this->Form->create(false, array(
                                            'class' => 'form-horizontal',
                                            'url' => array('controller' => 'notificarAsunto', 'action' => 'responsable_add'),
                                            'id' => 'responsableAdd'
                                        )); ?>
                                        <? for ($i = 0; $i <= 20; $i++) : ?>
                                            <tr class="hidden clone-tr-responsable">
                                                <td align="center">
                                                    <?= $this->Form->input(sprintf('%d.nombre', $i), array('type' => 'text', 'label' => '', 'class' => 'form-control')); ?>
                                                </td>
                                                <td align="center">
                                                    <?= $this->Form->input(sprintf('%d.correo', $i), array('type' => 'text', 'label' => '', 'class' => 'form-control')); ?>
                                                </td>
                                                <td align="center" style="vertical-align: middle; width: 100px;">
                                                    <?= $this->Form->checkbox(sprintf('%d.activo', $i), array('label' => '', 'default' => 1)); ?>
                                                </td>
                                                <td align="center" style="vertical-align: middle; width: 100px;">
                                                    <?= $this->Form->checkbox(sprintf('%d.default', $i), array('label' => '', 'default' => 0)); ?>
                                                </td>
                                                <td valign="center" align="center" style="vertical-align: middle; width: 100px;" w>
                                                    <button type="button" class="remove_tr remove-tr-responsable btn-danger"><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
                                        <? endfor; ?>
                                        <? foreach ($responsables as $respnsable) : ?>
                                            <tr>
                                                <td> <?= $respnsable['Notificar']['nombre'] ?></td>
                                                <td> <?= $respnsable['Notificar']['correo'] ?></td>
                                                <td align="center" style="vertical-align: middle; width: 100px;"><?= $respnsable['Notificar']['activo'] ? 'Si' : 'No' ?></td>
                                                <td align="center" style="vertical-align: middle; width: 100px;"> <?= $respnsable['Notificar']['default'] ? 'Si' : 'No' ?></td>
                                                <td style="vertical-align: middle; width: 100px;">
                                                    <button type="button" data-toggle="modal" data-target="#modal-editar-responsable-<?= $respnsable['Notificar']['id'] ?>" class="btn btn-info btn-block ">Editar</button>
                                                    <button type="button" data-toggle="modal" data-target="#modal-eliminar-responsable-<?= $respnsable['Notificar']['id'] ?>" class="btn btn-danger btn-block ">Eliminar</button>
                                                </td>
                                            </tr>
                                        <? endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div id="guardar-responsable" class="row hidden">
                        <div class="col-xs-12">
                            <div class="pull-right pagination">
                                <button type="submit" class="btn btn-success btn-block start-loading-then-redirect ">Guardar nuevos responsables</button>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="page-content-wrap">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Administrar asuntos</h3>
                    <ul class="panel-controls">
                        <li><a href="#" class="clone-boton-administrar_asuntos"><span class="fa fa-plus"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="max-height: 600px;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 500px;"><?= 'Correo a Notificar' ?></th>
                                    <th><?= 'Responsable del correo' ?></th>
                                    <th><?= 'Asunto' ?></th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $this->Form->create(false, array(
                                    'class' => 'form-horizontal',
                                    'url' => array('controller' => 'notificarAsunto', 'action' => 'asuntos_responsables_add'),

                                )); ?>
                                <? for ($i = 0; $i <= 20; $i++) : ?>
                                    <tr class="hidden clone-tr-administrar_asuntos">
                                        <td>
                                            <?= $this->Form->select(sprintf('%d.notificar_id', $i), $responsables_activos, array('empty' => 'Seleccione', 'class' => 'form-control')); ?>
                                        </td>
                                        <td align="center">
                                            " - "
                                        </td>
                                        <td>
                                            <?= $this->Form->select(sprintf('%d.asunto_id', $i), $asuntos_activos, array('empty' => 'Seleccione', 'class' => 'form-control')); ?>
                                        </td>

                                        <td valign="center" align="center" style="vertical-align: middle; width: 100px;" w>
                                            <button type="button" class="remove_tr remove-tr-administrar_asuntos btn-danger"><i class="fa fa-minus"></i></button>
                                        </td>
                                    </tr>
                                <? endfor; ?>
                                <? foreach ($NotificarAsuntos as $NotificarAsunto) : ?>
                                    <tr>
                                        <td> <?= $NotificarAsunto['Notificar']['correo'] ?> </td>
                                        <td> <?= $NotificarAsunto['Notificar']['nombre'] ?> </td>
                                        <td> <?= $NotificarAsunto['Asunto']['nombre'] ?></td>
                                        <td style="vertical-align: middle; width: 100px;">
                                            <button type="button" data-toggle="modal" data-target="#modal-eliminar-administrar_asuntos-<?= $NotificarAsunto['NotificarAsunto']['id'] ?>" class="btn btn-danger btn-block ">Eliminar</button>
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="guardar-administrar-asuntos" class="row hidden">
                    <div class="col-xs-12">
                        <div class="pull-right pagination">
                            <button type="submit" class="btn btn-success btn-block start-loading-then-redirect ">Guardar nueva relación</button>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>

    </div>
</div>

<!-- Modal Editar Asunto -->
<?php foreach ($asuntos as $asunto) : ?>
    <div class="modal fade" id="modal-editar-asunto-<?= $asunto['Asunto']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-editar-asunto-<?= $asunto['Asunto']['id']; ?>-label">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center">Asunto #<?= $asunto['Asunto']['id'] ?></h4>
                </div>

                <div class="modal-body">
                    <?= $this->Form->create(false, array(
                        'class' => 'form-horizontal',
                        'url' => array('controller' => 'notificarAsunto', 'action' => 'asuntos_edit'),
                        'id' => 'AsuntosEdit'
                    )); ?>
                    <div>
                        <div class="form-group">
                            <?= $this->Form->input('id', array('label' => '', 'default', "class" => "hidden", 'default' => $asunto['Asunto']['id'])); ?>
                            <?= $this->Form->input('nombre', array("class" => "form-control", 'default' => $asunto['Asunto']['nombre'])); ?>
                        </div>
                        <div class="form-group">
                            <label><?= __('Descripción'); ?></label>
                            <br>
                            <?= $this->Form->textarea('descripcion', array("class" => "form-control", 'default' => $asunto['Asunto']['descripcion'])); ?>
                        </div>
                        <div class="form-group">
                            <label><?= __('Activo'); ?></label>
                            <br>
                            <?= $this->Form->checkbox('activo', array('label' => '', 'default' => $asunto['Asunto']['activo'])); ?>
                            <p style="color: red;">Si el Asunto queda inactivo se eliminaran sus relaciones.</p>
                        </div>
                        <div class="col-xs-12">
                            <div class="pull-right pagination">
                                <button type="submit" class="btn btn-success btn-block start-loading-then-redirect">Guardar edición</button>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>

        </div>
    </div>
<? endforeach; ?>
<!-- Fin modal Editar Asunto -->

<!-- Modal Eliminar Asunto -->
<?php foreach ($asuntos as $asunto) : ?>
    <div class="modal fade" id="modal-eliminar-asunto-<?= $asunto['Asunto']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-eliminar-asunto-<?= $asunto['Asunto']['id']; ?>-label">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center">Asunto #<?= $asunto['Asunto']['id'] ?></h4>
                </div>

                <div class="modal-body">
                    <?= $this->Form->create(false, array(
                        'class' => 'form-horizontal',
                        'url' => array('controller' => 'notificarAsunto', 'action' => 'asuntos_delete'),
                        'id' => 'AsuntosDelete'
                    )); ?>
                    <div>
                        <div class="form-group">
                            <?= $this->Form->input('id', array('label' => '', 'default', "class" => "hidden", 'default' => $asunto['Asunto']['id'])); ?>
                            <label><?= "Al eliminar el asunto '{$asunto['Asunto']['nombre']}' se eliminaran todas las relaciones que existentes entre esta y los responsables."; ?></label>
                        </div>
                        <div class="col-xs-12">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-success  start-loading-then-redirect">Continuar</button>
                                <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger ">Cancelar</button>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>

        </div>
    </div>
<? endforeach; ?>
<!-- Fin modal Eliminar Asunto -->

<!-- Modal Editar Responsable -->
<?php foreach ($responsables as $responsable) : ?>
    <div class="modal fade" id="modal-editar-responsable-<?= $responsable['Notificar']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-editar-responsable-<?= $responsable['Notificar']['id']; ?>-label">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center">Responsable #<?= $responsable['Notificar']['id'] ?></h4>
                </div>

                <div class="modal-body">
                    <?= $this->Form->create(false, array(
                        'class' => 'form-horizontal',
                        'url' => array('controller' => 'notificarAsunto', 'action' => 'responsable_edit'),
                    )); ?>
                    <div>
                        <div class="form-group">
                            <?= $this->Form->input('id', array('label' => '', 'default', "class" => "hidden", 'default' => $responsable['Notificar']['id'])); ?>
                            <?= $this->Form->input('nombre', array("class" => "form-control", 'default' => $responsable['Notificar']['nombre'])); ?>
                        </div>
                        <div class="form-group">
                            <label><?= __('Correo'); ?></label>
                            <br>
                            <?= $this->Form->textarea('correo', array("class" => "form-control", 'default' => $responsable['Notificar']['correo'])); ?>
                        </div>

                        <div class="form-group">
                            <label><?= __('Atención por defecto'); ?></label>
                            <br>
                            <?= $this->Form->checkbox('default', array('label' => '', 'default' => $responsable['Notificar']['default'])); ?>
                        </div>

                        <div class="form-group">
                            <label><?= __('Activo'); ?></label>
                            <br>
                            <?= $this->Form->checkbox('activo', array('label' => '', 'default' => $responsable['Notificar']['activo'])); ?>
                            <p style="color: red;">Si el Resposable queda inactivo se eliminaran sus relaciones.</p>
                        </div>
                        <div class="col-xs-12">
                            <div class="pull-right pagination">
                                <button type="submit" class="btn btn-success btn-block start-loading-then-redirect">Guardar edición</button>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>

        </div>
    </div>
<? endforeach; ?>
<!-- Fin modal Editar Responsable -->

<!-- Modal Eliminar Responsable -->
<?php foreach ($responsables as $responsable) : ?>
    <div class="modal fade" id="modal-eliminar-responsable-<?= $responsable['Notificar']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-eliminar-responsable-<?= $responsable['Notificar']['id']; ?>-label">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center">Responsable #<?= $responsable['Notificar']['id'] ?></h4>
                </div>

                <div class="modal-body">
                    <?= $this->Form->create(false, array(
                        'class' => 'form-horizontal',
                        'url' => array('controller' => 'notificarAsunto', 'action' => 'responsable_delete'),

                    )); ?>
                    <div>
                        <div class="form-group">
                            <?= $this->Form->input('id', array('label' => '', 'default', "class" => "hidden", 'default' => $responsable['Notificar']['id'])); ?>
                            <label><?= "Al eliminar el Responsable '{$responsable['Notificar']['nombre']}' se eliminaran todas las relaciones que existentes entre esta y los asuntos."; ?></label>
                        </div>
                        <div class="col-xs-12">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-success  start-loading-then-redirect">Continuar</button>
                                <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger ">Cancelar</button>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>

        </div>
    </div>
<? endforeach; ?>
<!-- Fin modal Eliminar Responsable -->


<!-- Modal Eliminar Notificar -->
<?php foreach ($NotificarAsuntos as $NotificarAsunto) : ?>
    <div class="modal fade" id="modal-eliminar-administrar_asuntos-<?= $NotificarAsunto['NotificarAsunto']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-eliminar-administrar_asuntos-<?= $NotificarAsunto['Notificar']['id']; ?>-label">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center">Administrar asuntos #<?= $NotificarAsunto['NotificarAsunto']['id'] ?></h4>
                </div>

                <div class="modal-body">
                    <?= $this->Form->create(false, array(
                        'class' => 'form-horizontal',
                        'url' => array('controller' => 'notificarAsunto', 'action' => 'asuntos_responsables_delete'),

                    )); ?>
                    <div>
                        <div class="form-group">
                            <?= $this->Form->input('id', array('label' => '', 'default', "class" => "hidden", 'default' => $NotificarAsunto['NotificarAsunto']['id'])); ?>
                            <label><?= "Estas seguro de eliminar la relación entre '{$NotificarAsunto['Notificar']['nombre']}' y '{$NotificarAsunto['Asunto']['nombre']}'."; ?></label>
                        </div>
                        <div class="col-xs-12">
                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-success  start-loading-then-redirect">Continuar</button>
                                <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger ">Cancelar</button>
                            </div>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>

        </div>
    </div>
<? endforeach; ?>
<!-- Fin modal Eliminar Notificar -->