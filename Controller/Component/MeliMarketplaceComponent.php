<?

App::import('Vendor', 'Mercadolibre', array('file' => 'Meli/meli.php'));
App::uses('Component', 'Controller');

class MeliMarketplaceComponent extends Component
{	

	public static $MeliConexion;
	public static $accessToken;
	public $components = array('Session');
	public $estados = array(
		'approved' => 'Aprobar pedido',
	);


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
					
					if ($user['httpCode'] < 300) {
						// Now we save credentials with the authenticated user
						$m['Marketplace']['access_token']  = $user['body']->access_token;
						$m['Marketplace']['expires_token'] = time() + $user['body']->expires_in;
						$m['Marketplace']['refresh_token'] = $user['body']->refresh_token;
						$m['Marketplace']['seller_id']     = $user['body']->user_id;

						self::$accessToken = $user['body']->access_token;
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
						
						if ($refresh['httpCode'] < 300) {
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

					if (!isset($marketplace['MarketplaceTipo'])) {
						$marketplace['MarketplaceTipo']['nombre'] = ClassRegistry::init('MarketplaceTipo')->field('nombre', $marketplace['marketplace_tipo_id']);
					}

					#$response['success'] = sprintf('%s sigue conectado a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}
			}
			
			// save in db marketplace tokens
			if (!empty($m) && empty($response[$marketplace['id']]['errors'])) {

				ClassRegistry::init('Marketplace')->id = $marketplace['id'];

				if (!isset($marketplace['MarketplaceTipo'])) {
					$marketplace['MarketplaceTipo']['nombre'] = ClassRegistry::init('MarketplaceTipo')->field('nombre', $marketplace['marketplace_tipo_id']);
				}

				if (!ClassRegistry::init('Marketplace')->save($m)) {
					$response['errors'] = sprintf('%s no se logró conectar a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}else{

					# Guardamos los nuevos valores de conexion
					$this->Session->write('Marketplace.access_token', $m['Marketplace']['access_token']);
					$this->Session->write('Marketplace.expires_token', $m['Marketplace']['expires_token']);
					$this->Session->write('Marketplace.refresh_token', $m['Marketplace']['refresh_token']);
					$this->Session->write('Marketplace.seller_id', $m['Marketplace']['seller_id']);

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
			'access_token' => self::$accessToken,
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
         	
        	if ( $result['httpCode'] < 300 && count($result['body']->results) == 0  ) {
        		$ejecutar = false;
        	}elseif($result['httpCode'] < 300){

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
		
		if ($mensajes['httpCode'] < 300) {
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
		
		if ($detallesVenta['httpCode'] < 300) {
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
	public function mercadolibre_obtener_etiqueta_envio($detallesVenta, $type = 'zpl2')
	{	
		if (isset($detallesVenta['shipping']['id'])) {
			
			// No hay documentos
			if ($detallesVenta['shipping']['status'] != 'ready_to_ship') {
				return array();
			}

			$shipping_id = $detallesVenta['shipping']['id'];

			$curl = curl_init();

			if ($type == 'Y') {
				$endpoint = "https://api.mercadolibre.com/shipment_labels?shipment_ids=".$shipping_id."&savePdf=".$type."&access_token=" . self::$accessToken;
			}else{
				$endpoint = "https://api.mercadolibre.com/shipment_labels?shipment_ids=".$shipping_id."&response_type=".$type."&access_token=" . self::$accessToken;
			}

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $endpoint,
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

				if ($type != 'Y') {
					header('Content-type:application/zip');
					header('Content-Disposition:attachment;filename="'.$shipping_id.'.zip"');
					echo $response;
				}

				return $response;
			}
		}

		return;
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
		
		if ($producto['httpCode'] < 300) {
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
		
		if ($producto['httpCode'] < 300) {
			if (!empty($producto['body']) ) {
				return $producto['body'];
			}			
		}

		return array();
	}


	public function obtener_scroll_id($seller_id)
	{
		$params = array(
			'search_type' => 'scan',
			'access_token' => self::$accessToken,
		);

		try {
			$q = self::$MeliConexion->get('/users/' . $seller_id . '/items/search' , $params);
			$q = to_array($q);
		} catch (Exception $e) {
			# 
		}

		if (isset($q['body']['scroll_id'])) {
			return $q['body']['scroll_id'];	
		}

		return '';
	}


	public function mercadolibre_obtener_productos($seller_id = '', $limit = 50, $offset = 0, $scroll_id = '')
	{
		$params = array(
			'access_token' => self::$accessToken,
			'limit' => $limit,
			'offset' => $offset
		);

		if (!empty($scroll_id)) {
			$params = array(
				'search_type'  => 'scan',
				'access_token' => self::$accessToken,
				'scroll_id'    => $scroll_id
			);
		}

		try {
			$producto = self::$MeliConexion->get('/users/' . $seller_id . '/items/search' , $params);
			$producto = to_array($producto);
		} catch (Exception $e) {
			
		}
		
		if ($producto['httpCode'] < 300) {
			if (!empty($producto['body']) ) {
				return $producto['body'];
			}			
		}else{

		}

		return array();
	}


	public function mercadolibre_obtener_todos_productos($seller_id)
	{
		$productosMeli = $this->mercadolibre_obtener_productos($seller_id);
		$scroll_id     = $this->obtener_scroll_id($seller_id);

		$misProductos = array();

		if (!empty($productosMeli)) {

			$totalItems = $productosMeli['paging']['total'];
			$limite     = $productosMeli['paging']['limit'];
			$salto      = $productosMeli['paging']['offset'];
			
			$iteraciones = round($totalItems/$limite, 0)+1;

			for ($i=0; $i < $iteraciones; $i++) {
				
				$salto = $salto + $limite;

				$query = $this->mercadolibre_obtener_productos($seller_id, $limite, $salto, $scroll_id);	
				
				$misProductos[$i] = (!empty($query['results'])) ? $query['results'] : array();		

				if (count($misProductos[$i]) < 50 && !empty($misProductos[$i])) {
					break;
				}

			}
		}

		return Hash::flatten($misProductos);
	}



	public function mercadolibre_actualizar_stock($meli, $stock)
	{	
		// We construct the item to POST
		$item = array("available_quantity" => $stock);
		
		// We call the post request to list a item
		$result = self::$MeliConexion->put('/items/' . $meli, $item, array('access_token' => self::$accessToken));

		return $result;
	}


	public function mercadolibre_normalizar_seller_custom_field($seller_id)
	{
		$misProductosIds = $this->mercadolibre_obtener_todos_productos($seller_id);
		$misProductos = array();

		foreach ($misProductosIds as $idMCL) {
			$misProductos[$idMCL] = $this->mercadolibre_obtener_producto($idMCL);
		}

		foreach ($misProductos as $item) {
			
			# Buscamos en atributos el valor seller_sku
			$seller_sku = Hash::extract($item['attributes'], '{n}[id=SELLER_SKU].value_name');

			if (isset($seller_sku[0])) {

				$itemGuardar = array("seller_custom_field" => $seller_sku[0]);
			
				# Actualizamos el seller_custom_fields si corresponde
				$guardado = $this->update($item['id'], $itemGuardar);
					
			}

		}

		echo 'Finalizado';
		exit;
	}


	/**
	 * Categorias
	 */

	/**
	 * Sites puede ofrecerte la estructura de categorías para un país en particular
	 * 
	 * Más info: http://developers.mercadolibre.com/es/categoriza-productos/#Categor%C3%ADas-por-Site
	 * @return 	Objeto de categorias
	 */
	public function mercadolibre_obtener_categorias($siteId = 'MLC')
	{

		$result = self::$MeliConexion->get('/sites/'.$siteId.'/categories');

		return $result;
	}


	/**
	 * Tipos de publicación
	 * Existen diferentes tipos de publicación disponibles para cada país.
	 * El método obtiene los tipos de publicación disponibles para el pais
	 *
	 * Más información: http://developers.mercadolibre.com/es/publica-productos/#Tipos-de-publicacion
	 *
	 * @param $type 	$tring 		Identificador del tipo de publicación.
	 *
	 * @return 	Objeto de categorias
	 */
	public function mercadolibre_tipo_publicacion($type = '', $lista = false)
	{
		$result = to_array(self::$MeliConexion->get('/sites/MLC/listing_types/' . $type));

		$listType = array();
		if ($result['httpCode'] != 200) {
			return '';
		}else{
			foreach ($result['body'] as $k => $type) {
				$listType[$type['id']] = $type['name'];
			}
		}

		if ($lista) {
			return $listType;
		}

		return $result['body'];
	}


	/**
	 * El recurso de predicción de categorías fue creado para ayudar a vendedores y 
	 * desarrolladores a predecir en qué categoría se debería publicar un artículo determinado. 
	 * Actualmente se encuentra en funcionamiento en Argentina, Bolivia, Brasil, 
	 * Chile, Colombia, Costa Rica, Dominicana, Ecuador, Honduras, Guatemala, México, Nicaragua, 
	 * Paraguay, Panamá, Perú, Portugal, Salvador, Uruguay y Venezuela.
	 *
	 * Más info: http://developers.mercadolibre.com/es/api-prediccion-categorias/
	 * @param 	$title 				String 		El título del artículo a predecir. Debe ser un título completo 
	 *											en el idioma del sitio. Este parámetro es obligatorio.
	 * @param 	$category_from		String 		Este parámetro acepta una categoría de nivel 1 y se utiliza para 
	 *											limitar la predicción al subárbol que abarca desde category_from hasta la raíz. 
	 *											Este parámetro es opcional.
	 * @param 	$price 				String 		El precio del artículo a predecir. El objetivo de este parámetro 
	 * 											es ofrecer información adicional para mejorar la predicción. Este parámetro es opcional.
	 * @param 	$seller_id 			String 		ID del vendedor del artículo a predecir. El objetivo de este parámetro es ofrecer 
	 *											información adicional para mejorar la predicción. Este parámetro es opcional.
	 * @return 	Objeto de categorias
	 */
	public function mercadolibre_obtener_categoria_preferida($title, $category_from = '', $price = '', $seller_id = '')
	{	
		if (empty($title)) {
			return;
		}

		$params = array();

		$params['title'] = str_replace(' ', '%', $title);

		if (!empty($category_from)) {
			$params['category_from'] = $category_from;
		}

		if (!empty($price)) {
			$params['price'] = $price;
		}

		if (!empty($seller_id)) {
			$params['seller_id'] = $seller_id;
		}

		$result = self::$MeliConexion->get('/sites/MLC/category_predictor/predict', $params);

		return $result;
	}


	/**
	 * Categorías de segundo nivel o información relacionada con categorías específicas, 
	 * debemos utilizar el recurso Categorías y enviar el ID de categoría como parámetro.
	 * 
	 * Más info: http://developers.mercadolibre.com/es/categoriza-productos/#Categor%C3%ADas-por-Site
	 * @param 	$id 	string 		Identificador de la categoria
	 * @return 	Objeto de categorias
 	 */
	public function mercadolibre_obtener_categoria_por_id($id =  '')
	{
		return self::$MeliConexion->get(sprintf('/categories/%s', $id));
	}


	/************************************************************
							Envio
	*************************************************************/

	public function mercadolibre_obtener_modo_envio($category_id, $precio = '', $seller_id)
	{
		$params =  array(
			'category_id' => $category_id,
			'item_price' => round($precio, 0)
		);

		$result = to_array(self::$MeliConexion->get('/users/' . $seller_id . '/shipping_modes', $params));
		
		if ($result['httpCode'] != 200) {
			return;
		}else{
			return array_reverse($result);
		}
	}


	/**
	 * Calcula los costos de envío gratis por artículo
	 * 
	 * Más información : http://developers.mercadolibre.com/es/enviogratis/
	 * 
	 * @param  string  	$id   Identificador del item
	 * @param  string   $type   tipo de envio
	 * @return float       Precio del envío
	 */
	public function mercadolibre_obtener_costo_envio($id, $type = 'free')
	{
	
		$result = to_array(self::$MeliConexion->get('items/'.$id.'/shipping_options/' . $type));
		
		if ($result['httpCode'] != 200) {
			return;
		}else{
			return $result['body']['coverage']['all_country']['list_cost'];
		}
	}

	/**
	 * Función que retorna la información de envio de un producto
	 * @param $id 		String 		Identificador del producto en MELI
	 * @return array 	Arreglo con la información del envio del item
	 */
	public function mercadolbre_obtener_metodo_envio_item($id)
	{

		$result = to_array(self::$MeliConexion->get('items/'.$id.'/shipping_options'));
		
		if ($result['httpCode'] != 200) {
			return;
		}else{
			return $result['body'];
		}
	}


	/************************************************************
							Mi cuenta
	*************************************************************/



	public function admin_obtener_tiendas_oficiales($seller_id)
	{	
		
		$result = self::$MeliConexion->get('/users/' . $seller_id . '/brands');
		
		if ($result['httpCode'] != 200) {
			return array();
		}

		return to_array($result)['body']['brands'];
	}


	/**
	 * Actualizar un item en mercado libre
	 * 
	 * @param 	$id 					String 		Identificador de mercado libre del item.
	 * @param 	$title 					String 		El título es un atributo obligatorio y la clave para que los compradores encuentren 
	 *												tu producto; por eso, debes ser lo más específico posible.
	 * @param 	$price 					Bigint 		Éste es un atributo obligatorio: cuando defines un nuevo artículo, debe tener precio.
	 * @param 	$currency_id 			String 		Además del precio, debes definir una moneda. Este atributo también es obligatorio. 
	 *												Debes definirla utilizando un ID preestablecido.
	 * @param 	$available_quantity		String 		Este atributo define el stock, que es la cantidad de productos disponibles para la 
	 * 												venta de este artículo.
	 * @param 	$video_id 				String 		Identificador de video de Youtube
	 * @param  	$pictures 				Array 		Arreglo de imágenes con el formato array(array('source' => 'url_image'), array('source' => 'url_image_"'));
	 * 
	 * Más información en:  http://developers.mercadolibre.com/es/producto-sincroniza-modifica-publicaciones/#Actualiza-tu-art%C3%ADculo
	 *
	 * @return Arr devuelto por MELI	 
	 */
	public function update($id, $item = array())
	{			
		// We call the post request to list a item
		$result = self::$MeliConexion->put('/items/' . $id, $item, array('access_token' => self::$accessToken));
		$result = to_array($result);

		return $result;

	}


	/**
	 * Publicar un item en mercado libre
	 * 
	 * @param 	$title 					String 		El título es un atributo obligatorio y la clave para que los compradores encuentren 
	 *												tu producto; por eso, debes ser lo más específico posible.
	 * @param 	$category_id 			String 		Los vendedores deben definir una categoría en el site de MercadoLibre. 
	 * 												Este atributo es obligatorio y solo acepta ID preestablecidos.
	 * @param 	$price 					Bigint 		Éste es un atributo obligatorio: cuando defines un nuevo artículo, debe tener precio.
	 * @param 	$currency_id 			String 		Además del precio, debes definir una moneda. Este atributo también es obligatorio. 
	 *												Debes definirla utilizando un ID preestablecido.
	 * @param 	$available_quantity		String 		Este atributo define el stock, que es la cantidad de productos disponibles para la 
	 * 												venta de este artículo.
	 * @param 	$buying_mode			String 		Define el tipo de publicación (Vender ahora/ Subasta)
	 * @param 	$listing_type_id 		String 		Es otro caso de un atributo obligatorio que solo acepta valores predefinidos y es muy importante que lo entiendas.
	 *												Existen diferentes tipos de publicación disponibles para cada país. Debes realizar una 
	 *												llamada mixta a través de los sites y recursos listing_types para conocer los listing_types soportados.
	 * @param 	$condition 				String 		Nuevo /Usado
	 * @param 	$description 			Text 		Descripción del prodcuto en HTML o texto plano
	 * @param 	$video_id 				String 		Identificador de video de Youtube
	 * @param 	$warranty 				Text 		Texto que describe la garantía del item
	 * @param  	$pictures 				Array 		Arreglo de imágenes con el formato array(array('source' => 'url_image'), array('source' => 'url_image_"'));
	 * 
	 * Más información en:  http://developers.mercadolibre.com/es/publica-productos/#Publica-un-articulo
	 *
	 * @return Objeto devuelto por MELI	 
	 */
	public function publish($item = array(), $agregarCostoEnvio = true, $precioAdicional = 0)
	{	

		if (empty($item)) {
			return '';
		}
		
		# Validate item with MEli validator api
		$validItem = $this->mercadolibre_validar_item($item);
		
		if ($validItem['httpCode'] >= 300) {
			return $validItem;
		}else{

			$publicar = to_array(self::$MeliConexion->post('/items', $item, array('access_token' => self::$accessToken)));
			
			$actualizar = array();

			# Actualizamos el precio agregandole el costo de envio
			if ($agregarCostoEnvio && $publicar['httpCode'] < 300) {
				$costoEnvio = $this->mercadolibre_obtener_costo_envio($publicar['body']['id']);
				$actualizar['price'] = $publicar['body']['price'] + $costoEnvio;
			}

			if ($precioAdicional > 0 && $publicar['httpCode'] < 300) {
				$actualizar['price'] = (!empty($actualizar)) ? round($actualizar['price'] / $precioAdicional, 0) : round($publicar['body']['price'] / $precioAdicional, 0) ;
			}


			# subir imágenes
			if (!empty($item['pictures'])) {
				$actualizar['pictures'] =  $item['pictures'];
			}


			if (!empty($actualizar)) {
				#debug($actualizar);
				sleep(5);
				$modificar = $this->update($publicar['body']['id'], $actualizar);
				return $modificar;
				#prx($modificar);
			}else{
				return $publicar;
			}
		}

	}


	public function mercadolibre_modificar_descripcion($id, $desc = '')
	{
		$body = array('plain_text' => $desc);

		$response = self::$MeliConexion->put(sprintf('/items/%s/description', $id), $body,  array('access_token' => self::$accessToken));

		return $response;
	}


	/**
	 * Modificar un item en mercado libre
	 * 
	 * @param 	$title 					String 		El título es un atributo obligatorio y la clave para que los compradores encuentren 
	 *												tu producto; por eso, debes ser lo más específico posible.
	 * @param 	$category_id 			String 		Los vendedores deben definir una categoría en el site de MercadoLibre. 
	 * 												Este atributo es obligatorio y solo acepta ID preestablecidos.
	 * @param 	$price 					Bigint 		Éste es un atributo obligatorio: cuando defines un nuevo artículo, debe tener precio.
	 * @param 	$currency_id 			String 		Además del precio, debes definir una moneda. Este atributo también es obligatorio. 
	 *												Debes definirla utilizando un ID preestablecido.
	 * @param 	$available_quantity		String 		Este atributo define el stock, que es la cantidad de productos disponibles para la 
	 * 												venta de este artículo.
	 * @param 	$buying_mode			String 		Define el tipo de publicación (Vender ahora/ Subasta)
	 * @param 	$listing_type_id 		String 		Es otro caso de un atributo obligatorio que solo acepta valores predefinidos y es muy importante que lo entiendas.
	 *												Existen diferentes tipos de publicación disponibles para cada país. Debes realizar una 
	 *												llamada mixta a través de los sites y recursos listing_types para conocer los listing_types soportados.
	 * @param 	$condition 				String 		Nuevo /Usado
	 * @param 	$description 			Text 		Descripción del prodcuto en HTML o texto plano
	 * @param 	$video_id 				String 		Identificador de video de Youtube
	 * @param 	$warranty 				Text 		Texto que describe la garantía del item
	 * @param  	$pictures 				Array 		Arreglo de imágenes con el formato array(array('source' => 'url_image'), array('source' => 'url_image_"'));
	 * 
	 * Más información en:  http://developers.mercadolibre.com/es/publica-productos/#Publica-un-articulo
	 *
	 * @return Objeto devuelto por MELI	 
	 */
	public function modified_item($id, $item = array(), $agregarCostoEnvio = true, $precioAdicional = 0, $descripcion = '')
	{	

		if (empty($item) || empty($id)) {
			return '';
		}

		$publicar = to_array(self::$MeliConexion->put('/items/' . $id, $item, array('access_token' => self::$accessToken)));
		
		$actualizar = array();
			
		# Actualizamos el precio agregandole el costo de envio
		if ($agregarCostoEnvio && $publicar['httpCode'] < 300) {
			$costoEnvio = $this->mercadolibre_obtener_costo_envio($publicar['body']['id']);
			$actualizar['price'] = $publicar['body']['price'] + $costoEnvio;
		}

		if ($precioAdicional > 0 && $publicar['httpCode'] < 300) {
			$actualizar['price'] = (!empty($actualizar)) ? round($actualizar['price'] / $precioAdicional, 0) : round($publicar['body']['price'] / $precioAdicional, 0) ;
		}


		# subir imágenes
		if (!empty($item['pictures'])) {
			$actualizar['pictures'] =  $item['pictures'];
		}

		if (!empty($descripcion)) {
			$this->mercadolibre_modificar_descripcion($id, $descripcion);
		}

		if (!empty($actualizar)) {
			#debug($actualizar);
			sleep(2);
			$modificar = $this->update($publicar['body']['id'], $actualizar);
			return $modificar;
			#prx($modificar);
		}else{
			return $publicar;
		}

	}


	/**
	 * Valida que un item esté correctamente formateado según 
	 * la informacion de MELI.
	 *
	 * Más información: http://developers.mercadolibre.com/es/validador-de-publicaciones/
	 *
	 * @param 	$item 	$array() 	Item para validar
	 * 
	 * @return Objeto
	 */
	public function mercadolibre_validar_item($item = array())
	{
		if (!empty($item)) {
			return self::$MeliConexion->post('/items/validate', $item, array('access_token' => self::$accessToken));
		}
	}


	/**
	 * Más información: https://developers.mercadolibre.cl/es_ar/producto-sincroniza-modifica-publicaciones#Cambiar-los-estados-de-las-publicaciones
	 * @param  string 	$id     Identificador Meli
	 * @param  string 	$estado paused, closed, active
	 * @return array
	 */
	public function mercadolibre_cambiar_estado($id, $estado)
	{	
		$states = array('closed', 'paused', 'active');
		
		if (!in_array($estado, $states)) {
			return;
		}

		$item = array(
			"status" => $estado
		);

		$res = $this->update($id, $item);
	
		if ($estado == 'closed') {
			return $this->mercadolibre_eliminar_producto($id); // Se elimina de meli
		}

		return $res;

	}


	/**
	 * Eliminar item desde Meli
	 * Más información https://developers.mercadolibre.cl/es_ar/producto-sincroniza-modifica-publicaciones#Elimina-publicaciones
	 * @param  string 	$id 	Id MEli
	 * @return array  
	 */
	public function mercadolibre_eliminar_producto($id)
	{
		$item = array(
			"deleted" => true
		);

		return $this->update($id, $item);

	}

}