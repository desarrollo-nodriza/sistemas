$.extend({
	revision_oc : {
		validar: function(){
			$('.js-oc-proveedor').validate();

			$('.js-oc-proveedor input, .js-validate-oc select').each(function(){

				var $ths = $(this);

				if ($ths.hasClass('not-blank')) {
					$ths.rules("add", {
				        required: true,
				        messages: {
				        	required: 'Campo requerido'
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

			});
		},
		bind: function(){
			$(document).on('change', '.js-cantidad', function(){

				var contexto = $(this).parents('tr').eq(0),
					cantidad = contexto.data('cantidad'),
					valor    = $(this).val();
					
				if (valor == 0) {
					contexto.find('.js-opcion option[value="stockout"]').prop('selected', true);
					contexto.find('.js-cantidad').removeAttr('readonly');
					contexto.find('.js-cantidad').rules("remove", "min max");
					contexto.find('.js-wrapper-nota').removeClass('hidden');
					contexto.find('.js-nota').rules("add", {
				        required: true,
				        messages: {
				        	required: 'Indique si el producto estará disponible pronto.'
				        }
				    });
				}else if(valor == cantidad) {
					contexto.find('.js-opcion option[value="accept"]').prop('selected', true);
					contexto.find('.js-cantidad').attr('readonly', 'readonly');	
					contexto.find('.js-nota').rules("remove", "required");
					contexto.find('.js-cantidad').rules("remove", "min max");
					contexto.find('.js-nota').val('');
					contexto.find('.js-nota').removeClass('error');
					contexto.find('.js-wrapper-nota').addClass('hidden');
				}

			});


			$(document).on('change', '.js-opcion', function(){

				var contexto = $(this).parents('tr').eq(0),
					cantidad = contexto.data('cantidad'),
					valor    = $(this).val();
					
				if (valor == 'stockout') {

					contexto.find('.js-cantidad').val(0);
					contexto.find('.js-cantidad').attr('readonly', 'readonly');
					contexto.find('.js-cantidad').rules("remove", "min max");
					contexto.find('.js-wrapper-nota').removeClass('hidden');
					contexto.find('.js-nota').rules("add", {
				        required: true,
				        messages: {
				        	required: 'Indique si el producto estará disponible pronto.'
				        }
				    });

				}else if(valor == 'accept') {

					contexto.find('.js-cantidad').val(cantidad);
					contexto.find('.js-cantidad').attr('readonly', 'readonly');
					//contexto.find('.js-cantidad').removeAttr('readonly');			
					contexto.find('.js-nota').rules("remove", "required");
					contexto.find('.js-cantidad').rules("remove", "min max");
					contexto.find('.js-nota').val('');
					contexto.find('.js-nota').removeClass('error');
					contexto.find('.js-wrapper-nota').addClass('hidden');

				}else if(valor == 'modified') {
					
					contexto.find('.js-cantidad').val('');

					contexto.find('.js-cantidad').rules('add', {
						min: 1,
						max: cantidad,
						messages: {
							min : 'La cantidad debe ser mayor a 0 o seleccione "Sin stock"',
							max : 'La cantidad debe ser menor a ' + cantidad  
						}
					});

					contexto.find('.js-cantidad').removeAttr('readonly');			
					contexto.find('.js-wrapper-nota').removeClass('hidden');
					contexto.find('.js-nota').rules("add", {
				        required: true,
				        messages: {
				        	required: 'Indique el motivo de la diferencia de cantidades.'
				        }
				    });
				}else if (valor == 'price_error') {
					contexto.find('.js-cantidad').val(0);
					contexto.find('.js-cantidad').attr('readonly', 'readonly');
					contexto.find('.js-wrapper-nota').removeClass('hidden');
					contexto.find('.js-cantidad').rules("remove", "min max");
					contexto.find('.js-nota').rules("add", {
				        required: true,
				        messages: {
				        	required: 'Especifique el error de precio'
				        }
				    });
				}

			});


			$('#rechazar-todo').on('click', function(e){
				e.preventDefault();

				var contexto = $(this).parents('table').eq(0);

				contexto.find('tbody > tr').each(function(){

					var tr = $(this),
						cantidad = tr.data('cantidad');

					tr.find('.js-opcion option[value="price_error"]').prop('selected', true);
					tr.find('.js-cantidad').val(cantidad);
					tr.find('.js-cantidad').attr('readonly', 'readonly');
					tr.find('.js-wrapper-nota').removeClass('hidden');
					tr.find('.js-nota').rules("add", {
				        required: true,
				        messages: {
				        	required: 'Especifique el error de precio'
				        }
				    });
				});


			});
		},
		init: function(){
			
			$.revision_oc.bind();

			if ($('.js-oc-proveedor').length) {
				$.revision_oc.validar();
			}
		}
	}
});

$(document).on('ready', function(){
	$.revision_oc.init();
});