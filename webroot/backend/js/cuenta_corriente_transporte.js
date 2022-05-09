var cuenta_corriente_transporte = function () {
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
                mostrar_segun_dependencia()
            });

        },
        init: function () {
            if ($('.js-select-dependencia').length) {

                cuenta_corriente_transporte.bind();
            }
        }
    }
}();



var mostrar_segun_dependencia = function () {

    if ($('.js-select-dependencia').val() == 'starken') {

        // Desactivamos las otras dependencias
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-conexxion'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-boosmap'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-blueexpress'));

        // Activamos la dependencia
        cuenta_corriente_transporte.activarDependencia($('.js-panel-starken'));
    }

    if ($('.js-select-dependencia').val() == 'conexxion') {
        // Desactivamos las otras dependencias
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-starken'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-boosmap'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-blueexpress'));

        // Activamos la dependencia
        cuenta_corriente_transporte.activarDependencia($('.js-panel-conexxion'));
    }

    if ($('.js-select-dependencia').val() == 'boosmap') {
        // Desactivamos las otras dependencias
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-starken'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-conexxion'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-blueexpress'));
        // Activamos la dependencia
        cuenta_corriente_transporte.activarDependencia($('.js-panel-boosmap'));
    }

    if ($('.js-select-dependencia').val() == 'blueexpress') {

        // Desactivamos las otras dependencias
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-conexxion'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-boosmap'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-starken'));
        // Activamos la dependencia
        cuenta_corriente_transporte.activarDependencia($('.js-panel-blueexpress'));
    }

    if ($('.js-select-dependencia').val() == '') {
        // Desactivamos las otras dependencias
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-conexxion'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-boosmap'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-starken'));
        cuenta_corriente_transporte.desactivarDependencia($('.js-panel-blueexpress'));

    }

}

$(document).ready(function () {
    $.app.formularios.bind('#CuentaCorrienteTransporteAdminEditForm');
    cuenta_corriente_transporte.init();
})