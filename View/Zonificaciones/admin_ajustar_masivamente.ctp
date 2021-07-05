<div class="page-title">
	<h2><span class="fa fa-tags"></span> Ajustar de forma Masiva los Productos </h2>
</div>

<?= $this->Form->create('Zonificacion', array('inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate-producto', 'type' => 'file')); ?>


<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12 col-md-6 col-md-offset-3">
			
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-arrows" aria-hidden="true"></i> Cargar archivos</h3>
				</div>
				<div class="panel-body">
					<div class="form-group col-xs-12">
						<label>Descarga el archivo con stock actual de productos</label>
						<br>
						<?  
						$exportar_productos = array(
							'controller' => 'zonificaciones',
							'action' => 'exportar_stock_productos_ajustar'
						);
						if (isset($this->request->params['named'])) {
							$exportar_productos = array_replace_recursive($exportar_productos, $this->request->params['named']);
						}?>
						<?= $this->Html->link('<i class="fa fa-download"></i> Plantilla con stock actual', $exportar_productos, array('class' => 'btn btn-success', 'escape' => false)); ?>
						
					</div>
					<div class="form-group col-xs-12 col-md-6">
						<!-- <label>Archivo CSV,XLS,XLSX</label> -->
						<?=$this->Form->input('archivo', array(
							'class' => '<?=(empty($this->Session->read("edicionMasiva"))) ? "not-blank" : "" ; ?>',
							'type' => 'file'
							))?>
					</div>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-send" aria-hidden="true"></i> Continuar', array('type' => 'button', 'escape' => false, 'class' => 'btn btn-success btn-block', 'data-toggle' => 'modal','data-target' => '#modalHelp')); ?>
						</div>
					</div>
				</div>
				
			</div>
		</div>

	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalHelp" tabindex="-1" role="dialog" aria-labelledby="modalHelpLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalHelpLabel"><i class="fa fa-question"></i> Ayuda</h4>
      </div>
      <div class="modal-body">
        <p>Antes de subir un archivo asegurate de:</p>
        <ul>
        	<li>Quitar las filas vacias sobre tu cabecera.</li>
        	<li>Dejar la cebecera del archivo en la primera fila.</li>
        </ul>
		
		<br>
		<br>
        <p>Los formatos permitidos para realizar el cruce son:</p>
		<ul>
			<li>CSV</li>
			<li>XLS</li>
			<li>XLSX</li>
		</ul>
      </div>
      <div class="modal-footer">
      	<?= $this->Form->button('<i class="fa fa-upload" aria-hidden="true"></i> Continuar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-success start-loading-then-redirect')); ?>
      </div>
    </div>
  </div>
</div>


<?= $this->Form->end(); ?>


<script type="text/javascript">
	
	$(document).on('click', '.js-column', function(){

		$('.js-body').find('td').removeClass('success');
		$('.js-column').parents('table').eq(0).find('th').removeClass('success');

		if ($(this).is(':checked')) {
			var pos = $(this).parents('th').eq(0).index();
			
			$(this).parents('th').eq(0).addClass('success');

			$('.js-body tr').each(function(){
				$(this).find('td').eq(pos).addClass('success');
			});

		}
	});

</script>