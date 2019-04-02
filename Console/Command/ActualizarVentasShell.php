<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class ActualizarVentasShell extends AppShell {
	
	public function main() {

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Venta',
			'modulo_accion' => 'Inicia proceso de actualización: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		$this->out('Inicia obtener ventas nuevas: ' . date('Y-m-d H:i:s'));
		$this->hr();

		$controller = new VentasController(new CakeRequest(), new CakeResponse());
		$controller->shell = true;
		$controller->admin_actualizar_ventas();

		$this->hr();
		$this->out('Finaliza obtener ventas nuevas: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Venta',
			'modulo_accion' => 'Finaliza proceso de actualización: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);
	}

}