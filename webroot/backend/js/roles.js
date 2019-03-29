$.extend({

	roles: {
		validar: function() {
			
			$('.js-validate-roles').validate({
				rules : {
					'data[Rol][nombre]' : {
						required : true,
						maxlength : 200
					},
					'data[Rol][descuento_base]' : {
						number: true
					},
					'data[Rol][email_contacto]' : {
						email: true
					},
					'data[Rol][fono_contacto]' : {
						number: true
					},
					'data[Rol][rut_empresa]' : {
						rut: true
					}
				}
			});


			$('.not-blank').each(function(){
				$(this).rules("add", {
			        required: true,
			        messages: {
			        	required: 'Seleccione moneda'
			        }
			    });
			});

			$('.is-email').each(function(){
				$(this).rules("add", {
			        email: true,
			        messages: {
			        	email: 'Ingrese un email válido'
			        }
			    });
			});
		

			$('.js-descuento-input').each(function(){
				$(this).rules("add", {
			        required: true,
			        digits : true,
			        min: 1,
			        max: 100,
			        messages: {
			        	required: 'Ingrese descuento',
			        	digits: 'Solo números',
			        	min: 'Descuento debe ser mayor a 0',
			        	max: 'Descuento debe ser menor a 101'
			        }
			    });
			});
			
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


					if ($that.hasClass('not-blank')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Seleccione moneda'
					        }
					    });
					}

					if ($that.hasClass('is-email')) {
						$that.rules("add", {
					        email: true,
					        messages: {
					        	email: 'Ingrese un email válido'
					        }
					    });
					}


					if ($that.hasClass('js-descuento-input')) {
						$that.rules("add", {
					        required: true,
					        digits : true,
					        min: 1,
					        max: 100,
					        messages: {
					        	required: 'Ingrese descuento',
					        	digits: 'Solo números',
					        	min: 'Descuento debe ser mayor a 0',
					        	max: 'Descuento debe ser menor a 101'
					        }
					    });
					}


				});

			});
		},
		clonar: function() {		

			$(document).on('click', '.duplicate_tr, .copy_tr',function(e){

				e.preventDefault();

				$.roles.clonarElemento($(this));

			});


			$(document).on('click', '.remove_tr', function(e){

				e.preventDefault();

				var $th = $(this).parents('tr').eq(0);

				$th.fadeOut('slow', function() {
					$th.remove();
				});

			});
		},
		init: function(){

			if ($('.js-validate-roles').length ) {
				$.roles.validar();
				$.roles.clonar();
			}

		}
	}

});


$(document).ready(function(){
	$.roles.init();
});