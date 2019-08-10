$.extend({
	ordenCompraPagos: {
		procesar_pagos_masivo: {
			validar: function($id){

				$('.js-validate-pago').validate();
				
				$('.js-validate-pago input, .js-validate-pago select').each(function(){

					var $ths = $(this);

					if ($ths.hasClass('not-blank')) {
						$ths.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Campo requerido'
					        }
					    });
					}

					if ($ths.hasClass('is-rut')) {
						$ths.rules("add", {
					        rut: true,
					        messages: {
					        	rut: 'Ingrese un rut válido'
					        }
					    });
					}

					if ($ths.hasClass('is-email')) {
						$ths.rules("add", {
					        email: true,
					        messages: {
					        	email: 'Formato de email no válido'
					        }
					    });
					}

					if ($ths.hasClass('is-date')) {
						$ths.rules("add", {
					        date: true,
					        messages: {
					        	date: 'Formato de fecha no válido (ej:2001-01-30)'
					        }
					    });
					}

					if ($ths.hasClass('datepicker')) {
						$ths.datepicker({
							language	: 'es',
							format		: 'yyyy-mm-dd'
						});
					}

					if ($ths.hasClass('is-digit')) {
						$ths.rules("add", {
					        digits: true,
					        min: 0,
					        messages: {
					        	digits: 'Ingrese solo números',
					        	min: '0 es el mínimo'
					        }
					    });
					}

					if ($ths.hasClass('is-number')) {
						$ths.rules("add", {
					        number: true,
					        min: 0,
					        messages: {
					        	number: 'Ingrese solo números',
					        	min: '0 es el mínimo'
					        }
					    });
					}

			});

			},
			agregar: function(tr, id){
				var fila = tr;

				if ($('#pagar-masivo').find('tr[data-id="'+id+'"]').length) {
					return;
				}

				$('#pagar-masivo').append(fila);

				$.ordenCompraPagos.procesar_pagos_masivo.validar();

				$('.js-validate-pago').removeClass('hidden');



				page_content_onresize();

			},
			quitar: function($tr){
				$tr.fadeOut('slow', function() {
					$tr.remove();				
				});
			},
			calcular_monto_disponible: function(){

			}
		},
		calendario: {
			init: function(){
				$('#calendario_pagos').fullCalendar({
				    weekends : true,
				    locale: "es",
				    aspectRatio: 3,
				    eventLimit: 2,
				    eventSources : {
				    	url : webroot + 'ordenCompraPagos/obtener_pagos',
				    	method : 'GET',
				    	failure: function(){
				    		noty({text: 'Ocurrió un error al obtener los pagos. Regresque la página.', layout: 'topRight', type: 'error'});
				    	} 
				    },
			    	header : {
			    		right : 'prevYear,nextYear',
			    		left : 'prev,next,today',
			    		center : 'title'
			    	},
				    eventClick: function(info) {

				    	$.ordenCompraPagos.procesar_pagos_masivo.agregar(info.tr, info.id);

				    	$(".tagsinput").each(function(){
			                $(this).tagsInput({width: '100%',height:'auto',defaultText: 'n folio'});
			            });

				    	return false;
				    	
				  	}
				});


				$(document).on('click', '.remove_tr', function(e){
					e.preventDefault();
					var $th = $(this).parents('tr').eq(0);
					$.ordenCompraPagos.procesar_pagos_masivo.quitar($th);
				});

				$(document).on('click', '.js-select-factura', function(){
					var $ths 		= $(this),
						contexto 	= $ths.parents('tr').eq(0),
						monto_pendiente 		= parseInt($ths.data('pendiente-pago')),
						monto_pendiente_o 		= parseInt($ths.data('pendiente-pago-original')),
						monto_pendiente_html 	= contexto.find('.js-pendiente-pago'),
						monto_disponible        = parseInt($ths.parents('.modal-body').find('.js-disponible-asignar').eq(0).data('disponible-pagar')),
						monto_disponible_o      = parseInt($ths.parents('.modal-body').find('.js-disponible-asignar').eq(0).data('disponible-pagar-original')),
						monto_disponible_html   = $ths.parents('.modal-body').find('.js-disponible-asignar').eq(0),
						monto_pagado_input 		= contexto.find('.js-factura-monto-pagar');

					var nuevo_monto = 0;


					if ( $ths.is(':checked') && monto_disponible == 0) {
						$ths.prop('checked', false);
						noty({text: 'No puedes asignar éste pago a la factura. Monto diponible: $' + monto_disponible , layout: 'topRight', type: 'error'});
						return;
					}


					if ($ths.is(':checked')) {

						if (monto_disponible <= monto_pendiente) {

							nuevo_monto = monto_pendiente - monto_disponible;

							monto_pendiente_html.unmask();
							monto_pendiente_html.text(nuevo_monto).mask('000.000.000.000.000', {reverse: true}).prepend('$');

							$ths.data('pendiente-pago', nuevo_monto);

							monto_pagado_input.val(monto_disponible);
							
							monto_disponible_html.unmask();
							monto_disponible_html.text(0).mask('000.000.000.000.000', {reverse: true}).prepend('$')

							$ths.parents('.modal-body').find('.js-disponible-asignar').eq(0).data('disponible-pagar', 0);

						}else{

							nuevo_monto = monto_disponible - monto_pendiente;

							monto_pendiente_html.unmask();
							monto_pendiente_html.text(0).mask('000.000.000.000.000', {reverse: true}).prepend('$');

							$ths.data('pendiente-pago', 0);

							monto_pagado_input.val(monto_pendiente);
							
							monto_disponible_html.unmask();
							monto_disponible_html.text(nuevo_monto).mask('000.000.000.000.000', {reverse: true}).prepend('$')

							$ths.parents('.modal-body').find('.js-disponible-asignar').eq(0).data('disponible-pagar', nuevo_monto);

						}

					}else{

						if (monto_disponible <= monto_pendiente) {

							monto_pendiente_html.unmask();
							monto_pendiente_html.text(monto_pendiente_o).mask('000.000.000.000.000', {reverse: true}).prepend('$');

							$ths.data('pendiente-pago', monto_pendiente_o);
							
							nuevo_monto = monto_disponible + parseInt(monto_pagado_input.val());
							console.log(nuevo_monto);
							monto_pagado_input.val(0);

							monto_disponible_html.unmask();
							monto_disponible_html.text(nuevo_monto).mask('000.000.000.000.000', {reverse: true}).prepend('$')

							$ths.parents('.modal-body').find('.js-disponible-asignar').eq(0).data('disponible-pagar', nuevo_monto);

						}else{

							monto_pendiente_html.unmask();
							monto_pendiente_html.text(monto_pendiente_o).mask('000.000.000.000.000', {reverse: true}).prepend('$');

							$ths.data('pendiente-pago', monto_pendiente_o);

							monto_pagado_input.val(0);
							
							nuevo_monto =  parseInt(monto_disponible_o - monto_pendiente);
							console.log(nuevo_monto);
							monto_disponible_html.unmask();
							monto_disponible_html.text(nuevo_monto).mask('000.000.000.000.000', {reverse: true}).prepend('$')

							$ths.parents('.modal-body').find('.js-disponible-asignar').eq(0).data('disponible-pagar', nuevo_monto);

						}

					}

				});
			}
		},
		init: function(){
			if ($('#calendario_pagos').length) {
				$.ordenCompraPagos.calendario.init();
			}
		}
	}
});


$(document).ready(function(){
	$.ordenCompraPagos.init();
});