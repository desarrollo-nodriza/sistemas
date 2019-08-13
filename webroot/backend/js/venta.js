$(function() {

	var venta = function(){

		var ejecutando = false;
		
		var limiteEmpaquetar = 10;
		var offsetEmpaquetar = 0;

		var limiteEmpaquetando = 10;
		var offsetEmpaquetando = 0;

		var modalAbierto = null;

		var countdown = 120; // Countdown

		var autoRefresh = null;

		var levantar_modal = function() {
			$('#modal_alertas').addClass('show');
		}

		var cerrar_modal = function() {
			$('#modal_alertas').removeClass('show');
		}

		var generar_documento = function($ths){

			ejecutando = true;

			$('.loader').addClass('show');

			$.get( $ths.attr('href'), function(res){
				var result = $.parseJSON(res);
				
				ejecutando = false;
				$('.loader').removeClass('show');

				if (typeof(result.result) != 'undefined') {
					for (var i = 0; i <= result.result.length; i++) { 
						console.log(result.result[i]);
						window.open(result.result[i].document);
					}
				}else{
					noty({text: 'Ocurrió un error al generar los documentos.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
				}

				cerrar_modal();

			}).fail(function(){
				
				cerrar_modal();

				ejecutando = false;
				$('.loader').removeClass('show');

				noty({text: 'Ocurrió un error al generar los documentos.', layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);

			});
			
		}

		var generar_etiqueta = function($ths){

			ejecutando = true;

			$('.loader').addClass('show');

			$.get( $ths.attr('href'), function(res){
				var result = $.parseJSON(res);
				
				ejecutando = false;
				$('.loader').removeClass('show');

				if (typeof(result.result) != 'undefined') {
					for (var i = 0; i <= result.result.length; i++) {
						window.open(result.result[i].document);
					}
				}else{
					noty({text: 'Ocurrió un error al generar la etiqueta.', layout: 'topRight', type: 'error'});

					setTimeout(function(){
						$.noty.closeAll();
					}, 10000);
				}

				cerrar_modal();

			}).fail(function(){
				
				cerrar_modal();

				ejecutando = false;
				$('.loader').removeClass('show');

				noty({text: 'Ocurrió un error al generar la etiqueta.', layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);

			});
			
		}

		var consultar_dte = function(){
			ejecutando = true;

			$('.loader').addClass('show');

			$.get( $('.js-generar-documentos-venta-modal').attr('href'), function(res){
				var result = $.parseJSON(res);
				
				ejecutando = false;
				$('.loader').removeClass('show');			

				if (result.dte_generado) {
					cerrar_modal();
					generar_documento($('.js-generar-documentos-venta-primario'));
				}else{
					levantar_modal();
				}

			}).fail(function(){

				ejecutando = false;
				$('.loader').removeClass('show');

				noty({text: 'Ocurrió un error al generar los documentos.', layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);

			});
		}

		var agregar_venta = function($ths){
			var clonado = $ths.clone();
			clonado.addClass('hide');
			$('#VentaFacturacionMasivaForm').append(clonado);
		}

		var quitar_venta = function($ths){
			$('#VentaFacturacionMasivaForm').find('input[name="' + $ths.attr('name') + '"]').remove();
		}

		var obtener_transportista = function($ths) {

			if ($ths.val() == '') {
				return;
			}

			var contexto = $ths.parents('tr').eq(0);

			$('.loader').addClass('show');

			$.get( webroot + 'transportes/obtener_transporte/' + $ths.val(), function(res){
				var result = $.parseJSON(res);

				$('.loader').removeClass('show');

				if (result.code == 200) {

					contexto.find('.js-fecha-entrega').text(result.data.tiempo_entrega);
					contexto.find('.js-btn-seguimiento').text(result.data.url_seguimiento);

				}else{
					noty({text: result.message, layout: 'topRight', type: 'error'});
				}

			}).fail(function(){

				$('.loader').removeClass('show');

				noty({text: 'Ocurrió un error al obtener el transporte.', layout: 'topRight', type: 'error'});

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);

				$ths.val('');

			}).always(function(){
				$('.loader').removeClass('show');
			});
		}

		var quitar_transporte = function($ths) {

			if ($ths.data('id') == '') {
				return;
			}

			$.post( webroot + 'transportes/quitar_transporte/', {id: $ths.data('id')} ,function(res){
				var result = $.parseJSON(res);

				if (result.code == 200) {
					noty({text: result.message, layout: 'topRight', type: 'success'});
				}else{
					noty({text: result.message, layout: 'topRight', type: 'error'});
				}

			}).fail(function(){

				noty({text: 'Ocurrió un error al obtener el transporte.', layout: 'topRight', type: 'error'});

			}).always(function(){
				$('.loader').removeClass('show');
				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});
		}

		var cancelarOrdenamiento = function(){
			$("#tasks").sortable('cancel');
			$("#tasks_progreess").sortable('cancel');
			$("#tasks_completed").sortable('cancel');
		}

		var obtener_ventas_preparacion = function(){	

			$('.loader').addClass('show');
			$('#refrescar_manualmente').addClass('fa-spin');

			$.get(webroot + 'ventas/obtener_ventas_preparacion/' + limiteEmpaquetar + '/' + offsetEmpaquetar + '/' + limiteEmpaquetando + '/' + offsetEmpaquetando, function(data) {
				
				var res = $.parseJSON(data);

				if (res.code != 200) {
					cancelarOrdenamiento();
					noty({text: 'Ocurrió un error al refrescar las ventas. Intente actualizar la página.', layout: 'topRight', type: 'error'});
				}

				$('#tasks').html(res.data.empaquetar.html);
				$('#contador-listos').html('(' + res.data.empaquetar.total + ')');

				$('#tasks_progreess').html(res.data.empaquetando.html);
				$('#contador-preparacion').html('(' + res.data.empaquetando.total + ')');

				$('#tasks_completed').html(res.data.empaquetado.html);
				$('#contador-completos').html('(' + res.data.empaquetado.total + ')');

				//noty({text: 'Datos cargados con éxito.', layout: 'topRight', type: 'success'});
				
			}).fail(function(){
				cancelarOrdenamiento();
				noty({text: 'Ocurrió un error al refrescar las ventas. Intente actualizar la página.', layout: 'topRight', type: 'error'});

			}).always(function(){

				// Evitamos que se cierre el modal al recargarlo
				if (modalAbierto != null) {
					$(modalAbierto).modal('show');
				}

				page_content_onresize();

				$('.loader').removeClass('show');

				$('#refrescar_manualmente').removeClass('fa-spin');

				autoRefresh.reset(120000);

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});

		}

		var cambiar_venta_estado_preparacion = function($ths, $estado){
			var id_venta 	= $ths.data('id'),
				actualizado = false;

			$('.loader').addClass('show');
			$('#refrescar_manualmente').addClass('fa-spin');

			$.post(webroot + 'ventas/cambiar_subestado/' + id_venta, {'subestado':$estado}, function(res){

				var respuesta = $.parseJSON(res);

				if (respuesta.code == 200) {
					actualizado = true;
					obtener_ventas_preparacion();
				}else{
					$("#tasks").sortable('cancel');
					actualizado = false;
					noty({text: respuesta.message, layout: 'topRight', type: 'error'});
				}				

			}).fail(function(){
				$("#tasks").sortable('cancel');
				actualizado = false;
				$('.loader').removeClass('show');
				noty({text: 'Ocurrió un error al actualizar la venta. Intente actualizar la página.', layout: 'topRight', type: 'error'});

			}).always(function(){

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});
		}


		var obtener_ventas_preparacion_modal = function($id_venta){

			$.ajax({
				url: webroot + 'ventas/obtener_ventas_preparacion_modal/' + $id_venta
			})
			.done(function(res) {
				
				var $res = $.parseJSON(res);
				console.log($res);
				$('#wrapper-modal-venta-ver-mas').html($res.html);
				$('#wrapper-modal-venta-ver-mas .modal').modal('show');

			})
			.fail(function() {
				noty({text: 'Ocurrió un error al obtener la venta. Intente nuevamente.', layout: 'topRight', type: 'error'});
			})
			.always(function() {
				$('.loader').removeClass('show');
				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});
			

		}


		var cambiar_venta_estado_empaquetado = function($ths, $data){
			var id_venta 	= $ths.data('id'),
				actualizado = false;

			$('.loader').addClass('show');
			$('#refrescar_manualmente').addClass('fa-spin');
			$('.modal').modal('hide');

			$.post(webroot + 'ventas/cambiar_estado/' + id_venta, $data, function(res){

				var respuesta = $.parseJSON(res);
				
				if (respuesta.code == 200) {
					actualizado = true;
					obtener_ventas_preparacion();

					noty({text: respuesta.message, layout: 'topRight', type: 'success'});
				}else{
					$("#tasks_progreess").sortable('cancel');
					actualizado = false;
					$('.loader').removeClass('show');
					noty({text: respuesta.message, layout: 'topRight', type: 'error'});
				}

			}).fail(function(){
				$("#tasks_progreess").sortable('cancel');
				actualizado = false;
				$('.loader').removeClass('show');
				noty({text: 'Ocurrió un error al actualizar la venta. Intente actualizar la página.', layout: 'topRight', type: 'error'});

			}).always(function(){

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);
			});
		}

		var Timer = function(fn, t) {
		    var timerObj = setInterval(fn, t);

		    this.stop = function() {
		        if (timerObj) {
		            clearInterval(timerObj);
		            timerObj = null;
		        }
		        return this;
		    }

		    // start timer using current settings (if it's not already running)
		    this.start = function() {
		        if (!timerObj) {
		            this.stop();
		            timerObj = setInterval(fn, t);
		        }
		        return this;
		    }

		    // start with new interval, stop current interval
		    this.reset = function(newT) {
		        t = newT;
		        countdown = t/1000; // Reiniciamos el countdown
		        return this.stop().start();
		    }
		}


		var confirmarDetalle = function($id_venta, $id_detalle, $cantidad, $btn){

			var $itemsConfirmar = {
				'Detail' : [{ 'id' : $id_detalle, 'quantity' : $cantidad}]
			};
			

			$.post(webroot + 'api/ventas/picking/' + $id_venta + '.json?tadah=1', $itemsConfirmar, function(data, textStatus, xhr) {
				
				var respuesta = data;
				
				if (respuesta.response == true) {
					
					$btn.parents('tr').eq(0).replaceWith(respuesta.tr);
					noty({text: 'Item confirmado con éxito.', layout: 'topRight', type: 'success'});

				}else{
					noty({text: respuesta.message, layout: 'topRight', type: 'error'});
				}

			}).fail(function(){
				
				noty({text: 'Ocurrió un error inesperado. Intene nuevamente.', layout: 'topRight', type: 'error'});

			}).always(function(){

				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);

				$btn.removeAttr('disabled');

			});
		}

		var ver_imagen_producto = function($img){

			var titulo = $img.parents('td').eq(0).data('original-title');

			$('#modalImagenLabel').html(titulo);

			var imgnw = $img.clone(); 
				imgnw.removeClass('producto-td-imagen');

			$('#modalImagen .modal-body').html(imgnw);
			$('#modalImagen').modal('show');


		}

		return {
			init: function(){

				if ($('#preparacion_index').length) {
					
					$('.loader').addClass('show');
					
					obtener_ventas_preparacion();

					setInterval(function(){
						countdown = countdown - 1;

						if (countdown == 1) {
							$('#actualizacion-contdown').html(countdown + ' segundo');
						}else if(countdown > 1){
							$('#actualizacion-contdown').html(countdown + ' segundos');
						}else{
							$('#actualizacion-contdown').html('éste instante...');
						}

					}, 1000);

					autoRefresh = new Timer(function() {
					    obtener_ventas_preparacion();
					}, 120000);

				}

				$(document).on('click', '.js-venta-ver-mas', function(){
					$('.loader').addClass('show');
					obtener_ventas_preparacion_modal($(this).data('id'));
				});

				$('#refrescar_manualmente').on('click', function(){
					$('.loader').addClass('show');
					$('#refrescar_manualmente').addClass('fa-spin');
					obtener_ventas_preparacion();
				});

				$(document).on('change', '.js-select-transporte', function(){
					obtener_transportista($(this));
				});

				$('.js-generar-documentos-venta-modal').on('click', function(e){
					e.preventDefault();
					if (!ejecutando) {

						consultar_dte();

						//levantar_modal();
					}
				});


				$(document).on('click', 'button[data-toggle="modal"]' ,function(e){
					modalAbierto = $(this).data('target');
				});

				$(document).on('hide.bs.modal', '.modal', function(){
					modalAbierto = null;
				});

				$('.js-remove-seguimiento').on('click', function(e){
					quitar_transporte($(this));
				});

				$(document).on('click', '.js-generar-documentos-venta', function(e){
					e.preventDefault();
					if (!ejecutando) {
						cerrar_modal();
						generar_documento($(this));
					}
				});


				$(document).on('click', '.js-generar-etiqueta-venta', function(e){
					e.preventDefault();
					if (!ejecutando) {
						cerrar_modal();
						generar_etiqueta($(this));
					}
				});


				$(document).on('click', '.js-confirmar-detalle' , function(e){
					e.preventDefault();

					$(this).attr('disabled', 'disabled');

					var id_venta 	= $(this).parents('.modal').eq(0).data('id'),
						id_detalle 	= $(this).parents('tr').eq(0).data('id'),
						cantidad 	= $(this).parents('tr').eq(0).data('cantidad'),
						btn 		= $(this);

					btn.attr('disabled', 'disabled');

					confirmarDetalle(id_venta, id_detalle, cantidad, btn);

				});


				$('.mb-control-close').on('click', function(){
					cerrar_modal();
				});

				if ($('.tasks').length){
					
			        $("#tasks").sortable({
			            items: "> .task-item",
			            connectWith: "#tasks_progreess",
			            handle: ".task-text",            
			            receive: function(event, ui) {
			                if(this.id == "tasks_progreess"){
			                	cambiar_venta_estado_preparacion(ui.item, 'empaquetando');
			                }
			                if(this.id == "tasks"){
			                	cambiar_venta_estado_preparacion(ui.item, 'empaquetar');
			                }

			                page_content_onresize();
			            }
			        }).disableSelection();

			        $("#tasks_progreess:not(.not-move)").sortable({
			            items: "> .task-item",
			            connectWith: "#tasks,#tasks_completed",
			            handle: ".task-text",     
			            receive: function(event, ui) {
			                if(this.id == "tasks_progreess"){
			                	cambiar_venta_estado_preparacion(ui.item, 'empaquetando');
			                }
			                if(this.id == "tasks"){
			                	cambiar_venta_estado_preparacion(ui.item, 'empaquetar');
			                }

			                page_content_onresize();
			            }
			        }).disableSelection();

			        $("#tasks_completed").sortable({
			            items: "> .task-item",
			            handle: ".task-text",            
			            receive: function(event, ui) {
			                if(this.id == "tasks_completed"){
				                // LEvantamos modal de cambio de estado
								var id                = ui.item.data('id');
								var modalCambioEstado = $('#modal-cambiar-estado-' + id);

								if (modalCambioEstado.length == 0) {
									cambiar_venta_estado_preparacion(ui.item, 'empaquetado');
								}else{
									modalCambioEstado.modal('show');
									autoRefresh.stop(); 
								}
			                }

			                page_content_onresize();
			            }
			        }).disableSelection();

				}


				$(document).on('submit', '.js-form-cambiar-estado-venta', function(e){
					e.preventDefault();

					var data = $(this).serialize();
					
					cambiar_venta_estado_empaquetado($(this), data);

				});


				$(document).on('click', '.js-cancelar-cambio-estado', function(){
					
					$("#tasks_progreess").sortable('cancel');
				});

				$(document).on('hide.bs.modal', '.modal-cambiar-estado', function(){
					
					$("#tasks_progreess").sortable('cancel');
					autoRefresh.reset(120000);
				});

				$(document).on('show.bs.modal', '.modal-venta-detalle', function(){
					autoRefresh.stop();
				});

				$(document).on('hide.bs.modal', '.modal-venta-detalle', function(){
					autoRefresh.reset(120000);
				});


				$(document).on('click', '.facturacion_masiva' , function(){
					if ($(this).is(':checked')) {
						agregar_venta($(this));
					}else{
						quitar_venta($(this));
					}

					var selecionados = $('#VentaFacturacionMasivaForm .facturacion_masiva').length;
					$('#ventas-seleccionadas').text(selecionados);

					if (selecionados==0) {
						$('.btn-facturacion-masiva').attr('disabled', 'disabled');
					}else{
						$('.btn-facturacion-masiva').removeAttr('disabled');
					}

				});


				$(document).on('click', '.producto-td-imagen', function(){
					ver_imagen_producto($(this));
				});

			}
		}	

	}();

	venta.init();

});