var clientes = function(){
	return {
		clear_form: function(){
			$('#VentaClienteNombre').val('');
			$('#VentaClienteApellido').val('');
			$('#VentaClienteEmail').val('');
			$('#VentaClienteRut').val('');
			$('#VentaClienteTelefono').val('');
			$('#obtener_cliente').val('');
		},
		add: function(successCallback, failCallback){

			$.app.loader.mostrar();

			$('#success-mensaje-cliente').html('');
			$('#success-mensaje-cliente').parents('.alert').eq(0).addClass('hidden');
			$('#error-mensaje-cliente').html('');
			$('#error-mensaje-cliente').parents('.alert').eq(0).addClass('hidden');

			if(typeof successCallback != 'function' || typeof successCallback != 'function'){
		      $.app.loader.ocultar();
		      $('#error-mensaje-cliente').html('Callback functions not defined');
			  $('#error-mensaje-cliente').parents('.alert').eq(0).removeClass('hidden');
		    }

			$.ajax({
				url: webroot + 'api/clientes/add.json?token=' + $('#VentaClienteAccessToken').val(),
				type: 'POST',
				data: $('#VentaClienteAdminAddForm').serialize()
			})
			.done(function(res){
				successCallback.call(this, res);
			})
			.fail(function(error, textStatus, message) {

				failCallback(error);

			})
			.always(function(){
				$.app.loader.ocultar();
				clientes.clear_form();
			});

		},
		editar_direccion: function(res){
			$('#success-mensaje-direccion').html('200: Dirección editada exitosamente.');
			$('#success-mensaje-direccion').parents('.alert').eq(0).removeClass('hidden');
			console.log(res.response.direccion);
	      	// Mostramos la nueva dirección
	      	$('#clienteDirecciones').find('[data-id="'+res.response.direccion.id+'"]').eq(0).replaceWith(res.response.direccion.tr);

			setTimeout(function(){
				$('#modalCrearDireccion').modal('hide');
			}, 1500);
		},
		init: function(){
			
			$(document).on('change', '.js-cliente-tipo', function(){

				var val 		= $(this).val(),
					contexto 	= $(this).parents('form').eq(0),
					giro 		= contexto.find('.js-giro-comercial').eq(0);
	
				if (val == 'persona') {
					giro.parents('tr').eq(0).addClass('hidden');
					giro.attr('disabled', 'disabled');
					giro.rules("remove", "required");
				}
			
				if (val == 'empresa') {
					giro.parents('tr').eq(0).removeClass('hidden');
					giro.removeAttr('disabled');
					giro.rules("add", {
				        required: true,
				        messages: {
				        	required: 'Campo requerido'
				        }
				    });
				}

			});

			// Edición de direcciones
			$(document).on('submit', '#DireccionAdminAddForm', function(e){
				e.preventDefault();
				var form = $(this);

				if (!form.valid()) {
					return false;
				}

				// Editamos la direccion seleccionada
				direcciones.edit(
					form.data('id'),
					function(res){
						clientes.editar_direccion(res);
					},
					function(err){
						$('#error-mensaje-direccion').html(err.responseJSON.code + ': ' + err.responseJSON.message);
						$('#error-mensaje-direccion').parents('.alert').eq(0).removeClass('hidden');
					}
				);
				
			});	

			$(document).on('click', '.address-edit', function(e){

				e.preventDefault();

				var id_direccion = $(this).parents('tr').eq(0).data('id');

				$('#DireccionAdminAddForm').data('id', id_direccion);

				direcciones.view(
					id_direccion, 
					function(res){

						$('#modalCrearDireccion').modal('show');

						$('#DireccionAlias').val(res.direccion.Direccion.alias);
						$('#DireccionCalle').val(res.direccion.Direccion.calle);
						$('#DireccionNumero').val(res.direccion.Direccion.numero);
						$('#DireccionVentaClienteId').val(res.direccion.Direccion.venta_cliente_id);
						$('#DireccionComunaId').val(res.direccion.Direccion.comuna_id);
						$('#DireccionDepto').val(res.direccion.Direccion.depto);

						$('#DireccionComunaId').trigger("change");

					}, 
					function(err){

						noty({text: err.responseJSON.code + ': ' + err.responseJSON.message, layout: 'topRight', type: 'error'});

						setTimeout(function(){
							$.noty.closeAll();
						}, 10000);
					}
				);

			});


		}	
	};
}();



$(document).ready(function(){
	clientes.init();
});
