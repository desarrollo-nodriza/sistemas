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

			throw new BadRequestException("Asegurate de haber enviado todos los parametros");
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

	public function api_eliminar_token_administrador()
	{


		if (!isset($this->request->query['token_push'])) {

			throw new BadRequestException("Asegurate de haber enviado todos los parametros");
		}

		$tokenNotificacionPush = ClassRegistry::init('TokenNotificacionPush')->find('all', [
			'conditions' => ['token' => $this->request->query['token_push']]
		]);

		if ($tokenNotificacionPush) {
			ClassRegistry::init('TokenNotificacionPush')->deleteAll(array('TokenNotificacionPush.id' => Hash::extract($tokenNotificacionPush, '{*}.TokenNotificacionPush.id')));
		}

		$response = array(
			'name' 		=> 'success',
			'message' 	=> 'El token se a eliminado',
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

		if (
			!isset($this->request->query['token_push']) || !isset($this->request->query['administrador_id'])
			|| !isset($this->request->query['orden_compra_id']) || !isset($this->request->query['producto_id'])
		) {

			throw new BadRequestException("Asegurate de haber enviado todos los parametros");
		}

		$nombre_administrador = ClassRegistry::init('Administrador')->find('first', [
			'fields' 		=> ['nombre'],
			'conditions' 	=> ['id' => $this->request->query['administrador_id']]
		])['Administrador']['nombre'];

		$nombre_producto = ClassRegistry::init('VentaDetalleProducto')->find('first', [
			'fields' 		=> ['nombre'],
			'conditions' 	=> ['id' => $this->request->query['producto_id']]
		])['VentaDetalleProducto']['nombre'] ?? "Hubo un problema para obtener el nombre del producto {$this->request->query['producto_id']}";
		// * Se envia con la fecha para que tome las notificaciones como distintas y no se sobrepongan en el telefono
		$requerimiento = [
			'title'		=> "nz Warehouse",
			'message'	=> "Se require permiso para recepcionar OC 						|" . date('Y-m-d H:i:s'),
			'data'		=> [
				"accion"							=> "problemas_recepcion_productos",
				"orden_compra_id"					=> $this->request->query['orden_compra_id'],
				"producto_id"						=> $this->request->query['producto_id'],
				"nombre_producto"					=> $nombre_producto,
				"administrador_requerimiento_id"	=> $this->request->query['administrador_id'],
				"nombre_administrador"				=> $nombre_administrador,
				'requerimiento'						=> "Se require permiso para recepcionar OC",
				'token_push'						=> $this->request->query['token_push'],
			]
		];

		ClassRegistry::init('Requerimiento')->create;

		$respuesta = ClassRegistry::init('Requerimiento')->save([
			'Requerimiento' => [
				'administrador_requerimiento_id' => $this->request->query['administrador_id'],
				'requerimiento'					 => json_encode($requerimiento),
			]
		]);
		$requerimiento['data']['requerimiento_id'] 	= $respuesta['Requerimiento']['id'];

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

	public function api_respuesta_requerimiento_problemas_recepcion_productos()
	{
		# Existe token
		if (!isset($this->request->query['token'])) {

			throw new UnauthorizedException("Token requerido");
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {

			throw new UnauthorizedException("Token de sesión expirado o invalido");
		}

		if (
			!isset($this->request->query['token_push']) || !isset($this->request->query['administrador_id'])
			|| !isset($this->request->query['orden_compra_id']) || !isset($this->request->query['requerimiento_id']) || !isset($this->request->query['respuesta'])
		) {

			throw new BadRequestException("Asegurate de haber enviado todos los parametros");
		}

		$respuesta = ClassRegistry::init('Requerimiento')->find('first', [
			'conditions' => [
				'id' 		=> $this->request->query['requerimiento_id'],
				'atendido' 	=> 0
			]
		]);

		if (!$respuesta) {
			throw new NotFoundException("Requerimiento ya fue atendido por otro administrador");
		}

		$nombre_producto = ClassRegistry::init('VentaDetalleProducto')->find('first', [
			'fields' 		=> ['nombre'],
			'conditions' 	=> ['id' => $this->request->query['producto_id']]
		])['VentaDetalleProducto']['nombre'] ?? "Hubo un problema para obtener el nombre del producto {$this->request->query['producto_id']}";

		$nombre_administrador = ClassRegistry::init('Administrador')->find('first', [
			'fields' 		=> ['nombre'],
			'conditions' 	=> ['id' => $this->request->query['administrador_id']]
		])['Administrador']['nombre'];

		// * Se envia con la fecha para que tome las notificaciones como distintas y no se sobrepongan en el telefono
		$respuestaRequerimiento = [
			'title'		=> "nz Warehouse",
			'message'	=> "Han respondido tu requerimiento 						|" . date('Y-m-d H:i:s'),
			'data'		=> [
				"accion"							=> "respuesta_problemas_recepcion_productos",
				"orden_compra_id"					=> $this->request->query['orden_compra_id'],
				"producto_id"						=> $this->request->query['producto_id'],
				"nombre_producto"					=> $nombre_producto,
				"administrador_respuesta_id"		=> $this->request->query['administrador_id'],
				"nombre_administrador"				=> $nombre_administrador,
				'requerimiento'						=> "Han respondido tu requerimiento",
				'token_push'						=> $this->request->query['token_push'],
				'requerimiento_id'					=> $this->request->query['requerimiento_id'],
				'respuesta'							=> $this->request->query['respuesta']
			]
		];

		$respuesta['Requerimiento']['atendido'] 					= true;
		$respuesta['Requerimiento']['administrador_respuesta_id'] 	= $this->request->query['administrador_id'];
		$respuesta['Requerimiento']['respuesta'] 					= json_encode($respuestaRequerimiento);

		$respuesta = ClassRegistry::init('Requerimiento')->save($respuesta);

		$response = array(
			'name' 		=> 'success',
			'message' 	=> 'Respuesta fue guardada pero no pudo ser notificado',
			'data' 		=> $respuesta
		);


		try {
			$this->Pushy->sendPushNotification($respuestaRequerimiento, $this->request->query['token_push']);
			$response = array(
				'name' 		=> 'success',
				'message' 	=> 'Respuesta fue guardada y enviado',
				'data' 		=> $respuesta
			);
		} catch (\Throwable $th) {
		}


		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}
}
