/* jshint bitwise:true, browser:true, eqeqeq:true, forin:true, globalstrict:true, indent:4, jquery:true,
   loopfunc:true, maxerr:3, noarg:true, node:true, noempty:true, onevar: true, quotmark:single,
   strict:true, undef:true, white:false */
/* global FB, webroot, fullwebroot */

//<![CDATA[
'use strict';

/**
 * jQuery
 */
jQuery(document).ready(function($)
{	
	/**
	 * Función copiar al portapapeles
	 */
	if ( $('.btn-copy').length > 0 ) {
		var clipboard = new Clipboard('.btn-copy');

		clipboard.on('success', function(e) {
		    $('.btn-copy').popover({
		    	content: 'Texto copiado'
		    });
		    $('.btn-copy').popover('show');
		    setTimeout(function(){
		    	$('.btn-copy').popover('hide');
		    }, 1000);
		});

		clipboard.on('error', function(e) {
		    $('.btn-copy').popover({content: 'Error al copiar texto'});
		    $('.btn-copy').popover('show');
		    setTimeout(function(){
		    	$('.btn-copy').popover('hide');
		    }, 1000);
		});
	}

	/**
	 * Data tables
	 */
	 if($(".datatable").length > 0){
        $(".datatable").dataTable({
        	ordering:  false
        });
       
    }


	$('.js-query-ver-query').on('click', function(evento)
	{
		evento.preventDefault();
		var $this			= $(this),
			$tr				= $this.parents('tr').first(),
			$extracto		= $tr.find('.extracto'),
			$query			= $tr.find('.query');

		$extracto.hide();
		$query.show();
	});

	/**
	* Spinner
	*/
	if ($('.spinner_default').length > 0) {
		$(".spinner_default").spinner();
	}

	/**
	* ColorPicker
	*/
	if($(".colorpicker").length > 0) {
		$(".colorpicker").colorpicker({format: 'hex'});
	}

	/**
	* Copy icons
	*/
	if ($('#modal_iconos').length > 0) {
		$('.icons-list > li').on('click', function(){
			var inputIcon = $('#ModuloIcono');
			var icono = $(this).children().attr('class');

			inputIcon.val( icono );
			
			$('#modal_iconos').modal('hide');

		}); 
	}

	// WIZARD
	if($(".wizard").length > 0){

        //Check count of steps in each wizard
        $(".wizard > ul").each(function(){
            $(this).addClass("steps_"+$(this).children("li").length);
        });//end

        // This par of code used for example
        if($("#ReporteGenerateForm").length > 0){

            var validator = $("#ReporteGenerateForm").validate({
                    rules: {
                        'data[Reporte][id_reporte]': {
                            required: true
                        },
                        'data[Reporte][f_inicio]': {
                            required: true
                        },
                        'data[Reporte][f_final]': {
                            required: true,
                        },
                        'data[Reporte][id_grafico][]': {
                            required: true
                        },
                        name: {
                            required: true,
                            maxlength: 10
                        },
                        adress: {
                            required: true
                        }
                    },
                    messages: {
                    	'data[Reporte][id_reporte]': {
                            required: 'Seleccione un reporte para continuar'
                        },
                        'data[Reporte][f_inicio]': {
                            required: 'Seleccione una fecha inicial'
                        },
                        'data[Reporte][f_final]': {
                            required: 'Seleccione una fecha final'
                        },
                        'data[Reporte][id_grafico][]': {
                            required: 'Seleccione al menos un gráfico'
                        }
                    }
                });

        }// End of example

        $(".wizard").smartWizard({
            // This part of code can be removed FROM
            onLeaveStep: function(obj){
                var wizard = obj.parents(".wizard");

                if(wizard.hasClass("wizard-validation")){

                    var valid = true;

                    $('input,select,textarea',$(obj.attr("href"))).each(function(i,v){
                        valid = validator.element(v) && valid;
                    });

                    if(!valid){
                        wizard.find(".stepContainer").removeAttr("style");
                        validator.focusInvalid();
                        return false;
                    }

                }

                return true;
            },// <-- TO

            //This is important part of wizard init
            onShowStep: function(obj){

            	// Resume
				var step1 = $('#ReporteIdReporte option:selected').text();
				var step2_fi = $('#ReporteFInicio').val();
				var step2_ff = $('#ReporteFFinal').val();
				var $step3 = $('#ReporteIdGrafico option:selected');
		
				var step3Html = '<ul>';

				$step3.each(function(){

					step3Html += '<li>' + $(this).text() + '</li>';
				});

				step3Html += '</ul>';

				var htmlResume = '<table class="table table-striped">';
				htmlResume	+= '<thead>';
				htmlResume	+= '<th>Reporte</th>';
				htmlResume	+= '<th>Periodo</th>';
				htmlResume	+= '<th>Gráficos</th>';
				htmlResume	+= '</thead>';
				htmlResume	+= '<tbody>';
				htmlResume	+= '<tr>';
				htmlResume	+= '<td>' + step1 + '</td>';
				htmlResume	+= '<td>' + step2_fi + ' - ' + step2_ff + '</td>';
				htmlResume	+= '<td> ' + step3Html + '</td>';
				htmlResume	+= '</tr>';
				htmlResume	+= '</tbody>';

				$('#resume').html(htmlResume);

                var wizard = obj.parents(".wizard");

                if(wizard.hasClass("show-submit")){

                    var step_num = obj.attr('rel');
                    var step_max = obj.parents(".anchor").find("li").length;

                    if(step_num == step_max){
                        obj.parents(".wizard").find(".actionBar .btn-primary").css("display","block");
                    }
                }
                return true;
            }//End
        });
    }

    /**
     * Filtro de producto
     */
    if ($('#FiltroAdminIndexForm').length) {
    	if($('#FiltroFindby').val() == '') {
    		$('#FiltroNombreBuscar').attr('disabled', 'disabled');
    	}
    	$('#FiltroFindby').on('change', function(){
    		if($('#FiltroFindby').val() == '') {
	    		$('#FiltroNombreBuscar').attr('disabled', 'disabled');
	    	}else{
	    		$('#FiltroNombreBuscar').removeAttr('disabled');
	    	}
    	});

    }
                
	/* LOCK SCREEN */
    $('.lockscreen-box .lsb-access').on('click',function()
	{
		$(this).parent('.lockscreen-box').addClass('active').find('input').focus();
		return false;
	});

    $('.lockscreen-box .user_signin').on('click',function()
	{
		$('.sign-in').show();
		$(this).remove();
		$('.user').hide().find('img').attr('src', webroot + 'backend/assets/images/users/no-image.jpg');
		$('.user').show();
		return false;
	});
    /* END LOCK SCREEN */

	/**
	 * Ordenamiento de tablas generico
	 */
	if ( $('.js-generico-contenedor-sort').length ) {
		$('.js-generico-contenedor-sort').sortable(
		{
			axis			: 'y',
			cursor			: 'move',
			helper			: function(e, tr)
			{
				var $originals	= tr.children(),
					$helper		= tr.clone();

				$helper.children().each(function(index)
				{
					$(this).width($originals.eq(index).width());
				});
				return $helper;
			},
			stop			: function(e, ui)
			{
				$('td.js-generico-orden', ui.item.parent()).each(function(i)
				{
					var $this		= $(this);
					$this.find('input').val(i + 1);
					$this.find('span').text(i + 1);
				});

				var $form		= ui.item.parents('form').first();
				$.post($form.attr('action'), $form.serialize());
			}
		}).disableSelection();
	}

	$('.js-generico-handle-sort').on('click', function(evento)
	{
		evento.preventDefault();
	});



	/**
	 * Editor de ayudas
	 */
	if ( $('.summernote').length )
	{
		$('.summernote').summernote(
		{
			height		: 200,
			focus		: true,
			toolbar		: [
				['color', ['color']],
				['style', ['bold', 'italic', 'underline', 'clear']],
				['fontsize', ['fontsize']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['insert', []],
				['table', ['table']],
				['para', ['ul', 'ol', 'paragraph']],
				['misc', ['codeview']]
			]
		});
	}

	/**
	 * Select estados
	 */
	if($(".select").length > 0){
        $(".select").selectpicker();

        $(".select").on("change", function(){
            if($(this).val() == "" || null === $(this).val()){
                if(!$(this).attr("multiple"))
                    $(this).val("").find("option").removeAttr("selected").prop("selected",false);
            }else{
                $(this).find("option[value="+$(this).val()+"]").attr("selected",true);
            }
        });
    }

	if ( $('input.icheckbox').length )
	{
		$('input.icheckbox').iCheck(
		{
			checkboxClass	: 'icheckbox_flat-red',
			radioClass		: 'iradio_flat-red',
			increaseArea	: '20%'
		});
	}

	/**
	 * Check multiple
	 */
	if ( $('.icheckbox-multiple input[type="checkbox"]').length )
	{
		$('.icheckbox-multiple input[type="checkbox"]').iCheck(
		{
			checkboxClass	: 'icheckbox_flat-red',
			radioClass		: 'iradio_flat-red',
			increaseArea	: '20%'
		});
	}

	/**
	* Ventana modal
	* @param 	title 	Título de la ventana modal
	* @param 	body 	Cuerpo del modal
	* @param 	btn  	Botón del modal
	* @param    obj 	Objeto de datos
	* @returns 	bool 	Mostrar modal 
	*/
	function modal( title, body, btn, obj ) {}

	/**
	 * Funcion que permite obtener en formato YYYY-MM-DD una fecha determinada
	 * @param			{Object}			fecha			Fecha que se desea obtener
	 * @returns			{Object}			fecha			Fecha en formato YYYY-MM-DD
	 */
	function obtenerFecha(fecha)
	{
		return fecha.getFullYear() + '-' + (fecha.getMonth() + 1) + '-' + fecha.getDate();
	}

	/**
	 * Idioma español datepicker
	 */
	if ( $('.datepicker').length > 0 ) {
		!function(a)
		{
			a.fn.datepicker.dates.es = {
				days			: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
				daysShort		: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
				daysMin			: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
				months			: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				monthsShort		: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
				today			: 'Hoy',
				clear			: 'Borrar',
				weekStart		: 1,
				defaultDate 	: '2017-01-01',
				format			: 'yyyy-mm-dd'
			}
		}(jQuery);
	}

	/**
	 * Datepicker
	 */
	if ($('.datepicker').length > 0) {
		$('.datepicker').datepicker({
			language	: 'es',
			format		: 'yyyy-mm-dd'
		});
	}

	/**
	 * Buscador de OC - Datepicker rango fechas
	 */
	var $buscador_fecha_inicio		= $('#ReporteFInicio'),
		$buscador_fecha_fin			= $('#ReporteFFinal');

	if ( $buscador_fecha_inicio.length )
	{
		$buscador_fecha_inicio.datepicker(
		{
			language	: 'es',
			format		: 'yyyy-mm-dd'
		}).on('changeDate', function(data)
		{
			$buscador_fecha_fin.datepicker('setStartDate', data.date);
		});

		$buscador_fecha_fin.datepicker(
		{
			language	: 'es',
			format		: 'yyyy-mm-dd'
		}).on('changeDate', function(data)
		{
			$buscador_fecha_inicio.datepicker('setEndDate', data.date);
		});
	}

	/**
	 * Buscador de OC - Rango de fecha predeterminada
	 */
	$('.js-data-search').on('click', function(evento)
	{
		evento.preventDefault();

		var $this			= $(this),
			tipo			= $this.data('tipo'),
			rango			= $this.data('rango'),
			fecha_inicio	= new Date();

		/**
		 * Limpia las fechas
		 */
		if ( tipo == 'todo' )
		{
			$buscador_fecha_inicio.datepicker('update', '');
			$buscador_fecha_fin.datepicker('update', '');
		}
		else
		{
			var method = {
				dia		: ['setDate', 'getDate'],
				mes		: ['setMonth', 'getMonth'],
				ano		: ['setYear', 'getFullYear']
			};

			/**
			* Calcula la fecha
			*/
			fecha_inicio[method[tipo][0]](fecha_inicio[method[tipo][1]]() - rango);
			$buscador_fecha_inicio.datepicker('setDate', obtenerFecha(fecha_inicio));
			$buscador_fecha_fin.datepicker('setDate', obtenerFecha(new Date));
		}
	});

	/**
	 * Buscador de OC - Rango OC
	 */
	var $slider_oc		= $('#CompraRangoOc');
	if ( $slider_oc.length )
	{
		var minOC		= $slider_oc.data('min-oc'),
			maxOC		= $slider_oc.data('max-oc');

		$slider_oc.ionRangeSlider(
		{
			type				: 'double',
			grid				: true,
			min					: minOC,
			max					: maxOC,
			//from				: minOC,
			//to				: maxOC,
			prettify_separator	: '.',
			force_edges: false,
			prefix				: 'OT ',
			onChange			: function(data)
			{
				$(data.input).attr('disabled', false);
			}
		});

		if ( typeof(filtros) === 'object' && typeof(filtros.filtro) === 'object' && typeof(filtros.filtro.oc_min) !== 'undefined' )
		{
			$slider_oc.data('ionRangeSlider').update(
			{
				from		: parseInt(filtros.filtro.oc_min, 10),
				to			: parseInt(filtros.filtro.oc_max, 10)
			});
			$slider_oc.attr('disabled', false);
		}
	}

	/**
	 * Buscador de OC - Rango Monto
	 */
	var $slider_monto		= $('#CompraRangoMonto');
	if ( $slider_monto.length )
	{
		var minMonto		= $slider_monto.data('min-monto'),
			maxMonto		= $slider_monto.data('max-monto');

		$slider_monto.ionRangeSlider(
		{
			type				: 'double',
			grid				: true,
			min					: minMonto,
			max					: maxMonto,
			//from				: minMonto,
			//to					: maxMonto,
			prettify_separator	: '.',
			prefix				: '$',
			onChange			: function(data)
			{
				$(data.input).attr('disabled', false);
			}
		});

		if ( typeof(filtros) === 'object' && typeof(filtros.filtro) === 'object' && typeof(filtros.filtro.monto_min) !== 'undefined' )
		{
			$slider_monto.data('ionRangeSlider').update(
			{
				from		: parseInt(filtros.filtro.monto_min, 10),
				to			: parseInt(filtros.filtro.monto_max, 10)
			});
			$slider_monto.attr('disabled', false);
		}
	}

	/**
	 * Select estados
	 */
	if ( $('.selectpicker').length )
	{
		$('.selectpicker').selectpicker();
	}


	/**
	 * Limpia filtros
	 */
	$('.js-limpiar-busqueda').on('click', function(evento)
	{
		evento.preventDefault();
		var $this			= $(this),
			tipo			= $this.data('tipo'),
			$input			= $('[data-tipo="' + tipo + '"]').not(this);

		if ( tipo === 'libre' )
		{
			$input.val('');
		}
		if ( tipo === 'fecha' )
		{
			$input.datepicker('update', '').datepicker('clearDates');
		}
		if ( tipo === 'estado' )
		{
			$input.selectpicker('deselectAll');
		}
		if ( tipo === 'oc' || tipo === 'monto' )
		{
			$input.data('ionRangeSlider').reset();
			$input.prop('disabled', true);
		}
	});


	/**
	 * Input autocomplete y codigo usuario
	 */
	var $autocomplete		= $('[name="data[GrupoTarifario][usuario]"]');

	if ( $autocomplete.length )
	{
		/**
		* Limpieza inicial
		*/
		$autocomplete.val('');
	   /**
		* Autocomplete del nombre del usuario
		* Muestra
		* 			nombre
		* 			apellido materno
		* 			apellido paterno
		* 			email
		* 			telefono
		*/
		$autocomplete.typeahead(
		{
			/**
			 * Se obtiene el listado de los usuarios, filtrados el parametro enviado al controlador
			 */
			source					: function(query, process)
			{
				$.ajax(
				{
					type			: 'POST',
					url				: webroot + 'admin/grupo_tarifarios/ajax_usuariosTarifarios',
					dataType		: 'json',
					data			: { query: query },
					success			: process
				});
			},
			minLength				: 1,
			delay					: 200,
			autoSelect				: false,
			showHintOnFocus			: true,
			displayText				: function(item)
			{
				return item.Usuario.nombre_completo;
			}
		});

		/**
		 * Actualiza el codigo del usuario o elimina el usuario
		 * si no corresponde a una opcion del autoselector
		 */
		$autocomplete.on('change blur', function()
		{
			var $this			= $(this),
				current			= $this.typeahead('getActive');

			if ( typeof(current) !== 'undefined' )
			{
				if ( current.Usuario.nombre_completo === $this.val() )
				{
					// Se verifica que el id del usuario que se ingresa, no exita o este ingresado
					if ( ! $('.tabla-usuarios tbody tr[data-usuario_id="' + current.Usuario.id + '"]').length )
					{
						/**
						 * Se arma el arreglo que contiene los datos que se ingresan a la tabla
						 * de usuarios seleccionados
						 */
						var datos		= [
							current.TipoUsuario.nombre,
							current.Usuario.nombre,
							current.Usuario.email,
							current.Usuario.celular,
						];
						var html		= $.map(datos, function(texto)
						{
							return $('<td />', { text: texto });
						});

						/**
						 * Agregamos al primer td, un input type: hidde, el cual
						 * contendra el id del usuario que se selecciona
						 */
						html[0].append($('<input />',
						{
							type	: 'hidden',
							name	: 'data[Usuario][][usuario_id]',
							value	: current.Usuario.id
						}));

						/**
						 * Se agrega como ultimo td, el boton de accion, que permite eliminar el usuarios
						 * selecionado
						 */
						html.push('<td><a href="#" class="btn btn-danger js-elimina-usuario"><span class="fa fa-times"></span></td>');

						/**
						 * Se ingresan los datos del usuario en la tabla de usuarios seleccionados
						 */
						$('.tabla-usuarios tbody').append(
							$('<tr />', { 'data-usuario_id' :  current.Usuario.id }).append(html)
						);
					}
				}
				$this.val('').focus();
			}
		});

		/**
		 * Escucha que permite eliminar un item (usuario seleccionado) de la tabla de usuarios
		 */
		$('.tabla-usuarios tbody').on('click', '.js-elimina-usuario', function(evento)
		{
			evento.preventDefault();
			$(this).parents('tr').first().remove();
		});
	}

	if($(".owl-carousel").length > 0){
        $(".owl-carousel").owlCarousel({mouseDrag: false, touchDrag: true, slideSpeed: 300, paginationSpeed: 400, singleItem: true, navigation: false,autoPlay: true});
    }


	/*
	 * Gráficos y generación del reporte
	 */
	if ( $('#reporteId').length || $('#fechaInicial').length || $('#fechaFinal').length || $('#graficosId').length ) {

		//Lanzamos evento click para ejecutar el reporte
		$(document).ready(function(){
			$('#generarGraficoBtn').trigger('click');	
		});


		/**
		 * @param  {object}	 	Botón a habilitar
		 * @param  {object}		Elemento donde se podrá la fecha
		 * @return {null}
		 */
		function accionesReporte(boton, fecha) {
			var fechaActual = new Date();
			// Refrescar fecha de generación
			fecha.text( fechaActual.getDate() + '-' + fechaActual.getMonth() + '-' + fechaActual.getFullYear() + ' a las ' + fechaActual.getHours() + ':' + fechaActual.getMinutes() + ':' + fechaActual.getSeconds() ); 
			// Habilitar el botón
			boton.removeAttr('disabled');
		}

		function insertarElemento(html) {
			$('#grafics-container').append(html);
		}

		/**
		 * @param  Int	
		 * @param  String
		 * @return Html
		 */
		function crearContenedorGrafico(identificador, nombre) {
			var htmlContenedor = '';
			htmlContenedor += '<div class="col-xs-12 col-sm-6">';
			htmlContenedor += '<div class="panel panel-default">';
			htmlContenedor += '<div class="panel-heading">';
			htmlContenedor += '<h3 class="panel-title">' + nombre + '</h3>';
			htmlContenedor += '</div>';
			htmlContenedor += '<div class="panel-body">';
			htmlContenedor += '<div id="' + identificador + '" style="height: 300px"></div>';
			htmlContenedor += '</div>';
			htmlContenedor += '</div>';
			htmlContenedor += '</div>';

			insertarElemento(htmlContenedor);
		}

		function crearCajaVentas($data) {
			var cajaVentas = '';
			cajaVentas += '<div class="row">';
			cajaVentas += '<div class="col-xs-4">';
			cajaVentas += '<div class="tile tile-primary">';
			cajaVentas += '';
			cajaVentas += '<p>Total ventas del periodo</p>';
			cajaVentas += '<div class="informer informer-default"><span class="fa fa-shopping-cart"></span></div>';
			cajaVentas += '</div>';
			cajaVentas += '</div>';
			cajaVentas += '</div>';

			insertarElemento(cajaVentas);
		}


		function armarGraficosDonuts(elemento, datos, colores, formato) {
			Morris.Donut({
		      	element: elemento,
				data: datos,
				formatter: formato,
				colors: colores
		    });

		    /*Morris.Donut({
		        element: 'morris-donut-example',
		        data: [
		            {label: "Download Sales", value: 12},
		            {label: "In-Store Sales", value: 30},
		            {label: "Mail-Order Sales", value: 20}
		        ],
		        colors: ['#95B75D', '#3FBAE4', '#FEA223']
		    });*/
		}


		function armarGraficosLinea(elemento, datos, ejeX, ejeY, etiquetas, colores) {
			Morris.Line({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				xLabels: 'month',
				lineColors: colores
		    });
		}


		function armarGraficosArea(elemento, datos, ejeX, ejeY, etiquetas, colores) {
			Morris.Area({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				lineColors: colores
		    });
		}

		function armarGraficosBarra(elemento, datos, ejeX, ejeY, etiquetas, colores) {
			Morris.Bar({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				barColors: colores
		    });
		}


		function armarGraficoLineaNVD3(element, data, colores ) {
		
			nv.addGraph(function() {
				var chart = nv.models.lineChart().margin({
					left : 100
				})//Adjust chart margins to give the x-axis some breathing room.
				.useInteractiveGuideline(true)//We want nice looking tooltips and a guideline!
				.transitionDuration(350)//how fast do you want the lines to transition?
				.showLegend(true)//Show the legend, allowing users to turn on/off line series.
				.showYAxis(true)//Show the y-axis
				.showXAxis(true)//Show the x-axis
				.color(colores);
				chart.xAxis//Chart x-axis settings
				.axisLabel('Marcas').tickFormat(d3.format(',r'));

				chart.yAxis//Chart y-axis settings
				.axisLabel('Cantidad').tickFormat(d3.format('.02f'));

				d3.select(element)//Select the <svg> element you want to render the chart in.
				.datum(data)//Populate the <svg> element with chart data...
				.call(chart);
				//Finally, render the chart!

				//Update the chart when window resizes.
				nv.utils.windowResize(function() {
	                chart.update();
				});
			});
		}


		function productosDelPeriodo(data) {
			var element =  'productos_del_periodo';
			var datos = [];
			var colors = ['#5D58DD', '#524EC4', '#3C63F0', '#6783E8', '#2041BA', '#1D348A', '#1D5E8A', '#2495E0', '#0879C4', '#1EC4DA'];
			var count = 0;
			var total = 0;
			var porcentaje = 0;
			var i;
			var formato = function (y, data) { return y + '%' };

			for (i in data) {
			    if (data.hasOwnProperty(i)) {
			    	total = total + parseInt(data[i][0]['Cantidad']);
			    }
			}

			// Texto total de productos
			$('#cant_total_product').text(total);

			for (i in data) {
			    if (data.hasOwnProperty(i)) {
			    	porcentaje = (( parseInt(data[i][0]['Cantidad']) * 100 ) / total);
			    	datos.push({ label : data[i]['Producto']['Referencia'], value: parseInt(porcentaje) });
			        count++;
			    }
			}
			// Mostrar solo los 10 mayores elementos
			datos.splice(10, (count-10));

			armarGraficosDonuts(element, datos, colors, formato);
		}


		function categoriasDelPeriodo(data) {
			var element =  'categorias_del_periodo';
			var datos = [];
			var colors = ['#17191E', '#36B7E3', '#FE9C16', '#DE4444', '#BFDE44', '#6EDE5A', '#5ADEC6', '#5A5ADE', '#B65ADE', '#DE5AD9'];
			var count = 0;
			var total = 0;
			var i;
			var porcentaje = 0;
			var formato = function (y, data) { return y + ' ventas'};

			for (i in data) {
			    if (data.hasOwnProperty(i)) {
			    	total = total + parseInt(data[i][0]['Cantidad']);
			    }
			}

			for (i in data) {
			    if (data.hasOwnProperty(i)) {
			    	porcentaje = (( parseInt(data[i][0]['Cantidad']) * 100 ) / total);
			    	datos.push({ label : data[i]['IdiomaCategoria']['Nombre'], value: parseInt(data[i][0]['Cantidad']) });
			        count++;
			    }
			}
			// Mostrar solo los 10 mayores elementos
			datos.splice(10, (count-10));

			armarGraficosDonuts(element, datos, colors, formato);
		}


		function comparadorDePeriodos(data) {
			var element =  'comparador_de_periodos';
			var datos = [];
			var x = 'y';
			var y = ['a', 'b'];
			var labels = ['Total Comprado', 'Total Descuentos'];
			var colors = ['#3EBAE4', '#DE4444'];

			var count = 0;
			var i;

			for (i in data) {
			    if (data.hasOwnProperty(i)) {
			    	datos.push({ y : data[i][0]['Mes'], a: parseInt(data[i][0].VentaPeriodoActual), b: parseInt(data[i][0].TotalDescuentos) });
			        count++;
			    }
			}

			armarGraficosLinea(element, datos, x, y, labels, colors);
		}


		function marcasDelPeriodo(data) {
			var element =  'marcas_del_periodo';
			var datos = [];
			var colors = ['#17191E', '#36B7E3', '#FE9C16', '#DE4444', '#BFDE44', '#6EDE5A', '#5ADEC6', '#5A5ADE', '#B65ADE', '#DE5AD9'];
			var count = 0;
			var total = 0;
			var i;
			var porcentaje = 0;
			var formato = function (y, data) { return y + '%' };

			for (i in data) {
			    if (data.hasOwnProperty(i)) {
			    	total = total + parseInt(data[i][0]['Cantidad']);
			    }
			}

			// Texto total de productos
			$('#cant_total_marcas').text(total);
			
			for (i in data) {
			    if (data.hasOwnProperty(i)) {
			    	porcentaje = (( parseInt(data[i][0]['Cantidad']) * 100 ) / total);
			    	console.log(porcentaje);
			    	datos.push({ label : data[i]['Proveedor']['Nombre'], value: parseInt(porcentaje) });
			        count++;
			    }
			}
			// Mostrar solo los 10 mayores elementos
			datos.splice(10, (count-10));

			armarGraficosDonuts(element, datos, colors, formato);
		}


		function compradoresDelPeriodo(data) {
			var element =  'compradores_del_periodo';
			var datos = [];
			var colors = ['#17191E', '#36B7E3', '#FE9C16', '#DE4444', '#BFDE44', '#6EDE5A', '#5ADEC6', '#5A5ADE', '#B65ADE', '#DE5AD9'];
			var count = 0;
			var total = 0;
			var i;
			var formato = function (y, data) { return '$' + y };

			for (i in data) {
			    if (data.hasOwnProperty(i)) {
			    	datos.push({ label : data[i][0]['nombre'], value: parseInt(data[i][0]['pagado']) });
			        count++;
			    }
			}
			// Mostrar solo los 10 mayores elementos
			datos.splice(10, (count-10));

			armarGraficosDonuts(element, datos, colors, formato);
		}

		// Generar reporte
		$('#generarGraficoBtn').on('click', function(){
			//var $boton = $(this);
			var $fechaReporte = $('#fecha_reporte');
			var graficosId = $("#graficosId").data("value").split(',').map(JSON.parse);
			var graficos = [];

			// Deshabilitar el botón
			//$boton.attr('disabled', 'disabled');

			// Armar array de ids de los gráficos
			for (var i = 0; i <= graficosId.length - 1; i++) {
				graficos.push(graficosId[i]);
			}

			// Separar elementos del array
			graficos.join(', ');

			// Request
			$.ajax({
			    data: {graficos, "reporte" : $('#reporteId').data('value'), "f_inicio" :  $('#fechaInicial').data('value'), "f_final" :  $('#fechaFinal').data('value') },
			    type: "POST",
			    //dataType: "json",
			    url: webroot + "reportes/get_query_result_json",
			})
			.done(function( data, textStatus, jqXHR ) {
					console.log(data);
			    var result = $.parseJSON(data);
		    	var ventasTotal = result['total_ventas_del_periodo'];
		    	var categorias = result['categorias_del_periodo'];
		    	var comparador = result['comparador_de_periodos'];
		    	var productos = result['productos_del_periodo'];
		    	var descuentos = result['total_descuentos_del_periodo'];
		    	var pedidos = result['pedidos_del_periodo'];
		    	var compradores = result['compradores_del_periodo'];
		    	var marcas = result['marcas_del_periodo'];

		    	// Comparador de periodos
				if (comparador) {
		    		comparadorDePeriodos(comparador);
		    	}

		    	// Productos del periodo
		    	if (productos) {
		    		productosDelPeriodo(productos);
		    	}

		    	// Categorias del periodo
		    	if (categorias) {
		    		categoriasDelPeriodo(categorias);
		    	}

		    	// Categorias del periodo
		    	if (marcas) {
		    		marcasDelPeriodo(marcas);
		    	}

		    	// Categorias del periodo
		    	if (compradores) {
		    		compradoresDelPeriodo(compradores);
		    	}

			    //accionesReporte($boton, $fechaReporte);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
			    console.log( "La solicitud a fallado: " +  textStatus);
			    //accionesReporte($boton, $fechaReporte);
			});
		})

	}

	$("body").tooltip({selector:'[data-toggle="tooltip"]',container:"body"});
	

   /*if ( typeof(valores_oc) !== 'undefined' )
   {
        Morris.Line({
         element: 'dashboard-line-2',
         data: valores_oc,
         xkey: 'y',
         ykeys: ['total_compra', 'total_lista', 'total_reserva'],
         labels: ['Compra','Lista', 'Reserva'],
         resize: false,
         lineColors: ['#848484','#FF8000', 'blue'],
		 parseTime: true,
		 preUnits: '$'
       });
   }*/

	/*if ( typeof(cantidad_estados) !== 'undefined' )
    {
        Morris.Line({
        element: 'dashboard-line-1',
        data: cantidad_estados,
        xkey: 'y',
        ykeys: ['1', '2', '3', '4', '5'],
        labels: $.map(estados.estados, function(el) { return el }),
        resize: true,
        hideHover: false,
        gridTextSize: '10px',
        lineColors: ['#FF8000', '#B64645', '#8A0808', '#95B75D', '#848484'],
        gridLineColor: '#E5E5E5',
		 parseTime: true
        });
    }*/
});
//]]>
