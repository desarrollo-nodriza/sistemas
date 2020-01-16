/**
 * JS DTE
 */

function validaRut(campo){
    if ( campo.length == 0 ){ return false; }
    if ( campo.length < 8 ){ return false; }

    campo = campo.replace('-','')
    campo = campo.replace(/\./g,'')

    if ( campo.length == 0 ){ return false; }
    if ( campo.length < 8 ){ return false; }

        campo = campo.replace('-','')
        campo = campo.replace(/\./g,'')

    if ( campo.length > 9 ){ return false; }

    var suma = 0;
    var caracteres = "1234567890kK";
    var contador = 0;    
    for (var i=0; i < campo.length; i++){
        u = campo.substring(i, i + 1);
        if (caracteres.indexOf(u) != -1)
        contador ++;
    }
    if ( contador==0 ) { return false }
    
    var rut = campo.substring(0,campo.length-1)
    var drut = campo.substring( campo.length-1 )
    var dvr = '0';
    var mul = 2;
    
    for (i= rut.length -1 ; i >= 0; i--) {
        suma = suma + rut.charAt(i) * mul
                if (mul == 7)   mul = 2
                else    mul++
    }
    res = suma % 11
    if (res==1)     dvr = 'k'
                else if (res==0) dvr = '0'
    else {
        dvi = 11-res
        dvr = dvi + ""
    }
    if ( dvr != drut.toLowerCase() ) { return false; }
    else { return true; }
}

