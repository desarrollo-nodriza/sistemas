$(function() {

	var transportistas = function(){

		var validarFormulario = function(){

			var validar = function($formulario){
				$formulario.validate({
                    rules: {
                        'data[OrdenTransporte][transporte]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_codigo_producto]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_codigo_servicio]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_eoc]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_numero_tcc]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_comuna_origen]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_remitente_nombre]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_remitente_email]': {
                            required: true,
                            email: true
                        },
                        'data[OrdenTransporte][e_remitente_celular]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_destinatario_nombre]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_destinatario_email]': {
                            required: true,
                            email: true
                        },
                        'data[OrdenTransporte][e_destinatario_celular]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_direccion_comuna]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_direccion_calle]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_direccion_numero]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_direccion_d_comuna]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_direccion_d_calle]': {
                            required: true
                        },
                        'data[OrdenTransporte][e_direccion_d_numero]': {
                            required: true
                        }
                    },
                    messages: {
                    	'data[OrdenTransporte][transporte]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_codigo_producto]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_codigo_servicio]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_eoc]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_numero_tcc]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_comuna_origen]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_remitente_nombre]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_remitente_email]': {
                            required: 'Requerido',
                            email: 'Ingrese un email válido'
                        },
                        'data[OrdenTransporte][e_remitente_celular]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_destinatario_nombre]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_destinatario_email]': {
                            required: 'Requerido',
                            email: 'Ingrese un email válido'
                        },
                        'data[OrdenTransporte][e_destinatario_celular]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_direccion_comuna]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_direccion_calle]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_direccion_numero]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_direccion_d_comuna]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_direccion_d_calle]': {
                            required: 'Requerido'
                        },
                        'data[OrdenTransporte][e_direccion_d_numero]': {
                            required: 'Requerido'
                        }
                    }
                });
			}

			return {
				init: function(){
					if($('#OrdenTransporteAdminGenerarChilexpressForm').length){
						validar($('#OrdenTransporteAdminGenerarChilexpressForm'));
					}
				}
			}
		}();

		var chilexpress = function(){

			var container            = $('#containerResponse');
			var containerButtons 	 = $('#containerButtons');
			var calleDestino         = $('#OrdenTransporteEDireccionCalle').val();
			var numeroDestino        = $('#OrdenTransporteEDireccionNumero').val();
			var complementoDestino   = $('#OrdenTransporteEDireccionComplemento').val();
			var $btnResetear         = '<button class="btn btn-xs btn-primary resetear btn-block"><i class="fa fa-refresh"></i> Resetear campos</button>';
			var $btnValidarDireccion = '<p>Verifique que ésta dirección es válida.</p><button class="btn btn-xs btn-primary btn-despacho btn-block"><i class="fa fa-check"></i> Validar dirección</button>';
			var $btnGenerar          = $('.btn-generar');

			var obtenerLocales = function(comuna){
				if (comuna != '') {
					$.get( webroot + 'ordenTransportes/obtener_sucursales_comuna/' + comuna, function(res){
						var result = $.parseJSON(res);
						
						if (result.code == 200) {
							container.html(result.tabla);

							noty({text: result.message, layout: 'topRight', type: 'success'});

							setTimeout(function(){
								$.noty.closeAll();
							}, 5000);
						}

						if (result.code == 300) {
							noty({text: result.message, layout: 'topRight', type: 'error'});

							setTimeout(function(){
								$.noty.closeAll();
							}, 5000);
						}

						if (result.code == 404) {
							container.html(result.tabla);

							noty({text: result.message, layout: 'topRight', type: 'error'});

							setTimeout(function(){
								$.noty.closeAll();
							}, 5000);
						}

						activarBoton($btnGenerar);

					}).fail(function(){
						
						noty({text: 'Ocurrió un error al obtener las sucursales. Intente nuevamente.', layout: 'topRight', type: 'error'});

						setTimeout(function(){
							$.noty.closeAll();
						}, 10000);

						desactivarBoton($btnGenerar);
					});

					$('.resetear').remove();

				}else{
					return false;
				}
			}

			var activarBoton = function($boton) {
				$boton.removeAttr('disabled');
			}

			var desactivarBoton = function($boton) {
				$boton.attr('disabled', 'disabled');
			}

			var selectedDefault = function(){
				desactivarBoton($btnGenerar);
				$('#OrdenTransporteEComunaOrigen').val('SANTIAGO CENTRO');
				$('#OrdenTransporteEDireccionDComuna').val('NUNOA');
				$('#OrdenTransporteEDireccionDCalle').val('DIAGONAL ORIENTE');
				$('#OrdenTransporteEDireccionDNumero').val('1355');
				$('#OrdenTransporteEDireccionDComplemento').val('Toolmania');

				if ($('#OrdenTransporteEDireccionComuna').data('selected').length > 0) {
					var selectComunaDestino = $('#OrdenTransporteEDireccionComuna').data('selected');
					$('#OrdenTransporteEDireccionComuna').val(selectComunaDestino.toUpperCase());
				}
			}


			var iniciarSelectComunas = function(){
				$('.js-comuna-destino').on('change', function(){

					var $selectComunas	= $(this),
						$selectTipos 	= $('.js-tipo-despacho');

					if ($selectTipos.val() == 1 && $selectComunas.val() != '') {
						obtenerLocales($selectComunas.val());
					}

				});
			}


			var resetearCamposDestinos = function(){
				containerButtons.html($btnResetear);

				$('body').on('click', '.resetear', function(event){
					event.preventDefault();
					$('#OrdenTransporteEDireccionCalle').val(calleDestino);
					$('#OrdenTransporteEDireccionNumero').val(numeroDestino);
					$('#OrdenTransporteEDireccionComplemento').val(complementoDestino);

					$(this).remove();
					$('.js-select-sucursal').prop('checked', false);
				});
			}


			var validarDireccion = function(){

				$(document).on('click', '.btn-despacho', function(event){
					event.preventDefault();

					var comuna 		= $('#OrdenTransporteEDireccionComuna').val(),
						calle 		= $('#OrdenTransporteEDireccionCalle').val(),
						numero 		= $('#OrdenTransporteEDireccionNumero').val(),
						$ths 		= $(this);
						txtbtn 		= $ths.html(),
						txtbtnload 	= '<i class="fa fa-spinner fa-spin"></i> Cargando';
						
					desactivarBoton($ths);
					$ths.html(txtbtnload);

				
					if (comuna != '' && calle != '' && numero != '') {
						$.get( webroot + 'ordenTransportes/validar_direccion/' + comuna + '/' + calle + '/' + numero, function(res){
							var result = $.parseJSON(res);
							
							if (result.code == 200) {

								noty({text: result.message, layout: 'topRight', type: 'success'});

								setTimeout(function(){
									$.noty.closeAll();
								}, 5000);

								activarBoton($btnGenerar);
							}

							if (result.code == 300) {
								noty({text: result.message, layout: 'topRight', type: 'error'});

								setTimeout(function(){
									$.noty.closeAll();
								}, 5000);
							}

							if (result.code == 404) {

								noty({text: result.message, layout: 'topRight', type: 'error'});

								setTimeout(function(){
									$.noty.closeAll();
								}, 5000);
							}

							activarBoton($ths);
							$ths.html(txtbtn);

						}).fail(function(){
							
							noty({text: 'Ocurrió un error al obtener las sucursales. Intente nuevamente.', layout: 'topRight', type: 'error'});

							setTimeout(function(){
								$.noty.closeAll();
							}, 10000);

							activarBoton($ths);
							desactivarBoton($btnGenerar);
							$ths.html(txtbtn);
						});

					}else{
						noty({text: 'No se permiten campos vacios.', layout: 'topRight', type: 'error'});
						activarBoton($ths);
						$ths.html(txtbtn);
					}


				});

			}


			var selectSucursal = function(){

				$('body').on('click', '.js-select-sucursal', function(){
					
					$('.resetear').remove();

					var $ts = $(this);
					if($ts.prop('checked')) {

						var $tr 					= $ts.parents('tr').eq(0),
						$direccionSucural 		= $tr.children('td').eq(2),
						$numeroSucursal 		= $tr.children('td').eq(3),
						$comunaSucursal 		= $tr.children('td').eq(4),
						$complementoSucursal 	= $tr.children('td').eq(5);

						$('#OrdenTransporteEDireccionCalle').val($direccionSucural.text());
						$('#OrdenTransporteEDireccionNumero').val($numeroSucursal.text());
						$('#OrdenTransporteEDireccionComplemento').val($complementoSucursal.text());

						resetearCamposDestinos();
					
					};
				});
			}


			var iniciarSelectTipoDespacho = function(){

				$('.js-tipo-despacho').on('change', function(){

					var $selectTipos	= $(this),
						$selectComunas 	= $('.js-comuna-destino');

					if ($selectTipos.val() == 1 && $selectComunas.val() != '') {
						obtenerLocales($selectComunas.val());
						containerButtons.html('');
					}

					if ($selectTipos.val() == 0) {
						containerButtons.html($btnValidarDireccion);
						container.html('');
					}
				});
			}

			var inicio = function(){
				var $selectTipos	= $('.js-tipo-despacho'),
					$selectComunas 	= $('.js-comuna-destino');

				if ($selectTipos.val() == 1 && $selectComunas.val() != '') {
					obtenerLocales($selectComunas.val());
				}

				if ($selectTipos.val() == 0) {
					containerButtons.html($btnValidarDireccion);
					container.html('');
				}
			}

			var imprimirEtiqueta = function(){

				$('.btn-imprimir-ot').on('click', function(){

					var etiqueta = $(this).data('etiqueta');
					console.info(etiqueta);
					printJS({
						printable: etiqueta, 
						type: 'image'
					});
				});
				

			}

			return {
				init: function(){
					if ($('.js-comuna-destino').length > 0 && $('.js-comuna-destino').length > 0) {
						selectedDefault();
						inicio();
						iniciarSelectComunas();
						iniciarSelectTipoDespacho();
						selectSucursal();
						validarDireccion();
					}

					if ($('.btn-imprimir-ot').length) {
						imprimirEtiqueta();
					}

				}
			}	
		}();

		return {
			init: function(){
				validarFormulario.init();
				chilexpress.init();
			}
		}
	}();


	transportistas.init();

});