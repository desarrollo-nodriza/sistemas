$.extend({
	ordenCompra: {
		validate: function(){

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

			$(document).on('change', '.js-tipo-entrega', function(){

				var $val = $(this).val(),
					$contexto = $(this).parents('table').eq(0),
					$receptor = $contexto.find('.js-receptor-informado'),
					$detalleReceptor = $contexto.find('.js-informacion-entrega');
				
				if ($val === 'retiro')
				{
					$receptor.parents('tr').eq(0).removeClass('hidden');
					$detalleReceptor.parents('tr').eq(0).removeClass('hidden');
					
					$receptor.rules("add", {
				        required: true,
				        messages: {
				        	required: 'Campo requerido'
				        }
				    });
				}
				else
				{
					$receptor.parents('tr').eq(0).addClass('hidden');
					$detalleReceptor.parents('tr').eq(0).addClass('hidden');
					$receptor.val('');
					$detalleReceptor.val('');

					$receptor.rules('remove', 'required');
				}

			});


			$('.js-validate-oc').validate({
				rules : {
					'data[OrdenCompra][razon_cancelada]': {
						required: true
					}
				}
			});

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

				if ($ths.hasClass('js-cantidad-producto')) {
					$ths.rules("add", {
				        number: true,
				        min: parseInt($ths.attr('min')),
				        messages: {
				        	number: 'Ingrese solo números',
				        	min: 'La cantidad mínima de compra para este item es de ' + $ths.attr('min')
				        }
				    });
				}

				if ($ths.hasClass('js-tipo-descuento')) {
						$ths.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Seleccione tipo de descuento'
					        }
					    });
					}


					if ($ths.hasClass('js-descuento-input')) {
						$ths.rules("add", {
					        required: true,
					        number : true,
					        min: 1,
					        messages: {
					        	required: 'Ingrese nombre del descuento',
					        	number: 'Solo números',
					        	min: 'Descuento debe ser mayor a 0'
					        }
					    });
					}

					if ($ths.hasClass('js-f-inicio')) {
						$ths.rules("add", {
							required: true,
					        date: true,
					        messages: {
					        	required: 'Seleccione fecha inicio',
					        	date: 'Formato de fecha no válido'
					        }
					    });
					}

					if ($ths.hasClass('js-f-final')) {
						$ths.rules("add", {
							required: true,
					        date: true,
					        messages: {
					        	required: 'Seleccione fecha final',
					        	date: 'Formato de fecha no válido'
					        }
					    });
					}


					if ($ths.hasClass('js-h-inicio')) {
						$ths.rules("add", {
							required: true,
					        time: true,
					        messages: {
					        	required: 'Seleccione hora inicio',
					        	time: 'Formato de hora no válido'
					        }
					    });
					}

					if ($ths.hasClass('js-h-final')) {
						$ths.rules("add", {
							required: true,
					        time: true,
					        messages: {
					        	required: 'Seleccione hora final',
					        	time: 'Formato de hora no válido'
					        }
					    });
					}

			});

		},
		clonarElemento: function($ths){

			var $contexto = $ths.parents('.panel').eq(0).find('.clone-tr').eq(0);
			var limite    = $ths.parents('.js-clone-wrapper').eq(0).data('filas');
			var filas     = $ths.parents('.js-clone-wrapper').eq(0).find('tbody').find('tr:not(.hidden)').length;

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

					if ($that.hasClass('js-id-producto') || $that.hasClass('js-codigo-producto') || $that.hasClass('js-descripcion-producto')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Campo requerido'
					        }
					    });
					}


					if ($that.hasClass('js-total-producto')) {
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

					if ($that.hasClass('js-cantidad-producto')) {
						/*$that.rules("add", {
					        number: true,
					        min: $that.attr('min'),
					        messages: {
					        	number: 'Ingrese solo números',
					        	min: 'La cantidad mínima de compra para este item es de ' + $that.attr('min')
					        }
					    });*/
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
					        number : true,
					        min: 1,
					        messages: {
					        	required: 'Ingrese nombre del descuento',
					        	number: 'Solo números',
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

					if ($that.hasClass('mask-money')) {
						$that.mask('000.000.000.000.000', {reverse: true});
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

				    if ($('.form-pay').length) {
				    	$.ordenCompra.calcularMonto();
				    	$.ordenCompra.calcularMontoPendiente();
				    }
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
						contexto.find('.js-modal-precio-especifico').html(ui.item.html_modal);
						contexto.find('.btn-modificar-precio-especifico').attr('data-target', '#modalPrecio' + ui.item.id );
						contexto.find('.js-descuento-valor').attr('id', 'descuento-' + ui.item.id);
				    	
						// Bloqueamos nuevos campos de precios
				    	contexto.find('.js-precio-producto').attr('readonly', true);
				    	contexto.find('.js-descuento-producto').attr('readonly', true);
				    	contexto.find('.js-total-producto').attr('readonly', true);
				    	contexto.find('.js-tipo-descuento-producto').attr('readonly', true);

				    	contexto.find('.js-cantidad-producto').rules("add", {
					        min: ui.item.minimo_compra,
					        messages: {
					        	min: 'La cantidad mínima de compra para este item es de ' + ui.item.minimo_compra 
					        }
					    });

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
		habilitar: function(){
			$(document).on('click', '.habilitar-fila', function(e){

				e.preventDefault();

				var $tr = $(this).parents('tr').eq(0);

				$tr.find('input[readonly="readonly"]').each(function(){
					$(this).removeAttr('readonly');
				});

			});
		},
		modificar_precio_lista: function(){
			$(document).on('click', '.btn-modificar-precio-especifico', function(e){
				e.preventDefault();				
			});
		},
		bind: function(){
			$(document).on('change', '.js-precio-producto', function(){
				$.ordenCompra.calcularTotalesProducto();
				$.ordenCompra.calcularTotales();
			})

			$(document).on('change', '.js-descuento-producto', function(){
				$(this).data('descuento', $(this).val());
				
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
		calcularMontoPagar: function($id_oc, $id_moneda, $contexto){

			$('.loader').addClass('show');

			// Se quitan los TR creados
			$contexto.find('.table').find('tbody > tr:not(.hidden)').remove();

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

				console.log($result);

				// Monto a pagar
				$('#total_bruto').html($result.monto_pagar_html);
				$('#OrdenCompraTotal').val($result.monto_pagar);

				// Descuentos
				$('#OrdenCompraDescuento').val($result.descuento_porcentaje);
				$('#OrdenCompraDescuentoMonto').val($result.descuento_monto);
				$('#descuento_aplicado').html($result.descuento_porcentaje);

				if ($result.comprobante_requerido) {
					$('.js-adjuntos').removeClass('hidden');
					$.ordenCompra.clonarElemento( $('.copy_tr').eq(0) );
				}else{
					$('.js-adjuntos').addClass('hidden');
				}

				if ($result.pago_adelantado) {
					$contexto.find('.table').data('filas', 1);
				}

				if ($result.agendar) {
					$contexto.find('.table').data('filas', 10);
				}

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
		calcularMonto: function( $id_oc, $id_moneda, $contexto ) {

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

				if (!$result.pagado) {

					// Se quitan los otros TR
					$contexto.siblings('tr:not(.hidden)').remove();

					$contexto.parents('table').eq(0).find('.copy_tr').attr('disabled', 'disabled');
					$contexto.find('.js-monto-pagado').rules("add", {
				        min: $result.monto_pagar,
				        max: $result.monto_pagar,
				        messages: {
				        	min: 'Monto mínimo a pagar: ' + $result.monto_pagar_html,
				        	max: 'Monto máximo a pagar: ' + $result.monto_pagar_html
				        }
				    });
				}else{
					$contexto.parents('table').eq(0).find('.copy_tr').removeAttr('disabled');
					$contexto.find('.js-monto-pagado').rules("remove", "min max");
				}


				if ($result.comprobante_requerido) {
					$contexto.find('.js-comprobante').rules("add", {
				        required: true,
				        messages: {
				        	required: 'Comprobante requerido'
				        }
				    });
				}else{
					$contexto.find('.js-comprobante').rules("remove", "required");
				}


				if ($result.pendiente) {
					$contexto.find('.js-monto-pagado').rules("add", {
				        max: 0,
				        min: 0,
				        messages: {
				        	required: 'Requerido',
				        	min: 'Monto debe ser igual a 0',
				        	max: 'Monto debe ser igual a 0'
				        }
				    });

					$contexto.find('.js-identificador-pago').rules("remove", "required");
					$contexto.find('.js-cuenta-pago').rules("remove", "required");
					$contexto.find('.js-monto-pagado').val(0);

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

				$('.form-pay').data('pagar', $result.monto_pagar);
				$('.form-pay').data('pendiente-pago', $result.monto_pagar);
				$('#descuento_aplicado').text($result.descuento_porcentaje);
				$('#descuento_monto_aplicado').text($result.descuento_monto_html);
				$('#total_bruto').text($result.monto_pagar_html);
				
				$.ordenCompra.calcularMontoPendiente();

				$('#OrdenCompraDescuentoMonto').val($result.descuento_monto);
				$('#OrdenCompraDescuento').val($result.descuento_porcentaje);
				$('#OrdenCompraTotal').val($result.monto_pagar);


				/*$('#modalComentario').modal('show');*/

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
		calularMontoPagar: function(){

			var pagar = $('.form-pay').data('pagar');
			var totalPagar  = 0;
			var totalPagado = 0;
			$('.js-select-medio-pago:not(disabled)').each(function(){

				totalPagado = totalPagado + $(this).parents('tr').eq(0).find('.js-monto-pagado');

			});

			if (totalPagado == totalPagar) {
				$('.copy_tr').attr('disabled', 'disabled');
			}else{
				$('.copy_tr').removeAttr('disabled');
			}

		},
		calcularMontoPendiente: function() {
			var total_pendiente 		= parseInt($('.form-pay').data('pendiente-pago')),
				total_pendiente_inicial = parseInt($('.form-pay').data('pendiente-pago-inicial')),
				total_pagado 			= parseInt($('.form-pay').data('total-pagado'));

			$('.js-monto-pagado:not(disabled)').each(function(){

				var pagado = parseInt($(this).val().replace('.', '')); // quitamos los puntos
				
				if (!isNaN(pagado)) {
					total_pendiente = (total_pendiente - pagado);
				}
				

			});
			
			//$('.form-pay').data('pendiente-pago', total_pendiente);
			$('#total_pendiente').html(total_pendiente);
			$('#total_pendiente').unmask();
			$('#total_pendiente').mask('000.000.000.000.000', {reverse: true});

		},
		pagar: function(){

			$('.form-pay').validate({
				rules : {
					'data[OrdenCompra][moneda_id]' : {
						required : true
					}
				},
				messages: {
					'data[OrdenCompra][moneda_id]' : {
						required : 'Seleccione medio de pago global'
					}
				}
			});

			/*$(document).on('change', '.js-select-medio-pago', function(){
				var $ths = $(this);

				$('.loader').addClass('show');

				if ( $ths.val() != '' ) {

					var id_oc 		= $('.form-pay').data('oc'),
						moneda_id 	= $ths.val(),
						contexto    = $ths.parents('tr').eq(0);

					$.ordenCompra.calcularMonto(id_oc, moneda_id, contexto);
				
				}

			});*/


			$(document).on('keyup', '.js-monto-pagado', function(){	

				$.ordenCompra.calcularMontoPendiente();			

				var pagado = parseInt($(this).val().replace('.', ''));
				var filas  = $(this).parents('table').eq(0).data('filas');
				var pagar  = parseInt($('.form-pay').data('pendiente-pago'));

				if ( pagado < pagar ) {
					// Permitimos agregar una fila más ya que el monto pagado es inferior al total a pagar
					$(this).parents('table').eq(0).data('filas', filas+1);

					// Al ser el monto menor se anulan los descuentos
					

				}else{
					$(this).parents('table').eq(0).data('filas', filas);
					//$(this).parents('tr').eq(0).siblings('tr:not(.hidden)').remove();
				}

			});


			$('.btn-calcular-precio').on('click', function(e){
				e.preventDefault();

				if ($('.form-pay').valid()) {

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
					$('.js-contacto-proveedor-input').val('Estimado/a ' + $result.data.nombre_encargado + ' se envía adjunto la orden de compra.');
					$('.js-email-proveedor').val($result.data.email_contacto);
					$('.js-fono-proveedor').val($result.data.fono_contacto);
					$('.js-direccion-proveedor').val($result.data.direccion);

					$('.panel-body.hide').removeClass('hide');
					$('.panel-footer.hide').removeClass('hide');

					page_content_onresize();

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
		modificar_precio_lista_especifico: function($form, $body, $id){

			$form.find('input, select').validate();

			if ( $form.find('.error').length ) {
				noty({text: 'Verifique los campos.', layout: 'topRight', type: 'error'});
				$('.loader').removeClass('show');
				return;
			}

			$.ajax({
				url: webroot + 'ventaDetalleProductos/modificar_precio_lista_especifico/' + $id,
				type: 'POST',
				data: $form.find('input, select').serialize(),
			})
			.done(function(res) {
				var $result = $.parseJSON(res);

				if ($result.code == 200)
				{	
					$body.html($result.message);
					$form.validate();

					$('.modal').modal('hide');

					noty({text: 'Precios creados con éxito', layout: 'topRight', type: 'success'});
					
					$.ordenCompra.obtenerDescuentoProducto($id);
					
				}else{
					noty({text: $result.message, layout: 'topRight', type: 'error'});
				}
				
			})
			.fail(function(e) {
				noty({text: 'Ocurrió un error al actualizar el producto. Intente hacerlo manualmente.', layout: 'topRight', type: 'error'});
			})
			.always(function(){
				
				$('.loader').removeClass('show');

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
				
			});
		},
		obtenerDescuentoProducto: function(id) {
			$.ajax({
				url: webroot + 'ventaDetalleProductos/obtener_descuento_producto/' + id,
				type: 'GET'
			})
			.done(function(res) {
				var $result = $.parseJSON(res);

				if ($result.code == 200)
				{	
					// Destrumios los tooltips
					$('[data-toggle="tooltip"]').tooltip('destroy');

					$('#descuento-' + id).attr('title', $result.data.nombre_descuento);
					$('#descuento-' + id).find('.js-descuento-producto').eq(0).val($result.data.descuento_producto);
					$('#descuento-' + id).find('.js-descuento-producto').eq(0).data('descuento', $result.data.total_descuento);

					// Reiniciar tooltip
					$('[data-toggle="tooltip"]').tooltip();

					// Calcular totales con el nuevo descuento
					$.ordenCompra.calcularTotalesProducto();
					$.ordenCompra.calcularTotales();

				}else{
					noty({text: $result.message, layout: 'topRight', type: 'error'});
				}
				
			})
			.fail(function(e) {
				noty({text: 'Ocurrió un error al actualizar el producto. Intente hacerlo manualmente.', layout: 'topRight', type: 'error'});
			})
			.always(function(){
				
				$('.loader').removeClass('show');

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
				
			});
		},
		obtener_dte_compra: function(contexto){ 
			
			if (contexto.find('.js-tipo-documento-compra').val() != 33) {
				return;
			}

			$('.loader').addClass('show');

			$.ajax({
				url: webroot + 'api/compras/documentos.json?token=' + $('.js-recepcion').data('token'),
				data: {
					folio : contexto.find('.js-folio-dte-compra').val(),
					rut_emisor : function(){
						var rut = $('#OrdenCompraRutProveedor').val();
						// Quitamos puntos
						rut = rut.replace(/\./g, '');
						// Quitamos DV
						rut = rut.slice(0, rut.length - 2);
						return rut;
					},
					tipo_documento : contexto.find('.js-tipo-documento-compra').val()
				},
			})
			.done(function(res) {
				if (res.dtes.length == 0) {
					contexto.find('.js-folio-dte-compra').val('');

					noty({text: 'El folio ingresado no es válido', layout: 'topRight', type: 'error'});
					
					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);

				}else if (res.dtes[0].DteCompra.monto_total != $('#total-bruto').data('value')){

					contexto.find('.js-dte-monto-compra').val(res.dtes[0].DteCompra.monto_total);

					noty({text: 'El monto total de la factura es diferente al monto total de la OC', layout: 'topRight', type: 'error'});
					
					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);

				}else{

					contexto.find('.js-dte-monto-compra').val(res.dtes[0].DteCompra.monto_total);

					noty({text: 'Factura obtenida exitosamente.', layout: 'topRight', type: 'success'});
					
					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);

				}
			})
			.fail(function(err) {
				console.log(err);
			})
			.always(function(){
				$('.loader').removeClass('show');
			});
			

		},
		agregar_oc: function(){
			var cargarOrdenes = true;
	
			obtener_ordenes();

			$('.ctm-datatables').addClass('hide');

			function obtener_ordenes($limit = 200, $offset = 0)
			{	
				$('.loader').css('display', 'block');

				if (cargarOrdenes) {
				
					data = {
						'limit' : $limit,
						'offset' : $offset
					};

					$.get( webroot + 'ordenCompras/obtener_ordenes_ajax/', data , function(respuesta){
						if (respuesta != '0') {
							$('#wrapper-ordenes tbody').append(respuesta);
							$offset = $offset + $limit;
							obtener_ordenes($limit, $offset);
						}else{
							cargarOrdenes = false;

							$('.loader').css('display', 'none');

							$('.ctm-datatables').removeClass('hide');

							$('.ctm-datatables').DataTable({
								paging: false,
						    	scrollY: 400,
								ordering: true,
							});

						}

					})
					.fail(function(){

						$('.loader').css('display', 'none');

						noty({text: 'Ocurrió un error al obtener las ventas. Intente nuevamente.', layout: 'topRight', type: 'error'});

						setTimeout(function(){
							$.noty.closeAll();
						}, 10000);
					});
				}

			}


			var $seleccionado = null;


			$(".mb-control-close").on("click",function(){

		       $(this).parents(".message-box").removeClass("open");
		       
		       $seleccionado.prop('checked', false);

		       return false;
		    });

		    $('#confirmar_manifiesto').on('click', function(){
		    	
		    	crearInput();

		    	$(this).parents(".message-box").removeClass("open");

		    });


			function crearInput()
			{	
				$("#OrdenCompraValidateForm").append('<input id="venta_' + $seleccionado.val() + '" type="hidden" name="Venta[][venta_id]"/ value="' + $seleccionado.val() + '">');
			}


			function levantarModal()
			{
				/* MESSAGE BOX */
		        var box = $('#modal_alertas');

		        	$('#mensajeModal').html('La venta id #' + $seleccionado.data('id') + ' Ya ha sido agregada a ' + $seleccionado.data('ordencompras') + ' OC');
		        
		            box.toggleClass("open");

		            var sound = box.data("sound");

		            if(sound === 'alert')
		                playAudio('alert');

		            if(sound === 'fail')
		                playAudio('fail');
		    
		        return false;
			
			}


			function evaluarModal()
			{	
				if ($seleccionado.data('ordencompras') > 0) {
					levantarModal();
				}else{
					crearInput();
				}
			}

			$('.create_input').each(function(){
				if ( $(this).prop('checked') ) {
					$(this).prop('checked', false);
					
					/*
					
					$seleccionado = $(this);

					evaluarModal();
					
					*/
				}else{

				}
			});
			
			$(document).on('change', '.create_input', function(){

				var checkout = document.getElementsByClassName('create_input');

				if ( $(this).prop('checked') ) {
					total_checked +=1;
					let i;
					if (total_checked == 1) {
						noty({text: "Para generar una orden de compra solo se deben seleccionar ventas de una misma bodega", layout: 'topRight', type: 'warning'});
						setTimeout(function(){
							$.noty.closeAll();
						}, 10000);	
					}
					for (i = 0; i < checkout.length; i++) {
						if(checkout[i].dataset.bodega != $(this).data( 'bodega')){
							checkout[i].disabled = true;
						}
					}
				
					$seleccionado = $(this);
					evaluarModal();
				}else{

					total_checked -=1;
					if (total_checked<=0) {

						let i;
						for (i = 0; i < checkout.length; i++) {
							if(checkout[i].dataset.bodega != $(this).data( 'bodega')){
								checkout[i].disabled = false;
							}
						}
						
					}
					$("#venta_" + $(this).val() ).remove();
					$seleccionado = null;
				}
			});
		},
		init: function(){
			if ( $('.js-validate-oc').length ) {
				$.ordenCompra.validate();
				$.ordenCompra.clonar();
				$.ordenCompra.bind();
				$.ordenCompra.calcularTotales();
			}

			if ( $('#OrdenCompraValidateForm').length ) {
				$.ordenCompra.agregar_oc();
			}

			if ( $('.form-pay').length ) {
				$.ordenCompra.pagar();

				$.ordenCompra.clonar();

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

				$('.js-select-moneda').on('change', function(){
					var id_oc 		= $('.form-pay').data('id'),
						moneda_id 	= $(this).val(),
						contexto    = $(this).parents('.panel-body').eq(0);

					$.ordenCompra.calcularMontoPagar(id_oc, moneda_id, contexto);
				});
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

			$.ordenCompra.habilitar();
			$.ordenCompra.modificar_precio_lista();

			$(document).on('click', '.js-guardar-precios-especificos', function(e){

				$('.loader').addClass('show');

				e.preventDefault();

				var $form = $(this).parents('.fake-form').eq(0);

				$.ordenCompra.modificar_precio_lista_especifico( $form, $form.find('.js-fake-form-body').eq(0), $form.data('id') );
			});


			$(document).on('focusout', '.js-folio-dte-compra', function(){
				$.ordenCompra.obtener_dte_compra($(this).parents('tr').eq(0));
			});

			$(document).on('change', '.js-tipo-documento-compra', function(){
				$(this).parents('tr').eq(0).find('.js-folio-dte-compra').val('');
				$(this).parents('tr').eq(0).find('.js-dte-monto-compra').val('');
			});
		}
	}
});

var total_checked = 0;

$(document).ready(function(){
	$.ordenCompra.init();
	
	if ($('.review-oc').length == 0) {
		$.ordenCompra.calcularTotalesProducto();
		$.ordenCompra.calcularTotales();
	}
});