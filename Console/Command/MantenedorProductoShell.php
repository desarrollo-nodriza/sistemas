<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');

class MantenedorProductoShell extends AppShell {

	public function main() {

		$this->out('Inicia actualización de productos base: ' . date('Y-m-d H:i:s'));
		$this->hr();

		$productosController = new VentaDetalleProductosController(new CakeRequest(), new CakeResponse());

		$this->out($productosController->obtener_productos_base());
		$this->hr();
		$this->out('Finaliza actualización de productos base: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();

	}

}