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
				$refresh = $this->meli->refreshAccessToken();

				// Now we create the sessions with the new parameters
				$this->Session->write('Meli.access_token', $refresh['body']->access_token);
				$this->Session->write('Meli.expires_in', time() + $refresh['body']->expires_in);
				$this->Session->write('Meli.refresh_token', $refresh['body']->refresh_token);

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
		$user = $this->meli->authorize($code, $callbackUrl );
		
		// Now we create the sessions with the authenticated user
		$this->Session->write('Meli.access_token', $user['body']->access_token);
		$this->Session->write('Meli.expires_in', time() + $user['body']->expires_in);
		$this->Session->write('Meli.refresh_token', $user['body']->refresh_token);

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
}