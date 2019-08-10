<div class="row" style="margin-top: 15px; margin-bottom: 15px;">
	<div class="col-xs-12" style="display: flex; justify-content: space-between;align-items: center;">
		<label style="width: 10%;">Acceso rápido</label>
		<div class="btn-group"style="display: flex;justify-content: space-between;width: 90%;">
			<?= $this->Html->link('<i class="fa fa-ban"></i> Sin procesar', array('action' => 'index_no_procesadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-danger', 'escape' => false,)); ?>
			<?= $this->Html->link('<i class="fa fa-pencil-square-o"></i> En revisión', array('action' => 'index_revision'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-warning', 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-money"></i> Por pagar', array('action' => 'index_validadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-info', 'escape' => false)); ?>
			<!--<?= $this->Html->link('<i class="fa fa-envelope"></i> Listas para envio', array('action' => 'index_pagadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-success', 'escape' => false)); ?>-->
			<?= $this->Html->link('<i class="fa fa-truck"></i> Enviadas', array('action' => 'index_enviadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-primary', 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-meh-o"></i> Incompletas', array('action' => 'index_incompletas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-danger', 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-smile-o"></i> Completas', array('action' => 'index_finalizadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-success', 'escape' => false)); ?>
		</div>
	</div>
</div>