$.extend({
	hoy: function(){
		var hoy = new Date();
		if (hoy.getMonth() < 10 ) {
			if (hoy.getDate() < 10 ) {
				return hoy.getFullYear() + '-0' + (hoy.getMonth() + 1) + '-0' + hoy.getDate();
			}else{
				return hoy.getFullYear() + '-0' + (hoy.getMonth() + 1) + '-' + hoy.getDate();
			}
		}else{
			return hoy.getFullYear() + '-' + (hoy.getMonth() + 1) + '-' + hoy.getDate();
		}
	},
	inicioMes: function(){
		var inicioM = new Date();
		if (inicioM.getMonth() < 10 ) {
			return inicioM.getFullYear() + '-0' + (inicioM.getMonth() + 1) + '-01';
		}else{
			return inicioM.getFullYear() + '-' + (inicioM.getMonth() + 1) + '-01';
		}
	},
	inicioMesAnterior: function(){
		var inicioM = new Date();
		if (inicioM.getMonth() < 10 ) {
			return inicioM.getFullYear() + '-0' + (inicioM.getMonth()) + '-01';
		}else{
			return inicioM.getFullYear() + '-' + (inicioM.getMonth()) + '-01';
		}
	},
	calendario: function(f_inicio, f_final){
		/**
		 * Datepicker rango fechas
		 */
		var $buscador_fecha_inicio		= f_inicio,
			$buscador_fecha_fin			= f_final;

			$buscador_fecha_inicio.datepicker(
			{	
				language	: 'es',
				format		: 'yyyy-mm-dd',
			}).on('changeDate', function(data)
			{
				$buscador_fecha_fin.datepicker('setStartDate', data.date);
			});

			$buscador_fecha_fin.datepicker(
			{
				language	: 'es',
				format		: 'yyyy-mm-dd'
			}).on('changeDate', function(data)
			{
				$buscador_fecha_inicio.datepicker('setEndDate', data.date);
			});
	},
	graficosMeli: {
		init: function(){
			if ( $('#meli-account').length ) {
				$.graficosMeli.bind();
			}
		},
		graficoBarra: function(elemento, datos, ejeX, ejeY, etiquetas, colores){
			Morris.Bar({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				barColors: colores
		    });
		},
		graficoLinea: function(elemento, datos, ejeX, ejeY, etiquetas, colores){
			Morris.Line({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				lineColors: colores
		    });
		},
		graficoArea: function(elemento, datos, ejeX, ejeY, etiquetas, colores){
			Morris.Area({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				lineColors: colores
		    });
		},
		graficoDonuts: function(elemento, datos, colores, formato) {
			Morris.Donut({
		      	element: elemento,
				data: datos,
				formatter: formato,
				colors: colores
		    });
		},
		obtenerVisitasPorRango: function(){
			var divGrafico = $('#HistoricoVisitasMeli');
			
			// Request
			$.ajax({
				url: webroot + "mercadoLibres/totalVisitas/" + $('#VisitasFInicio').val() + '/' + $('#VisitasFFinal').val() + '/' + 'true',
			    dataType: "json"
			   
			})
			.done(function( data, textStatus, jqXHR ) {
					console.log(data);
					var datos = [];
					var colors = ['#39A23B', '#1C1D1C', '#737473'];
					var yKeys = [ 'a', 'b', 'c'];
					var etiquetas = ['Total', 'Toolmania', 'Walko'];
					var i;
					console.log(data);
					for (i in data) {
					    if (data.hasOwnProperty(i)) {
					    	/*datos.push({ y : data[i]['y'], a: data[i]['total'], b : data[i]['toolmania'], c: data[i]['walko'] });*/
					    }
					}

					$('#HistoricoVisitasMeli').html('');

					$.graficosMeli.graficoLinea(divGrafico, datos, 'y', yKeys, etiquetas, colors);

			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
			    console.log( "La solicitud a fallado: " +  textStatus);
			    //accionesReporte($boton, $fechaReporte);
			});
		},
		bind: function(){

			// Visitas
			$('#VisitasFInicio').val($.inicioMes());
			$('#VisitasFFinal').val($.hoy());
			$.calendario($('#VisitasFInicio'), $('#VisitasFFinal'));
			$('#VisitasAgrupar').val('dia');

			// Visitas
			$('#enviarFormularioVisitasMeli').on('click', function(){
				$.graficosMeli.obtenerVisitasPorRango();
			});


			$('#enviarFormularioVisitasMeli').trigger('click');
			

		},
	},
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
			if ( $('.toggle-input').length && $('.toggle-button').length ) {
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
		predictor : {
			request: function(nombre, precio){

				if (nombre != ''){
					var requestUrl 	= webroot + 'mercadoLibres/obtener_prediccion_categoria/' + nombre + '' + precio;
					var html = "";
					$.get(requestUrl, function(result){
						console.log(result);
						if(result != ''){
							html += '<div class="col-xs-12">';
							html += '<div class="alert alert-info">';
							html += 'Categoría sugerida: ' + result;
							html += '</div>';
							html += '</div>';
							$('#categoriSugerida').html(html);
						}else{
							html += '<div class="col-xs-12">';
							html += '<div class="alert alert-danger">';
							html += 'La categoría sugerida no está disponible. Intente modificar el nombre del producto y luego dar click en <button class="btn btn-default btn-xs" id="refreshPredictor"><i class="fa fa-refresh"></i> Refrescar</button>';
							html += '</div>';
							html += '</div>';
							$('#categoriSugerida').html(html);
						}
					});
				}

			},
			refresh : function(){
				$('body').on('click', '#refreshPredictor', function(event){

					event.preventDefault();

					var nombre   = $('#MercadoLibrProducto').val();
					var precio = $('#MercadoLibrPrecio').val();

					$.meli.predictor.request(nombre, precio);
					
				});
			},
			init: function(){

				var nombre   = $('#MercadoLibrProducto').val();
				var precio = $('#MercadoLibrPrecio').val();

				$.meli.predictor.request(nombre, precio);

			}
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

			$.graficosMeli.init();


			$.meli.predictor.refresh();

		}
	}
});
$(document).ready(function(){
	$.meli.init();
});