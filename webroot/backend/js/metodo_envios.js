var metodo_envios = function(){
	return {
		desactivarDependencia: function(obj){
			obj.addClass('hidden');
			obj.find('.form-control').each(function(){
				$(this).attr('disabled', 'disabled');
			});
		},
		activarDependencia: function(obj){
			obj.removeClass('hidden');
			obj.find('.form-control').each(function(){
				$(this).removeAttr('disabled');
			});
		},
		bind: function(){

			$(document).on('change', '.js-select-dependencia', function(){
				if ($(this).val() == 'starken') {
					// Desactivamos las otras dependencias
					metodo_envios.desactivarDependencia($('.js-panel-conexxion'));

					// Activamos la dependencia
					metodo_envios.activarDependencia($('.js-panel-starken'));
				}

				if ($(this).val() == 'conexxion') {
					// Desactivamos las otras dependencias
					metodo_envios.desactivarDependencia($('.js-panel-starken'));

					// Activamos la dependencia
					metodo_envios.activarDependencia($('.js-panel-conexxion'));
				}
			});
		},
		init: function(){
			if ($('.js-select-dependencia').length) {
				metodo_envios.bind();
			}
		}
	}
}();

$(document).ready(function(){
	metodo_envios.init();
});