<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'Onestock', array('file' => 'Onestock/Onestock.php'));


class OnestockComponent extends Component
{
	private $onestock;
	

	public function __construct()
	{
		$this->onestock = new Onestock;
		
	}

	public function obtenerProductoOneStock($producto_id)
	{	
		
		return $this->onestock->obtenerProductoOneStock($producto_id);
	}

	public function obtenerProductosClienteOneStock()
	{	
		
		return $this->onestock->obtenerProductosClienteOneStock();
	}
	
}