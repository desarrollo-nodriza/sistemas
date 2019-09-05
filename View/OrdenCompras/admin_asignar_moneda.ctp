<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Asignar método de pago</h2>
</div>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizontal form-pay', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'), 'data-id' => $this->request->data['OrdenCompra']['id'])); ?>
<?= $this->Form->input('id');?>
<?=$this->Form->hidden('estado', array('value' => 'asignacion_moneda'));?>
<?=$this->Form->hidden('descuento', array('value' => $this->request->data['OrdenCompra']['descuento']));?>
<?=$this->Form->hidden('descuento_monto', array('value' => $this->request->data['OrdenCompra']['descuento_monto']));?>
<?=$this->Form->hidden('total', array('value' => $this->request->data['OrdenCompra']['total']));?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title text-uppercase">
						<b>Proveedor:</b> <?=$this->request->data['Proveedor']['nombre'];?><br>
						<b>Rut:</b> <?=$this->request->data['Proveedor']['rut_empresa'];?><br>
					</h3>
				</div>
				<div class="panel-body">
					
				    <div class="row">
						<div class="col-xs-12 form-group text-center">
							<h1>OC N°<?=$this->request->data['OrdenCompra']['id'];?></h1>
							<h2>Monto a pagar: <span id="total_bruto"><?=CakeNumber::currency($this->request->data['OrdenCompra']['total'] , 'CLP');?></span></h2>
							<h3>Descuento aplicado: <span id="descuento_aplicado">0</span>%</h3>
							<!--<h3>Pendiente de pago: <span id="total_pendiente"><?=CakeNumber::currency($this->request->data['OrdenCompra']['pendiente_pago'], 'CLP');?></span></h3>-->
						</div>
						<div class="col-xs-12 form-group">
                            <?= $this->Form->label('moneda_id', 'Medio de pago (requerido)');?></p>
                            <?= $this->Form->input('moneda_id', array('class' => 'form-control not-blank js-select-moneda', 'empty' => 'Seleccione' )); ?>
							<span class="help-block">El medio de pago usado, se heredará a las facturas recibidas para ésta OC</span>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<button type="submit" class="btn btn-success esperar-carga" autocomplete="off" data-loading-text="Espera un momento..."><i class="fa fa-check"></i> Asignar y enviar a Proveedor</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>