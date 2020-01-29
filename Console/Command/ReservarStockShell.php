<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class ReservarStockShell extends AppShell {

	public function main() {

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Inicia proceso de reserva stock: ' . date('Y-m-d H:i:s')
		));

		$controller = new VentasController(new CakeRequest(), new CakeResponse());
		$controller->reservar_ventas_sin_reserva();

		
		/*
		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Resultado de reserva stock: (' . date('Y-m-d H:i:s') . ') ' . json_encode($resultado)
		));*/


		ClassRegistry::init('Log')->saveMany($log);

		return true;
	}
}