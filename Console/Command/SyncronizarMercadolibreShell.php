<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('MercadoLibresController', 'Controller');

class SyncronizarMercadolibreShell extends AppShell {

	public function main() {

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Mercadolibre',
			'modulo_accion' => 'Inicia proceso de sincronización de precio y stock: ' . date('Y-m-d H:i:s')
		));

		$controller = new MercadoLibresController(new CakeRequest(), new CakeResponse());
		$items = $controller->sincronizar_todo();
		
		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Mercadolibre',
			'modulo_accion' => json_encode($items)
		));


		ClassRegistry::init('Log')->saveMany($log);

		return true;
	}
}