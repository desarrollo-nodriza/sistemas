

$.extend({
	metodo_envios: {		
		clonarElemento: function($ths){

			var $contexto = $ths.parents('.panel').eq(0).find('.clone-tr').eq(0);
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

					if ( typeof($that.attr('name')) != 'undefined' ) {

						nombre		= $that.attr('name').replace(/(999)/g, (indx));
						$that.attr('name', nombre);

					}

					if ($that.hasClass('not-blank')) {
						$that.rules("add", {
					        required: true,
					        messages: {
					        	required: 'Campo requerido'
					        }
					    });
					}

				});

			});
		},
		clonar: function() {		

			$(document).on('click', '.duplicate_tr, .copy_tr',function(e){

				e.preventDefault();

				$.metodo_envios.clonarElemento($(this));
				page_content_onresize();

			});


			$(document).on('click', '.remove_tr', function(e){

				e.preventDefault();

				var $th = $(this).parents('tr').eq(0);

				$th.fadeOut('slow', function() {
					$th.remove();
					page_content_onresize();
				});

			});
		},
		init: function(){
			if ( $('#MetodoEnvioNotificaciones').length ) {
				$.metodo_envios.clonar();
			}
		}
	}
});


$(document).ready(function () {

	$.metodo_envios.init();

	$.app.formularios.bind('#MetodoEnvioAdminEditForm');
	$.app.formularios.bind('#MetodoEnvioNotificaciones');

	$('#MetodoEnvioEmbaladoVentaEstadoId').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});
	$('#MetodoEnvioEmbaladoVentaEstadoParcialId').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$('#MetodoEnvioBodegaId').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$('#MetodoEnvioCuentaCorrienteTransporteId').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$('#MetodoEnvioConsolidacionVentaEstadoId').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$('#MetodoEnvioPesoMaximo').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$('#MetodoEnvioPesoDefault').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$('#MetodoEnvioAltoDefault').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$('#MetodoEnvioAnchoDefault').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});
	
	$('#MetodoEnvioLargoDefault').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$('#MetodoEnvioVolumenMaximo').rules("add", {
		required: true,
		messages: {
			required: 'Campo requerido'
		}
	});

	$(document).on('change', '#MetodoEnvioGenerarOt', function () {

		if ($('#MetodoEnvioGenerarOt').prop('checked')) {

			$(".cuenta_corriente_transporte_id").removeClass('hidden');

		} else {
			$(".cuenta_corriente_transporte_id").addClass('hidden');

		}

	});

	$(document).on('click', '.clone-boton', function (e) {
		e.preventDefault();

		let clone_tr = document.getElementsByClassName("clone-tr");
		if (clone_tr.length > 0) {
			let elementoremoveClass = clone_tr.item(0);
			elementoremoveClass.removeAttribute('class')
			const classes_2 = elementoremoveClass.classList
			classes_2.add("nuevo_elemento");
			classes_2.add("fila");
		}
	});

	$(document).on('click', '.remove-tr', function (e) {

		e.preventDefault();
		var $th = $(this).parents('tr').eq(0);

		$th.fadeOut('slow', function () {
			$th.remove();
			ordenar();
		});
	});

	$("#sortable tbody").sortable({
		cursor: "move",
		placeholder: "sortable-placeholder",
		helper: function (e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function (index) {
				// Set helper cell sizes to match the original sizes
				$(this).width($originals.eq(index).width());

			});

			return $helper;
		},
		stop: function (event, ui) {
			ordenar();
		}
	}).disableSelection();

	function ordenar() {
		const tableRows = document.querySelectorAll('#sortable tr.fila');

		for (let i = 0; i < tableRows.length; i++) {
			const row = tableRows[i];
			const status = row.querySelector('.orden');
			console.log('orden-antes: ', status.value);
			status.value = (i + 1)
			console.log('orden-dsps: ', status.value);

		}
	}
});
