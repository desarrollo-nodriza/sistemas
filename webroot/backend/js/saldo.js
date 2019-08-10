$.extend({
	saldo : {
		bind: function(){

			$('.js-form-saldo').validate();
				
			$('.js-form-saldo input, .js-form-saldo select').each(function(){

				var $ths = $(this);

				if ($ths.hasClass('not-blank')) {
					$ths.rules("add", {
				        required: true,
				        messages: {
				        	required: 'Campo requerido'
				        }
				    });
				}

				if ($ths.hasClass('is-rut')) {
					$ths.rules("add", {
				        rut: true,
				        messages: {
				        	rut: 'Ingrese un rut válido'
				        }
				    });
				}

				if ($ths.hasClass('is-email')) {
					$ths.rules("add", {
				        email: true,
				        messages: {
				        	email: 'Formato de email no válido'
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

				if ($ths.hasClass('datepicker')) {
					$ths.datepicker({
						language	: 'es',
						format		: 'yyyy-mm-dd'
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
		init: function(){
			if ($('.js-form-saldo').length) {
				$.saldo.bind();
			}
		}
	}
});


$(document).ready(function(){
	$.saldo.init();
});
