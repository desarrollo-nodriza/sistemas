var cotizacion = function(){
	return {
		calcular_totales: function(){

			var total_neto = 0,
				total_descuento = 0,
				total_iva = 0,
				total_transporte = (typeof($('#CotizacionTransporte').val()) == 'undefined') ? 0 : parseFloat($('#CotizacionTransporte').val()),
				total_bruto = 0;

			$('.js-total-neto').each(function(){
				var $ths = $(this);
				total_neto = parseFloat(total_neto) + parseFloat($ths.val());
			});

			total_iva = parseFloat(total_neto) * parseFloat(0.19);

			total_descuento = parseFloat($('#CotizacionDescuento').val());  

			total_bruto = parseFloat(total_neto) + parseFloat(total_iva) - parseFloat(total_descuento) + parseFloat(total_transporte);

			$('#CotizacionTotalNeto').val(parseFloat(total_neto).toFixed(2));
			$('#CotizacionDescuento').val(parseFloat(total_descuento).toFixed(2));
			$('#CotizacionIva').val(parseFloat(total_iva).toFixed(2));
			$('#CotizacionTotalBruto').val(Math.round(total_bruto));

		},
		init: function(){

			$(document).on('change', '.js-calcular-totales', function(){
				cotizacion.calcular_totales();
			});

			$(document).on('change', '.js-cantidad-item', function(){

				var contexto = $(this).parents('tr').eq(0),
					neto     = contexto.find('.js-neto-item').val();

				var total_neto = parseFloat(neto) * parseFloat($(this).val());

				contexto.find('.js-total-neto').val( total_neto );

				cotizacion.calcular_totales();
			});


			$(document).on('change', '.js-neto-item', function(){

				var contexto = $(this).parents('tr').eq(0),
					cantidad = contexto.find('.js-cantidad-item').val();

				var total_neto = parseFloat(cantidad) * parseFloat($(this).val());

				contexto.find('.js-total-neto').val( total_neto );

				cotizacion.calcular_totales();
			});

			$('.block').keypress(function(e){
				e.preventDefault();
			});
		}	
	};
}();


