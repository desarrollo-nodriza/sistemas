<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Prisync', array('file' => 'Prisync/Prisync.php'));

class PrisyncComponent extends Component
{	

	public $Prisync;


	public function initialize(Controller $controller)
	{
    	$this->Controller = $controller;

		try
		{
			Configure::load('prisync');
		}
		catch ( Exception $e )
		{
			throw new Exception('No se encontró el archivo Config/prisync.php');
		}
	}


	public function autenticacion()
	{	
		$activo = Configure::read('Prisync.activo');

		if (!$activo) {
			return false;
		}

		$api_key   = Configure::read('Prisync.prisync_key');
		$api_token = Configure::read('Prisync.prisync_token');

		if (empty($api_key) || empty($api_token)) {
			return false;
		}

		$this->Prisync = new Prisync($api_key, $api_token);
	
	}



	public function obtenerProductos($url = '/api/v2/list/product/startFrom/0')
	{	
		$this->autenticacion();

		$productos = $this->Prisync->get($url);
		
		if ($productos['httpCode'] >= 300) {
			throw new Exception( sprintf('%s. Código de error: %d', $productos['body']->error, $productos['body']->errorCode));
			return;
		}else{
			return to_array($productos['body']);
		}
	}


	public function obtenerProductoPorId($id = '')
	{	
		if (!empty($id)) {

			$this->autenticacion();
			
			$url = '/api/v2/get/product/id/' . $id;
			$producto = $this->Prisync->get($url);
			
			if ($producto['httpCode'] >= 300) {
				throw new Exception( sprintf('%s. Código de error: %d', $producto['body']->error, $producto['body']->errorCode));
				return;
			}else{
				return to_array($producto['body']);
			}
		}
	}


	public function obtenerCompetidoresPorProducto($id = '')
	{
		if (!empty($id)) {

			$this->autenticacion();

			$url  = '/api/v2/get/url/id/' . $id;
			
			$urls = $this->Prisync->get($url);
			
			if ($urls['httpCode'] >= 300) {
				throw new Exception( sprintf('%s. Código de error: %d', $urls['body']->error, $urls['body']->errorCode));
				return;
			}else{
				return to_array($urls['body']);
			}

		}
	}
}