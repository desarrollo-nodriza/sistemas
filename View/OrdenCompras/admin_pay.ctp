<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Pagar OC <small class="text-mutted">(Validada por <?=$ocs['OrdenCompra']['nombre_validado'];?>)</small></h2>
</div>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizontal form-pay', 'data-oc' => $ocs['OrdenCompra']['id'] , 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>

	<?=$this->Form->hidden('descuento', array('value' => $ocs['OrdenCompra']['descuento']));?>
	<?=$this->Form->hidden('descuento_monto', array('value' => $ocs['OrdenCompra']['descuento_monto']));?>
	<?=$this->Form->hidden('total', array('value' => $ocs['OrdenCompra']['total']));?>


<div class="page-content-wrap">
	
	<? if (!empty($ocs['OrdenCompra']['comentario_validar'])) : ?>
		
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-comments"></i> Anotación del administrador</h3>
					</div>
					<div class="panel-body">
						<?=$this->Text->autoParagraph($ocs['OrdenCompra']['comentario_validar']);?>
					</div>
				</div>
			</div>
		</div>

	<? endif; ?>

	<? 	if ( ! empty($ocs['OrdenCompra']['adjunto']) ) : ?>
	<div class="row">
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-file"></i> Comprobante de pago</h3>
					</div>
					<div class="panel-body">
						<?= $this->Html->link(
						'<i class="fa fa-eye"></i> Ver',
						sprintf('/img/%s', $ocs['OrdenCompra']['adjunto']['path']),
						array(
							'class' => 'btn btn-info btn-xs btn-block', 
							'target' => '_blank', 
							'fullbase' => true,
							'escape' => false) 
						); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<? endif; ?>
	
	<!--
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<div class="widget widget-success small-widget" style="min-height: 80px !important;">
                <div class="widget-title">TOTAL BRUTO</div>
                <div class="widget-int"><?=CakeNumber::currency($ocs['OrdenCompra']['total'] , 'CLP');?></div>
            </div>
        </div>
		<div class="col-xs-12 col-md-3">
            <div class="widget widget-info small-widget" style="min-height: 80px !important;">
                <div class="widget-title">TOTAL NETO</div>
                <div class="widget-int"><?=CakeNumber::currency($ocs['OrdenCompra']['total_neto'] , 'CLP');?></div>
            </div>
        </div>
       	<div class="col-xs-12 col-md-3">
            <div class="widget widget-primary small-widget" style="min-height: 80px !important;">
                <div class="widget-title">TOTAL IVA</div>
                <div class="widget-int"><?=CakeNumber::currency($ocs['OrdenCompra']['iva'] , 'CLP');?></div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="widget widget-warning small-widget" style="min-height: 80px !important;">
                <div class="widget-title">TOTAL DESCUENTO</div>
                <div class="widget-int"><?=CakeNumber::currency($ocs['OrdenCompra']['descuento_monto'] , 'CLP');?></div>
            </div>
		</div>
	</div>
	-->

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title text-uppercase"><b>Proveedor: <?=$ocs['Proveedor']['nombre'];?><br> Rut: <?=$ocs['Proveedor']['rut_empresa'];?> </b></h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 form-group text-center">
							<h1>OC N°<?=$ocs['OrdenCompra']['id'];?></h1>
							<h2>Monto a pagar sin descuentos: <?=CakeNumber::currency($ocs['OrdenCompra']['total'] , 'CLP');?></h2>
						</div>
						<div class="col-xs-12 form-group input-group-lg text-center">
                            <h3>Forma de pago</h3>
                            <?= $this->Form->input('moneda_id', array('class' => 'form-control js-select-moneda', 'empty' => 'Seleccione', 'default' => $ocs['OrdenCompra']['moneda_id'] )); ?>
						</div>
						<div class="col-xs-12 form-group">
							<button type="button" class="btn btn-primary btn-block btn-lg btn-calcular-precio"><i class="fa fa-money"></i> CONTINUAR</button>
						</div>
						<div class="col-xs-12 text-center form-group">
							<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="modalComentarioLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalComentarioLabel">Resultados de la operación</h4>
      </div>
      <div class="modal-body">
		<div class="form-group col-xs-12">
			<div class="table-responsive">
				<table class="table table-bordered">
					<th class="text-center">Monto OC</th>
					<th class="text-center">Descuento proveedor</th>
					<th class="text-center">Monto descuento</th>
					<tbody>
						<tr>
							<td align="center"><b><?=CakeNumber::currency($ocs['OrdenCompra']['total'] , 'CLP');?></b></td>
							<td align="center"><b id="descuento_aplicado">0%</b></td>
							<td align="center"><b id="descuento_monto_aplicado">0</b></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="form-group col-xs-12">
			<div class="widget widget-success" style="min-height: 80px !important;">
                <div class="widget-title">TOTAL A PAGAR</div>
                <div id="total_bruto" class="widget-int"><?=CakeNumber::currency($ocs['OrdenCompra']['total'] , 'CLP');?></div>
            </div>
		</div>

		<div class="form-group col-xs-12 text-center">
			<button class="btn btn-primary js-toggle-adjunto-oc" type="button" data-toggle="collapse" data-target="#collapseComments" aria-expanded="false" aria-controls="collapse" data-close="Click aquí para agregar un comprobante y/o comentario" data-open="Cerrar y limpiar">Click aquí para agregar un comprobante y/o comentario</button>
		</div>
		
		<div class="collapse" id="collapseComments">
	      	<div class="form-group col-xs-12">
	    		<?=$this->Form->label('adjunto', 'Adjunte un documento como comprobante, fotocopia, etc del pago.');?></p>
				<?= $this->Form->input('adjunto', array('type' => 'file', 'class' => '' )); ?>
	    	</div>
	      	<div class="form-group col-xs-12">
		        <?=$this->Form->label('comentario_finanza', 'Déje un comentario, instrucción o sugerencia para ' . $ocs['Administrador']['nombre'] . ' (opcional)');?></p>
		        <?=$this->Form->input('comentario_finanza', array('class' => 'form-control', 'placeholder' => 'Ingrese texto...')); ?>
	    	</div>
	    </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success esperar-carga" autocomplete="off" data-loading-text="Espera un momento..."><i class="fa fa-check"></i> Pagar OC</button>
        <!--<button type="submit" class="btn btn-danger reject-button"><i class="fa fa-ban"></i> Rechazar OC</button>-->
      </div>
    </div>
  </div>
</div>

<?= $this->Form->end(); ?>

<script type="text/javascript">
	$('.reject-button').on('click', function(e){
		e.preventDefault();

		var input = '<input type="hidden" name="data[OrdenCompra][estado]" value="rechazado">';

		$('form').append(input);
		$('form').submit();
	});

</script>
