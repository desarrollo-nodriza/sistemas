<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'Onestock', array('file' => 'Onestock/Onestock.php'));


class OnestockComponent extends Component
{

	public function obtenerProductoOneStock($producto_id)
	{

		$onestock = new Onestock;
		return $onestock->obtenerProductoOneStock($producto_id);
	}
}