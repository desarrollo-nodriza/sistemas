<div class="page-title">
	<h2><span class="fa fa-user"></span> Contacto n° <?=$contacto['Contacto']['id']; ?> - Creado el <?=$contacto['Contacto']['created']; ?></h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 col-md-7">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-envelope"></i> Detalles del contacto</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>N° de contacto</th>
                                <td><?=$contacto['Contacto']['id']; ?></td>
                            </tr>
                            <tr>
                                <th>Tienda origen</th>
                                <td><?=$contacto['Tienda']['nombre']; ?></td>
                            </tr>
                            <tr>
                                <th>Orgien del contacto</th>
                                <td><?=$contacto['Contacto']['origen']; ?></td>
                            </tr>
                            <tr>
                                <th>Asunto</th>
                                <td><?=$contacto['Contacto']['asunto']; ?></td>
                            </tr>
                            <tr>
                                <th>Mensaje</th>
                                <td><?=$contacto['Contacto']['mensaje']; ?></td>
                            </tr>
                            <tr>
                                <th>Estado interno</th>
                                <td>
                                    <?=($contacto['Contacto']['atendido']) ? '<label class="label label-success">Atendido</label>' : '<label class="label label-danger">No atendido</label>' ; ?>
                                </td>
                            </tr>
                            <? if (!empty($contacto['Administrador'])) : ?>
                            <tr>
                                <th>Atendido por</th>
                                <td><?=$contacto['Administrador']['nombre'];  ?> - <?=$contacto['Administrador']['email']; ?></td>
                            </tr>
                            <? endif; ?>
                            <tr>
                                <th>Estado externo</th>
                                <td>
                                <? if (!empty($contacto['Contacto']['fecha_no_confirmado_cliente'])) : ?>
                                    <label class="label label-danger">Cliente indica que no ha sido atendido.</label>
                                <? elseif ($contacto['Contacto']['confirmado_cliente']) : ?>
                                    <label class="label label-success">Cliente indica que fue atendido.</label>
                                <? else : ?>
                                    <label class="label label-info">Cliente aun no ha confirmado la atención.</label>
                                <? endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-5">
            
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-user"></i> Detalles del cliente</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Identificador</th>
                                <td><?=$this->Html->link('#' . $contacto['Contacto']['cliente_id'], array('controller' => 'ventaClientes', 'action' => 'edit', $contacto['Contacto']['cliente_id']), array('target' => '_blank')); ?></td>
                            </tr>
                            <tr>
                                <th>Nombre del cliente</th>
                                <td><?=$contacto['Contacto']['nombre_contacto']; ?> <?=$contacto['Contacto']['apellido_contacto']; ?></td>
                            </tr>
                            <tr>
                                <th>Email del cliente</th>
                                <td><?=$contacto['Contacto']['email_contacto']; ?></td>
                            </tr>
                            <tr>
                                <th>Teléfono del cliente</th>
                                <td><?=$contacto['Contacto']['fono_contacto']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-calendar"></i> Cambios en el contacto</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Fecha creación</th>
                                <td><?=$contacto['Contacto']['created']; ?></td>
                            </tr>
                            <tr>
                                <th>Fecha atendido</th>
                                <td><?=(!empty($contacto['Contacto']['fecha_atendido'])) ? $contacto['Contacto']['fecha_atendido'] : 'No informado'; ?></td>
                            </tr>
                            <tr>
                                <th>Fecha confirmación <br>de atención</th>
                                <td><?=(!empty($contacto['Contacto']['fecha_confirmado_cliente'])) ? $contacto['Contacto']['fecha_confirmado_cliente'] : 'No informado'; ?></td>
                            </tr>
                            <tr>
                                <th>Fecha rechazo <br>confirmación de atención</th>
                                <td><?=(!empty($contacto['Contacto']['fecha_no_confirmado_cliente'])) ? $contacto['Contacto']['fecha_no_confirmado_cliente'] : 'No informado'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-primary">
                <div class="panel-footer">
                    <div class="btn-group pull-right">
                    <? if (!$contacto['Contacto']['atendido'] && $contacto['Contacto']['administrador_id'] == $this->Session->read('Auth.Administrador.id')) : ?>
                    <?= $this->Html->link('<i class="fa fa-check"></i> Marcar como atendido', array('action' => 'atender', $contacto['Contacto']['id']), array('class' => 'btn btn-success', 'escape' => false)); ?>
                    <? endif; ?>
                    <? if ($contacto['Contacto']['atendido'] && !$contacto['Contacto']['confirmado_cliente'] && $contacto['Contacto']['administrador_id'] == $this->Session->read('Auth.Administrador.id')) : ?>
                    <?= $this->Html->link('<i class="fa fa-envelope"></i> Enviar notificación al cliente', array('action' => 'notificar_cliente', $contacto['Contacto']['id']), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                    <? endif; ?>
                    <?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger'))?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>