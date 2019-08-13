$.extend({
	pagos: {
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

				$.pagos.procesar_pagos_masivo.validar();

				$('.js-validate-pago').removeClass('hidden');



				page_content_onresize();

			},
			quitar: function($tr){
				$tr.fadeOut('slow', function() {
					$tr.remove();				
					$.pagos.configurar_pagos.dividirMontos();
				});
			},
			calcular_monto_disponible: function(){

			}
		},
		configurar_pagos: {
			dividirMontos: function(){
				// Calcular montos
				var filas           = $('#PagoAdminConfiguracionForm').find('tbody').find('tr:not(.hidden)').length;
				var total_facturado = $("#total-facturado").data('total');

				$('#PagoAdminConfiguracionForm').find('.js-monto-pagado:not(disabled)').each(function(index){
					
					var monto           = total_facturado / filas;
					console.log(monto);

					if ($(this).hasClass('monto-modificable')) {
						$(this).val(Math.ceil(monto));
					}
					
				});
			},
			clonarElemento: function($ths){

				var $contexto = $ths.parents('.panel').eq(0).find('.clone-tr').eq(0);
				var limite    = $ths.parents('.panel').eq(0).data('filas');
				var filas     = $ths.parents('.panel').eq(0).find('tbody').find('tr:not(.hidden)').length;

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

						if ($that.hasClass('datepicker')) {
							$that.datepicker({
								language	: 'es',
								format		: 'yyyy-mm-dd'
							});
						}

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

						if ($that.hasClass('is-number')) {
							$that.rules("add", {
						        number: true,
						        min: 0,
						        messages: {
						        	number: 'Ingrese solo números',
						        	min: '0 es el mínimo'
						        }
						    });
						}

					});

				});

				$.pagos.configurar_pagos.dividirMontos();
				
			},
			clonar: function() {		

				$(document).on('click', '.duplicate_tr, .copy_tr',function(e){

					e.preventDefault();

					$.pagos.configurar_pagos.clonarElemento($(this));

				});


				
			},
			cambiarMoneda: function($id_oc, $id_moneda, $contexto){
				$.ajax({
					url: webroot + 'ordenCompras/calcularMontoPagar',
					type: 'POST',
					data: {
						'orden_compra_id' : $id_oc,
						'moneda_id' : $id_moneda
					},
				})
				.done(function(res) {

					$result = $.parseJSON(res);
					
					


					if ($result.comprobante_requerido && $contexto.find('.js-comprobante').length) {
						$contexto.find('.js-comprobante').rules("add", {
					        required: true,
					        messages: {
					        	required: 'Comprobante requerido'
					        }
					    });
					}else if( $contexto.find('.js-comprobante').length ){
						$contexto.find('.js-comprobante').rules("remove", "required");
					}


					if ($result.pago_contra_factura) {
						$contexto.find('.js-monto-pagado').rules("remove", "required");
						$contexto.find('.js-identificador-pago').rules("remove", "required");
						$contexto.find('.js-cuenta-pago').rules("remove", "required");

						$.pagos.configurar_pagos.dividirMontos();

					}else{
						$contexto.find('.js-monto-pagado').rules("remove", "max");
					}

					if ($result.agendar) {
						$contexto.find('.js-agendar').rules("add", {
					        required: true,
					        messages: {
					        	required: 'Ingrese fecha del pago'
					        }
					    });

					}else{
						$contexto.find('.js-agendar').rules("remove", "required");
					}

					$contexto.find('.js-monto-pagar').eq(0).tooltip('destroy');
					$contexto.find('.js-monto-pagar').eq(0).val($result.monto_pagar);
					$contexto.find('.js-monto-pagar').eq(0).tooltip({
						show : true,
						title : 'Descuento aplicado: ' + $result.descuento_monto_html,
					});

				})
				.fail(function() {

					noty({text: 'Ocurrió un error al calcular el monto. Intente nuevamente.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
				})
				.always(function(){
					$('.loader').removeClass('show');
				});
			},
			init: function(){

				$(document).on('click', '.remove_tr', function(e){
					e.preventDefault();
					var $th = $(this).parents('tr').eq(0);
					$.pagos.procesar_pagos_masivo.quitar($th);
				});

				$(document).on('change', '.js-select-medio-pago', function(){
					var $ths = $(this);

					$('.loader').addClass('show');

					if ( $ths.val() != '' ) {

						var id_oc 		= $('#PagoAdminConfiguracionForm').data('oc'),
							moneda_id 	= $ths.val(),
							contexto    = $ths.parents('tr').eq(0);

						$.pagos.configurar_pagos.cambiarMoneda(id_oc, moneda_id, contexto);
					
					}

				});

				$.pagos.procesar_pagos_masivo.validar();

				$.pagos.configurar_pagos.clonar();

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
				    	url : webroot + 'pagos/obtener_pagos',
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

				    	if (typeof(info.tr) == 'undefined') {
				    		return false;
				    	}

				    	$.pagos.procesar_pagos_masivo.agregar(info.tr, info.id);

				    	$(".tagsinput").each(function(){
			                $(this).tagsInput({width: '100%',height:'auto',defaultText: 'n folio'});
			            });

			            $(".tooltip").each(function(){
			                $(this).tooltip('toggle');
			            });

				    	return false;
				    	
				  	},
				  	eventRender: function(event, element) {
					    var contenido 	= event.description,
					    	titulo 		= event.title;
					    	disparador  = event.trigger;
			            $(element).attr("data-content", contenido)
			            $(element).popover({ html: true, container: "body", trigger: disparador, title: titulo, placement : 'top'})
					  }
				});


				$(document).on('click', '.close-pop', function(){
					$(this).parents('.popover').popover('hide');
				});

				$(document).on('click', '.ver-pago', function(e){
					e.preventDefault();
				});

				$(document).on('click', '.remove_tr', function(e){
					e.preventDefault();
					var $th = $(this).parents('tr').eq(0);
					$.pagos.procesar_pagos_masivo.quitar($th);
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
				$.pagos.calendario.init();
			}

			if ($('.js-config-pago').length) {
				$.pagos.configurar_pagos.init();
			}
		}
	}
});


$(document).ready(function(){
	$.pagos.init();
});