<?php 

App::uses('CakeEmail', 'Network/Email');
App::uses('View', 'View');

# /var/www/html/sistemav2/ && ~/cakephp/lib/Cake/Console/cake verificar_retraso_ventas >/dev/null 2>&1
class VerificarRetrasoVentasShell extends AppShell {
	
	public function main() {

		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Inicia proceso de notificacion de ventas retrasadas: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);
		
		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
			'notificacion_retraso_venta_dias', 'notificacion_retraso_venta_limite', 'notificacion_retraso_venta'
		));

		if (!$tienda['Tienda']['notificacion_retraso_venta'])
			return;

		# Ventas no procesadas
		$ventas = ClassRegistry::init('Venta')->obtener_ventas_retrasadas($tienda['Tienda']['notificacion_retraso_venta_dias'], $tienda['Tienda']['notificacion_retraso_venta_limite']);
		
		$emailsNotificar = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('ventas');
	
		// Guardamos el email
		if (!empty($ventas) && !empty($emailsNotificar)) {
			if ($this->guardarEmail($ventas, $emailsNotificar)) {
				$this->out('Emails registrados con éxito');
				exit;
			}else{
				$this->out('Error al guardar');
				exit;
			}
		}


		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'No existen ventas retrasadas o no hay receptores: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		$this->out('No existen ventas retrasadas o no hay receptores');
		exit;

	}


	/**
     * Se encarga de definir cuantos días de retraso tiene la venta según su fecha de venta.
     * @param  string $fecha [description]
     * @return boolean       Restrasada o no
     */
    public function calcular_retraso($fecha = '', $dias = 1)
    {
    	if (!empty($fecha)) {
    		
    		$fechaVenta = new DateTime($fecha);
			$hoy = new DateTime(date('Y-m-d H:i:s'));
			$retraso = $hoy->diff($fechaVenta);

			$retrasoHoras = $fechaVenta->diff($hoy);

			if ($retrasoHoras->days > $dias) {
				return true;
			}

			return false;
    	}
    }



    /**
     * Se encarga de definir cuantos días de retraso tiene la venta según su fecha de venta.
     */
    public function calcular_retraso_dias($fecha = '', $dias = 1)
    {
    	if (!empty($fecha)) {
    		
    		$fechaVenta = new DateTime($fecha);
			$hoy = new DateTime(date('Y-m-d H:i:s'));
			$retraso = $hoy->diff($fechaVenta);

			$retrasoHoras = $fechaVenta->diff($hoy);

			if ($retrasoHoras->days > $dias) {
				return $retrasoHoras->days;
			}

			return false;
    	}
    }



    public function guardarEmail($ventas = array(), $emails = array()) 
    {

		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'Ventas' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		$this->Correo				= ClassRegistry::init('Correo');
		
		/**
		 * Correo a ventas
		 */
		$this->View->set(compact('ventas'));
		$html						= $this->View->render('notificar_venta_retraso');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		if ( $this->Correo->save(array(
			'estado'					=> 'Notificación ventas restrasada',
			'html'						=> $html,
			'asunto'					=> '[NDRZ] Ventas para procesar',
			'destinatario_email'		=> trim(implode(',', $emails)),
			'destinatario_nombre'		=> '',
			'remitente_email'			=> 'cristian.rojas@nodriza.cl',
			'remitente_nombre'			=> 'Sistemas - Nodriza Spa',
			'cc_email'					=> '',
			'bcc_email'					=> 'cristian.rojas@nodriza.cl',
			'traza'						=> null,
			'proceso_origen'			=> null,
			'procesado'					=> 0,
			'enviado'					=> 0,
			'reintentos'				=> 0,
			'atachado'					=> null
		)) ) {
			return true;
		}

		return false;
	}

}