$(function() {

	
	var venta = function(){

		var ejecutando = false;
		
		var limiteEmpaquetar = -1;
		var offsetEmpaquetar = 0;

		var limiteEmpaquetando = 25;
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

		var generar_etiqueta_dte = function($ths){

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


		var obtener_ventas_preparacion_busqueda = function(){	

			$('.loader').addClass('show');
			$('#refrescar_manualmente').addClass('fa-spin');

			var $id_venta 			= $('#filtro-venta-id').val() == '' ? 0 : $('#filtro-venta-id').val() ,
				$id_metodo_envio 	= $('#filtro-venta-envio').val() == '' ? 0 : $('#filtro-venta-envio').val() ,
				$id_marketplace 	= $('#filtro-venta-marketplace').val() == '' ? 0 : $('#filtro-venta-marketplace').val(),
				$comuna 	        = $('#filtro-venta-comuna').val() == '' ? 0 : $('#filtro-venta-comuna').val(),
				$id_tienda 			= $('#filtro-venta-tienda').val() == '' ? 0 : $('#filtro-venta-tienda').val();

			$.get(webroot + 'ventas/obtener_ventas_preparacion/' + limiteEmpaquetar + '/' + offsetEmpaquetar + '/' + limiteEmpaquetando + '/' + offsetEmpaquetando + '/' + $id_venta + '/' + $id_metodo_envio + '/' + $id_marketplace + '/' + $id_tienda + '/' + $comuna, function(data) {
				
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

			// Verificamos que existe solo una venta en preparación
			if ($estado === 'empaquetando')
			{
				var tareasProcesadas = $('#tasks_progreess').find("[data-responsable='" + loggeduser.email + "']").length; 

				if (tareasProcesadas > 0)
				{
					noty({text: 'No se permite preparar más de una venta a la vez.', layout: 'topRight', type: 'error'});
					$("#tasks").sortable('cancel');
					$('.loader').removeClass('show');
					
					return;
				}
			}

			$.post(webroot + 'ventas/cambiar_subestado/' + id_venta, {'subestado':$estado}, function(res){

				var respuesta = $.parseJSON(res);

				if (respuesta.code == 200) {
					actualizado = true;
					obtener_ventas_preparacion_busqueda();
				}else{
					$("#tasks").sortable('cancel');
					actualizado = false;
					noty({text: respuesta.message, layout: 'topRight', type: 'error'});
				}				

			})
			.fail(function(){
				$("#tasks").sortable('cancel');
				actualizado = false;
				noty({text: 'Ocurrió un error al actualizar la venta. Intente actualizar la página.', layout: 'topRight', type: 'error'});

			}).always(function(){
				
				$('.loader').removeClass('show');

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
				
				$('#wrapper-modal-venta-ver-mas').html($res.html);
				$('#wrapper-modal-venta-ver-mas .modal').modal('show');
				$('#wrapper-modal-venta-ver-mas .modal').modal('handleUpdate');

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
					obtener_ventas_preparacion_busqueda();

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

		var cambiar_venta_estado_en_revision = function($ths){
			var id_venta 	= $ths.find('input[name="data[Venta][id]"]').val();

			$('#mb-set-picking-revision').removeClass('open');
			
			$('.loader').addClass('show');

			$.post(webroot + 'api/ventas/set_picking/' + id_venta + '.json?token=' + loggeduser.token.token, $ths.serialize(), function(res){

				var respuesta = res;
				
				if (respuesta.response.code == 200) {

					$('.modal').modal('hide');
		
					obtener_ventas_preparacion_busqueda();

					noty({text: respuesta.response.message, layout: 'topRight', type: 'success'});

				}else{
					
					$('.loader').removeClass('show');
					noty({text: respuesta.message, layout: 'topRight', type: 'error'});
				}

			}).fail(function(){
				
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

		var obtener_metodo_envio_venta = function($id){

			$.app.loader.mostrar();
		
			$.ajax({
				url: webroot + 'metodoEnvios/ajax_obtener_metodo_envio/' + $id,
				type: 'GET',
			})
			.done(function(res) {
				
				habilitar_formulario(res);

			})
			.fail(function() {
				
			})
			.always(function(){
				$.app.loader.ocultar();
			});
			
		}

		var obtener_metodo_envio_venta_v2 = function($id){

			$.ajax({
				url: webroot + 'metodoEnvios/ajax_obtener_metodo_envio/' + $id,
				type: 'GET',
			})
			.done(function(res) {
				
				habilitar_formulario(res);

			});
			
		}
		function habilitar_formulario(res) {
			var $metodo_envio = JSON.parse(res);

			if ($metodo_envio.MetodoEnvio.retiro_local) {
			
				$('#VentaDireccionEntrega').parents('div').eq(0).addClass('hidden');
				$('#VentaComunaEntrega').parents('div').eq(0).addClass('hidden');
				$('#VentaNumeroEntrega').parents('div').eq(0).addClass('hidden');
				$('#VentaOtroEntrega').parents('div').eq(0).addClass('hidden');
				$('#VentaCiudadEntrega').parents('div').eq(0).addClass('hidden');
				$('#VentaRutReceptorEntrega').parents('div').eq(0).addClass('hidden');
				$('#VentaNombreReceptor').parents('div').eq(0).addClass('hidden');
				$('#VentaFonoReceptor').parents('div').eq(0).removeClass('hidden');
				$('#VentaCostoEnvio').parents('div').eq(0).addClass('hidden');

				$('#VentaDireccionEntrega').rules("remove", "required");
				$('#VentaNumeroEntrega').rules("remove", "required");
				$('#VentaCiudadEntrega').rules("remove", "required");
				$('#VentaRutReceptor').rules("remove", "required");
				$('#VentaNombreReceptor').rules("remove", "required");
				$('#VentaComunaEntrega').rules("remove", "required");
				$('#VentaCostoEnvio').rules("remove", "required");

			}else{
				$('#VentaDireccionEntrega').parents('div').eq(0).removeClass('hidden');
				$('#VentaComunaEntrega').parents('div').eq(0).removeClass('hidden');
				$('#VentaNumeroEntrega').parents('div').eq(0).removeClass('hidden');
				$('#VentaOtroEntrega').parents('div').eq(0).removeClass('hidden');
				$('#VentaCiudadEntrega').parents('div').eq(0).removeClass('hidden');
				$('#VentaRutReceptor').parents('div').eq(0).removeClass('hidden');
				$('#VentaNombreReceptor').parents('div').eq(0).removeClass('hidden');
				$('#VentaFonoReceptor').parents('div').eq(0).removeClass('hidden');
				$('#VentaCostoEnvio').parents('div').eq(0).removeClass('hidden');

				$('#VentaDireccionEntrega').rules("add", {
					required: true,
					messages: {
						required: 'Campo requerido'
					}
				});

				$('#VentaNumeroEntrega').rules("add", {
					required: true,
					messages: {
						required: 'Campo requerido'
					}
				});

				$('#VentaCiudadEntrega').rules("add", {
					required: true,
					messages: {
						required: 'Campo requerido'
					}
				});

				$('#VentaRutReceptor').rules("add", {
					required: true,
					rut: true,
					messages: {
						required: 'Campo requerido'
					}
				});

				$('#VentaNombreReceptor').rules("add", {
					required: true,
					messages: {
						required: 'Campo requerido'
					}
				});

				$('#VentaComunaEntrega').rules("add", {
					required: true,
					messages: {
						required: 'Campo requerido'
					}
				});

				$('#VentaCostoEnvio').rules("add", {
					required: true,
					messages: {
						required: 'Campo requerido'
					}
				});
			}
		}

		// Start Smart Wizard
        var pasos_compra = function(){

            if($("#buy-steps").length > 0){

            	jQuery.validator.setDefaults({
				  ignore: ''
				});

            	var validator = $('#VentaAdminAddForm').validate();

            	var cantidad_producto = 0,
            		subtotal_producto = 0,
            		total_transporte = 0,
            		total_productos = 0;

				var finalizarWizard = true;

                //Check count of steps in each wizard
                $("#buy-steps > ul").each(function(){
                    $(this).addClass("steps_"+$(this).children("li").length);
                });//end

                $("#buy-steps").smartWizard({
                	lang: {
                		next: "Siguiente",
                		previous: "Anterior"
                	},
                	anchorSettings: {
						anchorClickable: true, // Enable/Disable anchor navigation
						enableAllAnchors: false, // Activates all anchors clickable all times
						markDoneStep: true, // add done css
						enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
					},            
                    // This part of code can be removed FROM
                    onLeaveStep: function(obj){
                        

                        // Se calcula el total a pagar
                        if(obj.attr('rel') == 1) {

							var wizard = obj.parents("#buy-steps");

							if(wizard.hasClass("wizard-validation")){

								var valid = true;

								$('input,textarea,select',$(obj.attr("href"))).each(function(i,v){
									valid = validator.element(v) && valid;
								});

								if(!valid){
									wizard.find(".stepContainer").removeAttr("style");
									validator.focusInvalid();

									noty({text: 'Complete todos los campos requeridos.', layout: 'topRight', type: 'error'});
									setTimeout(function(){
										$.noty.closeAll();
									}, 2000);

									finalizarWizard = false;

									return false;
								}

							}

                        	$('#transporte').data('value', $('#VentaCostoEnvio').val());
							recalcular_monto_pagar();

                        	venta.clonarElemento( obj.parents('.wizard').eq(0).find('.stepContainer > #step-2').find('.js-pagos-wrapper > .clone-tr').eq(0) );

                        }

                        return true;

                        
                    },// <-- TO

                    //This is important part of wizard init
                    onShowStep: function(obj){
                        var wizard = obj.parents("#buy-steps");

                        if(wizard.hasClass("show-submit")){

                            var step_num = obj.attr('rel');
                            var step_max = obj.parents(".anchor").find("li").length;

                            if(step_num == step_max){
                                obj.parents("#buy-steps").find(".actionBar .btn-primary").css("display","block");
                            }
                        }

                        page_content_onresize();

                        return true;
                    },//End
					onFinish: function(){

						var wizard = $("#buy-steps");

						if(wizard.hasClass("wizard-validation")){

							var valid = true;

							$('input,textarea,select',$('#VentaAdminAddForm')).each(function(i,v){
								valid = validator.element(v) && valid;
							});

							if(!valid){
								wizard.find(".stepContainer").removeAttr("style");
								validator.focusInvalid();

								noty({text: 'Complete todos los campos requeridos.', layout: 'topRight', type: 'error'});
								setTimeout(function(){
									$.noty.closeAll();
								}, 2000);

								return false;
							}

						}

						if($('.js-pagos-wrapper > tr:not(.hidden)').length == 0){

                        	noty({text: 'Debe agregar al menos un pago', layout: 'topRight', type: 'error'});
							
							setTimeout(function(){
								$.noty.closeAll();
							}, 10000);

							return false;
                        }

						if($('.js-productos-wrapper > tr').length == 0){

                        	noty({text: 'Debe agregar al menos un producto', layout: 'topRight', type: 'error'});
							
							setTimeout(function(){
								$.noty.closeAll();
							}, 10000);

							return false;
                        }
						
						$.app.loader.mostrar();

						$('#VentaAdminAddForm').submit();
							
					}
                });
            }

        }// End Smart Wizard
		
		$(document).on('change', '.js-monto-pago', function(){
			validar_tipo_venta();
		});

		$(document).on('change', '.venta_estado_id', function(){
			validar_tipo_venta();
		});
		var validar_tipo_venta = function () {

			$.app.formularios.bind('#VentaAdminAddForm');
			let pagado = 0;
			$('.js-monto-pago').each(function(){
				if($(this).val())
				{
					pagado = parseFloat(pagado) + parseFloat($(this).val());
				}
			});
			
			if ($('.venta_estado_id').val() == 13) {

				if ($('#VentaTotal').val() != pagado) {
					$('#buy-steps').find(".actionBar .btn-primary").addClass('disabled');
					noty({text: 'El pago debe ser igual al total de la venta.', layout: 'topRight', type: 'error'});
					return ;
				}
			}

			$('#buy-steps').find(".actionBar .btn-primary").removeClass('disabled');
			
		}

        var recalcular_monto_pagar = function(){

        	var subtotal_pagar     = $('#subtotal').data('value'),
				iva_pagar          = $('#iva').data('value'),
				transporte         = parseFloat($('#transporte').data('value')),
				descuento          = parseFloat($('#VentaDescuento').data('value')),
				total_pagar        = $('#VentaTotal').data('value'),
				subtotal_producto  = 0;

			$('.js-subtotal-producto').each(function(){
				var p_neto =  parseFloat($(this).data('value') / 1.19);
        		subtotal_producto = parseFloat(subtotal_producto) + p_neto; 
        	});


        	subtotal_pagar = subtotal_producto;
        	iva_pagar = parseFloat(subtotal_pagar * 1.19) - subtotal_pagar;

        	$('#subtotal').data('value', subtotal_pagar.toFixed(2));
        	$('#subtotal').text(subtotal_pagar.toFixed(2));

        	$('#iva').data('value', iva_pagar.toFixed(2));
        	$('#iva').text(iva_pagar.toFixed(2));

        	$('#transporte').data('value', transporte.toFixed(2));
			$('#transporte').text(transporte.toFixed(2));
        	
			$('#VentaDescuento').data('value', descuento.toFixed(2));

			total_pagar = parseFloat((parseFloat(subtotal_pagar) + parseFloat(iva_pagar) + parseFloat(transporte)) - parseFloat(descuento)).toFixed(2);

			$('#VentaTotal').data('value', total_pagar);
			$('#VentaTotal').val(total_pagar);

			$('#total-resumen').text(total_pagar);
			$('#total-resumen').mask('000.000.000.000.000', {reverse: true});

			// PAgos y vuelto
			var pagado = 0;
			var vuelto = 0;
			$('.js-monto-pago').each(function(){
				if ($(this).val() > 0) {
					pagado = parseFloat(pagado) + parseFloat($(this).val());
				}
			});

			if (pagado > total_pagar) {
				vuelto = parseFloat(pagado) - parseFloat(total_pagar);
			}

			$('#pagado-resumen').text(pagado);
			$('#pagado-resumen').mask('000.000.000.000.000', {reverse: true});

			$('#vuelto-resumen').text(vuelto);
			$('#vuelto-resumen').mask('000.000.000.000.000', {reverse: true});

        }

        var calcular_monto_pagar = function(){

        	recalcular_monto_pagar();

        	$(document).on('change', '.js-cantidad-producto', function(){
				
        		var contexto = $(this).parents('tr').eq(0),
        			cantidad = $(this).val(),
        			monto = contexto.find('.js-precio-producto').val();

				if ($(this).val() > 0 && monto > 0) {
					
					$(this).data('value', $(this).val());

					var subtotal = parseInt(cantidad) * parseInt(monto);

					contexto.find('.js-subtotal-producto').data('value', subtotal)
					contexto.find('.js-subtotal-producto').text(subtotal);

					recalcular_monto_pagar();
				}

			});

			$(document).on('change', '.js-precio-producto', function(){

				var contexto = $(this).parents('tr').eq(0),
        			monto = $(this).val(),
        			cantidad = contexto.find('.js-cantidad-producto').val();
				
				if ($(this).val() > 0 && cantidad > 0) {

					$(this).data('value', $(this).val());

					var subtotal = parseInt(cantidad) * parseInt(monto);

					contexto.find('.js-subtotal-producto').data('value', subtotal)
					contexto.find('.js-subtotal-producto').text(subtotal);

					recalcular_monto_pagar();
				}

			});

			$(document).on('change', '#VentaDescuento', function(){
				$(this).data('value', $(this).val());
				recalcular_monto_pagar();
			});

			$(document).on('click', '.js-recalcular-montos', function(e){
				e.preventDefault();

				var $th = $(this).parents('tr').eq(0);

				$th.fadeOut('slow', function() {
					$th.remove();
					recalcular_monto_pagar();
				});
			});

			$(document).on('change, focusout', '.js-monto-pago', function(){
				if ($(this).val() > 0) {
					$(this).data('value', $(this).val());
					recalcular_monto_pagar();
				}
			});

        }

		return {
			autocompletar_productos: function(){

				$( "#obtener_producto" ).autocomplete({
			      source: function( request, response ) {
			        $.ajax( {
			          url: webroot + 'api/productos.json',
			          data: {
			          	token : $('#VentaAccessToken').val(),
			          	limit: 20,
			            s: request.term,
			            tr: 1, // Solicitamos que nos retorne el TR
			            external: 1
			          },
			          success: function( data ) {

			          	var $result = [];
			          	
			          	for (var i = 0; i < data.productos.length; i++) {

			          		$result[i] = {
			          			label :  data.productos[i].VentaDetalleProducto.nombre,
			          			value :  data.productos[i].VentaDetalleProducto.nombre,
			          			id :  data.productos[i].VentaDetalleProducto.id,
			          			tr : data.productos[i].VentaDetalleProducto.tr,
			          			todo: data.productos[i].VentaDetalleProducto
			          		}
			          	}
			            response( $result );
			          },
			          fail: function(error){
			          	noty({text: error.responseJSON.message, layout: 'topRight', type: 'error'});
						setTimeout(function(){
							$.noty.closeAll();
						}, 10000);
			          }
			        } );
			      },
			      minLength: 3,
			      select: function( event, ui ) {
			      	
			      	// Si el producto no tiene stock inmediato, se avisa
			      	if (ui.item.todo.stock_fisico_total == 0) {
			      		noty({text: 'El item ' + ui.item.label + ' no está disponible para entrega inmediata.', layout: 'topRight', type: 'error'});
			      	}else{
			      		noty({text: '¡El item ' + ui.item.label + ' está disponible para entrega inmediata!', layout: 'topRight', type: 'success'});
			      	}

			      	$('#obtener_producto').val('');

			      	$('.js-productos-wrapper').append(ui.item.tr);

			      	$('.js-productos-wrapper').find('tr[data-id="'+ui.item.id+'"]').eq(0).find('.js-precio-producto').val(ui.item.todo.external.precio_venta);

			      	$.app.formularios.bind('#VentaAdminAddForm');

			      }
			    });

			},
			crear_cliente: function(res){

				$('#success-mensaje-cliente').html('200: Cliente creado exitosamente.');
				$('#success-mensaje-cliente').parents('.alert').eq(0).removeClass('hidden');

				$('#VentaClienteNombre').val('');
				$('#VentaClienteApellido').val('');
				$('#VentaClienteEmail').val('');
				$('#VentaClienteRut').val('');
				$('#VentaClienteTelefono').val('');

				$('#VentaVentaClienteId').val(res.response.cliente.id);
				$('#obtener_cliente').val(res.response.cliente.email);

				setTimeout(function(){
					$('#modalCrearCliente').modal('hide');
					$('#success-mensaje-cliente').html('');
					$('#error-mensaje-cliente').html('');

					$('#VentaVentaClienteId').val(res.response.cliente.id);
					$('#obtener_cliente').val(res.response.cliente.email);
					$('#VentaRutReceptor').val(res.response.cliente.rut);
				    $('#VentaNombreReceptor').val(res.response.cliente.nombre + ' ' + res.response.cliente.apellido);

				}, 1500);				
				
			},
			autocompletar_clientes: function(){

				    $( "#obtener_cliente" ).autocomplete({
				      source: function( request, response ) {
				        $.ajax( {
				          url: webroot + 'api/clientes.json',
				          data: {
				          	token : $('#VentaAccessToken').val(),
				          	limit: 40,
				            email: request.term
				          },
				          success: function( data ) {

				          	var $result = [];
				          	
				          	for (var i = 0; i < data.clientes.length; i++) {

				          		$result[i] = {
				          			todo : data.clientes[i].VentaCliente,
				          			label :  data.clientes[i].VentaCliente.nombre + ' ' + data.clientes[i].VentaCliente.apellido + ' <' + data.clientes[i].VentaCliente.email + '>',
				          			value :  data.clientes[i].VentaCliente.email,
				          			id :  data.clientes[i].VentaCliente.id
				          		}
				          	}
				            response( $result );
				          }
				        } );
				      },
				      minLength: 5,
				      select: function( event, ui ) {

				      	$('#VentaVentaClienteId').val(ui.item.id);

				      	$('#VentaRutReceptor').val(ui.item.todo.rut);
				      	$('#VentaNombreReceptor').val(ui.item.todo.nombre + ' ' + ui.item.todo.apellido);

				      }
				    } );
				
			},
			clonarElemento: function($ths){

				var $contexto = $ths.parents('.panel,.table').eq(0).find('.clone-tr').eq(0);
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

				      	$.app.formularios.bind('#VentaAdminAddForm');

					});

				});
			},
			clonar: function() {		

				$(document).on('click', '#VentaAdminAddForm .duplicate_tr, #VentaAdminAddForm .copy_tr',function(e){

					e.preventDefault();

					venta.clonarElemento($(this));

				});


				$(document).on('click', '.remove_tr', function(e){

					e.preventDefault();

					var $th = $(this).parents('tr').eq(0);

					$th.fadeOut('slow', function() {
						$th.remove();
					});

				});
			},
			seleccion_masiva_factura: function(){
				$(document).on('click', '#seleccionar-todo', function(){

					if ($(this).is(':checked')) {
						$(this).parents('table').eq(0).find('.facturacion_masiva:not(disabled)').click();
					}else{
						$(this).parents('table').eq(0).find('.facturacion_masiva:not(disabled)').click();
					}
				});
			},
			init: function(){

				if ($('#preparacion_index').length) {
					
					$('.loader').addClass('show');
					
					obtener_ventas_preparacion_busqueda();

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
					    obtener_ventas_preparacion_busqueda();
					}, 120000);

					$(document).on('click', '.js-open-set-picking-revision', function(){

						$.app.formularios.bind('#VentaRevisionForm');

						$('.js-revision-form').removeClass('hidden');

						$(this).parents('.modal').eq(0).modal('handleUpdate');
						
						$(this).parents('.modal').eq(0).animate({ 
							scrollTop: $('.js-revision-form').offset().top 
						}, 300);

					});

					$(document).on('click', '.js-close-set-picking-revision', function(e){
						e.preventDefault();

						$('.js-revision-form').addClass('hidden');

					});

					$(document).on('submit', '#VentaRevisionForm', function(e){
						e.preventDefault();
						var form = $(this);
						
						if (!form.valid()) {
							return false;
						}

						cambiar_venta_estado_en_revision($(this));
						
					});

				}
				
				venta.seleccion_masiva_factura();

				$(document).on('click', '#filtro-venta-btn', function(){
					obtener_ventas_preparacion_busqueda();
				});

				$(document).on('click', '.js-venta-ver-mas', function(){
					$('.loader').addClass('show');
					obtener_ventas_preparacion_modal($(this).data('id'));
				});

				$('#refrescar_manualmente').on('click', function(){
					$('.loader').addClass('show');
					$('#refrescar_manualmente').addClass('fa-spin');
					obtener_ventas_preparacion_busqueda();
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

				$(document).on('click', '.js-generar-etiqueta-venta-dte', function(e){
					e.preventDefault();
					if (!ejecutando) {
						cerrar_modal();
						generar_etiqueta_dte($(this));
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


				$('.toggle-comuna').on('click', function(e){
					
					$(this).find('.fa').toggle();
					$(this).parents('td').eq(0).find('.comuna-select').toggleClass('hide');

					if ($(this).parents('td').eq(0).find('.comuna-select').hasClass('hide')) {
						$('.js-comuna-entrega').val( $('.js-comuna-entrega').data('value') );
					}

				});

				$('.toggle-metodo-envio').on('click', function(e){
					
					$(this).find('.fa').toggle();
					$(this).parents('td').eq(0).find('.metodo-envio-select').toggleClass('hide');

					if ($(this).parents('td').eq(0).find('.metodo-envio-select').hasClass('hide')) {
						$('.js-metodo-envio-entrega').val( $('.js-metodo-envio-entrega').data('value') );
					}

				});

				$('.toggle-direccion').on('click', function(e){
					
					$(this).find('.fa').toggle();
					$(this).parents('td').eq(0).find('.direccion-select').toggleClass('hide');

					if ($(this).parents('td').eq(0).find('.direccion-select').hasClass('hide')) {
						$('#VentaDireccionEntrega').attr('readonly', 'readonly');
						$('.js-direccion-entrega').val( $('.js-direccion-entrega').data('value') );
					}else{
						$('#VentaDireccionEntrega').removeAttr('readonly');
					}

				});

				$('.toggle-numero-entrega').on('click', function(e){
					
					$(this).find('.fa').toggle();
					$(this).parents('td').eq(0).find('.numero-entrega-select').toggleClass('hide');

					if ($(this).parents('td').eq(0).find('.numero-entrega-select').hasClass('hide')) {
						$('#VentaNumeroEntrega').attr('readonly', 'readonly');
						$('.js-numero-entrega').val( $('.js-numero-entrega').data('value') );
					}else{
						$('#VentaNumeroEntrega').removeAttr('readonly');
					}

				});


				$('.toggle-otro-entrega').on('click', function(e){
					
					$(this).find('.fa').toggle();
					$(this).parents('td').eq(0).find('.otro-entrega-select').toggleClass('hide');

					if ($(this).parents('td').eq(0).find('.otro-entrega-select').hasClass('hide')) {
						$('#VentaOtroEntrega').attr('readonly', 'readonly');
						$('.js-otro-entrega').val( $('.js-otro-entrega').data('value') );
					}else{
						$('#VentaOtroEntrega').removeAttr('readonly');
					}

				});

				$('.js-comuna-select').on('change', function(){
					var val = $(this).val();
					if (val != '') {
						$('.js-comuna-entrega').val(val);
					}

				});

				if ($('.copy_tr').length) {
					venta.clonar();
				}


				if ($('#VentaAdminAddForm').length) {

					$('.js-metodo-envios-ajax').on('change', function(){
						var $id = $(this).val();
						if ($id != '') {
							obtener_metodo_envio_venta($id);
						}
					});

					if ($('.js-metodo-envios-ajax').val() != '') {
						obtener_metodo_envio_venta($('.js-metodo-envios-ajax').val());
					}

				}
				
				let primera = true;

				if ($('#VentaEditForm').length) {

					$.app.formularios.bind('#VentaEditForm', objForm);

					$('.js-metodo-envios-ajax').on('change', function(){
						var $id = $(this).val();
						if ($id != '') {
							obtener_metodo_envio_venta($id);
						}
					});

					if (primera) {
					
						let $id = document.querySelector('#MetodoEnvio').value;
						if ($id != '') {
							obtener_metodo_envio_venta_v2($id);
						}
						primera= false;
					}

				}
				$("#VentaEditButtom").on('click',function () {
					let form = $( "#VentaEditForm" );
					if (form.valid()) {
						$.app.loader.mostrar()
					}
				
				})

				if ($('#obtener_cliente').length) {
					venta.autocompletar_clientes();
				}

				if ($('#obtener_producto').length) {
					venta.autocompletar_productos();
				}

				if ($('#VentaClienteAdminAddForm').length) {

					var objForm = {
			      		ignore : ':hidden:not(#VentaComunaEntrega)',
			      		errorPlacement: function(error, element) {
					    	if($(element).prop("id") === "VentaComunaEntrega") {
						        error.insertAfter($(element).siblings('.bootstrap-select').eq(0));
						    }
						    else {
						        error.insertAfter(element); // default error placement.
						    }
					  	}
			      	};

			      	$.app.formularios.bind('#VentaAdminAddForm', objForm);

					$(document).on('submit', '#VentaClienteAdminAddForm', function(e){
						e.preventDefault();
						var form = $(this);
						if (!form.valid()) {
							return false;
						}

						clientes.add(
							function(res){
								venta.crear_cliente(res);
							},
							function(err){
								$('#error-mensaje-cliente').html(err.responseJSON.code + ': ' + err.responseJSON.message);
								$('#error-mensaje-cliente').parents('.alert').eq(0).removeClass('hidden');
							}
						);

					});	

					$(document).on('click', '.pago_tr', function(e){
						e.preventDefault();

						venta.clonarElemento( $(this) );
					});

					calcular_monto_pagar();
				}

				pasos_compra();

				$('.btn-expandir-venta').on('click', function(){
					$(this).parents('tr').eq(0).toggleClass('expanded');
					$(this).parents('tr').eq(0).next().toggleClass('expanded-div');
				});

			}
		}	

		

	}();

	venta.init();

});