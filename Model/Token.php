<?php
App::uses('AppModel', 'Model');
class Token extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'token';

	/**
	 * VALIDACIONES
	 */
	public $validate = array(
		'administrador_id' => array(
			'alphaNumeric' => array(
                'rule' => 'alphaNumeric',
                'message' => 'user_id is not integer'
            )
		),
		'proveedor_id' => array(
			'alphaNumeric' => array(
                'rule' => 'alphaNumeric',
                'message' => 'user_id is not integer'
            )
		)
	);


	/**
	 * Asociaciones
	 * @var array
	 */
	public $belongsTo = array(
		'Administrador' => array(
			'className'				=> 'Administrador',
			'foreignKey'			=> 'administrador_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Proveedor' => array(
			'className'				=> 'Proveedor',
			'foreignKey'			=> 'proveedor_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'VentaCliente' => array(
			'className'				=> 'VentaCliente',
			'foreignKey'			=> 'venta_cliente_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

	
	/**
	 * [crear_token description]
	 * @param  [type]  $administrador_id identificador del administrador
	 * @param  string  $tienda_id        identificador de la tienda
	 * @param  integer $duracion         horas de duración del token
	 * @return bool
	 */
	public function crear_token($administrador_id, $tienda_id = '', $duracion = 8)
	{	
		$expira = new DateTime(date('Y-m-d H:i:s'));
		$expira->modify(sprintf('+%d hours', $duracion));

		$token_acceso = $this->generar_token(24);

		$token['Token'] = array(
			'administrador_id' => $administrador_id,
			'token'            => $token_acceso,
			'expires'          => $expira->format('Y-m-d H:i:s')
		);

		if (!empty($tienda_id)) {
			$token['Token']['tienda_id'] = $tienda_id;
		}

		$this->create();
		if ($this->save($token)) {
			
			return array(
				'expires_token' => $expira->format('Y-m-d H:i:s'),
				'token'         => $token_acceso
			);

		}else{
			return $this->validationErrors;
		}
	}

	
	/**
	 * crear_token_proveedor
	 *
	 * @param  mixed $proveedor_id  Identificador del proveedor
	 * @param  mixed $tienda_id Identificador de la tienda
	 * @param  mixed $duracion Duración en horas del token
	 * @return token
	 */
	public function crear_token_proveedor($proveedor_id, $tienda_id = '', $duracion = 240)
	{	
		$expira = new DateTime(date('Y-m-d H:i:s'));
		$expira->modify(sprintf('+%d hours', $duracion));

		$token_acceso = $this->generar_token(24);

		$token['Token'] = array(
			'proveedor_id' => $proveedor_id,
			'token'            => $token_acceso,
			'expires'          => $expira->format('Y-m-d H:i:s'),
			'tipo'			   => 'proveedor'
		);

		if (!empty($tienda_id)) {
			$token['Token']['tienda_id'] = $tienda_id;
		}

		$this->create();
		if ($this->save($token)) {
			
			return array(
				'expires_token' => $expira->format('Y-m-d H:i:s'),
				'token'         => $token_acceso
			);

		}else{

			$log = array(
				'Log' => array(
					'administrador' => 'Crear Token Proveedor: ' . $proveedor_id,
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode($this->validationErrors)
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->save($log);

			return array(
				'expires_token' => '',
				'token'         => ''
			);
		}
	}

	
	/**
	 * crear_token_cliente
	 *
	 * @param  mixed $cliente_id  Identificador del cliente
	 * @param  mixed $tienda_id Identificador de la tienda
	 * @param  mixed $duracion Duración en horas del token
	 * @return token
	 */
	public function crear_token_cliente($cliente_id, $tienda_id = '', $duracion = 48)
	{	
		$expira = new DateTime(date('Y-m-d H:i:s'));
		$expira->modify(sprintf('+%d hours', $duracion));

		$token_acceso = $this->generar_token(24);

		$token['Token'] = array(
			'venta_cliente_id' => $cliente_id,
			'token'            => $token_acceso,
			'expires'          => $expira->format('Y-m-d H:i:s'),
			'tipo'			   => 'cliente'
		);

		if (!empty($tienda_id)) {
			$token['Token']['tienda_id'] = $tienda_id;
		}

		$this->create();
		if ($this->save($token)) {
			
			return array(
				'expires_token' => $expira->format('Y-m-d H:i:s'),
				'token'         => $token_acceso
			);

		}else{

			$log = array(
				'Log' => array(
					'administrador' => 'Crear Token Cliente: ' . $cliente_id,
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode($this->validationErrors)
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->save($log);

			return array(
				'expires_token' => '',
				'token'         => ''
			);
		}
	}

	
	/**
	 * generar_token
	 *
	 * @param  mixed $largo largo en bytes del hash
	 * @return hash
	 */
	public function generar_token($largo = 24)
	{
		return bin2hex(openssl_random_pseudo_bytes($largo));
	}


		
	/**
	 * validar_token
	 *
	 * @param  mixed $token Token string
	 * @param  mixed $tipo admin-proveedor-cliente
	 * @return bool
	 */
	public function validar_token($token, $tipo = 'admin')
	{
		$token = $this->find('first', array(
			'conditions' => array(
				'Token.token' => trim($token),
				'Token.tipo' => $tipo
			),
			'fields' => array(
				'Token.expires'
			)
		));


		if (empty($token)) {
			throw new Exception("Token not found", 404);
		}

		$now     = strtotime('now');
		$expires = strtotime($token['Token']['expires']);

		if ($now >= $expires) {
			return false;
		}else{
			return true;
		}

	}
	
	/**
	 * obtener_propietario_token
	 * 
	 * Obtiene información básica del usuario
	 *
	 * @param  mixed $token
	 * @return void
	 */
	public function obtener_propietario_token($token)
	{
		$token = $this->find('first', array(
			'conditions' => array(
				'Token.token' => trim($token)
			),
			'contain' => array(
				'Administrador' => array(
					'fields' => array(
						'Administrador.id',
						'Administrador.nombre',
						'Administrador.email'
					)
				),
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.id',
						'Proveedor.nombre'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.id',
						'VentaCliente.nombre',
						'VentaCliente.email'
					)
				)
			),
			'fields' => array(
				'Token.administrador_id',
				'Token.proveedor_id',
				'Token.venta_cliente_id'
			)
		));

		if (empty($token))
		{
			return 'No definido';
		}

		# Token admin
		if (!empty($token['Token']['administrador_id']))
		{
			return $token['Administrador']['nombre'] . ' <' . $token['Administrador']['email'] . '>';
		}

		# Token proveedor
		if (!empty($token['Token']['proveedor_id']))
		{
			return $token['Proveedor']['nombre'];
		}

		# Token cliente
		if (!empty($token['Token']['venta_cliente_id']))
		{
			return $token['Ventacliente']['nombre'] . ' <' . $token['Ventacliente']['email'] . '>';
		}

		return 'No definido';

	}
	

	/**
	 * obtener_propietario_token_full
	 * 
	 * Obtiene toda la finromación del propietario del token
	 *
	 * @param  mixed $token
	 * @return void
	 */
	public function obtener_propietario_token_full($token)
	{
		$token = $this->find('first', array(
			'conditions' => array(
				'Token.token' => trim($token)
			),
			'contain' => array(
				'Administrador' => array(
					'fields' => array(
						'Administrador.id',
						'Administrador.nombre',
						'Administrador.email'
					),
					'Rol'=> [
						'Bodega' => [
							'fields' => [
								'Bodega.id',
								'Bodega.nombre'
							]
						],
						'fields'=>[
							'Rol.id',
							'Rol.bodega_id'
							]
					]
				),
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.id',
						'Proveedor.nombre'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.id',
						'VentaCliente.nombre',
						'VentaCliente.email'
					)
				)
			),
			'fields' => array(
				'Token.administrador_id',
				'Token.proveedor_id',
				'Token.venta_cliente_id'
			)
		));

		return $token;

	}


}
