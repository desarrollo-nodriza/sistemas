/**
 * Dashboard Scripts
 * @Cristian Rojas 2017
 */

$.extend({
	hoy: function(){
		var hoy = new Date();
		if (hoy.getMonth() < 10 ) {
			if (hoy.getDate() < 10 ) {
				return hoy.getFullYear() + '-0' + (hoy.getMonth() + 1) + '-0' + hoy.getDate();
			}else{
				return hoy.getFullYear() + '-0' + (hoy.getMonth() + 1) + '-' + hoy.getDate();
			}
		}else{
			return hoy.getFullYear() + '-' + (hoy.getMonth() + 1) + '-' + hoy.getDate();
		}
	},
	inicioMes: function(){
		var inicioM = new Date();
		if (inicioM.getMonth() < 10 ) {
			return inicioM.getFullYear() + '-0' + (inicioM.getMonth() + 1) + '-01';
		}else{
			return inicioM.getFullYear() + '-' + (inicioM.getMonth() + 1) + '-01';
		}
	},
	inicioMesAnterior: function(){
		var inicioM = new Date();
		if (inicioM.getMonth() < 10 ) {
			return inicioM.getFullYear() + '-0' + (inicioM.getMonth()) + '-01';
		}else{
			return inicioM.getFullYear() + '-' + (inicioM.getMonth()) + '-01';
		}
	},
	calendario: function(f_inicio, f_final){
		/**
		 * Datepicker rango fechas
		 */
		var $buscador_fecha_inicio		= f_inicio,
			$buscador_fecha_fin			= f_final;

			$buscador_fecha_inicio.datepicker(
			{	
				language	: 'es',
				format		: 'yyyy-mm-dd',
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
	},
	graficos: {
		init: function(){
			if ( $('#dashboard').length ) {
				$.graficos.bind();
			}
		},
		graficoBarra: function(elemento, datos, ejeX, ejeY, etiquetas, colores){
			Morris.Bar({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				barColors: colores
		    });
		},
		graficoLinea: function(elemento, datos, ejeX, ejeY, etiquetas, colores){
			Morris.Line({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				lineColors: colores
		    });
		},
		graficoArea: function(elemento, datos, ejeX, ejeY, etiquetas, colores){
			Morris.Area({
		      	element: elemento,
				data: datos,
				xkey: ejeX,
				ykeys: ejeY,
				labels: etiquetas,
				resize: true,
				lineColors: colores
		    });
		},
		obtenerVentasPorRango: function(){
			var divGrafico = $('#GraficoVentasHistorico');
			
			// Request
			$.ajax({
				url: webroot + "pages/get_all_sales/" + $('#VentasFInicio').val() + '/' + $('#VentasFFinal').val() + '/' + $('#VentasAgrupar').val() + '/' + 'true',
			    dataType: "json"
			   
			})
			.done(function( data, textStatus, jqXHR ) {
					console.log(data);
					var datos = [];
					var colors = ['#8FB255', '#F55A00', '#5A2602'];
					var yKeys = [ 'a', 'b', 'c'];
					var etiquetas = ['Total', 'Toolmania', 'Walko'];
					var i;

					for (i in data) {
					    if (data.hasOwnProperty(i)) {
					    	datos.push({ y : data[i]['y'], a: data[i]['total'], b : data[i]['toolmania'], c: data[i]['walko'] });
					    }
					}

					$('#GraficoVentasHistorico').html('');

					$.graficos.graficoLinea(divGrafico, datos, 'y', yKeys, etiquetas, colors);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
			    console.log( "La solicitud a fallado: " +  textStatus);
			    //accionesReporte($boton, $fechaReporte);
			});
		},
		obtenerDescuentosPorRango: function(){
			var divGrafico = $('#GraficoDescuentosHistorico');
			
			// Request
			$.ajax({
				url: webroot + "pages/get_all_discount/" + $('#DescuentosFInicio').val() + '/' + $('#DescuentosFFinal').val() + '/' + $('#DescuentosAgrupar').val() + '/' + 'true',
			    dataType: "json"
			   
			})
			.done(function( data, textStatus, jqXHR ) {
					console.log(data);
					var datos = [];
					var colors = ['#45C13A', '#3AADC1', '#C13AB1'];
					var yKeys = [ 'a', 'b', 'c'];
					var etiquetas = ['Total', 'Toolmania', 'Walko'];
					var i;

					for (i in data) {
					    if (data.hasOwnProperty(i)) {
					    	datos.push({ y : data[i]['y'], a: data[i]['total'], b : data[i]['toolmania'], c: data[i]['walko'] });
					    }
					}

					$('#GraficoDescuentosHistorico').html('');

					$.graficos.graficoLinea(divGrafico, datos, 'y', yKeys, etiquetas, colors);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
			    console.log( "La solicitud a fallado: " +  textStatus);
			    //accionesReporte($boton, $fechaReporte);
			});
		},
		obtenerPedidosPorRango: function(){
			var divGrafico = $('#GraficoPedidosHistorico');
			
			// Request
			$.ajax({
				url: webroot + "pages/get_all_orders/" + $('#PedidosFInicio').val() + '/' + $('#PedidosFFinal').val() + '/' + $('#PedidosAgrupar').val() + '/' + 'true',
			    dataType: "json"
			   
			})
			.done(function( data, textStatus, jqXHR ) {
					console.log(data);
					var datos = [];
					var colors = ['#C13A70', '#653AC1', '#226468'];
					var yKeys = [ 'a', 'b', 'c'];
					var etiquetas = ['Total', 'Toolmania', 'Walko'];
					var i;

					for (i in data) {
					    if (data.hasOwnProperty(i)) {
					    	datos.push({ y : data[i]['y'], a: data[i]['total'], b : data[i]['toolmania'], c: data[i]['walko'] });
					    }
					}

					$('#GraficoPedidosHistorico').html('');

					$.graficos.graficoArea(divGrafico, datos, 'y', yKeys, etiquetas, colors);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
			    console.log( "La solicitud a fallado: " +  textStatus);
			    //accionesReporte($boton, $fechaReporte);
			});
		},
		bind: function(){
			// Ventas
			$('#VentasFInicio').val($.inicioMesAnterior());
			$('#VentasFFinal').val($.hoy());
			$.calendario($('#VentasFInicio'), $('#VentasFFinal'));
			$('#VentasAgrupar').val('mes');

			// Descuentos
			$('#DescuentosFInicio').val($.inicioMesAnterior());
			$('#DescuentosFFinal').val($.hoy());
			$.calendario($('#DescuentosFInicio'), $('#DescuentosFFinal'));
			$('#DescuentosAgrupar').val('mes');

			// Pedidos
			$('#PedidosFInicio').val($.inicioMesAnterior());
			$('#PedidosFFinal').val($.hoy());
			$.calendario($('#PedidosFInicio'), $('#PedidosFFinal'));
			$('#PedidosAgrupar').val('mes');

			// Descuentos
			$('#enviarFormularioDescuentos').on('click', function(){
				$.graficos.obtenerDescuentosPorRango();
			});

			// Ventas
			$('#enviarFormularioVentas').on('click', function(){
				$.graficos.obtenerVentasPorRango();
			});

			// Pedidos
			$('#enviarFormularioPedidos').on('click', function(){
				$.graficos.obtenerPedidosPorRango();
			});

			// Descuentos
			$('#enviarFormularioDescuentos').trigger('click');

			// Ventas
			$('#enviarFormularioVentas').trigger('click');

			// Pedidos
			$('#enviarFormularioPedidos').trigger('click');
		}
	}
});

$(document).ready(function(){
	$.graficos.init();
});
