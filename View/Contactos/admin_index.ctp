<div class="page-title">
	<h2><span class="fa fa-user"></span> Contactos</h2>
</div>

<div class="page-content-wrap">

	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Contacto', array('url' => array('action' => 'index'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>

			<?
			$administrador_id   = (isset($this->request->params['named']['administrador_id'])) ? $this->request->params['named']['administrador_id'] : '';
			$id_contacto        = (isset($this->request->params['named']['id_contacto'])) ? $this->request->params['named']['id_contacto'] : '';
			$origen             = (isset($this->request->params['named']['origen'])) ? $this->request->params['named']['origen'] : '';
			$asunto             = (isset($this->request->params['named']['asunto'])) ? $this->request->params['named']['asunto'] : '';
			$email_contacto     = (isset($this->request->params['named']['email_contacto'])) ? $this->request->params['named']['email_contacto'] : '';
			$fono_contacto      = (isset($this->request->params['named']['fono_contacto'])) ? $this->request->params['named']['fono_contacto'] : '';
			$nombre_contacto    = (isset($this->request->params['named']['nombre_contacto'])) ? $this->request->params['named']['nombre_contacto'] : '';
			$apellido_contacto  = (isset($this->request->params['named']['apellido_contacto'])) ? $this->request->params['named']['apellido_contacto'] : '';
			$atendido           = (isset($this->request->params['named']['atendido'])) ? $this->request->params['named']['atendido'] : '';
			$confirmado_cliente = (isset($this->request->params['named']['confirmado_cliente'])) ? $this->request->params['named']['confirmado_cliente'] : '';
			$fecha_desde        = (isset($this->request->params['named']['fecha_desde'])) ? $this->request->params['named']['fecha_desde'] : '';
			$fecha_hasta        = (isset($this->request->params['named']['fecha_hasta'])) ? $this->request->params['named']['fecha_hasta'] : '';
			?>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Filtro de busqueda</h3>
				</div>
				<div class="panel-body">
					<div class="form-group col-sm-2 col-xs-12">
						<label>N° contacto</label>
						<?= $this->Form->input('id_contacto', array('class' => 'form-control', 'placeholder' => 'Ingrese N° contacto', 'value' => $id_contacto)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Responsable</label>
						<?= $this->Form->select('administrador_id', $administradores, array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $administrador_id)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Origen</label>
						<?= $this->Form->select('origen', $origenes, array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $origen)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Asunto</label>
						<?= $this->Form->select('asunto', $asuntos, array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $asunto)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Nombre cliente</label>
						<?= $this->Form->input('nombre_contacto', array('class' => 'form-control', 'placeholder' => 'Ingrese nombre cliente', 'value' => $nombre_contacto)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Apellido cliente</label>
						<?= $this->Form->input('apellido_contacto', array('class' => 'form-control', 'placeholder' => 'Ingrese apellido cliente', 'value' => $apellido_contacto)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Email cliente</label>
						<?= $this->Form->input('email_contacto', array('class' => 'form-control', 'placeholder' => 'Ingrese email del cliente', 'value' => $email_contacto)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Fono cliente</label>
						<?= $this->Form->input('fono_contacto', array('class' => 'form-control', 'placeholder' => 'Ingrese fono del cliente', 'value' => $fono_contacto)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Atendido</label>
						<?= $this->Form->select('atendido', array('si' => 'Atendido', 'no' => 'Sin atender'), array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $atendido)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Confirmado cliente</label>
						<?= $this->Form->select('confirmado_cliente', array('si' => 'Confirmado', 'no' => 'Sin confirmar'), array('class' => 'form-control', 'empty' => 'Seleccione', 'default' => $confirmado_cliente)); ?>
					</div>
					<div class="form-group col-sm-2 col-xs-12">
						<label>Rango de fecha</label>
						<div class="input-group">
							<?= $this->Form->input('fecha_desde', array(
								'class' => 'form-control datepicker',
								'type' => 'text',
								'value' => $fecha_desde
							)) ?>
							<span class="input-group-addon add-on"> - </span>
							<?= $this->Form->input('fecha_hasta', array(
								'class' => 'form-control datepicker',
								'type' => 'text',
								'value' => $fecha_hasta
							)) ?>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Filtrar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block')); ?>
						</div>
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-ban" aria-hidden="true"></i> Limpiar filtro', array('action' => 'index'), array('class' => 'btn btn-buscar btn-primary btn-block', 'escape' => false)); ?>
						</div>
					</div>
				</div>
			</div>
			<?= $this->Form->end(); ?>
		</div>
	</div>

	<div class="row">

		<div class="col-xs-12">

			<div class="panel panel-default">

				<div class="panel-heading">
					<h3 class="panel-title">Listado de contactos</h3>
				</div>

				<div class="panel-body">

					<?= $this->element('contador_resultados'); ?>

					<div class="table-responsive">

						<table class="table table-striped table-middle">

							<thead>
								<tr class="sort">
									<th><?= $this->Paginator->sort('id', 'N° de contacto', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('administrador_id', 'Responsable', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('origen', 'Origen', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('email_contacto', 'Email del contacto', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('fono_contacto', 'Fono del contacto', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('nombre_contacto', 'Nombre del cliente', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('atendido', 'Atendido', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('confirmado_cliente', 'Confirmado por cliente', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th><?= $this->Paginator->sort('created', 'Fecha creación', array('title' => 'Haz click para ordenar por este criterio')); ?></th>
									<th>Acciones</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ($contactos as $contacto) : ?>

									<tr>

										<td><?= h($contacto['Contacto']['id']); ?>&nbsp;</td>
										<td><?= h($contacto['AtencionCliente']['correo']); ?>&nbsp;</td>
										<td><?= h($contacto['Contacto']['origen']); ?>&nbsp;</td>
										<td><?= h($contacto['Contacto']['email_contacto']); ?>&nbsp;</td>
										<td><?= h($contacto['Contacto']['fono_contacto']); ?>&nbsp;</td>
										<td><?= h($contacto['Contacto']['nombre_contacto']); ?> <?= h($contacto['Contacto']['apellido_contacto']); ?>&nbsp;</td>
										<td><?= ($contacto['Contacto']['atendido'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= ($contacto['Contacto']['confirmado_cliente'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>'); ?>&nbsp;</td>
										<td><?= h($contacto['Contacto']['created']); ?>&nbsp;</td>

										<td>

											<?= $this->Html->link('<i class="fa fa-eye"></i> Ver detalle', array('action' => 'view', $contacto['Contacto']['id']), array('class' => 'btn btn-xs btn-info btn-block', 'rel' => 'tooltip', 'title' => 'Ver este registro', 'escape' => false)); ?>

											<?php
											if (!$contacto['Contacto']['atendido'] && $contacto['Contacto']['administrador_id'] == $this->Session->read('Auth.Administrador.id')) {
												echo $this->Form->postLink('<i class="fa fa-check"></i> Marcar como atendido', array('action' => 'atender', $contacto['Contacto']['id']), array('class' => 'btn btn-block btn-xs btn-primary mt-5', 'rel' => 'tooltip', 'title' => 'atender este registro', 'escape' => false));
											}
											?>

											<? if ($contacto['Contacto']['atendido'] && !$contacto['Contacto']['confirmado_cliente'] && $contacto['Contacto']['administrador_id'] == $this->Session->read('Auth.Administrador.id')) : ?>
												<?= $this->Html->link('<i class="fa fa-envelope"></i> Notificar', array('action' => 'notificar_cliente', $contacto['Contacto']['id']), array('class' => 'btn btn-xs btn-warning btn-block mt-5', 'rel' => 'tooltip', 'title' => 'Notificar este registro', 'escape' => false)); ?>
											<? endif; ?>

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

</div>