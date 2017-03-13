$.extend({
	app: {
		loader: {
			mostrar: function(){
				$('.loader').css('display', 'block');
			},
			ocultar: function(){
				$('.loader').css('display', 'none');
			}
		},
		obtenerRegiones: function(tienda, pais, contexto){
			$.get( webroot + 'regiones/regiones_por_tienda_pais/' + tienda + '/' + pais, function(respuesta){
				contexto.find('.js-region').html(respuesta);
				$.app.loader.ocultar();
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
				});

				$(document).on('change', '.js-pais', function() {		
					$.app.loader.mostrar();
					var pais 		= $(this).val(),
						contexto 	= $(this).parents('table').eq(0);
					$.app.obtenerRegiones(tienda, pais, contexto);

				});
			}
		},
		autocompletarBuscar: {
			init: function() {
				if ( $('.input-clientes-buscar').length > 0 ) {
					$.app.autocompletarBuscar.clientesBuscar();
				}
			},	
			obtenerDatosCliente: function( tienda, idCliente){
				$.get( webroot + 'clientes/cliente_por_tienda/' + tienda + '/' + idCliente, function(respuesta){
					
					var cliente 	= $.parseJSON(respuesta),
						direcciones = cliente.Clientedireccion;

					$('#ClienteIdGender').val(cliente.Cliente.id_gender);
					$('#ClienteFirstname').val(cliente.Cliente.firstname);
					$('#ClienteLastname').val(cliente.Cliente.lastname);
					
					if (typeof(direcciones) == 'object') {
						console.info('Tiene ' + direcciones.length + ' dirección/es');
						for (var itr = 0; itr <= direcciones.length - 1; itr++) {
							if (direcciones.length > 1) {
								console.info('Se crea tabla de direcciones nueva');
								$('.js-clon-agregar').trigger('click');
							}
							console.info('Dirección ' + itr + ' Id: ' + direcciones[itr].id_address );
							// Se completan los campos de direcciones
							$('.js-direccion-id').eq(itr + 1).val(direcciones[itr].id_address);
							$('.js-direccion-alias').eq(itr + 1).val(direcciones[itr].alias);
							if ( direcciones[itr].company != '' ) {
								$('.js-direccion-empresa').eq(itr + 1).val(direcciones[itr].company);
							}
						}
						
					}
					$.app.loader.ocultar();

					noty({text: 'Se completaron todos los campos del cliente.', layout: 'topRight', type: 'success'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 6000);
		      });
			},
			clientesBuscar: function(){

				$('#ProspectoExistente').on('change', function(){
					if ( !$(this).is(':checked')) {
						$('#ClienteEmail').val('');
						$('#ClienteIdGender').val('');
						$('#ClienteFirstname').val('');
						$('#ClienteLastname').val('');
						$('#ClienteBirthday').val('');
						$('input, select, texarea').removeAttr('disabled');
					}
				});

				$('.input-clientes-buscar').each(function(){
					var $esto = $(this);
					var tienda = $('.js-tienda').val();
					
					if (typeof(tienda) == 'undefined') {
						alert('Seleccione una tienda');
					}

					$esto.autocomplete({
					   	source: function(request, response) {
					      $.get( webroot + 'clientes/clientes_por_tienda/' + tienda + '/' + request.term, function(respuesta){
								if ($('#ProspectoExistente:checked').length > 0) {
									response( $.parseJSON(respuesta) );
								}else{
									
								}
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
					$.app.loader.mostrar();
					$.app.obtenerPaises.init();
				});
			}
		},
		clonarTabla: {
			init: function(){
				if($('.js-clon-contenedor').length) {
					$.app.clonarTabla.bind();
				}
			},
			bind: function(){

				// Clonar tabla
				$('.js-clon-contenedor').each(function(){

					var $this 			= $(this),
						tablaInicial 	= $this.find('.js-clon-base'),
						tablaClonada 	= tablaInicial.clone();

					tablaClonada.removeClass('js-clon-base');
					tablaClonada.removeClass('hidden');
					tablaClonada.addClass('js-clon-clonada');
					tablaClonada.find('input, select, textarea').removeAttr('disabled');

					$this.append(tablaClonada);

					$.app.clonarTabla.reindexar();
				});

				// Agregar tabla
				$('.js-clon-agregar').on('click', function(event){
					event.preventDefault();

					var $this 			= $(this).parent().siblings('.js-clon-contenedor'),
						tablaInicial 	= $this.find('.js-clon-base'),
						tablaClonada 	= tablaInicial.clone();
					
					tablaClonada.removeClass('js-clon-base');
					tablaClonada.removeClass('hidden');
					tablaClonada.addClass('js-clon-clonada');
					tablaClonada.find('input, select, textarea').removeAttr('disabled');

					$this.append(tablaClonada);

					$.app.clonarTabla.reindexar();
				});

			},
			reindexar: function() {
				var $contenedor			= $('.js-clon-contenedor');

				$contenedor.find('.table > tbody > tr').each(function(index)
				{
					$(this).find('input, select, textarea').each(function()
					{
						var $that		= $(this),
							nombre		= $that.attr('name').replace(/[(\d)]/g, (index + 1));

						$that.attr('name', nombre);
					});

					$(this).find('label').each(function()
					{
						var $that		= $(this),
							nombre		= $that.attr('for').replace(/[(\d)]/g, (index + 1));

						$that.attr('for', nombre);
					});
				});
			}
		},
		init: function(){
			$.app.clonarTabla.init();
			$.app.cambioTienda.init();
			$.app.obtenerPaises.init();
			$.app.autocompletarBuscar.init();
		}
	}
});

$(document).ready(function(){
	$.app.init();
});