$(function () {
    var boton_guardar_asunto = document.getElementById("guardar-asunto");
    const classes_asunto = boton_guardar_asunto.classList;

    $(document).on('click', '.clone-boton-asunto', function () {
        let clone_tr = document.getElementsByClassName("clone-tr-asunto");
        console.log(clone_tr.length);

        if (clone_tr.length > 0) {
            let elementoremoveClass = clone_tr.item(0);
            elementoremoveClass.removeAttribute('class')
            const classes_2 = elementoremoveClass.classList
            classes_2.add("nuevo_elemento_asunto");
            classes_asunto.remove("hidden");


        }
    });
    $(document).on('click', '.remove-tr-asunto', function (e) {
        let nuevo_elemento_asunto = document.getElementsByClassName("nuevo_elemento_asunto");
        console.log(nuevo_elemento_asunto.length);
        if (nuevo_elemento_asunto.length == 1) {
            classes_asunto.add("hidden");
        } else
            e.preventDefault();

        var $th = $(this).parents('tr').eq(0);

        $th.fadeOut('slow', function () {
            $th.remove();
        });

    });

    var boton_guardar_responsable = document.getElementById("guardar-responsable");
    const classes_responsable = boton_guardar_responsable.classList;

    $(document).on('click', '.clone-boton-responsable', function () {
        let clone_tr = document.getElementsByClassName("clone-tr-responsable");
        console.log(clone_tr.length);

        if (clone_tr.length > 0) {
            let elementoremoveClass = clone_tr.item(0);
            elementoremoveClass.removeAttribute('class')
            const classes_2 = elementoremoveClass.classList
            classes_2.add("nuevo_elemento_responsable");
            classes_responsable.remove("hidden");


        }
    });
    $(document).on('click', '.remove-tr-responsable', function (e) {
        let nuevo_elemento_responsable = document.getElementsByClassName("nuevo_elemento_responsable");
        console.log(nuevo_elemento_responsable.length);
        if (nuevo_elemento_responsable.length == 1) {
            classes_responsable.add("hidden");
        } else
            e.preventDefault();

        var $th = $(this).parents('tr').eq(0);

        $th.fadeOut('slow', function () {
            $th.remove();
        });

    });

    var boton_guardar_administrar_asuntos = document.getElementById("guardar-administrar-asuntos");
    const classes_administrar_asuntos = boton_guardar_administrar_asuntos.classList;

    $(document).on('click', '.clone-boton-administrar_asuntos', function (e) {
        let clone_tr = document.getElementsByClassName("clone-tr-administrar_asuntos");
        console.log(clone_tr.length);

        if (clone_tr.length > 0) {
            e.preventDefault();
            let elementoremoveClass = clone_tr.item(0);
            elementoremoveClass.removeAttribute('class');

            const classes_2 = elementoremoveClass.classList;
            classes_2.add("nuevo_elemento_administrar_asuntos");
            classes_administrar_asuntos.remove("hidden");


        }
    });

    $(document).on('click', '.remove-tr-administrar_asuntos', function (e) {
        let nuevo_elemento_administrar_asuntos = document.getElementsByClassName("nuevo_elemento_administrar_asuntos");
        console.log(nuevo_elemento_administrar_asuntos.length);
        if (nuevo_elemento_administrar_asuntos.length == 1) {
            classes_administrar_asuntos.add("hidden");
        } else
            e.preventDefault();

        var $th = $(this).parents('tr').eq(0);

        $th.fadeOut('slow', function () {
            $th.remove();
        });

    });

});


