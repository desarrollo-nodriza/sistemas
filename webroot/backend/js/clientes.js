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


		}	
	};
}();



$(document).ready(function(){
	clientes.init();
});
