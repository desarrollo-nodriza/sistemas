var mensajes = function(){
	return {
		get: function($params, $token, successCallback, failCallback){
			$.app.loader.mostrar();

			$.ajax({
				url: webroot + 'api/mensajes.json?token=' + $token,
				data: $params
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
		view: function(message_id, $token, successCallback, failCallback){
			
			$.app.loader.mostrar();

			$.ajax({
				url: webroot + 'api/mensajes/view/'+message_id+'.json?token=' + $token
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
		add: function($data, $token, successCallback, failCallback){

			$.app.loader.mostrar();

			$.ajax({
				url: webroot + 'api/mensajes/add.json?token=' + $token,
				type: 'POST',
				data: $data
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
		delete: function($message_id, $token, successCallback, failCallback){

			$.app.loader.mostrar();

			$.ajax({
				url: webroot + 'api/mensajes/delete/' + $message_id + '.json?token=' + $token,
				type: 'POST'
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
		}
	};
}();