$.validator.addMethod("rut", function(value, element) { 
        return this.optional(element) || validaRut(value); 
}, "Revise el RUT");


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

					// Asignamos los valores vacios a sus respectivos inputs
					$('#DteRazonSocialReceptor').val('');
					$('#DteGiroReceptor').val('');
					$('#DteDireccionReceptor').val('');
					$('#DteComunaReceptor').val('');

					if(contribuyente === null){
						$.app.loader.ocultar();
						
						noty({text: 'Ocurrió un error al obtener los datos del cliente.', layout: 'topRight', type: 'error'});

						setTimeout(function(){
							$.noty.closeAll();
						}, 10000);

						return;
					}

					if (typeof(contribuyente) == 'object') {
						// Asignamos los valores obtenidos a sus respectivos inputs
						$('#DteRazonSocialReceptor').val(contribuyente.razon_social);
						$('#DteGiroReceptor').val(contribuyente.giro);
						$('#DteDireccionReceptor').val(contribuyente.direccion);
						$('#DteComunaReceptor').val(contribuyente.comuna_glosa);

						noty({text: 'Datos de facturación cargados.', layout: 'topRight', type: 'success'});

						setTimeout(function(){
							$.noty.closeAll();
						}, 10000);
					}
		     	})
		     	.fail(function(){
		     		$.app.loader.ocultar();
		     	})
		     	.always(function(){
		     		$.app.loader.ocultar();
		     	});
			},
			bind: function(){

				$(document).on('focusout', '#DteRutReceptor', function(){
					if ($('#DteRutReceptor').hasClass('valid')) {
						$.dte.rutChileno.autocompletar();
					}
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
		validar : {
			bind: function(){				

				$('#DteAdminGenerarForm').validate({
					ignore: ".editable_hidden",
				  	rules : {
				  		'data[Dte][tipo_documento]' : {
				  			required : true
				  		},
				  		'data[Dte][razon_social_receptor]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 33 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][rut_receptor]' : {
				  			rut : true,
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 33 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][giro_receptor]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 33 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][direccion_receptor]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 33 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][comuna_receptor]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 33 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][fecha]' : {
				  			date: true,
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 33 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][tipo_traslado]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 52 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][direccion_traslado]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 52 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][comuna_traslado]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 52 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][rut_transportista]' : {
				  			rut: true,
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 52 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][patente]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 52 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][rut_chofer]' : {
				  			rut: true,
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 52 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][nombre_chofer]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 52 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'data[Dte][tipo_ntc]' : {
				  			required: {
				  				depends : function(element) {
					  				if ( $('#DteTipoDocumento').val() == 61 ) {
					  					return true;
					  				}else{
					  					return false;
					  				}
					  			}
				  			}				  			
				  		},
				  		'editTransport' : {
				  			required : true,
				  			number : true,
				  		},
				  		'editDiscount' : {
				  			required : true,
				  			number: true
				  		},
				  		'editVlrCodigo[0]' : {
				  			required: true
				  		},
				  		'editNmbItem[0]' : {
				  			required: true
				  		},
				  		'editPrcItem[0]' : {
				  			required: true,
				  			number: true
				  		},
				  		'editPrcBrItem[0]' : {
				  			required: true,
				  			number: true
				  		},
				  		'editQtyItem[0]' : {
				  			required: true,
				  			min: 1,
				  			digits: true
				  		}
				  	},
				  	messages: {
				  		'data[Dte][tipo_documento]' : {
				  			required : 'Seleccione tipo de documento'
				  		},
				  		'data[Dte][razon_social_receptor]' : {
				  			required : 'Ingrese la razón social'
				  		},
				  		'data[Dte][rut_receptor]' : {
				  			rut : 'Ingrese un rut válido',
				  			required: 'Ingrese rut'				  			
				  		},
				  		'data[Dte][giro_receptor]' : {
				  			required: 'Ingrese Giro'				  			
				  		},
				  		'data[Dte][direccion_receptor]' : {
				  			required: 'Ingrese Dirección'				  			
				  		},
				  		'data[Dte][comuna_receptor]' : {
				  			required: 'Seleccione comuna'				  			
				  		},
				  		'data[Dte][fecha]' : {
				  			date: true,
				  			required: 'Selecione una fecha'				  			
				  		},
				  		'data[Dte][tipo_traslado]' : {
				  			required: 'Seleccione tipo de traslado'			  			
				  		},
				  		'data[Dte][direccion_traslado]' : {
				  			required: 'Ingrese dirección'			  			
				  		},
				  		'data[Dte][comuna_traslado]' : {
				  			required: 'Seleccione comuna' 				  			
				  		},
				  		'data[Dte][rut_transportista]' : {
				  			rut: 'Ingrese un rut válido',
				  			required: 'Ingrese rut del transportista'		  			
				  		},
				  		'data[Dte][patente]' : {
				  			required: 'Ingrese patente del vehículo'
				  		},
				  		'data[Dte][rut_chofer]' : {
				  			rut: 'Ingrese un rut válido',
				  			required: 'Ingrese rut del chofer' 				  			
				  		},
				  		'data[Dte][nombre_chofer]' : {
				  			required: 'Ingrese nombre del chofer'
				  		},
				  		'data[Dte][tipo_ntc]' : {
				  			required: 'Seleccione el tipo de NDC que creará'
				  		},
				  		'editTransport' : {
				  			required: 'Transporte debe ser mayor o igual a 0',
				  			number: 'Solo números'	  			
				  		},
				  		'editDiscount' : {
				  			required: 'Descuento debe ser mayor o igual a 0',
				  			number: 'Solo números'
				  		},
				  		'editVlrCodigo[0]' : {
				  			required: 'Ingrese código item'
				  		},
				  		'editNmbItem[0]' : {
				  			required: 'Ingrese nombre item'
				  		},
				  		'editPrcItem[0]' : {
				  			required: 'Ingrese precio neto item',
				  			number: 'Solo números'
				  		},
				  		'editPrcBrItem[0]' : {
				  			required: 'Ingrese precio bruto item',
				  			number: 'Solo números'
				  		},
				  		'editQtyItem[0]' : {
				  			required: 'Ingrese cantidad',
				  			min: 'Debe ser mayor a 0',
				  			digits: 'Solo números'
				  		}
				  	}

				});

			},
			init: function(){
				if ( $('#DteAdminGenerarForm').length) {
					$.dte.validar.bind();
				}
			}
		},
		tipoDocumento: {
			deshabilitar: function(){
				$('.js-no-boleta').addClass('hidden');


				/*
				$('#DteRutReceptor').attr('disabled', 'disabled');
				$('#DteRazonSocialReceptor').attr('disabled', 'disabled');
				$('#DteGiroReceptor').attr('disabled', 'disabled');
				$('#DteDireccionReceptor').attr('disabled', 'disabled');
				$('#DteComunaReceptor').attr('disabled', 'disabled');
				*/
			},
			habilitar: function(){

				$('.js-no-boleta').removeClass('hidden');

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
			Referencia: {
				habilitar: function(){
					if ( $('.js-referencia').hasClass('hide') ) {
						$('.js-referencia').removeClass('hide');
					}
				},
				deshabilitar: function(){
					if ( ! $('.js-referencia').hasClass('hide') ) {
						$('.js-referencia').addClass('hide');
					}
				}
			},
			tipoNdc: {
				habilitar: function(){
					if ( $('.js-tipo-ndc').hasClass('hide') ) {
						$('.js-tipo-ndc').removeClass('hide');
					}
				},
				deshabilitar: function(){
					if ( ! $('.js-tipo-ndc').hasClass('hide') ) {
						$('.js-tipo-ndc').addClass('hide');
					}
				}
			},
			bind:function(){

				var $selector = $('.js-dte-tipo');

				$.dte.tipoDocumento.Referencia.habilitar();


				if( $selector.val() == 39 ) {
					$.dte.tipoDocumento.deshabilitar();
					$.dte.bloquearCampos();
				}else if ($selector.val() == 61){
					$.dte.tipoDocumento.tipoNdc.habilitar();
					$.dte.tipoDocumento.habilitar();
					$.dte.desbloquearCampos();
				}else{
					$.dte.tipoDocumento.habilitar();
					$.dte.desbloquearCampos();
				}

				$selector.on('change', function(){
					// Si es boleta se limpian los valores de los campos que no se utilizan
					if ($(this).val() == 39) {
						$.dte.tipoDocumento.deshabilitar();
						$.dte.bloquearCampos();
					}else{
						$.dte.tipoDocumento.habilitar();
						$.dte.desbloquearCampos();
					}

					// Documentos referenciados factura
					if ($(this).val() == 33) {
						//$.dte.tipoDocumento.Referencia.habilitar();
						$.dte.bloquearCampos();
					}else{
						//$.dte.tipoDocumento.Referencia.deshabilitar();
						$.dte.desbloquearCampos();
					}

					// Habilitar referencia y desbloquear campos					
					if ($(this).val() == 61 || $(this).val() == 56 || $(this).val() == 52) {
						$.dte.tipoDocumento.Referencia.habilitar();
						$.dte.desbloquearCampos();
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
		modificarVenta: {
			calcularTotales: function(){

				var totalProductos = 0,
					totalIva       = 0,
					totalBruto     = 0;

				$('.editPrcItem').each(function(){
					var contxt = $(this).parents('tr').eq(0);

					if (!contxt.hasClass('.hidden')) {

						var tDescuento = Math.round( ( $('.editDiscount').val() / 1.19) );

						var tNeto = Math.round( $(this).val() * contxt.find('.editQtyItem').val() );
						
						totalProductos = totalProductos + tNeto;

						var tIva        = Math.round( (totalProductos - tDescuento) * 0.19 );
						var tTransporte = Math.round($('.editTransport').val());
						var tBruto      = Math.round( tTransporte + (totalProductos - tDescuento) + tIva);

						var neto 		= OSREC.CurrencyFormatter.format(totalProductos, { currency: 'CLP' });
						var iva 		= OSREC.CurrencyFormatter.format(tIva, { currency: 'CLP' });
						var bruto 		= OSREC.CurrencyFormatter.format(tBruto, { currency: 'CLP' });

						$('.total_neto').html(neto);
						$('.total_iva').html(iva);
						$('.total_bruto').html(bruto);
					}

				});
			},
			init: function() {

				//$("input.mask_money").mask('##########000.0', {reverse: true, placeholder: ''});

				$(document).on('click', '#modificar_productos_venta', function(e){
					e.preventDefault();
					$(this).toggleClass('cancelar');
					$(this).children('span').toggle();
					$('.permitido_modificar').children('span').toggle();

					if (!$(this).hasClass('cancelar')) {
						$('.editable').each(function(){
							$(this).val($(this).data('original'));
							$(this).siblings('.editable_hidden').val($(this).data('original'));
						});
					}

				});

				$(document).on('keyup', '.editVlrCodigo' ,function(){
					$(this).siblings('.editable_hidden').val($(this).val());
				});

				$(document).on('keyup', '.editNmbItem' ,function(){
					$(this).siblings('.editable_hidden').val($(this).val());
				});

				$(document).on('keyup', '.editDiscount', function(){
					$.dte.modificarVenta.calcularTotales();

					$(this).siblings('.editable_hidden').val( Math.round($(this).val() / 1.19) );

				});

				$(document).on('keyup', '.editTransport', function(){
					$.dte.modificarVenta.calcularTotales();
					$(this).siblings('.editable_hidden').val($(this).val());
				});

				$(document).on('keyup', '.editPrcItem' ,function(){


					var context 	= $(this).parents('tr').eq(0),
						conIVa 		= Math.round($(this).val() * 1.19),
						sinIva 		= ($(this).val() / 1.19).toFixed(1);

					$(this).siblings('.editable_hidden').val($(this).val());
					context.find('.editPrcBrItem').val( conIVa );

					var moneyTotal  = OSREC.CurrencyFormatter.format(Math.round( $(this).val() * context.find('.editQtyItem').val() ), { currency: 'CLP' });

					context.find('.precio_iva_total_productos').eq(0).html( moneyTotal );
					
					$.dte.modificarVenta.calcularTotales();

				});

				$(document).on('keyup', '.editPrcBrItem' ,function(){

					var context 	= $(this).parents('tr').eq(0),
						conIVa 		= Math.round($(this).val() * 1.19),
						sinIva 		= ($(this).val() / 1.19).toFixed(1);

					context.find('.editPrcItem').val( sinIva );
					context.find('.editPrcItem').siblings('.editable_hidden').val( sinIva );

					var moneyTotal  = OSREC.CurrencyFormatter.format(Math.round( sinIva * context.find('.editQtyItem').val() ), { currency: 'CLP' });

					context.find('.precio_iva_total_productos').eq(0).html( moneyTotal );
					
					$.dte.modificarVenta.calcularTotales();

				});

				$(document).on('focusout', '.editPrcItem', function(){
					$.dte.modificarVenta.calcularTotales();
				});

				$(document).on('keyup', '.editQtyItem' ,function(){

					$(this).siblings('.editable_hidden').val($(this).val());

					var context = $(this).parents('tr').eq(0);

					var money = OSREC.CurrencyFormatter.format( Math.round( context.find('.editPrcItem').val() * $(this).val() ) , { currency: 'CLP' });

					context.find('.precio_iva_total_productos').eq(0).html( money );
					$.dte.modificarVenta.calcularTotales();

				});

				$(document).on('focusout', '.editQtyItem', function(){
					$.dte.modificarVenta.calcularTotales();
				});


				$(document).on('click', '#DteAdminGenerarForm .duplicate_tr',function(e){

					e.preventDefault();

					$contexto = $(this).parents('.copy_tr').siblings('.clone-tr').eq(0);

					var newTr = $contexto.clone();


					newTr.removeClass('hidden');
					newTr.removeClass('clone-tr');
					newTr.find('input').each(function(){
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

							// Validar campos nuevos
							if ($that.hasClass('editVlrCodigo')) {
								$that.rules("add", {
							        required: true,
							        messages: {
							        	required: 'Ingrese código item'
							        }
							    });
							}


							if ($that.hasClass('editNmbItem')) {
								$that.rules("add", {
							        required: true,
							        messages: {
							        	required: 'Ingrese nombre item'
							        }
							    });
							}


							if ($that.hasClass('editPrcItem') || $that.hasClass('editPrcBrItem')) {
								$that.rules("add", {
							        required: true,
							        number: true,
							        messages: {
							        	required: 'Ingrese precio neto item',
							        	number: 'Solo números'
							        }
							    });
							}


							if ($that.hasClass('editQtyItem')) {
								$that.rules("add", {
							        required: true,
							        digits: true,
							        min: 1,
							        messages: {
							        	required: 'Ingrese cantidad item',
							        	digits: 'Solo números',
							        	min: 'Debe ser mayor a 0'
							        }
							    });
							}

							//$("input.mask_money").mask('#000.0', {reverse: true});

						});

					});

				});

			}
		},
		bloquearCampos: function() {
			$('.js-productos-dte input, .js-productos-dte select').each(function(){
				$(this).attr('readonly', 'readonly');
			});
			$('.js-productos-dte button').each(function(){
				$(this).addClass('hidden');
			});
		},
		desbloquearCampos: function() {
			$('.js-productos-dte input, .js-productos-dte select').each(function(){
				$(this).removeAttr('readonly');
				$('.js-productos-dte button').each(function(){
					$(this).removeClass('hidden');
				});
			});
		},
		init: function(){
			$.dte.rutChileno.init();
			$.dte.tipoDocumento.init();
			$.dte.dteReferencia.init();
			$.dte.validar.init();
			if ( $('#DteAdminGenerarForm').length ) {
				$.dte.modificarVenta.init();
			}
		}
	}
});

$(document).ready(function(){
	$.dte.init();

	$(document).on('click', '#DteAdminGenerarForm .remove_tr', function(e){

		e.preventDefault();

		var $th = $(this).parents('tr').eq(0);

		$th.fadeOut('slow', function() {
			$th.remove();
			$.dte.modificarVenta.calcularTotales();
		});

	});

	OSREC.CurrencyFormatter.formatAll(
	{
		selector: '.money',
		currency: 'CLP'
	});

});