<?php

App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');


# /var/www/html/sistemav2/ && ~/cakephp/lib/Cake/Console/cake notificar_venta_retraso_cliente >/dev/null 2>&1
class NotificarVentaRetrasoClienteShell extends AppShell {
    public function main() 
    {
        ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);

        $log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Venta',
			'modulo_accion' => 'Inicia proceso de notificacion de ventas retrasadas: ' . date('Y-m-d H:i:s')
		));

        $this->out('Inicia notificación');
        
        $controller = new VentasController();

        $resultado = $controller->notificar_retraso_ventas();

        $this->out('Finaliza notificaciones');

        $log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Venta',
			'modulo_accion' => 'Finaliza proceso de notificacion de ventas retrasadas: ' . json_encode($resultado)
        ));
        
        ClassRegistry::init('Log')->create();
        ClassRegistry::init('Log')->saveMany($log);
        
        return;

    }
}