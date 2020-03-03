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
				'VentaDetalle.fecha_llegada_en_espera <=' =>  date('Y-m-d'),
				'VentaDetalle.cantidad_anulada < VentaDetalle.cantidad',
			),
			'contain' => array(
				'Venta' => array(
					'fields' => array(
						'Venta.venta_estado_id'
					)
				)
			),
			'joins' => array(
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'estado',
					'type' => 'INNER',
					'conditions' => array(
						'estado.id = Venta.venta_estado_id'
					)
				),
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'estado_cat',
					'type' => 'INNER',
					'conditions' => array(
						'estado_cat.id = estado.venta_estado_categoria_id',
						'estado_cat.venta = 1'
					)
				)
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