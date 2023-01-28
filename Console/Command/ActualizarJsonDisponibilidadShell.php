<?php

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');

class ActualizarJsonDisponibilidadShell extends AppShell
{

	public function main()
	{

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'VentaDetalleProducto',
			'modulo_accion' => 'Inicia proceso de actualización de json de disponibilidad: ' . date('Y-m-d H:i:s')
		));

		$controller = new VentaDetalleProductosController(new CakeRequest(), new CakeResponse());

		# Actualizamos la base de datos con los cambios en los envios
		$resultado[] = $controller->generar_json_productos_disponibles();
		$resultado[] = $controller->admin_disponibilidad_full();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Finaliza proceso de actualización de json de disponibilidad: Actualizado - ' . json_encode($resultado)
		));

		# Guardamos el log
		ClassRegistry::init('Log')->saveMany($log);

		return true;
	}
}
