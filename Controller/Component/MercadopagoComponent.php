<?
App::import('Vendor', 'Mercadopago', array('file' => 'Mercadopago/mercadopago.php'));
App::uses('Component', 'Controller');

class MercadopagoComponent extends Component
{	

	public static $MPConexion;
	public static $accessToken;


	public function crearCliente($client_id, $client_secret)
	{	
		self::$MPConexion = new MP($client_id, $client_secret);
		self::$accessToken = self::$MPConexion->get_access_token();
	}

}