<?php

App::uses('AppController', 'Controller');


class NotificacionesPushController extends AppController
{
	public $components = array(
		'Pushy'
	);

	public function api_registrar_token_administrador()
	{
		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    	=> 401,
				'name' 		=> 'error',
				'message' 	=> 'Token requerido'
			);

			throw new UnauthorizedException("Token requerido");
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    	=> 401,
				'name' 		=> 'error',
				'message' 	=> 'Token de sesión expirado o invalido'
			);

			throw new UnauthorizedException("Token de sesión expirado o invalido");
		}

		if (!isset($this->request->query['token_push']) || !isset($this->request->query['administrador_id'])) {

			throw new BadRequestException("Asegruate de haber enviado todos los parametros");
		}

		$tokenNotificacionPush = ClassRegistry::init('TokenNotificacionPush')->find('all', [
			'conditions' => ['token' => $this->request->query['token_push']]
		]);

		if ($tokenNotificacionPush) {
			ClassRegistry::init('TokenNotificacionPush')->deleteAll(array('TokenNotificacionPush.id' => Hash::extract($tokenNotificacionPush, '{*}.TokenNotificacionPush.id')));
		}

		$data	= [];

		ClassRegistry::init('TokenNotificacionPush')->create();
		$data = ClassRegistry::init('TokenNotificacionPush')->save([
			'TokenNotificacionPush' =>
			[
				'administrador_id' 	=> $this->request->query['administrador_id'],
				'token' 			=> $this->request->query['token_push'],
			]
		]);

		$response = array(
			'name' 		=> 'success',
			'message' 	=> 'El token se a registrado correctamente',
			'data' 		=> $data
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}

	public function api_crear_requerimiento_problemas_recepcion_productos()
	{
		# Existe token
		if (!isset($this->request->query['token'])) {

			throw new UnauthorizedException("Token requerido");
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {

			throw new UnauthorizedException("Token de sesión expirado o invalido");
		}

		$requerimiento = [
			'title'		=> "nz Warehouse",
			'message'	=> "Se require permiso para recepcionar OC",
			'data'		=> [
				"accion"							=> "problemas_recepcion_productos",
				"orden_compra_id"					=> $this->request->query['orden_compra_id'],
				// info producto
				"producto_id"						=> $this->request->query['producto_id'],
				"nombre_producto"					=> ClassRegistry::init('VentaDetalleProducto')->find('first', [
					'fields' 		=> ['nombre'],
					'conditions' 	=> ['id' => $this->request->query['producto_id']]
				])['VentaDetalleProducto']['nombre'] ?? "Hubo un porblema para obtener el nombre del producto {$this->request->query['producto_id']}",
				// info administrador
				"administrador_requerimiento_id"	=> $this->request->query['administrador_id'],
				"nombre_administrador"				=> ClassRegistry::init('Administrador')->find('first', [
					'fields' 		=> ['nombre'],
					'conditions' 	=> ['id' => $this->request->query['administrador_id']]
				])['Administrador']['nombre'],
			]
		];

		ClassRegistry::init('Requerimiento')->create;

		$respuesta = ClassRegistry::init('Requerimiento')->save([
			'Requerimiento' => [
				'administrador_requerimiento_id' => $this->request->query['administrador_id'],
				'requerimiento'					 => json_encode($requerimiento),
			]
		]);
		$requerimiento['data']['requerimiento_id'] = $respuesta['Requerimiento']['id'];

		$respuesta = array_replace_recursive($respuesta, ClassRegistry::init('Requerimiento')->save([
			'Requerimiento' => [
				'requerimiento'					 => json_encode($requerimiento),
			]
		]));

		$administradores = ClassRegistry::init('Administrador')->find('all', [
			'fields' 		=> ['Administrador.id', 'Administrador.rol_id'],
			'contain' 		=> ['Rol', 'TokenNotificacionPush'],
			'conditions'	=> [
				'Rol.app_administrar' 		=> true,
			]
		]);
		$token = Hash::extract($administradores, '{*}.TokenNotificacionPush.{*}.token');

		$response = array(
			'name' 		=> 'success',
			'message' 	=> 'Requerimiento creado pero no pudo ser notificado',
			'data' 		=> $respuesta
		);

		if ($token) {
			try {
				$this->Pushy->sendPushNotification($requerimiento, implode(',', $token));
				$response = array(
					'name' 		=> 'success',
					'message' 	=> 'Requerimiento creado y enviado',
					'data' 		=> $respuesta
				);
			} catch (\Throwable $th) {
			}
		}

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}
}
