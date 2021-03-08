<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class ActualizarEstadoEnviosShell extends AppShell {

	public function main() {

        $log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Inicia proceso de actualización de envios y ventas: ' . date('Y-m-d H:i:s')
		));

        $controller = new VentasController(new CakeRequest(), new CakeResponse());

        $controller->shell = true;

        # Actualizamos la base de datos con los cambios en los envios
        $ventasProcesadas = $controller->actualizar_ventas_por_envios();

        $log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Finaliza proceso de actualización de envios y ventas: ' . json_encode($ventasProcesadas)
		));
    
        # Guardamos el log
        ClassRegistry::init('Log')->saveMany($log);

		return true;
	}
}