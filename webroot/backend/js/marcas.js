$.extend({

	marca: {
		validar: function() {

			/* Verifica que el tipo de descuento siempre sea % cuando la casilla de compuesto está activa */
			$(document).on('click, change', '.js-compuesto, .js-tipo-descuento', function(){
				var $contexto 	= $(this).parents('tr').eq(0),
					$ths 		= $contexto.find('.js-compuesto'),
					$tDescuento = $contexto.find('.js-tipo-descuento'),
					$descuento 	= $contexto.find('.js-descuento-input');

				if ($ths.is(':checked') && $tDescuento.val() == '0') {
					$tDescuento.val(1);
					$descuento.val('');
				}

			});


			$(document).on('click', '.js-infinito', function(){
				var $contexto 	= $(this).parents('tr').eq(0),
					$ths 		= $contexto.find('.js-infinito'),
					$finicio 	= $contexto.find('.js-f-inicio'),
					$ffinal 	= $contexto.find('.js-f-final');

				if ($ths.is(':checked')) {
					$finicio.val('2018-01-01');
					$finicio.attr('readonly', 'readonly');
					$ffinal.val('3000-01-01');
					$ffinal.attr('readonly', 'readonly');
				}else{
					$finicio.removeAttr('readonly');
					$ffinal.removeAttr('readonly');
				}

			});

			$(".timepicker24").timepicker({minuteStep: 5,showSeconds: true,showMeridian: false});
			
			$('.js-validate-marca').validate({
				rules : {
					'data[Marca][nombre]' : {
						required : true,
						maxlength : 200
					},
					'data[Marca][descuento_base]' : {
						number: true
					},
					'data[Marca][email_contacto]' : {
						email: true
					},
					'data[Marca][fono_contacto]' : {
						number: true
					},
					'data[Marca][rut_empresa]' : {
						rut: true
					},
					'data[PrecioEspecificoMarca][0][nombre]' : {
						required: true
					},
					'data[PrecioEspecificoMarca][0][tipo_descuento]' : {
						required: true
					},
					'data[PrecioEspecificoMarca][0][descuento]' : {
						required: true,
				        digits : true,
				        min: 1,
					},
					'data[PrecioEspecificoMarca][0][fecha_inicio]' : {
						required: true,
						date: true
					},
					'data[PrecioEspecificoMarca][0][hora_inicio]' : {
						required: true,
						time: true
					},
					'data[PrecioEspecificoMarca][0][fecha_termino]' : {
						required: true,
						date: true
					},
					'data[PrecioEspecificoMarca][0][hora_termino]' : {
						required: true,
						time: true
					},
				}
			});
		},
		clonarElemento: function($ths){

			$contexto = $ths.parents('.panel').eq(0).find('.clone-tr').eq(0);
			console.log($contexto);
			var newTr = $contexto.clone();


			newTr.removeClass('hidden');
			newTr.removeClass('clone-tr');
			newTr.find('input, select, textarea').each(function(){
				$(this).removeAttr('disabled');
			});

			// Agregar nuevo campo
			$contexto.parents('tbody').eq(0).append(newTr);

			// Re indexar
			$contexto.parents('tbody').eq(0).find('tr').each(function(indx){

				$(this).find('input, select, textarea').each(function() {
					
					var $that		= $(this);

					if ( typeof($that.attr('name')) != 'undefined' ) {

						nombre		= $that.attr('name').replace(/[(\d)]/g, (indx));
						$that.attr('name', nombre);

					}

					if ($that.hasClass('datepicker')) {
						$that.datepicker({
							language	: 'es',
							format		: 'yyyy-mm-dd'
						});
					}


					if ($that.hasClass('timepicker24')) {
						$that.timepicker({minuteStep: 5,showSeconds: true,showMeridian: false});
					}

					if ($that.hasClass('js-bodega-id')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Seleccione bodega'
					        }
					    });
					}

					if ($that.hasClass('js-bodega-cantidad')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Ingrese la cantidad',
								digits: 'Solo números',
								min: 'Debe ser mayor a 0'
					        }
					    });
					}


					if ($that.hasClass('js-nombre-producto')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Ingrese nombre del descuento'
					        }
					    });
					}


					if ($that.hasClass('js-tipo-descuento')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Seleccione tipo de descuento'
					        }
					    });
					}


					if ($that.hasClass('js-descuento-input')) {
						$that.rules("add", {
					        required: true,
					        digits : true,
					        min: 1,
					        messages: {
					        	required: 'Ingrese nombre del descuento',
					        	digits: 'Solo números',
					        	min: 'Descuento debe ser mayor a 0'
					        }
					    });
					}

					if ($that.hasClass('js-f-inicio')) {
						$that.rules("add", {
							required: true,
					        date: true,
					        messages: {
					        	required: 'Seleccione fecha inicio',
					        	date: 'Formato de fecha no válido'
					        }
					    });
					}

					if ($that.hasClass('js-f-final')) {
						$that.rules("add", {
							required: true,
					        date: true,
					        messages: {
					        	required: 'Seleccione fecha final',
					        	date: 'Formato de fecha no válido'
					        }
					    });
					}


					if ($that.hasClass('js-h-inicio')) {
						$that.rules("add", {
							required: true,
					        time: true,
					        messages: {
					        	required: 'Seleccione hora inicio',
					        	time: 'Formato de hora no válido'
					        }
					    });
					}

					if ($that.hasClass('js-h-final')) {
						$that.rules("add", {
							required: true,
					        time: true,
					        messages: {
					        	required: 'Seleccione hora final',
					        	time: 'Formato de hora no válido'
					        }
					    });
					}


				});

			});
		},
		clonar: function() {		

			$(document).on('click', '.duplicate_tr, .copy_tr',function(e){

				e.preventDefault();

				$.marca.clonarElemento($(this));

			});


			$(document).on('click', '.remove_tr', function(e){

				e.preventDefault();

				var $th = $(this).parents('tr').eq(0);

				$th.fadeOut('slow', function() {
					$th.remove();
				});

			});
		},
		init: function(){

			if ($('.js-validate-marca').length ) {
				$.marca.validar();
				$.marca.clonar();
			}

		}
	}

});


$(document).ready(function(){
	$.marca.init();
});