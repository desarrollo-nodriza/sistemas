$.extend({
	app: {
		seleccionDireccion: {
			init: function() {
				if ($('.js-direccion-utilizar').length) {
					$.app.seleccionDireccion.bind();
				}
			},
			bind: function() {
	
				$('input[type="submit"]').on('click', function(event){
					if ($('.js-direccion-utilizar:checked').length > 1 ) {
						event.preventDefault();
						noty({text: 'Seleccione solo una dirección para cotizar', layout: 'topRight', type: 'error'});
					}else{
						if ($(this).hasClass('js-a-cotizacion')) {
							$('.js-input-a-cotizacion').val('1');
						}else{
							$('.js-input-a-cotizacion').val('0');
						}
						var direccionSeleccion = $('.js-direccion-utilizar:checked').parents('.js-clon-clonada').eq(0).find('.js-direccion-id');
						$('#ProspectoIdAddress').val(direccionSeleccion.val());
					}
				});
				
			}
		},
		clienteExistente: function(){
			if ($('.js-cliente-existente').length) {
				// Cargamos la información del cliente
				//$.app.loader.mostrar();
				//$.app.autocompletarBuscar.obtenerDatosCliente($('.js-tienda').val(), $('.js-cliente-existente').val());

				// Seleccionamos la dirección
				var direccionseleccionada = $('.js-address-exist').val();

				$('.js-direccion-id').each(function(){
					console.info('Existe direccion seleccionada');
					var $este = $(this),
						$direccion = $este.parents('.js-clon-contenedor').find('js-direccion-utilizar');
					if ($este.val() == direccionseleccionada) {
						console.info('Coinciden direcciones');
						$direccion.prop('checked', true);
					}
				});
			}
			
		},
		dataValor: {
			init: function(){
				if ($('[data-value]').length) {
					$.app.dataValor.bind();
				}
			},
			bind: function(){
				console.info('Data valor activado');
				$('[data-value]').each(function(){
					var $este = $(this);
					$este.val($este.data('value'));
				});
			}
		},
		validarProspecto: {
			init: function(){
				if ($('#ProspectoAdminAddForm').length) {
					$.app.validarProspecto.bind();
				}
			},
			bind: function(){
				var validator = $("#ProspectoAdminAddForm").validate({
                    rules: {
                        'data[Prospecto][nombre]': {
                            required: true
                        },
                        'data[Prospecto][descripcion]': {
                            required: true
                        }
                    },
                    messages: {
                    	'data[Prospecto][nombre]': {
                            required: 'Ingrese un nombre al prospecto'
                        },
                        'data[Prospecto][descripcion]': {
                            required: 'Ingrese una descripción al prospecto'
                        },
                        'data[Cliente][1][email]': {
                        	email: 'Ingrese un email válido'
                        },
                        'data[Cliente][1][siret]': {
                        	required: 'Ingrese el rut de la empresa'
                        },
                        'input[type="tel"]': {
                        	minlength : "Ingrese un teléfono de 9 dígitos",
                        	maxlength : "Ingrese un teléfono de 9 dígitos"
                        },
                        'input[type="number"]': {
                        	min : "Mínimo es 0",
                        	max : "Máximo 100"
                        }
                    }
                });

                // Semaforo de rut empresa
                $('.js-tipo-cliente').on('change', function(){
                	$this 		= $(this),
                	campoRut 	= $this.parents('table').eq(0).find('.js-rut-empresa');

                	if ($this.val() == 2) {
                		campoRut.attr('required', 'required');
                	}else{
                		campoRut.removeAttr('required');
                	}

                });

			}	
		},
		rutChileno: {
			init: function() {
				if ($('.rut').length) {
					$.app.rutChileno.bind()
				}
			},
			bind: function(){		

				$('.rut').rut();

				$(document).on('rutInvalido', '.rut', function(e) {
					if ( $(this).val() != '' ) {
						if ($(this).hasClass('valid')) {
					    	$(this).removeClass('valid');
					    }
					    $(this).addClass('error');
					    if ( $(this).parent().find('label').length == 0 ) {
					    	$(this).parent().append('<label id="' + $(this).attr('id') + '-error" class="rut-error" for="' + $(this).attr('id') + '">Ingrese un rut válido</label>');
					    }
					}else{
						$(this).parent().find('label').remove();
						$(this).removeClass('valid');
						$(this).removeClass('error');
					}
				});

				$(document).on('rutValido', '.rut', function(e, rut, dv) {
				    if ($(this).hasClass('error')) {
				    	$(this).removeClass('error');
				    }
				    if ( $(this).parent().find('label').length > 0 ) {
				    	$(this).parent().find('label').remove();
				    }

				    $(this).addClass('valid');	
					
				});
			}
		},
		mascara: {
			init: function(){
				if ($('input[class^="mascara_"]').length) {
					$.app.mascara.bind();
				}
			},
			bind: function(){
				$('.mascara_fono').mask('99999-9999', {clearIfNotMatch: true, placeholder: "xxxxx-xxxx"});
			}
		},
		toggle: {
			init: function() {
				if ($('.btn-toggle').length) {
					$.app.toggle.bind();
				}
			},
			bind: function() {
				$(document).on('click', '.btn-toggle', function(){
					$(this).find('.fa').toggle();
				});
			}
		},
		loader: {
			mostrar: function(){
				$('.loader').css('display', 'block');
			},
			ocultar: function(){
				$('.loader').css('display', 'none');
			}
		},
		historialCompra: {
			init: function() {
				if ($('.ver-pedidos-cliente').length) {
					$.app.historialCompra.bind();
				}
			},
			modal: function(titulo, cuerpo){
				var $modal 		= $('#modalVacio');
				$modal.find('.modal-title').html(titulo);
				$modal.find('.modal-body').html(cuerpo);
				$modal.modal('show');
				$.app.loader.ocultar();
			},
			ver: function(tienda, cliente) {
				console.info('Obtener historial de compra para cliente ID: ' + cliente);
				$.get( webroot + 'prospectos/historial_pedidos/' + tienda + '/' + cliente, function(respuesta){
					
					// Lanzamos el modal con el html de la rspuesta
					$.app.historialCompra.modal('Historial de pedidos del cliente', respuesta);
				})
				.fail(function(){
					$.app.loader.ocultar();
					noty({text: 'Ocurrió un error al obtener el historial de compra del cliente. Intente nuevamente.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
				});
			},
			bind: function(){
				$(document).on('click', '.ver-pedidos-cliente', function(event) {
					$.app.loader.mostrar();
					event.preventDefault();

					var tienda 		= $('.js-tienda').val(),
						cliente 	= $('#Cliente1IdCustomer').val(); 
					
					$.app.historialCompra.ver(tienda, cliente);
				});
			}
		},
		obtenerRegiones: function(tienda, pais, contexto, region){
			console.info('Obtener región ejecutada');
			$.get( webroot + 'regiones/regiones_por_tienda_pais/' + tienda + '/' + pais, function(respuesta){
				contexto.find('.js-region').html(respuesta);
				if (region > 0) {
					contexto.find('.js-region').val(region);
				}
				
				$.app.loader.ocultar();
			})
			.fail(function(){
				$.app.loader.ocultar();

				noty({text: 'Ocurrió un error al obtener la información. Intente nuevamente.', layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});
		},
		resetearTablas: function(){

			$(document).find('.js-clon-clonada').remove();

			// Clonar tabla
			$('.js-clon-contenedor').each(function(){

				var $this 			= $(this),
					tablaInicial 	= $this.find('.js-clon-base'),
					tablaClonada 	= tablaInicial.clone();

				tablaClonada.removeClass('js-clon-base');
				tablaClonada.removeClass('hidden');
				tablaClonada.addClass('js-clon-clonada');
				tablaClonada.find('.js-pais').addClass('js-pais-valida');
				tablaClonada.find('input, select, textarea').removeAttr('disabled');
				tablaClonada.find('.js-direccion-utilizar').attr('checked', 'checked');

				$('#accordion').append(tablaClonada);

				$.app.clonarTabla.reindexar();
			});
		},
		obtenerPaises: {
			init: function(){
				if ($('.js-pais').length) {
					var tienda = $('.js-tienda').val();
					if (typeof(tienda) != 'undefined') {
						$.app.obtenerPaises.bind(tienda);
					}
				}
			},
			bind: function(tienda) {
				$.get( webroot + 'paises/paises_por_tienda/' + tienda, function(respuesta){
					$('.js-pais').html(respuesta);
					$.app.loader.ocultar();
				})
				.fail(function(){
					$.app.loader.ocultar();

					noty({text: 'Ocurrió un error al obtener la información. Intente nuevamente.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
				});

				$(document).on('change', '.js-pais', function(e, data) {		
					$.app.loader.mostrar();
					var pais 		= $(this).val(),
						contexto 	= $(this).parents('table').eq(0),
						region 		= 0;
					console.log(data)
					//Sí data es un objeto se carga el pais y la región 
					if(typeof(data) == 'object') {
						console.info('Ejecutado desde nuevo campo pais');
						$.app.obtenerRegiones(tienda, data.valorPais, contexto, data.valorRegion);
					}else{
						console.info('Ejecutado desde campo pais existente');
						$.app.obtenerRegiones(tienda, pais, contexto, region);
					}
				});
			}
		},
		autocompletarBuscar: {
			init: function() {
				if ( $('.input-clientes-buscar').length > 0 ) {
					$.app.autocompletarBuscar.clientesBuscar();
				}

				if ( $('.input-productos-buscar').length > 0 ) {
					$.app.autocompletarBuscar.buscar();
				}

				if ( $('.input-productos-buscar-meli').length > 0 ) {
					$.app.autocompletarBuscar.buscar();
				}
			},	
			obtenerDatosCliente: function( tienda, idCliente){
				/**
				 * Reseteamos las tablas de direcciones
				 */
				$.app.resetearTablas();

				$.get( webroot + 'clientes/cliente_por_tienda/' + tienda + '/' + idCliente, function(respuesta){
					var cliente 	= $.parseJSON(respuesta),	
						direcciones = cliente.Clientedireccion;
					$('#ProspectoIdCustomer').val(cliente.Cliente.id_customer);
					$('#Cliente1IdCustomer').val(cliente.Cliente.id_customer);
					$('#Cliente1Email').val(cliente.Cliente.email);
					$('#Cliente1Ape').val(cliente.Cliente.ape);
					$('#Cliente1Siret').val(cliente.Cliente.siret);
					$('#Cliente1IdGender').val(cliente.Cliente.id_gender);
					$('#Cliente1Firstname').val(cliente.Cliente.firstname);
					$('#Cliente1Lastname').val(cliente.Cliente.lastname);

					// Ejecutamos el visualizador de pedidos
					if ( $('.ver-pedidos-cliente').length == 0 ) {
						$('#Cliente1Email').parent().append('<button class="btn btn-xs btn-primary ver-pedidos-cliente" style="margin-top: 5px;">Historial de pedidos</button>');
					}

					$.app.historialCompra.init();
					
					if (typeof(direcciones) == 'object') {
						console.info('Tiene ' + direcciones.length + ' dirección/es');
						for (var itr = 0; itr <= direcciones.length - 1; itr++) {
							if (direcciones.length > 1 && itr > 0) {
								console.info('Se crea tabla de direcciones nueva');
								$('.js-clon-agregar').trigger('click');
							}

							console.info('Dirección ' + (itr + 1) + ' Id: ' + direcciones[itr].id_address );

							// Se completan los campos de direcciones (itr + 1) para seleccionar el campo que no está oculto
							$('.js-direccion-id').eq(itr + 1).val(direcciones[itr].id_address);
							$('.js-direccion-alias').eq(itr + 1).val(direcciones[itr].alias);
							$('.js-direccion-empresa').eq(itr + 1).val(direcciones[itr].company);
							$('.js-direccion-empresa-rut').eq(itr + 1).val(direcciones[itr].vat_number);
							$('.js-direccion-nombre').eq(itr + 1).val(direcciones[itr].firstname);
							$('.js-direccion-apellido').eq(itr + 1).val(direcciones[itr].lastname);
							$('.js-direccion-direccion1').eq(itr + 1).val(direcciones[itr].address1);
							$('.js-direccion-direccion2').eq(itr + 1).val(direcciones[itr].address2);
							$('.js-direccion-postal').eq(itr + 1).val(direcciones[itr].postcode);
							$('.js-direccion-ciudad').eq(itr + 1).val(direcciones[itr].city);
							
							$('.js-direccion-pais').eq(itr + 1).val(direcciones[itr].id_country);

							// Pasamos el id de pais y el id de región al enevto change para que se seleccionen automáticamente
							$('.js-direccion-pais').eq(itr + 1).trigger('change', [{valorPais : direcciones[itr].id_country, valorRegion : direcciones[itr].id_state}] );
							
							$('.js-direccion-region').eq(itr + 1).val(direcciones[itr].id_state);
							$('.js-direccion-fono').eq(itr + 1).val(direcciones[itr].phone);
							$('.js-direccion-celular').eq(itr + 1).val(direcciones[itr].phone_mobile);
							$('.js-direccion-otro').eq(itr + 1).val(direcciones[itr].other);
						}
					}

					$.app.loader.ocultar();

					noty({text: 'Se completaron todos los campos del cliente.', layout: 'topRight', type: 'success'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
		     	})
		     	.fail(function(){
		     		$.app.loader.ocultar();

					noty({text: 'Ocurrió un error al obtener el cliente. Intente nuevamente.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
		     	});

			},
			buscar: function(){
				var todo = '';

				$('.input-clientes-buscar').each(function(){
					var $esto 	= $(this),
						tienda 	= $('.js-tienda').val();
					
					if (typeof(tienda) == 'undefined') {
						alert('Seleccione una tienda');
					}
					
					$esto.autocomplete({
					   	source: function(request, response) {
					      	$.get( webroot + 'clientes/clientes_por_tienda/' + tienda + '/' + request.term, function(respuesta){
								response( $.parseJSON(respuesta) );
					      	})
					      	.fail(function(){
								$.app.loader.ocultar();

								noty({text: 'Ocurrió un error al obtener la información. Intente nuevamente.', layout: 'topRight', type: 'error'});

								setTimeout(function(){
									$.noty.closeAll();
								}, 10000);
							});
					    },
					    select: function( event, ui ) {
					        console.log("Seleccionado: " + ui.item.value + " id " + ui.item.id);
					        $.app.loader.mostrar();
					        $.app.autocompletarBuscar.obtenerDatosCliente(tienda, ui.item.id);

					    },
					    open: function(event, ui) {
		                    var autocomplete = $(".ui-autocomplete:visible");
		                    var oldTop = autocomplete.offset().top;
		                    var width  = $esto.width();
		                    var newTop = oldTop - $esto.height() + 25;

		                    autocomplete.css("top", newTop);
		                    autocomplete.css("width", width);
		                    autocomplete.css("position", 'absolute');
		                }
					});
				});

				$('.input-productos-buscar').each(function(){
					var $esto 	= $(this),
						tienda 	= $('.js-tienda').val();
					
					if (typeof(tienda) == 'undefined') {
						alert('Seleccione una tienda');
					}
					
					$esto.autocomplete({
					   	source: function(request, response) {
					      	$.get( webroot + 'productotiendas/obtener_productos/' + tienda + '/' + request.term, function(respuesta){
								response( $.parseJSON(respuesta) );
					      	})
					      	.fail(function(){
								$.app.loader.ocultar();

								noty({text: 'Ocurrió un error al obtener la información. Intente nuevamente.', layout: 'topRight', type: 'error'});

								setTimeout(function(){
									$.noty.closeAll();
								}, 10000);
							});
					    },
					    select: function( event, ui ) {
					        console.log("Seleccionado: " + ui.item.value + " id " + ui.item.id);
					        todo = ui.item.todo;
					        console.log(todo);
					    },
					    open: function(event, ui) {
		                    var autocomplete = $(".ui-autocomplete:visible");
		                    var oldTop = autocomplete.offset().top;
		                    var width  = $esto.width();
		                    var newTop = oldTop - $esto.height() + 25;

		                    autocomplete.css("top", newTop);
		                    autocomplete.css("width", width);
		                    autocomplete.css("position", 'absolute');
		                }
					});
				});

				$('.input-productos-buscar-meli').each(function(){
					var $esto 	= $(this),
						image 	= '',
						name 	= '',
						description = '',
						specs = '';
					console.log($esto);
					$esto.autocomplete({
					   	source: function(request, response) {
					      	$.get( webroot + 'mercadoLibres/obtener_productos/' + request.term, function(respuesta){
								response( $.parseJSON(respuesta) );
					      	})
					      	.fail(function(){
								$.app.loader.ocultar();

								noty({text: 'Ocurrió un error al obtener la información. Intente nuevamente.', layout: 'topRight', type: 'error'});

								setTimeout(function(){
									$.noty.closeAll();
								}, 10000);
							});
					    },
					    select: function( event, ui ) {
					        console.log("Seleccionado: " + ui.item.value + " id " + ui.item.id);
					        $('.id-product').val(ui.item.id);
					        $('.js-precio').val(ui.item.precio);
					        $('.js-imagen').val(ui.item.imagen);
					        $('.js-imagen-preview').attr('src', ui.item.imagen);
					    }
					});
				});

				// Botón agregar producto a la lista
				$('.button-productos-buscar').on('click', function(event) {
					event.preventDefault();

					$('#tablaProductos tbody').append(todo);
					$('.input-productos-buscar').val('');
				});

				// Botón quitar elemento de la lista
				$(document).on('click', '.quitar', function(event){
					event.preventDefault();
					$(this).parents('tr').eq(0).remove();
				});

			},
			clientesBuscar: function(){

				$('#ProspectoExistente').on('change', function(){
					if ( !$(this).is(':checked')) {
						$('#Cliente1Email').val('');
						$('#Cliente1Siret').val('');
						$('#Cliente1IdGender').val('');
						$('#Cliente1Firstname').val('');
						$('#Cliente1Lastname').val('');
						$('#Cliente1Birthday').val('');
						$('input, select, texarea').removeAttr('disabled');
					}else{
						$.app.autocompletarBuscar.buscar();
					}
				});
			}
		},
		cambioTienda: {
			init: function() {
				if ($('.js-tienda').length) {
					$.app.cambioTienda.bind();
				}
			},
			bind: function() {
				$('.js-tienda').on('change', function(){
					$(this).parents('form').eq(0).submit();
					/*$('.js-tienda-input').val($(this).val());
					$.app.loader.mostrar();
					$.app.obtenerPaises.init();*/
				});
			}
		},
		clonarTabla: {
			clonar: function() {
				// Clonar tabla
				$('.js-clon-contenedor').each(function(){

					var $this 			= $(this),
						tablaInicial 	= $this.find('.js-clon-base'),
						tablaClonada 	= tablaInicial.clone();
						console.log('Contenedor clonar disparado');
					tablaClonada.removeClass('js-clon-base');
					tablaClonada.removeClass('hidden');
					tablaClonada.addClass('js-clon-clonada');
					tablaClonada.find('.js-pais').addClass('js-pais-valida');
					tablaClonada.find('input, select, textarea').removeAttr('disabled');
					tablaClonada.find('.js-direccion-utilizar').attr('checked', 'checked');

					$this.append(tablaClonada);

					$.app.clonarTabla.reindexar();
				});
			},
			init: function(){
				if($('.js-clon-contenedor').length) {
					$.app.clonarTabla.clonar();
					$.app.clonarTabla.bind();
				}
			},
			bind: function(){

				// Agregar tabla click
				$('.js-clon-agregar').on('click', function(event){
					event.preventDefault();
					$.app.clonarTabla.clonar();
				});

			},
			reindexar: function() {
				var $contenedor			= $('.js-clon-contenedor');

				$contenedor.find('.panel').each(function(indice){
					
					$(this).find('.panel-heading').each(function(){
						
						var $headPanel 				= $(this),
							linkHead 				= $headPanel.find('a[data-toggle="collapse"]');
						
						$headPanel.attr('id', 'PanelHeading' + indice);
						linkHead.attr('href', '#PanelCollapse' + indice);

						if ( $headPanel.parent('.panel').find('.collapse').hasClass('in') ) {
							linkHead.html('<i class="fa fa-chevron-down" style="display: none;" aria-hidden="true"></i><i class="fa fa-chevron-up" aria-hidden="true"></i> <b>Dirección ' + indice + '</b>');
						}else{
							linkHead.html('<i class="fa fa-chevron-down" aria-hidden="true"></i><i class="fa fa-chevron-up" style="display: none;" aria-hidden="true"></i> <b>Dirección ' + indice + '</b>');
						}

						$headPanel.find('input').each(function()
						{
							var $that		= $(this),
								nombre		= $that.attr('name').replace(/[(\d)]/, (indice));

							$that.attr('name', nombre);
						});
						
					});

					$(this).find('.panel-collapse').each(function(){
						var $collapsePanel 		= $(this);
						
						$collapsePanel.attr('id', 'PanelCollapse' + indice);
					});
				});

				$contenedor.find('.table').each(function(index)
				{	
					$(this).find('input, select, textarea').each(function()
					{
						var $that		= $(this),
							nombre		= $that.attr('name').replace(/[(\d)]/, (index));

						$that.attr('name', nombre);
					});

					$(this).find('label').each(function()
					{
						var $that		= $(this),
							nombre		= $that.attr('for').replace(/[(\d)]/, (index));

						$that.attr('for', nombre);
					});
				});

				$.app.mascara.init();	
				
			}
		},
		init: function(){
			$.app.clonarTabla.init();
			$.app.toggle.init();
			$.app.cambioTienda.init();
			$.app.obtenerPaises.init();
			$.app.autocompletarBuscar.init();
			$.app.mascara.init();
			
			$.app.validarProspecto.init();
			$.app.rutChileno.init();
			$.app.seleccionDireccion.init();
			$.app.clienteExistente();
			$.app.dataValor.init();
			
		}
	}
});

$(document).ready(function(){
	$.app.init();
});