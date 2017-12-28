<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('View', 'View');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('MercadoLibresController', 'Controller');

class ActualizarPreciosShell extends AppShell
{
	public $CakeEmail		= null;
	public $host			= 'localhost';

	public function main()
	{	
		# Instanciamos el controlador MercadoLibres
		$Meli = new MercadoLibresController(new CakeRequest(), new CakeResponse());

		# Obtenemos las tiendas configuradas
		#$tiendas = ClassRegistry::init('Tienda')->find('all');
		
		# Variable que almacena los productos
		#$productos = array();

		# Obtenemos productos por tiendas
		#foreach ($tiendas as $indice => $tienda) {
		#	$productos[$tienda['Tienda']['configuracion']] = $Meli->getProductsMeli($tienda);
		#}
		
		# Actualizamos de los productos publicados, tanto interna como en MELI
		#$result = $Meli->actualizarPrecios($productos);

		if($Meli->verificarCambiosDePreciosStock(true)) {
			$this->guardarEmail();
			$this->out('Email guardado');
		}else{
			$this->out('No existen cambios');
		}
	}


	public function guardarEmail() {

		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'Correos' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		$this->Correo				= ClassRegistry::init('Correo');
		
		/**
		 * Correo a ventas
		 */
		$html						= $this->View->render('notificar_actualizacion_precios_meli');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		$this->Correo->save(array(
			'estado'					=> 'Notificación sincronización precios meli',
			'html'						=> $html,
			'asunto'					=> '[MELI] ¡Los precios en Mercado libre están desactualizados!',
			'destinatario_email'		=> 'ventas@toolmania.cl',
			'destinatario_nombre'		=> '',
			'remitente_email'			=> 'no-reply@nodriza.cl',
			'remitente_nombre'			=> 'Sistemas - Nodriza Spa',
			'cc_email'					=> '',
			'bcc_email'					=> 'cristian.rojas@nodriza.cl',
			'traza'						=> null,
			'proceso_origen'			=> null,
			'procesado'					=> 0,
			'enviado'					=> 0,
			'reintentos'				=> 0,
			'atachado'					=> null
		));

		return;
		
	}
}
