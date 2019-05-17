$(function() {

	var venta = function(){

		var ejecutando = false;

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

		return {
			init: function(){

				$('.js-generar-documentos-venta-modal').on('click', function(e){
					e.preventDefault();
					if (!ejecutando) {

						consultar_dte();

						//levantar_modal();
					}
				});

				$('.js-generar-documentos-venta').on('click', function(e){
					e.preventDefault();
					if (!ejecutando) {
						cerrar_modal();
						generar_documento($(this));
					}
				});

				$('.mb-control-close').on('click', function(){
					cerrar_modal();
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

			}
		}	

	}();

	venta.init();

});