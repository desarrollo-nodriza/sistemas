<div id="">
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<div class="panel panel-primary">
				<div class="panel-body">
					<div class="form-group">
						<label>Estado de ventas</label>
						<select id="estado-venta" class="form-control">
							<?=$this->Html->crear_opciones_por_arraglo($estados); ?>
						</select>
					</div>
					<div class="form-group">
						<label>Metodo de envio</label>
						<select id="metodo-envio" class="form-control">
							<?=$this->Html->crear_opciones_por_arraglo($metodoEnvios); ?>
						</select>
					</div>
				</div>
				<div id="lista-ventas" data-token="<?= $token;?>" class="panel-body list-group list-group-contacts" style="max-height: 400px; overflow-y: auto;"></div>
			</div>
		</div>
		<div class="col-xs-12 col-md-9">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label for="direeccion-1">Dirección de origen</label>
					<input id="direeccion-1" class="form-control" type="text" placeholder="Calle/pasaje y número">
					<span id="direeccion-1-detalle" class="help-block"></span>
				</div>
				<div class="form-group">
					<label for="direeccion-2">Dirección de destino</label>
					<input id="direeccion-2" class="form-control" type="text" placeholder="Calle/pasaje y número">
					<span id="direeccion-2-detalle" class="help-block"></span>
				</div>
			</div>
		</div>
		
		<button id="calcular" class="btn btn-primary btn-block">Calcular distancia entre 2 puntos</button>
		
		<div id="resultado">
			<a class="list-group-item" href="#">Sin resultados</a>
		</div>

<script>

		var objCurrentAddress = {
			'origin' : '',
			'destiny' : ''
		};


		function toRad($n)
		{
			return $n * Math.PI / 180;
		}


		function calcularDesitanciaEntreDosPuntosTerrestres(lat1, lon1, lat2, lon2, earthradio = 6371)
		{
			var x1   = lat2-lat1;
			var dLat = toRad(x1);  
			var x2   = lon2-lon1;
			var dLon = toRad(x2);  
			
			var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2);  
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = earthradio * c; 
			
			return parseFloat(d).toFixed(2);
		}

		function obtener_lat_long($address, $inx)
		{
			$.ajax({
				url: 'https://nominatim.openstreetmap.org/search',
				data: {
					q: $address,
					format: 'json',
					country: 'Chile'
				},
			})
			.done(function(res) {
				
				objCurrentAddress[$inx] = res[0];
				calcular();

			})
			.fail(function() {
				
			})
			.always(function() {
				
			});
			
		}


		function calcular()
		{
			if (objCurrentAddress.origin != '' && objCurrentAddress.destiny != '') {
				$('#direeccion-1-detalle').text(objCurrentAddress.origin.display_name);
				$('#direeccion-2-detalle').text(objCurrentAddress.destiny.display_name);

				var lat1 = objCurrentAddress.origin.lat,
					lon1 = objCurrentAddress.origin.lon,
					lat2 = objCurrentAddress.destiny.lat,
					lon2 = objCurrentAddress.destiny.lon;

				var distancia = calcularDesitanciaEntreDosPuntosTerrestres(lat1, lon1, lat2, lon2);
				
				$('#resultado').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button> La distancia es de ' + distancia + ' km</div>');

			}

		}


		function obtener_ventas()
		{
			var estado_venta = $('#estado-venta').val(),
				metodo_envio = $('#metodo-envio').val(),
				access_token = $('#lista-ventas').data('token');

			$.ajax({
				url: webroot + 'api/ventas.json',
				data: {
					token: access_token,
					estado: estado_venta,
					envio: metodo_envio,
					limit: 100,
				},
			})
			.done(function(res) {
				
				var $lista_html = '';

				if (res.ventas.lengh == 0) {
					$lista_html += '<a class="list-group-item" href="#">Sin resultados</a>';
				}else{
					for (var i = 0; i < res.ventas.length; i++) {
						$lista_html += '<a href="#" class="list-group-item js-agregar-listado" data-id="'+res.ventas[i].Venta.id+'">';                                    
	                    $lista_html += '	<span class="pull-left">#' + res.ventas[i].Venta.id + '</span>';
	                    $lista_html += '	<span class="contacts-title">' + res.ventas[i].Venta.picking_estado + '</span>';
	                    $lista_html += '	<p>' + res.ventas[i].Venta.direccion_entrega + ' ' +  res.ventas[i].Venta.comuna_entrega + '</p>';                                    
	                	$lista_html += '</a>';
					}
				}

				$('#lista-ventas').html($lista_html);

			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});
			
		}


		$(document).ready(function(){

			$('#calcular').on('click', function(){
				
				var origen 	= $('#direeccion-1').val(),
					destino = $('#direeccion-2').val();

				if (origen == '' || destino == '') {
					alert('Origen y destino no debe estar vacio.');
				}

				obtener_lat_long(origen, 'origin');
				obtener_lat_long(destino, 'destiny');

			});

			$('#estado-venta').on('change', function(){

				obtener_ventas();

			});

			$('#metodo-envio').on('change', function(){

				obtener_ventas();

			});

			obtener_ventas();

		});	

</script>
