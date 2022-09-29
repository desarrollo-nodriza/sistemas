<?php

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('MetodoEnvioRetrasosController', 'Controller');

class NotificarVentasRetrasoShell extends AppShell
{

	public function main()
	{

		$log = array();
		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'VentasRetrasoShell',
			'modulo_accion' => 'Inicia proceso de notificar ventas con retraso: ' . date('Y-m-d H:i:s')
		));

		$MetodoEnvioRetrasosController = new MetodoEnvioRetrasosController();

		# crear_registro_retrasos la base de datos con los cambios en los envios
		$notificar_restaso = $MetodoEnvioRetrasosController->notificar_restaso();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'VentasRetrasoShell',
			'modulo_accion' => 'Finaliza proceso de notificar ventas con retraso: ' . json_encode($notificar_restaso)
		));

		# Guardamos el log
		ClassRegistry::init('Log')->saveMany($log);

		return true;
	}
}
