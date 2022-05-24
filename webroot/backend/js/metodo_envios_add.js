

$(document).ready(function () {


	$.app.formularios.bind('#MetodoEnvioAdminAddForm');
	
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
});