var direcciones = function(){
	return {
		clear_form: function(){
			$('#DireccionAlias').val('');
			$('#DireccionCalle').val('');
			$('#DireccionNumero').val('');
			$('#DireccionVentaClienteId').val('');
			$('#DireccionComunaId').val('');
			$('#DireccionDepto').val('');
		},
		view: function(address_id, successCallback, failCallback){
			
			$.app.loader.mostrar();

			$('#success-mensaje-direccion').html('');
			$('#success-mensaje-direccion').parents('.alert').eq(0).addClass('hidden');
			$('#error-mensaje-direccion').html('');
			$('#error-mensaje-direccion').parents('.alert').eq(0).addClass('hidden');

			$.ajax({
				url: webroot + 'api/direcciones/view/'+address_id+'.json?token=' + $('#DireccionAccessToken').val()
			})
			.done(function(res){
				successCallback.call(this, res);
			})
			.fail(function(error, textStatus, message) {

				failCallback(error);

			})
			.always(function(){
				$.app.loader.ocultar();
			});

		},
		add: function(successCallback, failCallback){

			$.app.loader.mostrar();

			$('#success-mensaje-direccion').html('');
			$('#success-mensaje-direccion').parents('.alert').eq(0).addClass('hidden');
			$('#error-mensaje-direccion').html('');
			$('#error-mensaje-direccion').parents('.alert').eq(0).addClass('hidden');

			if(typeof successCallback != 'function' || typeof successCallback != 'function'){
		      $.app.loader.ocultar();
		      $('#error-mensaje-direccion').html('Callback functions not defined');
			  $('#error-mensaje-direccion').parents('.alert').eq(0).removeClass('hidden');
		    }

			$.ajax({
				url: webroot + 'api/direcciones/add.json?token=' + $('#DireccionAccessToken').val(),
				type: 'POST',
				data: $('#DireccionAdminAddForm').serialize()
			})
			.done(function(res){
				successCallback.call(this, res);
			})
			.fail(function(error, textStatus, message) {

				failCallback(error);

			})
			.always(function(){
				$.app.loader.ocultar();
				direcciones.clear_form();
			});

		},
		edit: function(address_id, successCallback, failCallback){

			$.app.loader.mostrar();

			$('#success-mensaje-direccion').html('');
			$('#success-mensaje-direccion').parents('.alert').eq(0).addClass('hidden');
			$('#error-mensaje-direccion').html('');
			$('#error-mensaje-direccion').parents('.alert').eq(0).addClass('hidden');

			if(typeof successCallback != 'function' || typeof successCallback != 'function'){
		      $.app.loader.ocultar();
		      $('#error-mensaje-direccion').html('Callback functions not defined');
			  $('#error-mensaje-direccion').parents('.alert').eq(0).removeClass('hidden');
		    }

			$.ajax({
				url: webroot + 'api/direcciones/edit/'+address_id+'.json?token=' + $('#DireccionAccessToken').val(),
				type: 'POST',
				data: $('#DireccionAdminAddForm').serialize()
			})
			.done(function(res){
				successCallback.call(this, res);
			})
			.fail(function(error, textStatus, message) {

				failCallback(error);

			})
			.always(function(){
				$.app.loader.ocultar();
				direcciones.clear_form();
			});

		},
		init: function(){

			
		}	
	};
}();



$(document).ready(function(){
	direcciones.init();
});
