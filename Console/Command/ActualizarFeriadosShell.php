<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class ActualizarFeriadosShell extends AppShell {

	public function main() {

        $log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'ActualizarFeriadosShell',
			'modulo_accion' => 'Inicia proceso de actualizar feriados'
		));

		ClassRegistry::init('Feriado')->actualizar_feriados();
		ClassRegistry::init('Feriado')->actualizar_feriados_sabado_domingo();

        $log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'ActualizarFeriadosShell',
			'modulo_accion' => 'Finaliza proceso de actualizar feriados'
		));
    
        # Guardamos el log
        ClassRegistry::init('Log')->saveMany($log);

		return true;
	}
}