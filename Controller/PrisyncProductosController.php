<?php
App::uses('AppController', 'Controller');

class PrisyncProductosController extends AppController {

	public function admin_ver_resultados_productos()
	{
		$productos = $this->PrisyncProducto->find('all', array(
			'contain' => array(
				'PrisyncRuta'
			)
		));

		if (empty($productos)) {
			$response = array(
				'code'    => 200,
				'message' => 'No se encontraron productos',
				'value'   => 0
			);

			echo json_encode($response);
			exit;
		}

		$resultados		= array();
		$productosAlta  = array();
		$productosBaja  = array();
		$productosIgual = array();
		$contAlta       = 0;
		$contBaja       = 0;
		$contIgual      = 0;

		$competidores = array();
		$micompania = 'toolmania';

		foreach ($productos as $ip => $producto) {
			foreach ($producto['PrisyncRuta'] as $ir => $competidor) {
				$url      = parse_url($competidor['url']);
				$compania = explode('.', str_replace('www.', '', $url['host']));
				
				$competidores[$ip][$compania[0]]['url']          = $compania[0];
				$competidores[$ip][$compania[0]]['product_id'] 	= $producto['PrisyncProducto']['id'];
				$competidores[$ip][$compania[0]]['product_name'] = $producto['PrisyncProducto']['name'];
				$competidores[$ip][$compania[0]]['product_code'] = $producto['PrisyncProducto']['internal_code'];
				$competidores[$ip][$compania[0]]['price']        = $competidor['price'];
				
			}	
		}


		if (!empty($competidores)) {
			
			$base = (int) 0;
			
			foreach ($competidores as $ic => $competidor) {

				if (array_key_exists($micompania, $competidor)) {
					
					$base = $competidor[$micompania]['price'];

					foreach ($competidor as $ico => $comp) {
						if ($ico != $micompania) {
							if ($comp['price'] > $base) {
								$contBaja                    = $contBaja + 1;
								$resultados[$ico]['Alto']['total'] = $contBaja;		
							}

							if ($comp['price'] < $base) {
								$contAlta                    = $contAlta + 1;
								$resultados[$ico]['Bajo']['total'] = $contAlta;		
							}

							if ($comp['price'] == $base) {
								$contIgual                    = $contIgual + 1;
								$resultados[$ico]['Igual']['total'] = $contIgual;		
							}
						}
					}
				}
							
			}
			
		}

		$response = array(
			'code'    => 200,
			'message' => 'Resultados de la operación',
			'value'   => $resultados
		);

		echo json_encode($response);
		exit;
	}

}