<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Chilexpress.GeoReferenciaWS', array('file' => 'georeferencia/GeoReferenciaWS.php'));


class GeoReferenciaComponent extends Component
{	
	private $GeoReferenciaWS;

	public function initialize(Controller $controller)
	{
		$this->Controller = $controller;
		try
		{
			Configure::load('Chilexpress.chilexpress');
		}
		catch ( Exception $e )
		{
			throw new Exception('No se encontró el archivo Plugin/Config/chilexpress.php');
		}
	}

	/**
	 * Iniciar la conexión con el WS
	 * @return void
	 */
	public function connect()
	{
		$this->GeoReferenciaWS = new GeoReferenciaWS(Configure::read('Chilexpress.georeferencia.endpoint'));
	}

	public function obtenerRegiones()
	{
		$this->connect();

		$xmlresponse = $this->GeoReferenciaWS->soapClient->__getLastResponse();
		
		$ConsultarRegiones = new ConsultarRegiones();

		prx($ConsultarRegiones);
	}
}