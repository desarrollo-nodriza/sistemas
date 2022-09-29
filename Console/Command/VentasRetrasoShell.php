<?php
App::uses('Controller', 'Controller');
App::uses('MetodoEnvioRetrasosController', 'Controller');

class VentasRetrasoShell extends AppShell
{

	public function main()
	{

		$log = array();
		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'VentasRetrasoShell',
			'modulo_accion' => 'Inicia proceso de obtener ventas con retraso: ' . date('Y-m-d H:i:s')
		));

		$MetodoEnvioRetrasosController = new MetodoEnvioRetrasosController();

		# crear_registro_retrasos la base de datos con los cambios en los envios
		$crear_registro_retrasos = $MetodoEnvioRetrasosController->crear_registro_retrasos();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'VentasRetrasoShell',
			'modulo_accion' => 'Finaliza proceso de obtener ventas con retraso: ' . json_encode($crear_registro_retrasos)
		));

		# Guardamos el log
		ClassRegistry::init('Log')->saveMany($log);

		return true;
	}
}
