<?php

App::uses('AppController', 'Controller');


class NotificacionesPushController extends AppController
{
	public $components = array(
		'Pushy',
		'WarehouseNodriza',
		'Mandrill',
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

		ClassRegistry::init('Requerimiento')->create;
		$requerimiento = ClassRegistry::init('Requerimiento')->save([
			'Requerimiento' => []
		]);

		// * Se envia con numero de requerimiento para que las notificaciones sean distintas y no se sobrepongan
		$message 			=  "Se require permiso para recepcionar OC | Rº {$requerimiento['Requerimiento']['id']}";
		$requerimientoPush 	= [
			'title'		=> "nz Warehouse",
			'message'	=> $message,
			'data'		=> [
				"accion"							=> "problemas_recepcion_productos",
				"orden_compra_id"					=> $this->request->query['orden_compra_id'],
				"producto_id"						=> $this->request->query['producto_id'],
				"nombre_producto"					=> $nombre_producto,
				"administrador_requerimiento_id"	=> $this->request->query['administrador_id'],
				"nombre_administrador"				=> $nombre_administrador,
				'requerimiento'						=> "Se require permiso para recepcionar OC",
				'token_push'						=> $this->request->query['token_push'],
				'requerimiento_id'					=> $requerimiento['Requerimiento']['id']
			]
		];

		$requerimiento['Requerimiento']['administrador_requerimiento_id'] 	= $this->request->query['administrador_id'];
		$requerimiento['Requerimiento']['requerimiento'] 					= json_encode($requerimientoPush);

		$requerimiento = ClassRegistry::init('Requerimiento')->save($requerimiento);
		$administradores = ClassRegistry::init('Administrador')->find('all', [
			'fields' 		=> [
				'Administrador.id',
				'Administrador.rol_id',
				'Administrador.email',
				'Administrador.nombre',
			],
			'contain' 		=> ['Rol', 'TokenNotificacionPush'],
			'conditions'	=> [
				'Rol.app_administrar' 		=> true,
			]
		]);

		$tokens 	= Hash::extract($administradores, '{*}.TokenNotificacionPush.{*}.token');
	
		try {
			$response 	= $this->WarehouseNodriza->UltimaApk();
			$tienda 	= ClassRegistry::init('Tienda')->tienda_principal(array(
				'mandrill_apikey', 'nombre'
			));

			$mandrill_apikey = $tienda['Tienda']['mandrill_apikey'];

			$remitente = array(
				'email' 	=> 'no-reply@nodriza.cl',
				'nombre' 	=> 'Nodriza Spa'
			);

			$html = $response['response']['body']['ruta_descarga'] ?? false ?
				"<div>
				<p>Revisa nuestra App nz Warehouse para atender requerimientos de los usuarios</p>
				<a href='{$response['response']['body']['ruta_descarga']}'>¡Si no la tienes la puedes descargar ACÁ!</a>
			</div>"
				:
				"<div>
				<p>Revisa nuestra App nz Warehouse para atender requerimientos de los usuarios</p>
			</div>";

			foreach ($administradores as $correo) {
				$destinatarios[] = [
					'email' 	=> $correo['Administrador']['email'],
					'nombre' 	=> $correo['Administrador']['nombre']
				];
			}
			$this->Mandrill->conectar($mandrill_apikey);
			$this->Mandrill->enviar_email($html, $message, $remitente, $destinatarios);

		} catch (\Throwable $th) {
			
		}

		$response = array(
			'name' 		=> 'success',
			'message' 	=> 'Requerimiento creado pero no pudo ser notificado',
			'data' 		=> $requerimiento
		);

		if ($tokens) {
			foreach ($tokens as $token) {
				try {
					$this->Pushy->sendPushNotification($requerimientoPush, $token);
					$response = array(
						'name' 		=> 'success',
						'message' 	=> 'Requerimiento creado y enviado',
						'data' 		=> $requerimiento
					);
				} catch (\Throwable $th) {

					$logs[] = array('Log' => array(
						'administrador' => 'Problemas para notificar',
						'modulo' 		=> 'api_crear_requerimiento_problemas_recepcion_productos',
						'modulo_accion' => json_encode($th)
					));

					ClassRegistry::init('Log')->create();
					ClassRegistry::init('Log')->saveAll($logs);
				}
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

		// * Se envia con numero de requerimiento para que las notificaciones sean distintas y no se sobrepongan

		$respuestaRequerimiento = [
			'title'		=> "nz Warehouse",
			'message'	=> "Han respondido tu requerimiento | Rº {$respuesta['Requerimiento']['id']}",
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

		if ($this->request->query['respuesta']) {
			$respuestaWarehouse = $this->WarehouseNodriza->Editarproducto([
				[
					"id" 							=> $this->request->query['producto_id'],
					"permitir_ingreso_sin_barra" 	=> true,
				]
			]);

			$logs[] = array('Log' => array(
				'administrador' => 'Se actualiza producto por notificación',
				'modulo' 		=> 'api_respuesta_requerimiento_problemas_recepcion_productos',
				'modulo_accion' => json_encode($respuestaWarehouse)
			));
		}

		$logs[] = array('Log' => array(
			'administrador' => 'Notificacion Push',
			'modulo' 		=> 'api_respuesta_requerimiento_problemas_recepcion_productos',
			'modulo_accion' => json_encode($respuesta)
		));



		$response = array(
			'name' 		=> 'success',
			'message' 	=> 'Respuesta fue guardada pero no pudo ser notificado',
			'data' 		=> json_encode($respuesta)
		);


		try {
			$this->Pushy->sendPushNotification($respuestaRequerimiento, $this->request->query['token_push']);
			$response = array(
				'name' 		=> 'success',
				'message' 	=> 'Respuesta fue guardada y enviado',
				'data' 		=> $respuesta
			);
		} catch (\Throwable $th) {

			$logs[] = array('Log' => array(
				'administrador' => 'Problemas para notificar',
				'modulo' 		=> 'api_respuesta_requerimiento_problemas_recepcion_productos',
				'modulo_accion' => json_encode($respuesta)
			));
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveAll($logs);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}


	public function api_requerimientos_sin_atender()
	{
		# Existe token
		if (!isset($this->request->query['token'])) {

			throw new UnauthorizedException("Token requerido");
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {

			throw new UnauthorizedException("Token de sesión expirado o invalido");
		}

		$respuesta = ClassRegistry::init('Requerimiento')->find('all', [
			'conditions' => [
				'atendido' 	=> 0
			]
		]);

		if (!$respuesta) {
			throw new NotFoundException('No hay requerimientos por atender');
		}

		$response = array(
			'name' 		=> 'success',
			'message' 	=> 'Se han encontrado requerimientos sin atender',
			'data' 		=> $respuesta
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}
}
