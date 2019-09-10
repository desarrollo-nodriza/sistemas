$.extend({
	campannas: {
		validate: function(){

			$('.js-form-campana').validate({
				rules : {
					'data[Campana][nombre]': {
						required: true
					},
					'data[Campana][categoria_id]': {
						required: true
					}
				},
				messages : {
					'data[Campana][nombre]': {
						required: 'Ingrese nombre de la campaña'
					},
					'data[Campana][categoria_id]': {
						required: 'seleccione categoría principal'
					}
				}
			});

			$('.js-form-campana input, .js-form-campana select').each(function(){

				var $ths = $(this);

				if ($ths.hasClass('not-blank')) {
					$ths.rules("add", {
				        required: true,
				        messages: {
				        	required: 'Campo requerido'
				        }
				    });
				}

			});

		},
		clonarElemento: function($ths){

			var $contexto = $ths.parents('.panel').eq(0).find('.clone-tr').eq(0);
			var limite    = $ths.parents('.js-clone-wrapper').eq(0).data('filas');
			var filas     = $ths.parents('.js-clone-wrapper').eq(0).find('tbody').find('tr:not(.hidden)').length;

			if (typeof(limite) == 'undefined') {
				limite = 200;
			}

			if ( filas == limite ) {
				noty({text: 'No puede agregar más de ' + limite + ' filas.', layout: 'topRight', type: 'error'});
				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
				return;
			}

			var newTr = $contexto.clone();


			newTr.removeClass('hidden');
			newTr.removeClass('clone-tr');
			newTr.find('input, select, textarea').each(function(){
				$(this).removeAttr('disabled');
			});

			// Agregar nuevo campo
			$contexto.parents('tbody').eq(0).append(newTr);

			// Re indexar
			$contexto.parents('tbody').eq(0).find('tr:not(.hidden)').each(function(indx){
				
				$(this).find('input, select, textarea').each(function() {
					
					var $that = $(this);

					if ( typeof($that.attr('name')) != 'undefined' ) {

						nombre		= $that.attr('name').replace(/(999)/g, (indx));
						$that.attr('name', nombre);

					}

					if ($that.hasClass('not-blank')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Campo requerido'
					        }
					    });
					}

				});

			});
		},
		clonar: function() {		

			$(document).on('click', '.duplicate_tr, .copy_tr',function(e){

				e.preventDefault();

				$.campannas.clonarElemento($(this));
				page_content_onresize();

			});


			$(document).on('click', '.remove_tr', function(e){

				e.preventDefault();

				var $th = $(this).parents('tr').eq(0);

				$th.fadeOut('slow', function() {
					$th.remove();
					page_content_onresize();
				});

			});
		},
		init: function(){
			if ($('.js-form-campana').length) {
				$.campannas.validate();				
			}

			if ( $('.js-crear-etiquetas-campana').length ) {
				$.campannas.clonar();
			}

			$('.js-select-categoria-main').on('change', function(){
				$('.js-crear-etiquetas-campana').remove();
			});
		}
	}
});

$(document).ready(function(){
	$.campannas.init();
});