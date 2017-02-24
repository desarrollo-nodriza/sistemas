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
	obtenerTiendas: function(obj, element){
		$.ajax({
			url: webroot + "pages/get_shops_list",
		    dataType: "json"
		   
		})
		.done(function( data, textStatus, jqXHR ) {
				console.log(data);
				var listaHtml = '';
				var i;

				for (i in data) {
				    if (data.hasOwnProperty(i)) {
				    	listaHtml += '<option value="' + data[i]['Tienda']['id'] + '">' +data[i]['Tienda']['nombre']+ '</li>';
				    }
				}

				obj.html(listaHtml);

				element.trigger('click');

		})
		.fail(function( jqXHR, textStatus, errorThrown ) {
		    console.log( "La solicitud a fallado: " +  textStatus);
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
		graficoDonuts: function(elemento, datos, colores, formato) {
			Morris.Donut({
		      	element: elemento,
				data: datos,
				formatter: formato,
				colors: colores
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
					var colors = ['#39A23B', '#1C1D1C', '#737473'];
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
					var colors = ['#2B40BC', '#4EAEEA', '#A479EF'];
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
					var colors = ['#5C5C5C', '#819AFC', '#000000'];
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
		obtenerProductosPorRango: function(){
			var divGrafico = $('#GraficoProductosDonuts');
			
			// Request
			$.ajax({
				url: webroot + "pages/top_products/" + $('#ProductosFInicio').val() + '/' + $('#ProductosFFinal').val() + '/' + $('#ProductosTienda').val() + '/' + 'true',
			    dataType: "json"
			   
			})
			.done(function( data, textStatus, jqXHR ) {
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

				for (i in data) {
				    if (data.hasOwnProperty(i)) {
				    	porcentaje = (( parseInt(data[i][0]['Cantidad']) * 100 ) / total);
				    	datos.push({ label : data[i]['Producto']['Referencia'], value: parseInt(porcentaje) });
				        count++;
				    }
				}
				// Mostrar solo los 10 mayores elementos
				datos.splice(10, (count-10));

				$('#totalProductos').html('Total de productos vendidos: ' + total);

				$('#GraficoProductosDonuts').html('');

				$.graficos.graficoDonuts(divGrafico, datos, colors, formato);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
			    console.log( "La solicitud a fallado: " +  textStatus);
			    //accionesReporte($boton, $fechaReporte);
			});
		},
		obtenerMarcasPorRango: function(){
			var divGrafico = $('#GraficoMarcasDonuts');
			
			// Request
			$.ajax({
				url: webroot + "pages/top_brands/" + $('#MarcasFInicio').val() + '/' + $('#MarcasFFinal').val() + '/' + $('#MarcasTienda').val() + '/' + 'true',
			    dataType: "json"
			   
			})
			.done(function( data, textStatus, jqXHR ) {
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

				for (i in data) {
				    if (data.hasOwnProperty(i)) {
				    	porcentaje = (( parseInt(data[i][0]['Cantidad']) * 100 ) / total);
				    	datos.push({ label : data[i]['Fabricante']['Nombre'], value: parseInt(porcentaje) });
				        count++;
				    }
				}
				// Mostrar solo los 10 mayores elementos
				datos.splice(10, (count-10));

				$('#totalMarcas').html('Total de productos vendidos: ' + total);

				$('#GraficoMarcasDonuts').html('');

				$.graficos.graficoDonuts(divGrafico, datos, colors, formato);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
			    console.log( "La solicitud a fallado: " +  textStatus);
			    //accionesReporte($boton, $fechaReporte);
			});
		},
		obtenerClientesPorRango: function(){
			var divGrafico = $('#GraficoClientesDonuts');
			
			// Request
			$.ajax({
				url: webroot + "pages/top_customers/" + $('#ClientesFInicio').val() + '/' + $('#ClientesFFinal').val() + '/' + $('#ClientesTienda').val() + '/' + 'true',
			    dataType: "json"
			   
			})
			.done(function( data, textStatus, jqXHR ) {
				var datos = [];
				var colors = ['#5D58DD', '#524EC4', '#3C63F0', '#6783E8', '#2041BA', '#1D348A', '#1D5E8A', '#2495E0', '#0879C4', '#1EC4DA'];
				var i;
				var formato = function (y, data) { return '$' + new Intl.NumberFormat('de-DE').format(y) };

				for (i in data) {
				    if (data.hasOwnProperty(i)) {
				    	datos.push({ label : data[i]['nombre'], value: data[i]['pagado'] });
				    }
				}

				$('#GraficoClientesDonuts').html('');

				$.graficos.graficoDonuts(divGrafico, datos, colors, formato);
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
			    console.log( "La solicitud a fallado: " +  textStatus);
			    //accionesReporte($boton, $fechaReporte);
			});
		},
		bind: function(){

			// Ventas
			$('#VentasFInicio').val($.inicioMes());
			$('#VentasFFinal').val($.hoy());
			$.calendario($('#VentasFInicio'), $('#VentasFFinal'));
			$('#VentasAgrupar').val('dia');

			// Descuentos
			$('#DescuentosFInicio').val($.inicioMes());
			$('#DescuentosFFinal').val($.hoy());
			$.calendario($('#DescuentosFInicio'), $('#DescuentosFFinal'));
			$('#DescuentosAgrupar').val('dia');

			// Pedidos
			$('#PedidosFInicio').val($.inicioMes());
			$('#PedidosFFinal').val($.hoy());
			$.calendario($('#PedidosFInicio'), $('#PedidosFFinal'));
			$('#PedidosAgrupar').val('dia');

			// Productos
			$('#ProductosFInicio').val($.inicioMes());
			$('#ProductosFFinal').val($.hoy());
			$.calendario($('#ProductosFInicio'), $('#ProductosFFinal'));

			// Marcas
			$('#MarcasFInicio').val($.inicioMes());
			$('#MarcasFFinal').val($.hoy());
			$.calendario($('#MarcasFInicio'), $('#MarcasFFinal'));

			// Clientes
			$('#ClientesFInicio').val($.inicioMes());
			$('#ClientesFFinal').val($.hoy());
			$.calendario($('#ClientesFInicio'), $('#ClientesFFinal'));

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

			// Productos
			$('#enviarFormularioProductos').on('click', function(){
				$.graficos.obtenerProductosPorRango();
			});

			// Marcas
			$('#enviarFormularioMarcas').on('click', function(){
				$.graficos.obtenerMarcasPorRango();
			});

			// Clientes
			$('#enviarFormularioClientes').on('click', function(){
				$.graficos.obtenerClientesPorRango();
			});

			var cargaVentas = setTimeout(function(){
				// Ventas
				$('#enviarFormularioVentas').trigger('click');
			}, 100);

			var cargaDescuentos = setTimeout(function(){
				// Descuentos
				$('#enviarFormularioDescuentos').trigger('click');
			}, 2000);

			var cargaDescuentos = setTimeout(function(){
				// Pedidos
				$('#enviarFormularioPedidos').trigger('click');
			}, 4000);

			var cargaProductos = setTimeout(function(){
				// Cargar Productos
				$.obtenerTiendas($('#ProductosTienda'), $('#enviarFormularioProductos'));
			}, 8000);

			var cargaMarcas = setTimeout(function(){
				// Cargar Marcas
				$.obtenerTiendas($('#MarcasTienda'), $('#enviarFormularioMarcas'));
			}, 10000);

			var cargaMarcas = setTimeout(function(){
				// Cargar Clientes
				$.obtenerTiendas($('#ClientesTienda'), $('#enviarFormularioClientes'));
			}, 12000);	

		}
	}
});

$(window).on('load', function(){
	$.graficos.init();
});
