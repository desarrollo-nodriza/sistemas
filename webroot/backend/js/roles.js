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

			var newTr = $contexto.clone();

			newTr.removeClass('hidden');
			newTr.removeClass('clone-tr');
			newTr.find('input, select, textarea').each(function(){
				$(this).removeAttr('disabled');
			});

			// Agregar nuevo campo
			$nw = $contexto.parents('tbody').eq(0).append(newTr);

			$.roles.reordenar();

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

				$('.ddd').nestable({
					maxDepth  : 1,
					rootClass : 'ddd',
					listClass : 'ddd-list',
					itemClass : 'ddd-item',
					handleClass : 'ddd-handle',
					listNodeName : 'tbody',
					itemNodeName : 'tr'
				});

			});


			$(document).on('click', '.remove_tr', function(e){

				e.preventDefault();

				var $th = $(this).parents('tr').eq(0);

				$th.remove();
				$.roles.reordenar();
			
			});
		},
		reordenar: () => {
			let $ddd = $('.ddd');

			if (!$ddd.length)
			{
				return;
			}

			// todos los trs creados
			let trs = $ddd.find('.ddd-item');
			console.log(trs.length);
			// Asignamos los indices
			for (let index = 0; index < trs.length; index++) {
				const element = trs[index];
				$(element).attr('data-id', index);
				$(element).find('.js-orden').val(index);
			}

			return;

		},
		init: function(){

			if ($('.js-validate-roles').length ) {
				$.roles.validar();
				$.roles.clonar();
			}

			$('.ddd').nestable({
				maxDepth  : 1,
				rootClass : 'ddd',
				listClass : 'ddd-list',
				itemClass : 'ddd-item',
				handleClass : 'ddd-handle',
				listNodeName : 'tbody',
				itemNodeName : 'tr'
			});

			$('.ddd').on('change', function() 
			{
				let $that = $(this);
				
				orden  = $that.nestable('serialize');
				
				orden.forEach((posicion, index) => {
					if (posicion.id !== '')
					{
						$that.find('[data-id='+posicion.id+']').find('.js-orden').val(index);
					}
				});

			});

		}
	}

});


$(document).ready(function(){
	$.roles.init();
});