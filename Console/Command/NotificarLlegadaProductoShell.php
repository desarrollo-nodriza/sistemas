<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class NotificarLlegadaProductoShell extends AppShell {

	public function main() {

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Inicia proceso de notificar llegada producto: ' . date('Y-m-d H:i:s')
		));

		$ventaDetalles = ClassRegistry::init('VentaDetalle')->find('all', array(
			'conditions' => array(
				'VentaDetalle.cantidad_en_espera >' => 0,
				'VentaDetalle.fecha_llegada_en_espera <=' =>  date('Y-m-d')
			), 
			'fields' => array(
				'VentaDetalle.id'
			)
		));
		
		if (empty($ventaDetalles)) {
			return;
		}

		$controller = new VentasController(new CakeRequest(), new CakeResponse());


		$ids = Hash::extract($ventaDetalles, '{n}.VentaDetalle.id');

		$controller->admin_notificar_llegada_productos($ids);

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