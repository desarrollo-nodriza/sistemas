<?php

App::uses('Controller', 'Controller');
App::uses('ProveedoresController', 'Controller');

class GenerarActualizarTiempoDespachoShell extends AppShell
{

	public function main()
	{

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'GenerarActualizarTiempoDespachoShell',
			'modulo_accion' => 'Inicia proceso de crear OC automaticas: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Proveedor')->actualizar_tiempo_despacho_proveedor();

		$ProveedoresController = new ProveedoresController();
		$respuesa              = $ProveedoresController->admin_cronjob_despacho_pedido();
		
		if ($respuesa) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' 		=> 'GenerarActualizarTiempoDespachoShell',
				'modulo_accion' => json_encode(
					[
						'Finaliza proceso de crear OC automaticas: ' . date('Y-m-d H:i:s') => $respuesa
					]
				)
			));

			# Guardamos el log
			ClassRegistry::init('Log')->saveMany($log);
		}


		return true;
	}
}
