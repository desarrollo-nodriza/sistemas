var metodo_envios = function(){
	return {
		bind: function(){

			$(document).on('change', '.js-select-dependencia', function(){
				if ($(this).val() == 'starken') {
					$('.js-panel-starken').removeClass('hidden');
				}else{
					$('.js-panel-starken').addClass('hidden');
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