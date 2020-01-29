<div class="row" style="margin-top: 15px; margin-bottom: 15px;">
	<div class="col-xs-12" style="display: flex; justify-content: space-between;align-items: center;">
		<div class="btn-group"style="display: flex;justify-content: space-between;width: 100%;">
			<?= $this->Html->link('<i class="fa fa-list"></i> Todo', array('action' => 'index_todo'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-info', 'escape' => false,)); ?>
			<?= $this->Html->link('<i class="fa fa-ban"></i> Sin procesar', array('action' => 'index_no_procesadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc(""), 'escape' => false,)); ?>
			<?= $this->Html->link('<i class="fa fa-pencil-square-o"></i> En revisión', array('action' => 'index_revision'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("iniciado"), 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-money"></i> Asignación m. pago', array('action' => 'index_asignacion_moneda'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("validado"), 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-user"></i> En proveedor', array('action' => 'index_validadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("asignacion_moneda"), 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-money"></i> Por pagar', array('action' => 'index_validada_proveedores'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("validado_proveedor"), 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-truck"></i> Enviadas', array('action' => 'index_enviadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("enviado"), 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-meh-o"></i> Incompletas', array('action' => 'index_incompletas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("incompleto"), 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-exclamation-circle"></i> Factura pendiente', array('action' => 'index_pendiente_facturas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("pendiente_factura"), 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-smile-o"></i> Completas', array('action' => 'index_finalizadas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("recibido"), 'escape' => false)); ?>
			<?= $this->Html->link('<i class="fa fa-trash"></i> Canceladas', array('action' => 'index_canceladas'), array('style' => 'margin-top: 0;', 'class' => 'btn btn-block btn-xs btn-' . $this->Html->colorOc("cancelada"), 'escape' => false)); ?>
		</div>
	</div>
</div>