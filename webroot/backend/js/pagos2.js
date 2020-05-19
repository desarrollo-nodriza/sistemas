var pagos = function(){
	return {
		add: function(token, data, successCallback, failCallback){

			$.app.loader.mostrar();

			if(typeof successCallback != 'function' || typeof successCallback != 'function'){
		      $.app.loader.ocultar();
		      console.log('Callback functions not defined');
		    }

			$.ajax({
				url: webroot + 'api/pagoproveedor/add.json?token=' + token,
				type: 'POST',
				data: data,
				dataType: "JSON",
				processData: false,
        		contentType: false,
			})
			.done(function(res){
				successCallback.call(this, res);
			})
			.fail(function(error, textStatus, message) {

				failCallback(error);

			})
			.always(function(){
				$.app.loader.ocultar();
			});

		},
		gtMonth: function(mes){

			var meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
			return meses[mes];

		},
		init: function(){
			if ( $('#PagoAdminProcesarForm').length ) {

				var token = $('#PagoAccessToken').val(),
					total_pagar = parseFloat($('#PagoTotalPagar').val()),
					total_pagado = parseFloat($('#monto-pagado-text').data('pagado'));

				$('#PagoMontoPagado').rules("add", {
			        max: total_pagar,
			        messages: {
			        	max: 'Monto a pagar debe ser igual o menor a $' + total_pagar
			        }
			    });

				$(document).on('submit', '#PagoAdminProcesarForm', function(e){
					e.preventDefault();
					
					var form = $(this);

					if (!form.valid()) {
						return false;
					}

					// Con serialize no funciona
					var dataForm = new FormData(this);
	
					pagos.add(token, dataForm,
						function(res){

							total_pagar = total_pagar - parseFloat(res.response.pago.Pago.monto_pagado);
							total_pagado = total_pagado + parseFloat(res.response.pago.Pago.monto_pagado);

							// Seteamos el nuevo monto a pagar en el input
							$('#PagoTotalPagar').val(total_pagar);
							$('#PagoMontoPagado').rules("add", {
						        max: total_pagar,
						        min: 1,
						        messages: {
						        	min: 'Monto debe ser mayor a 0',
						        	max: 'Monto a pagar debe ser igual o menor a $' + total_pagar
						        }
						    });

							// Seteamos el nuevo monto a pagar
							$('.total-a-pagar').unmask();
							$('.total-a-pagar').text(total_pagar).mask('000.000.000.000.000', {reverse: true});

							// Seteamos el total pagado
							$('#monto-pagado-text').unmask();
							$('#monto-pagado-text').text(total_pagado).mask('000.000.000.000.000', {reverse: true}).prepend('$');
							$('#monto-pagado-text').data('pagado', total_pagado);

							// Quitamos la alerta de no pagos
							$('#alerta-no-pagos').remove();

							// Agregamos el pago creado
							$('#pago-wrapper').append(res.response.pago.Pago.block);
							
							$('#success-mensaje-pago').html('200: Pago creado exitosamente.');
							$('#success-mensaje-pago').parents('.alert').eq(0).removeClass('hidden');

							// Se limpian los campos
							$('#PagoMonedaId').val('');
							$('#PagoCuentaBancariaId').val('');
							$('#PagoIdentificador').val('');
							$('#PagoMontoPagado').val('');
							$('#PagoFechaPago').val('');
							$('#PagoAdjunto').val('');
							$('#PagoPagado').prop('checked', false);

							// Marcamos facturas pagadas
							for (var i = res.response.pago.OrdenCompraFactura.length - 1; i >= 0; i--) {
								if (res.response.pago.OrdenCompraFactura[i].pagada) {
									$('#facturas').find('tr[data-id="' + res.response.pago.OrdenCompraFactura[i]['id'] + '"]').find('i').removeClass('fa-times-circle text-danger');
									$('#facturas').find('tr[data-id="' + res.response.pago.OrdenCompraFactura[i].id + '"]').find('i').addClass('fa-check-circle text-success');
								}
							}

							setTimeout(function(){
								$('#modalCrearPago').modal('hide');
								$('#success-mensaje-pago').html('');
								$('#error-mensaje-pago').html('');
								$('#success-mensaje-pago').parents('.alert').eq(0).addClass('hidden');
							}, 1500);				

						},
						function(err){
							$('#error-mensaje-pago').html(err.responseJSON.code + ': ' + err.responseJSON.message);
							$('#error-mensaje-pago').parents('.alert').eq(0).removeClass('hidden');
						}
					);

				});


				// Validamos la moneda
				$(document).on('change', '#PagoMonedaId', function(){

					// Se limpian los campos
					$('#PagoCuentaBancariaId').val('');
					$('#PagoIdentificador').val('');
					$('#PagoMontoPagado').val('');
					$('#PagoFechaPago').val('');
					$('#PagoAdjunto').val('');
					$('#PagoPagado').prop('checked', false);


					// Obtenermos la monedas y validamos segun corresponda
					monedas.view(token, $(this).val(), function(res){

						var $result = res.moneda.Moneda;

						var fecha = new Date();
						var dia = fecha.getDate();

						if ($result.comprobante_requerido) {
							$('#PagoAdjunto').rules("add", {
						        required: true,
						        messages: {
						        	required: 'Comprobante requerido'
						        }
						    });
						}else{
							$('#PagoAdjunto').rules("remove", "required");
						}

						if ($result.tipo === 'pagar') {

							$('#PagoFechaPago').rules("add", {
						        required: true,
						        messages: {
						        	required: 'Ingrese la fecha de hoy'
						        }
						    });
							if (dia<10) {
								$('#PagoFechaPago').val(fecha.getFullYear() +'-'+ pagos.gtMonth(fecha.getMonth())+'-0'+ dia);
							}else{
								$('#PagoFechaPago').val(fecha.getFullYear() +'-'+ pagos.gtMonth(fecha.getMonth())+'-'+ dia);
							}
						    $('#PagoPagado').prop('checked', true);
						}

						if ($result.tipo === 'agendar') {
							$('#PagoFechaPago').rules("add", {
						        required: true,
						        messages: {
						        	required: 'Ingrese la fecha para agendar el pago'
						        }
						    });

						   	$('#PagoPagado').prop('checked', false);

						}

					}, 
					function(err){
						console.log(err);
					})

				});

			}
		}	
	};
}();


$(document).ready(function(){
	pagos.init();
});