var prospecto = function(){
	return {
		seleccionar_direccion: function($id_direccion){

			prospecto.deseleccionar_direcciones();

			var item = $('#ProspectoDirecciones').find('[data-id="'+$id_direccion+'"]').eq(0);

			item.removeClass('panel-default');
	      	item.addClass('panel-success');

			item.find('.address-select .fa-plus').addClass('hidden');
			item.find('.address-select .fa-check').removeClass('hidden');

	      	$('#ProspectoDireccionId').val($id_direccion);

		},
		deseleccionar_direcciones: function(){

			$('.js-address-block').each(function(){
				$(this).removeClass('panel-success');
				$(this).addClass('panel-default');
				$(this).find('.address-select .fa-plus').removeClass('hidden');
				$(this).find('.address-select .fa-check').addClass('hidden');
				
			});

			$('#ProspectoDireccionId').val('');
			$('#DireccionAdminAddForm').data('id', '');
		},
		editar_direccion: function(res){
			$('#success-mensaje-direccion').html('200: Dirección editada exitosamente.');
			$('#success-mensaje-direccion').parents('.alert').eq(0).removeClass('hidden');

	      	// Mostramos la nueva dirección
	      	$('#ProspectoDirecciones').find('[data-id="'+res.response.direccion.id+'"]').eq(0).replaceWith(res.response.direccion.block);

	      	prospecto.seleccionar_direccion(res.response.direccion.id);

			setTimeout(function(){
				$('#modalCrearDireccion').modal('hide');
			}, 1500);
		},
		crear_direccion: function(res){

			$('#success-mensaje-direccion').html('200: Dirección creada exitosamente.');
			$('#success-mensaje-direccion').parents('.alert').eq(0).removeClass('hidden');

			var html_direccion = '';
	      	
	      	html_direccion += '<div class="col-xs-12 col-md-6">' + res.response.direccion.block + '</div>';
	      	
	      	$('#ProspectoDirecciones').find('.panel-body').append(html_direccion);

	      	prospecto.seleccionar_direccion(res.response.direccion.id);

			setTimeout(function(){
				$('#modalCrearDireccion').modal('hide');
			}, 1500);
		},
		crear_cliente: function(res) {	
			
			$('#success-mensaje-cliente').html('200: Cliente creado exitosamente.');
			$('#success-mensaje-cliente').parents('.alert').eq(0).removeClass('hidden');

			setTimeout(function(){
				$('#modalCrearCliente').modal('hide');
			}, 1500);
			
		},
		calcular_totales: function(){

		},
		obtener_cliente: function($id){

			$.app.loader.mostrar();

			var $token = $('.js-prospecto').data('token');

			$.ajax({
				url: webroot + 'api/clientes/view/' + $id + '.json?token=' + $token
			})
			.done(function(res) {
				
				$('#obtener_cliente').val(res.cliente.email);

				var html_direccion = '';

		      	for (var i = 0; i < res.direccion.length; i++) {
		      		html_direccion += '<div class="col-xs-12 col-md-6">' + res.direccion[i].block + '</div>';
		      	}

		      	if (res.direccion.length == 0) {
		      		html_direccion += '<p>El cliente seleccionado no tiene dirección, <button data-toggle="modal" data-target="#modalCrearDireccion" class="btn btn-success btn-xs">Creela aquí</button></p>';
		      		$('#DireccionVentaClienteId').val(res.cliente.id);
		      	}

		      	$('#ProspectoDirecciones').find('.panel-body').html(html_direccion);

		      	prospecto.seleccionar_direccion($('#ProspectoDireccionId').val());

			})
			.fail(function(err) {

				noty({text: err.responseJSON.code + ': ' + err.responseJSON.message, layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			})
			.always(function() {
				$.app.loader.ocultar();
			});
			

		},
		autocompletar_clientes: function(){

			var $token = $('.js-prospecto').data('token');

		    $( "#obtener_cliente" ).autocomplete({
		      source: function( request, response ) {
		        $.ajax( {
		          url: webroot + 'api/clientes.json',
		          data: {
		          	token: $token,
		          	limit: 40,
		            email: request.term
		          },
		          success: function( data ) {

		          	var $result = [];
		          	
		          	for (var i = 0; i < data.clientes.length; i++) {

		          		$result[i] = {
		          			label :  data.clientes[i].VentaCliente.nombre + ' ' + data.clientes[i].VentaCliente.apellido + ' <' + data.clientes[i].VentaCliente.email + '>',
		          			value :  data.clientes[i].VentaCliente.email,
		          			id :  data.clientes[i].VentaCliente.id,
		          			todo : data.clientes[i]
		          		}
		          	}

		            response( $result );
		          }
		        } );
		      },
		      minLength: 3,
		      select: function( event, ui ) {

		      	$('#ProspectoVentaClienteId').val(ui.item.id);

		      	var html_direccion = '';

		      	for (var i = 0; i < ui.item.todo.Direccion.length; i++) {
		      		html_direccion += '<div class="col-xs-12 col-md-6">' + ui.item.todo.Direccion[i].block + '</div>';
		      	}

		      	if (ui.item.todo.Direccion.length == 0) {
		      		html_direccion += '<p>El cliente seleccionado no tiene dirección, <button data-toggle="modal" data-target="#modalCrearDireccion" class="btn btn-success btn-xs">Creela aquí</button></p>';
		      		$('#DireccionVentaClienteId').val(ui.item.id);
		      	}

		      	$('#ProspectoDirecciones').find('.panel-body').html(html_direccion);

		      	prospecto.calcular_totales();

		      }	
		    } );
			
		},
		autocompletar_productos: function(){
			
			var $token = $('.js-prospecto').data('token');

			$('#obtener_productos').autocomplete({
		      source: function( request, response ) {
		        $.ajax( {
		          url: webroot + 'api/productos.json',
		          data: {
		          	token: $token,
		          	limit: 10,
		            s: request.term,
		            tr: 1,
		            external: 1
		          },
		          success: function( data ) {
		            var $result = [];
		          	
		          	for (var i = 0; i < data.productos.length; i++) {

		          		$result[i] = {
		          			label :  data.productos[i].VentaDetalleProducto.nombre,
		          			value :  data.productos[i].VentaDetalleProducto.nombre,
		          			id :  data.productos[i].VentaDetalleProducto.id,
		          			todo : data.productos[i]
		          		}
		          	}

		            response( $result );
		          }
		        } );
		      },
		      minLength: 3,
		      select: function( event, ui ) {
		      	
		      	if ($('#ProspectoProductos').find('[data-id="'+ui.item.id+'"]').length) {

		      		noty({text: 'El producto ' + ui.item.label + ' ya fue agregado.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);

					return false;
		      	}

		      	$('#ProspectoProductos').append(ui.item.todo.VentaDetalleProducto.tr_prospecto);

		      }	
		    });
		},
		init: function(){

			if ($('.js-prospecto').length) {
				prospecto.autocompletar_clientes();
				prospecto.autocompletar_productos();
			}

			if ( $('#ProspectoVentaClienteId').length && $('#ProspectoVentaClienteId').val().length > 0) {
				prospecto.obtener_cliente($('#ProspectoVentaClienteId').val());
			}

			$(document).on('submit', '.js-prospecto', function(e){
				if ($('#ProspectoVentaClienteId').val() == '') {

					noty({text: 'Seleccione o cree un cliente.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);

					e.preventDefault();
				}

				if ($('#ProspectoDireccionId').val() == '') {

					noty({text: 'Seleccione una dirección.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
					
					e.preventDefault();
				}

				if ($('#ProspectoProductos > tr').length == 0) {

					noty({text: 'Agrega al menos 1 producto.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);

					e.preventDefault();
				}

				
			});

			$(document).on('submit', '#VentaClienteAdminAddForm', function(e){
				e.preventDefault();
				var form = $(this);
				if (!form.valid()) {
					return false;
				}

				clientes.add(
					function(res){
						prospecto.crear_cliente(res);
					},
					function(err){
						$('#error-mensaje-cliente').html(err.responseJSON.code + ': ' + err.responseJSON.message);
						$('#error-mensaje-cliente').parents('.alert').eq(0).removeClass('hidden');
					}
				);

			});	

			$(document).on('submit', '#DireccionAdminAddForm', function(e){
				e.preventDefault();
				var form = $(this);

				if (!form.valid()) {
					return false;
				}

				if (form.data('id') == '') {

					// se crea una nueva direccion
					direcciones.add(
						function(res){
							prospecto.crear_direccion(res);
						},
						function(err){
							$('#error-mensaje-direccion').html(err.responseJSON.code + ': ' + err.responseJSON.message);
							$('#error-mensaje-direccion').parents('.alert').eq(0).removeClass('hidden');
						}
					);
				}else{

					// Editamos la direccion seleccionada
					direcciones.edit(
						form.data('id'),
						function(res){
							prospecto.editar_direccion(res);
						},
						function(err){
							$('#error-mensaje-direccion').html(err.responseJSON.code + ': ' + err.responseJSON.message);
							$('#error-mensaje-direccion').parents('.alert').eq(0).removeClass('hidden');
						}
					);
				}

			});	

			$(document).on('click', '.address-edit', function(e){

				e.preventDefault();

				var id_direccion = $(this).parents('.js-address-block').eq(0).data('id');

				$('#DireccionAdminAddForm').data('id', id_direccion);

				direcciones.view(
					id_direccion, 
					function(res){

						$('#modalCrearDireccion').modal('show');

						$('#DireccionAlias').val(res.direccion.Direccion.alias);
						$('#DireccionCalle').val(res.direccion.Direccion.calle);
						$('#DireccionNumero').val(res.direccion.Direccion.numero);
						$('#DireccionVentaClienteId').val(res.direccion.Direccion.venta_cliente_id);
						$('#DireccionComunaId').val(res.direccion.Direccion.comuna_id);
						$('#DireccionDepto').val(res.direccion.Direccion.depto);

						$('#DireccionComunaId').trigger("change");

					}, 
					function(err){

						noty({text: err.responseJSON.code + ': ' + err.responseJSON.message, layout: 'topRight', type: 'error'});

						setTimeout(function(){
							$.noty.closeAll();
						}, 10000);
					}
				);

			});

			// Si el email cambia se limpia todo
			$(document).on('change', '#ProspectoDireccionId', function(){
				$('#ProspectoDireccionId').val('');
				$('#DireccionVentaClienteId').val('');
				$('#ProspectoDirecciones').find('.panel-body').html('');
			});

			$(document).on('click', '.js-address-block .address-select', function(){
				
				prospecto.seleccionar_direccion($(this).data('id'));
								
			});

			if ( $('#ProspectoDireccionId').val() != '' ) {
				// Se carga la info de direccion
				
				
			}

			$(document).on('click', '.js-a-cotizacion', function(e){
				e.preventDefault();

				$('#ProspectoCotizacion').val(1);
				$('.js-prospecto').submit();
			});

			$(document).on('click', '.remove_tr_prospecto', function(e){
				e.preventDefault();

				$(this).parents('tr').eq(0).remove();

				prospecto.calcular_totales();

			});

		}
	};
}();

$(document).ready(function(){
	cotizacion.init();
	prospecto.init();
});