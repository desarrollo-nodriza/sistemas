var pagado_ocf    = 0;
var facturado_ocf = 0;
var oc_seleccionada = 0;


$.extend({
	ordenCompraFacturas: {
		bind: function(){

			pagado_ocf    = $('#total-asignado').data('pagado');
			facturado_ocf = $('#total-facturado').data('facturado');

			// Asignar
			$(document).on('click', '.btn-usar-pago', function(){

				var nwtr                = $(this).parents('tr').eq(0).clone();
				
				$(this).parents('tr').eq(0).addClass('hidden');

				nwtr.find('.btn-usar-pago').addClass('hidden');
				nwtr.find('.btn-quitar-pago').removeClass('hidden');

				$('#pagos-asignados-contenedor').append(nwtr);

			});

			// Quitar
			$(document).on('click', '.btn-quitar-pago', function(){
				
				var tr = $(this).parents('tr').eq(0); console.log(tr.data('id')); console.log($('tr[data-id=' + tr.data('id') + ']'));
				var trOriginal = $('tr[data-id=' + tr.data('id') + ']').removeClass('hidden');

				tr.remove();
				
			});

		},
		indice: function(){
			// Seleccionar facturas
			$('.js-factura-id').on('click', function(){
				var $ths = $(this),
					id    = $ths.data('id'),
					id_oc = $ths.data('oc');

				
				if (oc_seleccionada == 0) {
					oc_seleccionada = id_oc;
				}

				if (oc_seleccionada != id_oc) {
					noty({text: 'Debe seleccionar facturas de la OC #' + oc_seleccionada, layout: 'topRight', type: 'error'});
					$ths.prop('checked', false);
					return;
				}

				if ($ths.is(':checked')) {

					var nwinput = $ths.clone();

					nwinput.addClass('hidden');

					$('#formulario-facturas-pago-masivo').append(nwinput);
				}else{
					$('#formulario-facturas-pago-masivo').find('input[data-id="' + id + '"]').remove();
				}

				if ($('#formulario-facturas-pago-masivo .js-factura-id').length == 0) {
					oc_seleccionada = 0;
				}

			});
		},
		init: function(){
			if ($('#OrdenCompraFacturaAdminViewForm').length) {
				$.ordenCompraFacturas.bind();
			}

			if ($('.js-factura-id').length) {
				$.ordenCompraFacturas.indice();
			}
		}
	}
});


$(document).ready(function(){
	$.ordenCompraFacturas.init();
});