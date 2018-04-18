(function($) {

	var semaforo = true

	var ejemplosGraficos = function() {

	    /*Morris.Line({
	      element: 'morris-line-example',
	      data: [
	        { y: '2006', a: 100, b: 90 },
	        { y: '2007', a: 75,  b: 65 },
	        { y: '2008', a: 50,  b: 40 },
	        { y: '2009', a: 75,  b: 65 },
	        { y: '2010', a: 50,  b: 40 },
	        { y: '2011', a: 75,  b: 65 },
	        { y: '2012', a: 100, b: 90 }
	      ],
	      xkey: 'y',
	      ykeys: ['a', 'b'],
	      labels: ['Series A', 'Series B'],
	      resize: true,
	      lineColors: ['#33414E', '#95B75D']
	    });


	    Morris.Area({
	        element: 'morris-area-example',
	        data: [
	            { y: '2006', a: 100, b: 90 },
	            { y: '2007', a: 75,  b: 65 },
	            { y: '2008', a: 50,  b: 40 },
	            { y: '2009', a: 75,  b: 65 },
	            { y: '2010', a: 50,  b: 40 },
	            { y: '2011', a: 75,  b: 65 },
	            { y: '2012', a: 100, b: 90 }
	        ],
	        xkey: 'y',
	        ykeys: ['a', 'b'],
	        labels: ['Series A', 'Series B'],
	        resize: true,
	        lineColors: ['#3FBAE4', '#FEA223']
	    });


	    Morris.Bar({
	        element: 'morris-bar-example',
	        data: [
	            { y: '2006', a: 100, b: 90 },
	            { y: '2007', a: 75,  b: 65 },
	            { y: '2008', a: 50,  b: 40 },
	            { y: '2009', a: 75,  b: 65 },
	            { y: '2010', a: 50,  b: 40 },
	            { y: '2011', a: 75,  b: 65 },
	            { y: '2012', a: 100, b: 90 }
	        ],
	        xkey: 'y',
	        ykeys: ['a', 'b'],
	        labels: ['Series A', 'Series B'],
	        barColors: ['#B64645', '#33414E']
	    });


	    Morris.Donut({
	        element: 'morris-donut-example',
	        data: [
	            {label: "Download Sales", value: 12},
	            {label: "In-Store Sales", value: 30},
	            {label: "Mail-Order Sales", value: 20}
	        ],
	        colors: ['#95B75D', '#3FBAE4', '#FEA223']
	    });*/

	}();


  	var mostrarGrafico = function($data, $xlabel)
  	{
  		Morris.Line({
	      element: 'graficoHistorico',
	      data: $data,
	      xkey: 'y',
	      ykeys: ['a'],
	      labels: ['Precio'],
	      xLabels: $xlabel,
	      xLabelAngle: 45,
	      resize: true,
	      lineColors: ['#33414E']
	    });
  	}
    
    var obtenerHistorico = function($id, $f_inicial, $f_final, $agrupar){

    	// Limpiar el grafico
    	$('#graficoHistorico').html('');
    	$('.btnCerrarModal').attr('disabled', 'disabled');
    	$('#procesarGrafico').attr('disabled', 'disabled');

    	if (semaforo == true) {
    		
    		semaforo = false;

    		$.get(webroot + 'socio/historico/' + $id + '/' + $f_inicial + '/' + $f_final + '/' + $agrupar, function(result) {

	        	var json 	= $.parseJSON(result),
	        		grafico = [];

	        	if (json.length > 0) {
	        		for (i in json) {
					    if (json.hasOwnProperty(i)) {
					    	grafico.push({ y : json[i]['y'], a: json[i]['a'] });
					    }
					}

					var xlabel;

					if ($agrupar == 'semana') {
						xlabel = 'week';
					}else{
						xlabel = 'day';
					}

					// Limpiar el grafico
		    		$('#graficoHistorico').html('');

		        	mostrarGrafico(grafico, xlabel);
	        	}else{
		    		$('#graficoHistorico').html('<label>No existen datos para la fecha consultada.</label>');
	        	}

	        	semaforo = true;
	        	$('.btnCerrarModal').removeAttr('disabled');
	        	$('#procesarGrafico').removeAttr('disabled');
	        });
    	}

    }


    var obtenerHistoricoPersonalizado = function(){

    	$('#procesarGrafico').on('click', function(){
    		var $competidor = $('#competidor').val(),
    			$f_inicial 	= $('#fechaInicial').val(),
    			$f_final 	= $('#fechaFinal').val(),
    			$agrupar 	= $('#agrupado').val();

    		obtenerHistorico($competidor, $f_inicial, $f_final, $agrupar);

    	});

    }


  	var modalGraficos = function(){

  		var mostrarModal = function($titulo, $id){
  			$('#modalSocios').find('.modal-title').html($titulo);
  			$('#modalSocios').find('#competidor').val($id);
  			$('#modalSocios').modal({
  				show : true,
  				backdrop: false 
  			});
  		}

  		var ejecutarModal = function(){
  			$(document).on('click', '.js-mostrar-grafico', function(){

  				var $ths 		= $(this),
  					$titulo 	= $ths.data('titulo'),
  					$competidor = $ths.data('competidor');

  				// Levantar el modal
  				mostrarModal($titulo, $competidor);

  				// Mostrar grafico
  				obtenerHistorico($competidor);

  			});
  		}

  		return {
  			init: function(){
  				if ($('.js-mostrar-grafico').length) {
  					ejecutarModal();
  				}

  				if ($('#procesarGrafico').length) {
  					obtenerHistoricoPersonalizado();
  				}
  			}
  		}

  	}();

  $(document).ready(function(){
    modalGraficos.init();
  });

})(jQuery);

