<?php

App::uses('Controller', 'Controller');
App::uses('OrdenComprasController', 'Controller');

class GenerarOCAutomaticasShell extends AppShell
{

	public function main()
	{

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'GenerarOCAutomaticasShell',
			'modulo_accion' => 'Inicia proceso de crear OC automativas: ' . date('Y-m-d H:i:s')
		));

		$OrdenComprasController = new OrdenComprasController();;
		$respuesa               = $OrdenComprasController->RecorrerProveedor();
		
		if ($respuesa) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' 		=> 'GenerarOCAutomaticasShell',
				'modulo_accion' => json_encode(
					[
						'Finaliza proceso de crear OC automativas: ' . date('Y-m-d H:i:s') => $respuesa
					]
				)
			));

			# Guardamos el log
			ClassRegistry::init('Log')->saveMany($log);
		}


		return true;
	}
}
