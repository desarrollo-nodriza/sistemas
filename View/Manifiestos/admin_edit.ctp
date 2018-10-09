<div class="page-title">
	<h2><i class="fa fa-table" aria-hidden="true"></i> Nuevo Manifiesto</h2>
</div>


<?= $this->Form->create('Manifiesto', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
	<?= $this->Form->input('id');?>
	<?= $this->Form->input('administrador_id', array('value' => $this->Session->read('Auth.Administrador.id'), 'type' => 'hidden')); ?>
	<?= $this->Form->input('tienda_id', array('value' => $this->Session->read('Tienda.id'), 'type' => 'hidden')); ?>

	<div class="page-content-wrap">
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-truck"></i> Transportista</h3>
					</div>
					<div class="panel-body row">
						<div class="form-group col-xs-12 col-md-6">
							<?= $this->Form->input('transporte_id', array(
								'class' => 'form-control',
								'empty'	=> 'Seleccione',
								'required' => true
								)); ?>
						</div>
						<div class="form-group col-xs-12 col-md-6">
							<?= $this->Form->input('ot_manual', array('class' => 'form-control', 'placeholder' => 'Ingrese la OT del transporte (opcional)' )); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-cubes"></i> Empaquetar</h3>
					</div>
					<div class="panel-body row">
						
						<p>Seleccione los pedidos que contendrá el manifiesto</p>

						<div id="wrapper-ordenes" style="max-height: 400px;">
							<div class="table-responsive">
								<table class="table table-bordered table-stripped ctm-datatables">
									<thead>
										<th></th>
										<th>ID</th>
										<th>REFERENCIA</th>
										<th>ESTADO</th>
										<th>TOTAL</th>
										<th>CLIENTE</th>
										<th>CREADA</th>
										<th>MANIFIESTOS</th>
										<th>ITEMS</th>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="panel-footer">
						<div class="pull-right">
							<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar Manifiesto">
							<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?= $this->Form->end(); ?>

<!-- MESSAGE BOX-->
<div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="modal_alertas">
    <div class="mb-container">
        <div class="mb-middle">
            <div class="mb-title" id="modal_alertas_label"><i class="fa fa-alert"></i> Confirmar orden</div>
            <div class="mb-content">
                <p id="mensajeModal"></p>                    
            </div>
            <div class="mb-footer">
                <div class="pull-right">
                	<button class="btn btn-primary btn-lg" id="confirmar_manifiesto">Agregar de todas formas</button>
                    <button class="btn btn-default btn-lg mb-control-close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MESSAGE BOX-->

<script type="text/javascript">
	
	var cargarOrdenes = true;
	obtener_ordenes();

	$('.ctm-datatables').addClass('hide');

	function obtener_ordenes($limit = 200, $offset = 0)
	{	
		$('.loader').css('display', 'block');

		if (cargarOrdenes) {

			data = {
				'id' : '<?=$this->request->data['Manifiesto']['id'];?>',
				'limit' : $limit,
				'offset' : $offset
			};

			$.get( webroot + 'manifiestos/obtener_ordenes_ajax/', data , function(respuesta){
			
				if (respuesta != '0') {
					$('#wrapper-ordenes tbody').append(respuesta);
					$offset = $offset + $limit;
					obtener_ordenes($limit, $offset);
				}else{
					cargarOrdenes = false;

					$('.loader').css('display', 'none');

					$('.ctm-datatables').removeClass('hide');

					$('.ctm-datatables').DataTable({
						paging: false,
				    	scrollY: 400,
						ordering: false
					});

					$('.create_input').each(function(){
						if ( $(this).prop('checked') ) {

							$seleccionado = $(this);
							crearInput();
							
						}else{

						}
					});

				}

			})
			.fail(function(){

				$('.loader').css('display', 'none');

				noty({text: 'Ocurrió un error al obtener las ordenes. Intente nuevamente.', layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});
		}

	}


	var $seleccionado = null;


	$(".mb-control-close").on("click",function(){

       $(this).parents(".message-box").removeClass("open");
       
       $seleccionado.prop('checked', false);

       return false;
    });

    $('#confirmar_manifiesto').on('click', function(){
    	
    	crearInput();

    	$(this).parents(".message-box").removeClass("open");

    });


	function crearInput()
	{	
		$("#ManifiestoAdminEditForm").append('<input id="venta_' + $seleccionado.val() + '" type="hidden" name="data[Orden][][venta_id]"/ value="' + $seleccionado.val() + '">');
	}


	function levantarModal()
	{
		/* MESSAGE BOX */
        var box = $('#modal_alertas');

        	$('#mensajeModal').html('La orden id #' + $seleccionado.data('id') + ' Ya ha sido agregada a ' + $seleccionado.data('manifiestos') + ' manifiestos');
        
            box.toggleClass("open");

            var sound = box.data("sound");

            if(sound === 'alert')
                playAudio('alert');

            if(sound === 'fail')
                playAudio('fail');
    
        return false;
	
	}


	function evaluarModal()
	{	
		if ($seleccionado.data('manifiestos') > 0) {
			levantarModal();
		}else{
			crearInput();
		}
	}

	$(document).on('change', '.create_input', function(){
		if ( $(this).prop('checked') ) {
			$seleccionado = $(this);
			evaluarModal();
		}else{
			$("#venta_" + $(this).val() ).remove();
			$seleccionado = null;
		}
	});

</script>


<div class="loader"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>