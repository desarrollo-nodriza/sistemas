var metodo_envios = function () {
	return {
		desactivarDependencia: function (obj) {
			obj.addClass('hidden');
			obj.find('.form-control').each(function () {
				$(this).attr('disabled', 'disabled');
			});
		},
		activarDependencia: function (obj) {
			obj.removeClass('hidden');
			obj.find('.form-control').each(function () {
				$(this).removeAttr('disabled');
			});
		},
		bind: function () {
			mostrar_segun_dependencia();

			$(document).on('change', '.js-select-dependencia', function () {
				mostrar_segun_dependencia();
				mostrar_solo_a_dependencias();
			});

		},
		init: function () {
			if ($('.js-select-dependencia').length) {
				mostrar_solo_a_dependencias();
				metodo_envios.bind();
			}
		}
	}
}();

var campos_requeridos_blue_express = function (opcion) {
	$.app.formularios.bind('#MetodoEnvioAdminEditForm');
	if (opcion) {
		$('#MetodoEnvioTokenBlueExpress').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$('#MetodoEnvioClaveBlueExpress').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$('#MetodoEnvioUsuarioBlueExpress').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$('#MetodoEnvioCodUsuarioBlueExpress').rules("add", {
			required: true,
			messages: {
				required: 'Campo requerido'
			}
		});
		$('#MetodoEnvioCtaCorrienteBlueExpress').rules("add", {
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

	} else {
		$("#MetodoEnvioTokenBlueExpress").attr("required", false);
		$("#MetodoEnvioClaveBlueExpress").attr("required", false);
		$("#MetodoEnvioUsuarioBlueExpress").attr("required", false);
		$("#MetodoEnvioCodUsuarioBlueExpress").attr("required", false);
		$("#MetodoEnvioCtaCorrienteBlueExpress").attr("required", false);
		$("#MetodoEnvioBodegaId").attr("required", false);
	}

}

var mostrar_segun_dependencia = function () {

	campos_requeridos_blue_express(false)

	if ($('.js-select-dependencia').val() == 'starken') {
		// Desactivamos las otras dependencias
		metodo_envios.desactivarDependencia($('.js-panel-conexxion'));
		metodo_envios.desactivarDependencia($('.js-panel-boosmap'));
		metodo_envios.desactivarDependencia($('.js-panel-blueexpress'));

		// Activamos la dependencia
		metodo_envios.activarDependencia($('.js-panel-starken'));
	}

	if ($('.js-select-dependencia').val() == 'conexxion') {
		// Desactivamos las otras dependencias
		metodo_envios.desactivarDependencia($('.js-panel-starken'));
		metodo_envios.desactivarDependencia($('.js-panel-boosmap'));
		metodo_envios.desactivarDependencia($('.js-panel-blueexpress'));

		// Activamos la dependencia
		metodo_envios.activarDependencia($('.js-panel-conexxion'));
	}

	if ($('.js-select-dependencia').val() == 'boosmap') {
		// Desactivamos las otras dependencias
		metodo_envios.desactivarDependencia($('.js-panel-starken'));
		metodo_envios.desactivarDependencia($('.js-panel-conexxion'));
		metodo_envios.desactivarDependencia($('.js-panel-blueexpress'));
		// Activamos la dependencia
		metodo_envios.activarDependencia($('.js-panel-boosmap'));
	}

	if ($('.js-select-dependencia').val() == 'blueexpress') {
		campos_requeridos_blue_express(true)
		
		// Desactivamos las otras dependencias
		metodo_envios.desactivarDependencia($('.js-panel-conexxion'));
		metodo_envios.desactivarDependencia($('.js-panel-boosmap'));
		metodo_envios.desactivarDependencia($('.js-panel-starken'));
		// Activamos la dependencia
		metodo_envios.activarDependencia($('.js-panel-blueexpress'));
	}

	if ($('.js-select-dependencia').val() == '') {
		// Desactivamos las otras dependencias
		metodo_envios.desactivarDependencia($('.js-panel-conexxion'));
		metodo_envios.desactivarDependencia($('.js-panel-boosmap'));
		metodo_envios.desactivarDependencia($('.js-panel-starken'));
		metodo_envios.desactivarDependencia($('.js-panel-blueexpress'));

	}

}

let mostrar_solo_a_dependencias = function () {

	$.app.formularios.bind('#MetodoEnvioAdminEditForm');

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

$(document).ready(function () {
	metodo_envios.init();
});