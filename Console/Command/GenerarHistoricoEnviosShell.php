<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class GenerarHistoricoEnviosShell extends AppShell {

	public function main() {

        $log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Inicia proceso de generación de historicos de envios: ' . date('Y-m-d H:i:s')
		));

        $controller = new VentasController(new CakeRequest(), new CakeResponse());

        # Actualizamos la base de datos con los cambios en los envios
        $total = $controller->generar_historico_envios();

        $log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Finaliza proceso de generación de historico de envios: Total procesadas ' . $total 
		));
    
        # Guardamos el log
        ClassRegistry::init('Log')->saveMany($log);

		return true;
	}
}