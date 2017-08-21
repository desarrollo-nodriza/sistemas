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
		listShipping: function(categoria_hoja){
			var requestUrl 	= webroot + 'mercadoLibres/envioDisponible/' + categoria_hoja + '/true';
			var html = "";
			$.get(requestUrl, function(result){

				if (typeof(result) == 'object') {
					if (result.length > 0) {
						for (var itr = 0; itr <= result.length - 1; itr++) {
							if (result[itr]['mode'] == 'custom') {
								if (typeof(result[itr]['shipping_attributes']['local_pick_up']) != 'undefined') {
								html += "<div class='form-group'>";
								html +=	"<input type='checkbox' name='data[Envios][local_pick_up]'>";
								html += "<label>Tambien se puede retirar en persona</label>";
								html +=	"</div>";
								}
								
								html += "<div class='form-group'>";
								html += "<input type='checkbox' name='data[Envios][" + result[itr]['mode'] + "]' id='" + result[itr]['mode'] + "' class='meli-custom-shipment'>";
								html +=	" <label for='" + result[itr]['mode'] + "'>" + result[itr]['label'] + "</label>";
								html += "</div>";
								html += "<div class='meli-custom-list table-responsive'>";
								html +=	"<table class='table table-bordered js-clon-scope' data-limit='10'>";
								html +=	"	<thead>";
								html += "		<th>Descripción</th>";
								html += "		<th>Costo</th>";
								html += "		<th>Acciones</th>";
								html += "	</thead>";
								html += "	<tbody class='js-clon-contenedor js-clon-blank'>";
								html += "	<tr class='js-clon-base hidden'>";
								html += "		<td>";
								html += "			<input type='text' name='data[Envios][costs][999][description]' class='form-control' disabled='disabled'>";
								html += "		</td>";
								html += "		<td>";
								html += "			<input type='text' name='data[Envios][costs][999][cost]' class='form-control' disabled='disabled'>";
								html += "		</td>";
								html += "		<td>";
								html += "			<a href='#' class='btn btn-xs btn-danger js-clon-eliminar'><i class='fa fa-trash'></i> Quitar</a>";
								html += "		</td>";
								html += "	</tr>";
								html += "	</tbody>";
								html += "	<tfoot>";
								html += "	<tr>";
								html += "		<td colspan='3'><a href='#' class='btn btn-xs btn-success js-clon-agregar'><i class='fa fa-plus'></i> Agregar otro</a></td>";
								html += "	</tr>";
								html += "	</tfoot>";
								html += "</table>";
								html += "</div>";
							}else if (result[itr]['mode'] == 'me2') {
								html += "<div class='form-group'>";
								html += "<input type='checkbox' name='data[Envios][" + result[itr]['mode'] + "]' id='" + result[itr]['mode'] + "' class='meli-custom-shipment'>";
								html +=	" <label for='" + result[itr]['mode'] + "'>" + result[itr]['label'] + "</label>";
								html += "</div>";
								if (typeof(result[itr]['shipping_attributes']['local_pick_up']) != 'undefined') {
								html += "<div class='form-group'>";
								html +=	"<input type='checkbox' name='data[Envios][local_pick_up]'>";
								html += "<label>Tambien se puede retirar en persona</label>";
								html +=	"</div>";
								}
							}
						}
					}
					$('.shipping-container').html('');
					$('.shipping-container').html(html);
				}

				$.app.clonarTabla.init();
				$.meli.shipping();
			});
		},
		shipping: function(){

			$('.meli-custom-shipment').on('change', function(){
				if ($(this).is(':checked')) {
					$('.meli-custom-list').removeClass('hide');
				}else{
					$('.meli-custom-list').addClass('hide');
				}
			});

			if ($('.meli-custom-shipment').is(':checked')) {
				$('.meli-custom-list').removeClass('hide');
			}else{
				$('.meli-custom-list').addClass('hide');
			}
			
		},
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

							$('.shipping-container').html('');

							$.app.loader.ocultar();

						}else{

							$.meli.listShipping($this.val());
							
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

			if ($('.meli-custom-shipment').length) {
				$.meli.shipping();
			}

		}
	}
});
$(document).ready(function(){
	$.meli.init();
});