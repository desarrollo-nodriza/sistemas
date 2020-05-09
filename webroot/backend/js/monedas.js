var monedas = function(){
	return {
		index: function(token, data, successCallback, failCallback){

			$.app.loader.mostrar();

			if(typeof successCallback != 'function' || typeof successCallback != 'function'){
		      $.app.loader.ocultar();
		      console.log('Callback functions not defined');
		    }

			$.ajax({
				url: webroot + 'api/monedas.json?token=' + token,
				type: 'GET',
				data: data
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
		view: function(token, id, successCallback, failCallback){

			$.app.loader.mostrar();

			if(typeof successCallback != 'function' || typeof successCallback != 'function'){
		      $.app.loader.ocultar();
		      console.log('Callback functions not defined');
		    }

			$.ajax({
				url: webroot + 'api/monedas/view/' + id + '.json?token=' + token,
				type: 'GET'
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
		init: function(){

		}	
	};
}();



$(document).ready(function(){
	monedas.init();
});
