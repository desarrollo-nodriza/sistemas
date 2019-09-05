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
		)
	);

	
	/**
	 * [crear_token description]
	 * @param  [type]  $administrador_id identificador del administrador
	 * @param  string  $tienda_id        identificador de la tienda
	 * @param  integer $duracion         horas de duración del token
	 * @return bool
	 */
	public function crear_token($administrador_id, $tienda_id = '', $duracion = 6)
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


	public function crear_token_proveedor($proveedor_id, $tienda_id = '', $duracion = 365)
	{	
		$expira = new DateTime(date('Y-m-d H:i:s'));
		$expira->modify(sprintf('+%d hours', $duracion));

		$token_acceso = $this->generar_token(24);

		$token['Token'] = array(
			'proveedor_id' => $proveedor_id,
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



	public function generar_token($largo = 24)
	{
		return bin2hex(openssl_random_pseudo_bytes($largo));
	}


	/**
	 * [validar_token description]
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 */
	public function validar_token($token)
	{

		$token = $this->find('first', array(
			'conditions' => array(
				'Token.token' => trim($token)
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

}
