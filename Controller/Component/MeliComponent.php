<?php
App::uses('Component', 'Controller');
App::uses('AppController', 'Controller');
App::import('Vendor', 'Meli', array('file' => 'Meli/meli.php'));


class MeliComponent extends Component
{	
	public $meli;
	private $client_id;
	private $client_secret;

	public $components = array('Session');

	public $config = array(
		'client_id' 	=> '4986113264194236',
		'client_secret' 	=> 'bpygMfP7pX5dKrKSgK5LHF37kJxCZ2Rv'
	);

	public function initialize(Controller $controller)
	{
    	$this->Controller = $controller;

		try
		{
			Configure::load('meli');
		}
		catch ( Exception $e )
		{
			throw new Exception('No se encontró el archivo Config/meli.php');
		}
	}

	private function setComponentConfig ()
	{
		$app = new AppController();
    	$tienda = $app->tiendaConf($this->Session->read('Tienda.id'));

    	if (!empty($tienda)) {
    		$this->client_id = Configure::read(sprintf('Meli.%s.client_id', $tienda));
    		$this->client_secret = Configure::read(sprintf('Meli.%s.client_secret', $tienda));
    	}
	}

    public function checkTokenAndRefreshIfNeed()
    {	
    	# Configuración de la tienda
    	$this->setComponentConfig();

    	if( $this->Session->read('Meli.expires_in') < time()) {
    		try {

    			$this->meli = new Meli($this->client_id, $this->client_secret);

				// Make the refresh proccess
				$refresh = to_array($this->meli->refreshAccessToken());

				if (empty($refresh)) {
					return;
				}

				// Now we create the sessions with the new parameters
				$this->Session->write('Meli.access_token', $refresh['body']['access_token']);
				$this->Session->write('Meli.expires_in', time() + $refresh['body']['expires_in']);
				$this->Session->write('Meli.refresh_token', $refresh['body']['refresh_token']);

			} catch (Exception $e) {
			  	echo "Exception: ",  $e->getMessage(), "\n";
			}
    	}
    }


	public function login($code = '', $callbackUrl = '') {

		# Configuración de la tienda
    	$this->setComponentConfig();

		$this->meli = new Meli($this->client_id, $this->client_secret);
		// If the code was in get parameter we authorize
		$user = to_array($this->meli->authorize($code, $callbackUrl ));

		if (empty($user)) {
			return;
		}
		
		// Now we create the sessions with the authenticated user
		$this->Session->write('Meli.access_token', $user['body']['access_token']);
		$this->Session->write('Meli.expires_in', time() + $user['body']['expires_in']);
		$this->Session->write('Meli.refresh_token', $user['body']['refresh_token']);

	}

	public function getAuthUrl($redirect_uri)
	{	
		# Configuración de la tienda
    	$this->setComponentConfig();

		$this->meli = new Meli($this->client_id, $this->client_secret);

		return $this->meli->getAuthUrl($redirect_uri, Meli::$AUTH_URL['MLC']);
	}


	/**
	 * Si te encuentras logueado en MercadoLibre y tienes un token podrás hacer la siguiente 
	 * llamada y conocer qué información se encuentra relacionada a tu usuario.
	 *
	 * Más info: http://developers.mercadolibre.com/es/producto-consulta-usuarios/#Consultar-mis-datos-personales
	 * @return 	Objeto de datos de mi cuenta
	 */
	public function getMyAccountInfo()
	{
		# Configuración de la tienda
    	$this->setComponentConfig();

		$this->meli = new Meli($this->client_id, $this->client_secret);
		$params = array('access_token' => $this->Session->read('Meli.access_token'));
		$result = $this->meli->get('/users/me', $params);

		return $result;
	}


	public function getMyBrands()
	{
		# Configuración de la tienda
    	$this->setComponentConfig();

		$this->meli = new Meli($this->client_id, $this->client_secret);
		
		$me = json_decode(json_encode($this->getMyAccountInfo()), true);
		
		if ($me['httpCode'] != 200) {
			$result = '';
		}else{
			$result = $this->meli->get('/users/' . $me['body']['id'] . '/brands');
		}
		
		if ($result['httpCode'] != 200) {
			$result = '';
		}

		return $result;
	}

