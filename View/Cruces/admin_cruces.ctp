<div class="page-title">
	<h2 class="pull-left"><span class="fa fa-random"></span> Cruzar Datos</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<?= $this->Form->create('Cruzar', array('url' => array('controller' => 'cruces', 'action' => 'cruces'), 'inputDefaults' => array('div' => false, 'label' => false), 'type' => 'file')); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Cruzar datos</h3>
					<ul class="panel-controls">
                        <li><a href="#" class="modal-help" data-toggle="modal" data-target="#modalHelp"><span class="fa fa-question"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label>Archivo</label>
						<?=$this->Form->input('archivo', array(
							'class' => '',
							'type' => 'file'
							))?>
					</div>
				</div>
				<? if (!empty($this->Session->read('Cruxe.options'))) : ?>
				<div class="panel-body">
					<h5><?= __('Datos encontrados en el archivo'); ?> <small><?= __('(Se muestran máximo 20 registros)'); ?></small></h5>
					<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
						<table class="table table-bordered" >
							<caption>
								<label><?= __('Seleccione la columna a utilizar en el cruce');?></label>
							</caption>
							<thead>
								<? foreach ($this->Session->read('Cruxe.options') as $i => $cabecera) : ?>
								<th>
									<div class="radio">
										<label>
											<input type="radio" name="data[Cruzar][cabecera]" value="<?= $i; ?>" class="js-column">
											<?=strtoupper($cabecera);?>
										</label>
									</div>
								</th>
								<? endforeach; ?>
							</thead>
							<tbody class="js-body">
							<? foreach ($this->Session->read('Cruxe.data') as $id => $valor) : if ($id == 1) continue; ?>
							<tr>
								<? foreach ($this->Session->read('Cruxe.options') as $i => $op) : ?>
								
								
								<td><?= (!is_null($valor[$i]) || !empty($valor[$i])) ? $valor[$i] : 'Vacio' ;?></td>
								
								
								<? endforeach ; ?>
							</tr>
							<? if ($id > 20) break; endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<? endif; ?>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-search" aria-hidden="true"></i> Cruzar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-enviar btn-success btn-block')); ?>
						</div>
					</div>
				</div>
				<?= $this->Form->end(); ?>
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
        	<li>Dar formato de texto a la columna que contiene la identificadores para el cruce.</li>
        </ul>
		
		<br>
        <p>Los formatos permitidos para realizar el cruce son:</p>
		<ul>
			<li>CSV</li>
			<li>XLS</li>
			<li>XLSX</li>
		</ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	
	$(document).on('click', '.js-column', function(){

		$('.js-body').find('td').removeClass('success');
		$('.js-column').parents('th').eq(0).removeClass('success');

		if ($(this).is(':checked')) {
			var pos = $(this).parents('th').eq(0).index();
			
			$(this).parents('th').eq(0).addClass('success');

			$('.js-body tr').each(function(){
				$(this).find('td').eq(pos).addClass('success');
			});

		}
	});

</script>