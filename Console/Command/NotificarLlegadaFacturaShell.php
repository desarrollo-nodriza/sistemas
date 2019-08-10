<?php 
App::uses('CakeEmail', 'Network/Email');
App::uses('View', 'View');
App::uses('Controller', 'Controller');
App::uses('PagosController', 'Controller');


# cd /var/www/html/sistemanodrizadev/ && ~/cakephp/lib/Cake/Console/cake notificar_llegada_factura
class NotificarLlegadaFacturaShell extends AppShell {
	
	public function main() {

		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Pago',
			'modulo_accion' => 'Inicia proceso de notificacion de oc listas para agendar pago: ' . date('Y-m-d H:i:s')
		));


		$this->out('Inicia notificación');

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);
		
		
		$controller = new PagosController();

		$pagos = ClassRegistry::init('Pago')->pagos_pendiente_dte();

		if (empty($pagos)) {
			$log = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'Pago',
				'modulo_accion' => 'No hay pagos para agendar: ' . date('Y-m-d H:i:s')
			));

			$this->out('No hay pagos');

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->save($log);

		}else{

			foreach ($pagos as $key => $value) {
				$controller->guardarEmailPagoAgendar($value, array($value['OrdenCompra']['email_finanza']));

				$this->out('Pago #' . $value['Pago']['id'] . ' guardado.');
			}

			$log = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'Pago',
				'modulo_accion' => 'Se guardaron un total de '.count($pagos).' pagos: ' . date('Y-m-d H:i:s')
			));

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->save($log);

		}

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Pago',
			'modulo_accion' => 'Finaliza proceso de notificacion de oc listas para agendar pago: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		$this->out('Finaliza notificación');
		exit;

	}
}