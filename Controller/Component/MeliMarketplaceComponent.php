<?

App::import('Vendor', 'Mercadolibre', array('file' => 'Meli/meli.php'));

class MeliMarketplaceComponent extends Component
{	

	public static $MeliConexion;
	public static $accessToken;



	public function crearCliente($apiuser, $apikey, $accesstoken, $refreshtoken)
	{	
		self::$MeliConexion = new Meli($apiuser, $apikey, $accesstoken, $refreshtoken);
	}


	/**
	 * Método encargado de conectar MELI con Sistemas
	 * @param  array  $marketplace Arreglo con la información del Marketplace 
	 * @param  string $redirectURI Url de redirección para el login de MEli
	 * @param  string $siteId      Identificador de API (MLC = Mercadolibre Chile)
	 * @return array  			   Información del procedimiento	
	 */
	public function mercadolibre_conectar($code = '', $marketplace = array(), $redirectURI = '', $siteId = 'MLC') 
	{	
		$m = array();
		$response = array(
			'access' => array(),
			'success' => array(),
			'errors' => array()
		);

		if(!empty($code) || !empty($marketplace['access_token'])) {
			// If code exist and session is empty
			if(!empty($code) && empty($marketplace['access_token'])) {
				// //If the code was in get parameter we authorize
				try{

					$user = self::$MeliConexion->authorize($code, $redirectURI);
					
					if ($user['httpCode'] == 200) {
						// Now we save credentials with the authenticated user
						$m['Marketplace']['access_token']  = $user['body']->access_token;
						$m['Marketplace']['expires_token'] = time() + $user['body']->expires_in;
						$m['Marketplace']['refresh_token'] = $user['body']->refresh_token;
						$m['Marketplace']['seller_id']     = $user['body']->user_id;

						self::$accessToken = $refresh['body']->access_token;
					}

				}catch(Exception $e){
					$response['errors'] = $e->getMessage();
				}
			} else {
				// We can check if the access token in invalid checking the time
				if($marketplace['expires_token'] < time()) {

					try {
						// Make the refresh proccess
						$refresh = self::$MeliConexion->refreshAccessToken();
						
						if ($refresh['httpCode'] == 200) {
							// Now we save credentials with the new parameters
							$m['Marketplace']['access_token']  = $refresh['body']->access_token;
							$m['Marketplace']['expires_token'] = time() + $refresh['body']->expires_in;
							$m['Marketplace']['refresh_token'] = $refresh['body']->refresh_token;
							$m['Marketplace']['seller_id']     = $refresh['body']->user_id;

							self::$accessToken = $refresh['body']->access_token;

						}
					} catch (Exception $e) {
					  	$response['errors'] = $e->getMessage();
					}
				}else{

					self::$accessToken = $marketplace['access_token'];

					$response['success'] = sprintf('%s sigue conectado a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}
			}
			
			// save in db marketplace tokens
			if (!empty($m) && empty($response[$marketplace['id']]['errors'])) {

				ClassRegistry::init('Marketplace')->id = $marketplace['id'];
				if (!ClassRegistry::init('Marketplace')->save($m)) {
					$response['errors'] = sprintf('%s no se logró conectar a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}else{
					$response['success'] = sprintf('%s se ha conectado con éxito a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}
			}

			return $response;

		} else {

			$response['access']['marketplace'] = $marketplace['nombre'];
			$response['access']['url'] = self::$MeliConexion->getAuthUrl($redirectURI, Meli::$AUTH_URL[$siteId]);
			
			return $response;
		}
	}


	/**
	 * Obtiene las ventas desde mercadolibre
	 * @param  array   $marketplace Arreglo con la información del Marketplace
	 * @param  integer $offset      salto del puntero en la búsqueda de ventas
	 * @return array                Arreglo con las ventas obtenidas
	 */
	public function mercadolibre_obtener_ventas($marketplace = array(), $offset = 0)
	{	
		$limit = 50; // Máximo número de items por request

        // Se previene el ingreso de un offset mayor a 1000
        if ($offset > 50) {
        	$offset = 50;
        }

        $params = array(
			'access_token' => $marketplace['access_token'],
			'seller'       => $marketplace['seller_id'],
			'limit'        => $limit,
			'offset'       => $offset
		);

		//se obtiene la última venta registrada para consultar solo las nuevas a mercadolibre
		$venta = ClassRegistry::init('Venta')->find(
			'first',
			array(
				'conditions' => array(
					'Venta.marketplace_id' => $marketplace['id']
				),
				'fields' => array(
					'Venta.fecha_venta'
				),
				'order' => 'Venta.fecha_venta DESC'
			)
		);

		if (!empty($venta)) { //ventas a partir de la última registrada

			$fecha = date_create($venta['Venta']['fecha_venta'] . ' -0400');
			
			$ahora = date_create(date('Y-m-d H:i:s'));

			date_modify($fecha, '+1 second');
			date_modify($fecha, '-1 hour');
			
			$fecha = date_format($fecha, 'c');
			$ahora = date_format($ahora, 'c');
			
			// Filtro
	        $params = array_replace_recursive($params, array(
				"order.date_created.from"   => $fecha,
				"order.date_created.to"     => $ahora
	        	)
	    	);

		}
		

        $ventasArr['ventasMercadolibre'] = array(); // Almacena las ventas para ser retornado
        $ejecutar = true; // Semaforo que determina si continua haciendo request
        $cont = 0; // Iterador para listar las ventas

        // Obtendrá las ventas hasta que $result['body']->results === 0
        while ($ejecutar) {
        	
         	$result = self::$MeliConexion->get('/orders/search', $params);
         	
        	if ( $result['httpCode'] == 200 && count($result['body']->results) == 0  ) {
        		$ejecutar = false;
        	}elseif($result['httpCode'] == 200){

        		$params['offset'] = $params['offset'] + 50;
        		
        		foreach ($result['body']->results as $ir => $venta) {
        			if (!empty($venta)) {

        				$arrVenta = to_array($venta);

        				$ventaExiste =  ClassRegistry::init('Venta')->find('count', array('conditions' => array('Venta.id_externo' => $arrVenta['id'])));

        				if (!$ventaExiste) {
        					$ventasArr['ventasMercadolibre'][$cont] = $arrVenta;	
        				}
        			}
        			$cont++;
        		}
        	}else{
        		$ejecutar = false;
        	}
        }
		return $ventasArr;
	}


	/**
	 * Obtiene los mensajes de un pedido dado desde Meli
	 * @param  string $access_token token de acceso para peticiones privadas
	 * @param  string $id           Identificador de la venta de Meli
	 * @return array  				Mensajes del pedido
	 */
	public static function mercadolibre_obtener_mensajes($access_token = '', $id = '')
	{	
		$params = array(
			'access_token' => $access_token,
			'limit' => 20
		);

		try {
			$mensajes = self::$MeliConexion->get('/messages/orders/' . $id, $params);
			$mensajes = to_array($mensajes);
			
		} catch (Exception $e) {
			// 
		}
		
		if ($mensajes['httpCode'] == 200) {
			return $mensajes['body']['results'];
		}else{
			return array();
		}
	}



	/**
	 * Obtener detalle de una venta desde Meli por su ID 
	 * @param  string $access_token token de acceso para peticiones privadas
	 * @param  string $id           Identificador de la venta de Meli
	 * @param  bool  $todo 			True: Retorna toda la respuesta d el GET False: retorna solo los productos del GET
	 * @return array               	Detalle de la venta
	 */
	public function mercadolibre_obtener_venta_detalles($access_token = '', $id = '', $todo = false)
	{
		$params = array(
			'access_token' => $access_token
		);
		
		try {
			$detallesVenta = self::$MeliConexion->get('/orders/' . $id, $params);
			$detallesVenta = to_array($detallesVenta);
		} catch (Exception $e) {
			//
		}

		if ($detallesVenta['httpCode'] == 200) {
			if ($todo) {
				return $detallesVenta['body']; # Retornamos toda la venta
			}else{
				return Hash::extract($detallesVenta['body'], 'order_items.{n}'); # Retornamos solo los productos de la venta
			}
		}else{
			return array();
		}

	}


	/**
	 * Método encargado de obtener y crear un cliente registrado en Meli
	 * @param  array  $DataVenta Información de la venta ref: mercadolibre_obtener_venta_detalles
	 * @return bigint	         Identificador del Cliente creado o existente
	 */
	public function mercadolibre_obtener_cliente ($DataVenta = array()) {

		$rut = $DataVenta['buyer']['billing_info']['doc_number'];

		$VentaCliente = array();

		if (!empty($rut)) {
			$VentaCliente = ClassRegistry::init('VentaCliente')->find(
				'first',
				array(
					'conditions' => array(
						'VentaCliente.rut' => $rut
					),
					'fields' => array(
						'VentaCliente.id'
					)
				)
			);
		}

		if (!empty($VentaCliente)) {
			return $VentaCliente['VentaCliente']['id'];
		}

		//si el cliente no existe, se crea
		$data = array();
		$data['VentaCliente']['nombre']   = $DataVenta['buyer']['first_name'];
		$data['VentaCliente']['apellido'] = $DataVenta['buyer']['last_name'];
		$data['VentaCliente']['email']    = $DataVenta['buyer']['email'];
		$data['VentaCliente']['telefono'] = $DataVenta['buyer']['phone']['area_code'] . $DataVenta['buyer']['phone']['number'];
		$data['VentaCliente']['rut']      = $rut;

		ClassRegistry::init('VentaCliente')->create();
		ClassRegistry::init('VentaCliente')->save($data);

		return ClassRegistry::init('VentaCliente')->id;

	}


	/**
	 * Obtiene los documentos adjuntos de una venta en mercadolibre
	 * @param  string 	$access_token token de acceso a meli
	 * @param  array 	$detallesVenta Arreglo con la información de la venta
	 * @param  string 	$type 		  formato de retorno         	
	 * @return OBJ
	 */
	public function mercadolibre_obtener_etiqueta_envio($access_token, $detallesVenta, $type = 'zpl2')
	{	
		if (isset($detallesVenta['shipping']['id'])) {
			
			// No hay documentos
			if ($detallesVenta['shipping']['status'] != 'ready_to_ship') {
				return array();
			}

			$shipping_id = $detallesVenta['shipping']['id'];

			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.mercadolibre.com/shipment_labels?shipment_ids=".$shipping_id."&response_type=".$type."&access_token=" . $access_token,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
			    "Cache-Control: no-cache",
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  	echo "cURL Error #:" . $err;
			} else {
				header('Content-type:application/zip');
				header('Content-Disposition:attachment;filename="'.$shipping_id.'.zip"');

			  	echo $response;
			}
		}

		exit;
	}


	public function mercadolibre_producto_existe($id_externo, $seller_id )
	{
		$params = array(
			'sku' => $id_externo,
			'access_token' => self::$accessToken
		);

		$detalle = array(
			'existe' => 0,
			'item' => array()
		);
		
		try {
			$producto = self::$MeliConexion->get('/users/' . $seller_id . '/items/search' , $params);
			$producto = to_array($producto);
		} catch (Exception $e) {
			//
		}
		
		if ($producto['httpCode'] == 200) {
			if (!empty(Hash::extract($producto['body'], 'results.{n}')) ) {
				
				$item                   = $this->mercadolibre_obtener_producto($producto['body']['results'][0]);
				
				if (!empty($item)) {
					$detalle['existe']        = 1;
					$item['precio']           = round($item['base_price']);
					$item['estado']           = $item['status'];
					$item['stock_disponible'] = $item['available_quantity']; 
					$detalle['item']          = $item;
				}

			}			
		}

		return $detalle;

	}

	/**
	 * [mercadolibre_obtener_producto description]
	 * @param  [type] $meli [description]
	 * @return [type]       [description]
	 */
	public function mercadolibre_obtener_producto($meli)
	{
		$params = array(
			'access_token' => self::$accessToken
		);

		try {
			$producto = self::$MeliConexion->get('items/' . $meli , $params);
			$producto = to_array($producto);
		} catch (Exception $e) {
			//
		}
		
		if ($producto['httpCode'] == 200) {
			if (!empty($producto['body']) ) {
				return $producto['body'];
			}			
		}

		return array();
	}



	public function mercadolibre_actualizar_stock($meli, $stock)
	{	
		// We construct the item to POST
		$item = array("available_quantity" => $stock);
		
		// We call the post request to list a item
		$result = self::$MeliConexion->put('/items/' . $meli, $item, array('access_token' => self::$accessToken));

		return $result;
	}
}