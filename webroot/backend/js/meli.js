$.extend({
	toggleInput: {
		bind: function(){
			$('.toggle-button').on('click', function(e){
				e.preventDefault();

				var $contexto = $(this).parents('.js-toggle-wrapper').eq(0),
					$input 	 = $contexto.find('.toggle-input').eq(0);
					$text = $contexto.find('.toggle-text').eq(0);

				if ($input.attr('type') === 'hidden' ) {
					$input.attr('type', 'text');
					$text.addClass('hide');
					$(this).html('<i class="fa fa-chevron-circle-up" aria-hidden="true"></i> Cancelar');
				}else{
					$input.attr('type', 'hidden');
					$text.removeClass('hide');
					$(this).html('<i class="fa fa-chevron-circle-down" aria-hidden="true"></i> Actualizar');
				}
			});
		},
		init: function(){
			if ( $('.toggle-input').length && $('.toggle-button') ) {
				$.toggleInput.bind();
			};
		}
	},
	meli: {
		bind:function(){

			var $htmlBase = $('.js-base');

			$(document).on('change', '.js-cat' ,function(){
				
				$.app.loader.mostrar();

				var $this 			= $(this),
					requestUrl 		= webroot + 'mercadoLibres/obtenerCategoriasId/' + $this.val(),
					options 		= '<select name="data[MercadoLibr][categoria_0' + ($('.js-cat').index($this)+1) + ']" class="form-control js-cat" required="required"><option value="">Seleccione</option>',
					cantSelect 		= $('.js-cat').length,
					posicionSelect 	= $('.js-cat').index($this) + 1;

				$this.parent().find('.help-block').text('');

				$.get(requestUrl, function(result){
					
					if (typeof(result) == 'object') {
						if (result.length > 0) {
							for (var itr = 0; itr <= result.length - 1; itr++) {
								options += '<option value="' + result[itr]['id'] + '">' + result[itr]['name'] + '</option>';
							}
							options += '</select><span class="help-block"></span>';

							// Se copia el div con el selector siguiente
							var $nextHtmlCategory = $htmlBase.clone();
							$nextHtmlCategory.removeClass('js-base');
							$nextHtmlCategory.find('select').remove();
							$nextHtmlCategory.html(options);

							for (var i = posicionSelect; i < cantSelect; i++) {
								$('.js-cat').eq(posicionSelect).parent().remove();
							}

							$nextHtmlCategory.insertAfter($this.parent());

							$.app.loader.ocultar();

						}else{

							$.app.loader.ocultar();

							$this.removeClass('js-base');
							$('#categoria_hoja').val($this.val());
							$this.parent().find('.help-block').html('<i class="fa fa-check text-success"></i> Categoría final');
						}
						console.info('Tiene ' + result.length + ' Categorias');
					}
				});
			});

		},
		init:function(){
			if ( $('.js-cat').length ) {
				$.meli.bind();
				$.toggleInput.bind();
			}
		}
	}
});
$(document).ready(function(){
	$.meli.init();
});