	public function createTestUsr($name = '')
	{	
		$params = array('access_token' => $this->Session->read('Meli.access_token'));
		$data = array('site_id' => 'MLC');
		$result = $this->meli->post('/users/' . $name, $data, $params);

		return $result;
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
	public function getSiteCategories()
	{
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);
		$result = $this->meli->get('/sites/MLC/categories');

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
	public function getCategoriesByIdentifier($id =  '')
	{
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);
		$result = $this->meli->get(sprintf('/categories/%s', $id));

		return $result;
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
	public function getCategoriesByPredictor($title, $category_from = '', $price = '', $seller_id = '')
	{	
		if (empty($title)) {
			return;
		}

		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);

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

		$result = $this->meli->get('/sites/MLC/category_predictor/predict', $params);

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
	public function listing_types($type = '')
	{
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);

		if (!empty($type)) {
			$result = json_decode(json_encode($this->meli->get('/sites/MLC/listing_types/' . $type)), true);
		}else{
			$result = json_decode(json_encode($this->meli->get('/sites/MLC/listing_types/' . $type)), true);
		}

		$listType = array();
		if ($result['httpCode'] != 200) {
			return '';
		}else{
			foreach ($result['body'] as $k => $type) {
				$listType[$type['id']] = $type['name'];
			}
		}

		return $listType;
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
	public function validate($item = array())
	{
		if (!empty($item)) {
			return $this->meli->post('/items/validate', $item, array('access_token' => $this->Session->read('Meli.access_token')));
		}
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
	public function publish($title, $category_id, $price, $currency_id = 'CLP', $available_quantity = 1, $buying_mode = 'buy_it_now', $listing_type_id, $condition = 'new', $description = 'Item de test - No Ofertar', $video_id = '', $warranty = '', $pictures = array(), $shipping = array() )
	{	

		if (empty($title) ||
			empty($category_id) ||
			empty($price) ||
			empty($listing_type_id) ) {
			return '';
		}

		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);

		// We construct the item to POST
		$item = array(
			"title" => $title,
			"category_id" => $category_id,
			"price" => $price,
			"currency_id" => $currency_id,
			"available_quantity" => $available_quantity,
			"buying_mode" => $buying_mode,
			"listing_type_id" => $listing_type_id,
			"condition" => $condition,
			"video_id" => $video_id,
			"warranty" => $warranty,
			"pictures" => $pictures,
			"tags" => array(
		        "immediate_payment"
		    ),
		    "description" => $description,
		    "shipping" => $shipping
		);
		
		# Validate item with MEli validator api
		$validItem = $this->validate($item);

		if ($validItem['httpCode'] >= 300) {
			return $validItem;
		}else{
			return $this->meli->post('/items', $item, array('access_token' => $this->Session->read('Meli.access_token')));
		}

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
	 * @return Objeto devuelto por MELI	 
	 */
	public function update($id, $title, $price, $available_quantity = 1, $video_id = '', $pictures = array() , $shipping = array())
	{	
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);


		// We construct the item to POST
		$item = array(
			"title" => $title,
			"price" => $price,
			"available_quantity" => $available_quantity,
			"video_id" => $video_id,
			"pictures" => $pictures,
			"tags" => array(
		        "immediate_payment"
		    ),
		    "shipping" => $shipping
		);
		
		// We call the post request to list a item
		$result = $this->meli->put('/items/' . $id, $item, array('access_token' => $this->Session->read('Meli.access_token')));

		return $result;

	}


	public function viewItem($id)
	{
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);

		$result = '';
		
		if (!empty($id)) {
			$result = $this->meli->get('/items/' . $id);
		}

		return $result;
	}

	public function changeState($id, $state)
	{	
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);
		
		$states = array('closed', 'paused', 'active');
		
		if (!in_array($state, $states)) {
			return;
		}

		$item = array(
			"status" => $state
		);
		
		$result = $this->meli->put('/items/' . $id, $item, array('access_token' => $this->Session->read('Meli.access_token')));

		return $result;
	}


	public function updateDescription($id, $desc = '')
	{
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);

		$body = array('text' => $desc);

		$response = $this->meli->put(sprintf('/items/%s/description', $id), $body,  array('access_token' => $this->Session->read('Meli.access_token')));

		return $response;
	}


	public function getShippingMethod()
	{
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);

		$result = '';
		
		if (!empty($id)) {
			$result = $this->meli->get('/sites/MLC/shipping_methods');
		}

		return $result;
	}

	public function uploadFile($image)
	{
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);

		$result = '';
		
		if (!empty($image)) {
			$result = $this->meli->file('/pictures', $image ,array('access_token' => $this->Session->read('Meli.access_token')));
		}

		return $result;
	}


	public function linkImageToItem($image_id, $item_id)
	{
		# Configuración de la tienda
    	$this->setComponentConfig();
		$this->meli = new Meli($this->client_id, $this->client_secret);

		$result = '';

		if (!empty($image_id) && !empty($item_id)) {

			$data = array(
				'id' => $image_id
				);

			$result = $this->meli->post('/items/' . $item_id . '/pictures', $data, array('access_token' => $this->Session->read('Meli.access_token')));
		}

		return $result;
	}

	/**
	 * Shipping
	 */

	public function getShippingMode($category_id, $dimensions = '')
	{
		# Configuración de la tienda
    	$this->setComponentConfig();

		$this->meli = new Meli($this->client_id, $this->client_secret);
		
		$me = json_decode(json_encode($this->getMyAccountInfo()), true);
		
		# Creamos lista
		$list = array();

		if ($me['httpCode'] != 200) {
			return;
		}else{

			$params =  array(
				'category_id' => $category_id,
				'dimensions' => $dimensions
			);

			$result = to_array($this->meli->get('/users/' . $me['body']['id'] . '/shipping_modes', $params));
		}
		
		if ($result['httpCode'] != 200) {
			return;
		}else{
			return array_reverse($result);
		}
	}


	public function getShippingOptions($id)
	{
		# Configuración de la tienda
    	$this->setComponentConfig();

		$this->meli = new Meli($this->client_id, $this->client_secret);
	
		$result = to_array($this->meli->get('items/'.$id.'/shipping_options'));
		
		if ($result['httpCode'] != 200) {
			return;
		}else{
			return $result['body'];
		}
	}

}