<? 
	App::uses('AppHelper', 'View/Helper');
	
	App::uses('GeoReferenciaComponent', 'Controller/Component');

	class ChilexpressHelper extends AppHelper {

		public function obtenerRegion($r = '')
		{	
			$collection = new ComponentCollection();
        	$geo = new GeoReferenciaComponent($collection);

        	/* Obtener regiones */
			try {
				$resultado = $geo->obtenerRegiones();
			} catch (Exception $e) {
				$resultado = $e;
			}

			$resultado = to_array($resultado);

			if (isset($resultado['respObtenerRegion']['CodEstado']) && $resultado['respObtenerRegion']['CodEstado'] == 0) {
				foreach ($resultado['respObtenerRegion']['Regiones'] as $ir => $region) {
					if ($region['idRegion'] == $r) {
						return $region['GlsRegion'];
					}
				}	
			}

			return $r;
		}


		public function verEtiqueta($imagen = '', $otcode = '', $barcode = '')
		{	
			$collection = new ComponentCollection();
        	$ot = new OtComponent($collection);

        	if (!empty($imagen) && !empty($otcode) && !empty($barcode)){
        		$etiqueta = $ot->verEtiqueta($imagen, $otcode, $barcode);

        		return $etiqueta;
        	}
        	
		}

		
	}
?>