$.extend({
	ordenCompra: {
		validate: function(){

			$('.js-validate-oc').validate();

			$('.js-validate-oc input, .js-validate-oc select').each(function(){

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

				if ($ths.hasClass('js-precio-producto')) {
					$ths.rules("add", {
				        number: true,
				        min: 1,
				        messages: {
				        	number: 'Ingrese solo números',
				        	min: 'Costo no puede ser 0'
				        }
				    });
				}

			});

		},
		clonarElemento: function($ths){

			$contexto = $ths.parents('.panel').eq(0).find('.clone-tr').eq(0);
			
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

					if ($that.hasClass('js-id-producto') || $that.hasClass('js-codigo-producto') || $that.hasClass('js-descripcion-producto')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Campo requerido'
					        }
					    });
					}


					if ($that.hasClass('js-cantidad-producto') || $that.hasClass('js-total-producto')) {
						$that.rules("add", {
					        required: true,
					        number: true,
					        min: 1,
					        messages: {
					        	required: 'Campo requerido',
					        	number: 'Ingrese solo números',
					        	min: '1 es el mínimo'
					        }
					    });
					}

					if ($that.hasClass('js-descuento-producto')) {
						$that.rules("add", {
					        required: true,
					        number: true,
					        min: 0,
					        messages: {
					        	required: 'Campo requerido',
					        	number: 'Ingrese solo números',
					        	min: '0 es el mínimo'
					        }
					    });
					}

					if ($that.hasClass('js-precio-producto')) {
						$that.rules("add", {
					        required: true,
					        number: true,
					        min: 1,
					        messages: {
					        	required: 'Campo requerido',
					        	number: 'Ingrese solo números',
					        	min: 'Costo no puede ser 0'
					        }
					    });
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
		},
		clonar: function() {		

			$(document).on('click', '.duplicate_tr, .copy_tr',function(e){

				e.preventDefault();

				$.ordenCompra.clonarElemento($(this));

			});


			$(document).on('click', '.remove_tr', function(e){

				e.preventDefault();

				var $th = $(this).parents('tr').eq(0);

				$th.fadeOut('slow', function() {
					$th.remove();
					$.ordenCompra.calcularTotalesProducto();
				    $.ordenCompra.calcularTotales();
				});

			});
		},
		calcularTotalesProducto : function(){

			$('.js-total-producto').each(function(){

				var contx = $(this).parents('tr').eq(0);

				var cantidad 		= contx.find('.js-cantidad-producto').val(),
					precio 			= contx.find('.js-precio-producto').val(),
					tipoDescuento 	= contx.find('.js-tipo-descuento-producto').val(),
					descuento 		= contx.find('.js-descuento-producto').data('descuento'),
					nuevoPrecio 	= 0;
					
					// Porcentaje
					if (tipoDescuento == 1) {
						descuento = (descuento/100).toFixed(2);
						descuento = Math.round(precio*descuento);
					}
					
					nuevoPrecio = precio - descuento;

					if (cantidad > 0) {
						contx.find('.js-descuento-producto').val(cantidad*descuento);
					}
					
					$(this).val(cantidad*nuevoPrecio);


			});

		},
		calcularTotales: function(){
			$('.js-oc').each(function(){
				var $ths = $(this);
				var tipoDescuentoProveedor = $ths.find('.js-tipo-descuento-proveedor').eq(0);
				var descuentoProveedor = $ths.find('.js-descuento-proveedor').eq(0);

				var neto 		= $ths.find('.js-total-neto').eq(0),
					descuento 	= $ths.find('.js-total-descuento').eq(0),
					iva 		= $ths.find('.js-total-iva').eq(0),
					total 		= $ths.find('.js-total-oc').eq(0);
				
				var totalNeto = 0;

				// Total neto
				$ths.find('.js-total-producto:visible').each(function(){
					totalNeto = parseInt(parseInt(totalNeto) + parseInt($(this).val()));
				});

				neto.val(totalNeto);

				// Descuento
				var totalDescuento = 0;
				if (tipoDescuentoProveedor.val() == 1) {
					totalDescuento = (descuentoProveedor.val()/100).toFixed(2);
					totalDescuento = Math.round(totalNeto*totalDescuento);
				}else{
					totalDescuento = descuentoProveedor.val();
				}
				descuento.val(totalDescuento);

				// IVA
				var ivaTotal = Math.round(parseInt(totalNeto) * 0.19);
				iva.val(ivaTotal);

				// Total OC
				var totalOc = (parseInt(totalNeto) + parseInt(ivaTotal));
				total.val(totalOc);
				
			});
		},
		buscar: function(){

			$(document).on('keyup', '.js-buscar-producto', function(){

				var $esto = $(this);
				
				// Se buscan productos
				$esto.autocomplete({
				   	source: function(request, response) {
				      	$.get( webroot + 'ventaDetalleProductos/buscar/' + request.term, function(respuesta){
							response( $.parseJSON(respuesta) );
				      	})
				      	.fail(function(){

							noty({text: 'Ocurrió un error al obtener la información. Intente nuevamente.', layout: 'topRight', type: 'error'});

							setTimeout(function(){
								$.noty.closeAll();
							}, 10000);
						});
				    },
				    select: function( event, ui ) {
				        
				    	var contexto = $esto.parents('tr').eq(0);
				    	
				    	contexto.find('.js-id-producto').val(ui.item.id);
				    	contexto.find('.js-codigo-producto').val(ui.item.codigo);
				    	contexto.find('.js-precio-producto').val(ui.item.precio_costo);
				    	contexto.find('.js-descuento-producto').val(ui.item.descuento);
				    	contexto.find('.js-descuento-producto').attr('data-descuento', ui.item.descuento);
				    	contexto.find('.js-descuento-valor').attr('title', ui.item.nombre_descuento);
				    	contexto.find('.js-tipo-descuento-producto').val(ui.item.tipo_descuento);

				    	// Bloqueamos nuevos campos de precios
				    	contexto.find('.js-precio-producto').attr('readonly', true);
				    	contexto.find('.js-descuento-producto').attr('readonly', true);
				    	contexto.find('.js-total-producto').attr('readonly', true);
				    	contexto.find('.js-tipo-descuento-producto').attr('readonly', true);

				    	//contexto.find('.js-descuento-valor').tooltip();

				    	$.ordenCompra.calcularTotalesProducto();
				    	$.ordenCompra.calcularTotales();

				    },
				    /*open: function(event, ui) {
		                var autocomplete = $(".ui-autocomplete:visible");
		                var oldTop = autocomplete.offset().top;
		                var width  = $esto.width();
		                var newTop = oldTop - $esto.height() + 25;

		                autocomplete.css("top", newTop);
		                autocomplete.css("width", width);
		                autocomplete.css("position", 'absolute');
		            }*/
				});
			});

		},
		bind: function(){
			$(document).on('change', '.js-precio-producto', function(){
				$.ordenCompra.calcularTotalesProducto();
				$.ordenCompra.calcularTotales();
			})

			$(document).on('change', '.js-descuento-producto', function(){
				$.ordenCompra.calcularTotalesProducto();
				$.ordenCompra.calcularTotales();
			})

			$(document).on('change', '.js-cantidad-producto', function(){
				$.ordenCompra.calcularTotalesProducto();
				$.ordenCompra.calcularTotales();
			})

			$(document).on('change', '.js-tipo-descuento-producto', function(){
				$.ordenCompra.calcularTotalesProducto();
				$.ordenCompra.calcularTotales();
			})

			$(document).on('change', '.js-descuento-proveedor', function(){
				$.ordenCompra.calcularTotalesProducto();
				$.ordenCompra.calcularTotales();
			})

			$(document).on('change', '.js-tipo-descuento-proveedor', function(){
				$.ordenCompra.calcularTotalesProducto();
				$.ordenCompra.calcularTotales();
			})

		},
		calcularMonto: function( $id_oc, $id_moneda ) {

			$('.loader').css('display', 'block');

			$.ajax({
				url: webroot + 'ordenCompras/calcularMontoPagar',
				type: 'POST',
				data: {
					'orden_compra_id' : $id_oc,
					'moneda_id' : $id_moneda
				},
			})
			.done(function(res) {

				var $result = $.parseJSON(res);

				$('#descuento_aplicado').text($result.descuento_porcentaje);
				$('#descuento_monto_aplicado').text($result.descuento_monto_html);
				$('#total_bruto').text($result.monto_pagar_html);

				$('#OrdenCompraDescuentoMonto').val($result.descuento_monto);
				$('#OrdenCompraDescuento').val($result.descuento_porcentaje);
				$('#OrdenCompraTotal').val($result.monto_pagar);

				$('.loader').css('display', 'none');

				$('#modalComentario').modal('show');

			})
			.fail(function() {

				$('.loader').css('display', 'none');

				noty({text: 'Ocurrió un error al calcular el monto. Intente nuevamente.', layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});
			
		},
		pagar: function(){

			$('.form-pay').validate({
				'rules' : {
					'data[OrdenCompra][moneda_id]' : {
						required : true
					}
				},
				'messages' : {
					'data[OrdenCompra][moneda_id]' : {
						required : 'Seleccione forma de pago'
					}
				}
			});

			$('.btn-calcular-precio').on('click', function(e){
				e.preventDefault();

				if ( $('.js-select-moneda').val() != '' ) {

					var id_oc 		= $('.form-pay').data('oc'),
						moneda_id 	= $('.js-select-moneda').val();

					$.ordenCompra.calcularMonto(id_oc, moneda_id);

				}else{
					$('#modalComentario').modal('hide');
					$('.form-pay').valid();
				}

			});

		},
		obtenerProveedor: function(){

			$('.loader').css('display', 'block');

			$.ajax({
				url: webroot + 'Proveedores/obtenerProveedor/' + $('.js-select-proveedor').val(),
				data: {param1: 'value1'},
			})
			.done(function(res) {

				var $result = $.parseJSON(res);

				if ($result.code == 200) {

					$('.js-rut-proveedor').val($result.data.rut_empresa);
					$('.js-razon-social-proveedor').val($result.data.nombre);
					$('.js-giro-proveedor').val($result.data.giro);
					$('.js-contacto-proveedor').val($result.data.nombre_encargado);
					$('.js-email-proveedor').val($result.data.email_contacto);
					$('.js-fono-proveedor').val($result.data.fono_contacto);
					$('.js-direccion-proveedor').val($result.data.direccion);

					$('.panel-body.hide').removeClass('hide');

				}else{
					noty({text: $result.message, layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
				}

				$('.loader').css('display', 'none');
			})
			.fail(function() {

				$('.loader').css('display', 'none');

				noty({text: 'Ocurrió un error al obtener la infomación. Intente nuevamente.', layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});
			

		},
		recepcion: function(){

			/*$(document).on('submit', '.js-recepcion', function(e){

				$('.loader').css('display', 'block');

				$.ajax({
					url: webroot + 'ordenCompras/validateReception/' + $(this).data('id') ,
					type: 'POST',
					data: $(this).serialize(),
				})
				.done(function(res) {

					var $result = $.parseJSON(res);

					console.log($result);
					$('.loader').css('display', 'none');
				})
				.fail(function() {
					console.log("error");
					$('.loader').css('display', 'none');
				});
				

				if ($(this).data('valid')) {
					$(this).submit();
				}else{
					e.preventDefault();
				}

			});*/

			$('.copy_tr').trigger('click');

		},
		init: function(){
			if ( $('.js-validate-oc').length ) {
				$.ordenCompra.validate();
				$.ordenCompra.clonar();
				$.ordenCompra.bind();
				$.ordenCompra.calcularTotales();
			}

			if ( $('.form-pay').length ) {
				$.ordenCompra.pagar();

				var opentext = $('.js-toggle-adjunto-oc').data('open');
				var closetext = $('.js-toggle-adjunto-oc').data('close');

				$('#collapseComments').on('hide.bs.collapse', function () {
					$('.js-toggle-adjunto-oc').text(closetext);
					$('#OrdenCompraAdjunto').val('');
					$('#OrdenCompraComentarioFinanza').val('');
				})

				$('#collapseComments').on('show.bs.collapse', function () {
					$('.js-toggle-adjunto-oc').text(opentext);
				})
			}

			if ( $('.js-select-proveedor').length ) {

				$('.js-select-proveedor').on('change', function(){
					if ( $(this).val() != '' ) {
						$.ordenCompra.obtenerProveedor();
					}
				});
			}

			if ( $('.js-recepcion').length ) {
				$.ordenCompra.recepcion();
			}

			if ( $('.js-buscar-producto').length ) {
				$.ordenCompra.buscar();
			}
		}
	}
});


$(document).ready(function(){
	$.ordenCompra.init();
	$.ordenCompra.calcularTotalesProducto();
	$.ordenCompra.calcularTotales();
});