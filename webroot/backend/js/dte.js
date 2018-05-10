/**
 * JS DTE
 */

$.extend({
	dte: {
		rutLimpio: function(rut){

			var posGuion = rut.indexOf('-');
			if (posGuion > 0) {
				rut = rut.substr(0, posGuion);
			}else{
				rut = rut.substr(0, (rut.length - 1));
			}

			return rut.replace(/[.]/g, '');
		},
		rutChileno: {
			autocompletar: function(){

				$.app.loader.mostrar();
				var $rut 			= $('.rut-contribuyente'),
					rutFormateado 	= $.dte.rutLimpio($rut.val());
				
				$.get( webroot + 'ordenes/getContribuyenteInfo/' + rutFormateado + '/true', function(respuesta){
					var contribuyente 	= $.parseJSON(respuesta);	
					if (typeof(contribuyente) == 'object') {
						// Asignamos los valores obtenidos a sus respectivos inputs
						$('#DteRazonSocialReceptor').val(contribuyente.razon_social);
						$('#DteGiroReceptor').val(contribuyente.giro);
						$('#DteDireccionReceptor').val(contribuyente.direccion);
						$('#DteComunaReceptor').val(contribuyente.comuna);
					}

					$.app.loader.ocultar();

		     	})
		     	.fail(function(){
		     		$.app.loader.ocultar();
		     	});
			},
			bind: function(){
				$('.rut-contribuyente').rut();
				$('.rut-input').rut();

				$(document).on('rutInvalido', '.rut-contribuyente', function(e) {
					if ( $(this).val() != '' ) {
						if ($(this).hasClass('valid')) {
					    	$(this).removeClass('valid');
					    }
					    $(this).addClass('error');
					    if ( $(this).parent().find('span').length == 0 ) {
					    	$(this).parent().append('<span id="' + $(this).attr('id') + '-error" class="rut-error" for="' + $(this).attr('id') + '">Ingrese un rut válido</span>');
					    }
					}else{
						$(this).parent().find('span').remove();
						$(this).removeClass('valid');
						$(this).removeClass('error');
					}
				});

				$(document).on('rutValido', '.rut-contribuyente', function(e, rut, dv) {
				    if ($(this).hasClass('error')) {
				    	$(this).removeClass('error');
				    }
				    if ( $(this).parent().find('span').length > 0 ) {
				    	$(this).parent().find('span').remove();
				    }

				    $(this).addClass('valid');
				    $.dte.rutChileno.autocompletar();	
				});


				$(document).on('rutInvalido', '.rut-input', function(e) {
					if ( $(this).val() != '' ) {
						if ($(this).hasClass('valid')) {
					    	$(this).removeClass('valid');
					    }
					    $(this).addClass('error');
					    if ( $(this).parent().find('span').length == 0 ) {
					    	$(this).parent().append('<span id="' + $(this).attr('id') + '-error" class="rut-error" for="' + $(this).attr('id') + '">Ingrese un rut válido</span>');
					    }
					}else{
						$(this).parent().find('span').remove();
						$(this).removeClass('valid');
						$(this).removeClass('error');
					}
				});

				$(document).on('rutValido', '.rut-input', function(e, rut, dv) {
				    if ($(this).hasClass('error')) {
				    	$(this).removeClass('error');
				    }
				    if ( $(this).parent().find('span').length > 0 ) {
				    	$(this).parent().find('span').remove();
				    }

				    $(this).addClass('valid');
				});
			},	
			init: function(){
				if ($('.rut-contribuyente').length) {
					$.dte.rutChileno.bind();
				}
			}
		},
		dteReferencia: {
			bind: function(){

					$('.id-referencia').on('change', function(){
						var $es = $(this),
							$padre = $es.parents('tr').first(),
							$fecha = $padre.find('.fecha-referencia').eq(0),
							$tipo = $padre.find('.tipo-referencia').eq(0);
							$folio = $padre.find('.folio-referencia').eq(0);

						if ($es.val() != '') {
							$.get( webroot + 'ordenes/obtenerDte/' + $es.val() , function(respuesta){
								var dte 	= $.parseJSON(respuesta);	
								if (typeof(dte) == 'object') {
									// Asignamos los valores obtenidos a sus respectivos inputs
									$fecha.val(dte.Dte.fecha);
									$tipo.val(dte.Dte.tipo_documento);
									$folio.val(dte.Dte.folio);
								}

								$.app.loader.ocultar();

					     	})
					     	.fail(function(){
					     		$.app.loader.ocultar();
					     	});
						}
					});
			},
			init: function(){
				if ($('.id-referencia').length) {
					$.dte.dteReferencia.bind();
				}
			}
		},
		tipoDocumento: {
			deshabilitar: function(){
				$('#DteRutReceptor').val('');
				$('#DteRazonSocialReceptor').val('');
				$('#DteGiroReceptor').val('');
				$('#DteDireccionReceptor').val('');
				$('#DteComunaReceptor').val('');
				/*
				$('#DteRutReceptor').attr('disabled', 'disabled');
				$('#DteRazonSocialReceptor').attr('disabled', 'disabled');
				$('#DteGiroReceptor').attr('disabled', 'disabled');
				$('#DteDireccionReceptor').attr('disabled', 'disabled');
				$('#DteComunaReceptor').attr('disabled', 'disabled');
				*/
			},
			habilitar: function(){
				/*
				$('#DteRutReceptor').removeAttr('disabled');
				$('#DteRazonSocialReceptor').removeAttr('disabled');
				$('#DteGiroReceptor').removeAttr('disabled');
				$('#DteDireccionReceptor').removeAttr('disabled');
				$('#DteComunaReceptor').removeAttr('disabled');
				*/
			},
			transporte: {
				habilitar: function(){
					if ( $('.js-despacho').hasClass('hide') ) {
						$('.js-despacho').removeClass('hide');

						$('.js-despacho input, .js-despacho select').each(function(){
							var $este = $(this);
							$este.removeAttr('disabled');
						});
					}
				},
				deshabilitar: function(){
					if ( ! $('.js-despacho').hasClass('hide') ) {
						$('.js-despacho').addClass('hide');

						$('.js-despacho input, .js-despacho select').each(function(){
							var $este = $(this);
							$este.attr('disabled', 'disabled');
						});
					}
				}
			},
			bind:function(){

				var $selector = $('.js-dte-tipo');

				$selector.on('change', function(){
					// Si es boleta se limpian los valores de los campos que no se utilizan
					if ($(this).val() == 39) {
						$.dte.tipoDocumento.deshabilitar();
					}else{
						$.dte.tipoDocumento.habilitar();
					}

					// Si es guia de despacho de abren las opciones de transporte
					if ($(this).val() == 52) {
						$.dte.tipoDocumento.transporte.habilitar();
					}else{
						$.dte.tipoDocumento.transporte.deshabilitar();
					}
				});

				if($selector.val() == 52) {
					$.dte.tipoDocumento.transporte.habilitar();
				}

			},
			init: function(){
				if ( $('.js-dte-tipo').length ) {
					$.dte.tipoDocumento.bind();
				}
			}
		},
		init: function(){
			$.dte.rutChileno.init();
			$.dte.tipoDocumento.init();
			$.dte.dteReferencia.init();
		}
	}
});

$(document).ready(function(){
	$.dte.init();
});