$.extend({

	producto: {
		validar: function() {

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

			$(".timepicker").timepicker();
			$(".timepicker24").timepicker({minuteStep: 5,showSeconds: true,showMeridian: false});
			
			$('.js-validate-producto').validate({
				rules : {
					'data[VentaDetalleProducto][nombre]' : {
						required : true,
						maxlength : 200
					},
					'data[VentaDetalleProducto][marca_id]' : {
						required : true
					},
					'data[VentaDetalleProducto][PrecioEspecifico]' : {
						number: true
					},
					'data[VentaDetalleProducto][precio_costo]' : {
						number: true,
						required: true
					},
					'data[VentaDetalleProducto][cantidad_virtual]' : {
						digits : true
					},
					'data[Bodega][1][bodega_id]' : {
						required: true
					},
					'data[Bodega][1][cantidad]' : {
						required: true,
						digits: true,
						min: 1
					},
					'data[PrecioEspecificoProducto][0][nombre]' : {
						required: true
					},
					'data[PrecioEspecificoProducto][0][tipo_descuento]' : {
						required: true
					},
					'data[PrecioEspecificoProducto][0][descuento]' : {
						required: true,
				        number : true,
				        min: 1,
					},
					'data[PrecioEspecificoProducto][0][fecha_inicio]' : {
						required: true,
						date: true
					},
					'data[PrecioEspecificoProducto][0][hora_inicio]' : {
						required: true,
						time: true
					},
					'data[PrecioEspecificoProducto][0][fecha_termino]' : {
						required: true,
						date: true
					},
					'data[PrecioEspecificoProducto][0][hora_termino]' : {
						required: true,
						time: true
					},
				}
			});


			if ( $('.not-blank').length ) {
				$('.not-blank').rules("add", {
			        required: true,
			        messages: {
			        	required: 'Requerido'
			        }
			    });
			}

			if ( $('.is-number').length ) {
				$('.is-number').rules("add", {
			        number: true,
			        messages: {
			        	number: 'Solo números'
			        }
			    });
			}
					
		},
		clonarElemento: function($ths){

			$contexto = $ths.parents('.panel').eq(0).find('.clone-tr').eq(0);
			console.log($contexto);
			var newTr = $contexto.clone();


			newTr.removeClass('hidden');
			newTr.removeClass('clone-tr');
			newTr.find('input, select, textarea').each(function(){
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

					if ($that.hasClass('datepicker')) {
						$that.datepicker({
							language	: 'es',
							format		: 'yyyy-mm-dd'
						});
					}


					if ($that.hasClass('timepicker24')) {
						$that.timepicker({minuteStep: 5,showSeconds: true,showMeridian: false});
					}

					if ($that.hasClass('js-bodega-id')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Seleccione bodega'
					        }
					    });
					}

					if ($that.hasClass('js-bodega-cantidad')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Ingrese la cantidad',
								digits: 'Solo números',
								min: 'Debe ser mayor a 0'
					        }
					    });
					}


					if ($that.hasClass('js-nombre-producto')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Ingrese nombre del descuento'
					        }
					    });
					}

					if ($that.hasClass('not-blank')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Requerido'
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


				});

			});
		},
		clonar: function() {		

			$(document).on('click', '.duplicate_tr, .copy_tr',function(e){

				e.preventDefault();

				$.producto.clonarElemento($(this));

			});


			$(document).on('click', '.remove_tr', function(e){

				e.preventDefault();

				var $th = $(this).parents('tr').eq(0);

				$th.fadeOut('slow', function() {
					$th.remove();
				});

			});
		},
		ajuste_inventario: function(){

            	var validator = $('#VentaDetalleProductoAdminAjustarInventarioForm').validate({
            		rules: {
            			'tipo_ajuste': {
            				required: true
            			}
            		},
            		messages: {
            			'tipo_ajuste': {
            				required: 'Seleccione un tipo de ajuste'
            			}
            		}
            	});


                //Check count of steps in each wizard
                $("#wizard-ajuste > ul").each(function(){
                    $(this).addClass("steps_"+$(this).children("li").length);
                });//end

                $("#wizard-ajuste").smartWizard({
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
                        var wizard = obj.parents("#wizard-ajuste");

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

                                return false;
                            }

                        }

                        return true;

                        
                    },// <-- TO

                    //This is important part of wizard init
                    onShowStep: function(obj){
                        var wizard = obj.parents("#wizard-ajuste");

                        if(wizard.hasClass("show-submit")){

                            var step_num = obj.attr('rel');
                            var step_max = obj.parents(".anchor").find("li").length;

                            if(step_num == step_max){
                                obj.parents("#wizard-ajuste").find(".actionBar .btn-primary").css("display","block");
                            }
                        }

                        page_content_onresize();

                        return true;
                    }//End
                });


                $('#VentaDetalleProductoTipoAjusteAjustePrecio').on('change', function(){

                	if ($(this).is(':checked')) {
                		$('#lista-movimientos').removeClass('hidden');
                	}

                });

                $('#VentaDetalleProductoTipoAjusteAjusteNormal').on('change', function(){

                	if ($(this).is(':checked')) {
                		$('#lista-movimientos').addClass('hidden');
                		$('#lista-movimientos').find('input[name="movimiento_usar"]:checked').prop('checked', false);
                	}

                });

                $('input[name="movimiento_usar"]').on('change', function(){
                	if ($(this).is(':checked')) {

                		var costo = $(this).parents('tr').eq(0).find('.js-costo').data('value');

                		$('.js-costo-pmp').each(function(){
                			$(this).val(costo);
                		});
                	}
                });

                $('.js-usar-pmp').on('click', function(){
                	var input  = $(this).siblings('.js-costo-pmp');
                	input.val($(this).data('value'));
                });

		},
		init: function(){

			if ($('.js-validate-producto').length ) {
				$.producto.validar();
				$.producto.clonar();
			}

			if ($('#wizard-ajuste').length) {
				$.producto.ajuste_inventario();
			}

		}
	}

});


$(document).ready(function(){
	$.producto.init();
});