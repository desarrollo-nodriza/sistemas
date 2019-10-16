<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('OrdenComprasController', 'Controller');

class RecordatorioOrdenCompraShell extends AppShell {
	
	public function main() {

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'OrdenCompra',
			'modulo_accion' => 'Inicia proceso de recordatorio proveedores: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		$ocs = ClassRegistry::init('OrdenCompra')->obtener_ocs_por_estado('asignacion_moneda');

		$controller = new OrdenComprasController(new CakeRequest(), new CakeResponse());

		$dia_actual = date('N');
		$hora_actual = date('G');

		$dia_laboral_i     = '1';
		$dia_laboral_f     = '5';
		$horario_laboral_i = '9';
		$horario_laboral_f = '18';

		$es_feriado_api = json_decode(file_get_contents(sprintf('https://apis.digital.gob.cl/fl/feriados/%s/%s/%s', date('Y'), date('m'), date('d'))), true);
		
		# Solo notificar días hábiles
		if (isset($es_feriado_api['error']) 
			&& $dia_actual >= $dia_laboral_i 
			&& $dia_actual <= $dia_laboral_f
			&& $hora_actual >= $horario_laboral_i
			&& $hora_actual < $horario_laboral_f) {
			
			foreach ($ocs as $ic => $oc) {
				$controller->guardarEmailValidado($oc['OrdenCompra']['id'], true);
			}

			$log = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'OrdenCompra',
				'modulo_accion' => 'Oc notificadas: ' . date('Y-m-d H:i:s') . ' - ' . json_encode($ocs)
			));

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->save($log);

		}

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'OrdenCompra',
			'modulo_accion' => 'Finaliza proceso de recordatorio proveedores: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		return true;
	}

}