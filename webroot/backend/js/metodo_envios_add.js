$(document).on('change', '.js-select-dependencia', function () {
	mostrar_segun_dependencia();
});


let mostrar_segun_dependencia = function () {

	$.app.formularios.bind('#MetodoEnvioAdminAddForm');

	if ($('.js-select-dependencia').val() != '') {

		$('#MetodoEnvioPesoMaximo').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$("#peso_maximo").removeClass('hidden');

		$('#MetodoEnvioPesoDefault').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$("#peso_default").removeClass('hidden');

		$('#MetodoEnvioAltoDefault').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$("#alto_default").removeClass('hidden');

		$('#MetodoEnvioAnchoDefault').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$("#ancho_default").removeClass('hidden');

		$('#MetodoEnvioLargoDefault').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$("#largo_default").removeClass('hidden');

		$('#MetodoEnvioVolumenMaximo').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});

		$("#volumen_maximo").removeClass('hidden');
		
	} else {

		$("#MetodoEnvioPesoMaximo").attr("required", false);
		$("#MetodoEnvioPesoDefault").attr("required", false);
		$("#MetodoEnvioAltoDefault").attr("required", false);
		$("#MetodoEnvioAnchoDefault").attr("required", false);
		$("#MetodoEnvioLargoDefault").attr("required", false);
		$("#MetodoEnvioVolumenMaximo").attr("required", false);

		$("#peso_maximo").addClass('hidden');
		$("#peso_default").addClass('hidden');
		$("#alto_default").addClass('hidden');
		$("#ancho_default").addClass('hidden');
		$("#largo_default").addClass('hidden');
		$("#volumen_maximo").addClass('hidden');
	}

}

