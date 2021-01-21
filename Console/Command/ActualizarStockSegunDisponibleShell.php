<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');

class ActualizarStockSegunDisponibleShell extends AppShell {

	public function main() {

		$controller = new VentaDetalleProductosController(new CakeRequest(), new CakeResponse());
        
        $controller->actualizar_canales_stock_fisico();

		return true;
	}
}