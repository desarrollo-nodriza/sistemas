<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class ActualizarVentasShell extends AppShell {
	
	public function main() {

		$this->out('Inicia obtener ventas nuevas: ' . date('Y-m-d H:i:s'));
		$this->hr();

		$controller = new VentasController(new CakeRequest(), new CakeResponse());
		$controller->shell = true;
		$controller->admin_actualizar_ventas();

		$this->hr();
		$this->out('Finaliza obtener ventas nuevas: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();
	}

}