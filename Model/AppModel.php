<?php
App::uses('Model', 'Model');
App::uses('ApiResponse', 'Lib');
class AppModel extends Model
{
	public $recursive		= -1;
	public $actsAs			= array('Containable');

	/**
	 * VALIDACION -- REPETIR CLAVE
	 */
	function repetirClave($data)
	{
		return ($this->data[$this->name]['clave'] === $this->data[$this->name]['repetir_clave']);
	}


	/**
	 * VALIDACION -- VALIDA RUT CHILENO
	 */
	public function rutChileno($data = array(), $dv = null)
	{
		$rut		= $rutcalc = preg_replace('/[^\da-z]/i', '', current($data));
		if ( ! $dv )
		{
			$dv			= substr($rut, -1);
			$rut		= $rutcalc = substr_replace($rut, '', -1);
		}
		else
		{
			$dv			= $this->data[$this->name][$dv];
		}

		if ( ! $rut || ! is_numeric($rut) || strlen($rut) < 6 || strlen($dv) != 1 )
		{
			return false;
		}

		$suma		= 1;
		for ( $x = 0; $rutcalc != 0; $rutcalc /= 10 )
		{
			$suma	= ($suma + $rutcalc % 10 * (9 - $x++ % 6)) % 11;
		}
		$dvcalc		= chr($suma ? $suma + 47 : 75);

		return (strtolower($dvcalc) == strtolower($dv));
	}


	/**
	 * VALIDACION -- VALIDA QUE UNA LLAVE FORANEA EXISTA EN EL MODELO ASOCIADO
	 */
	public function validateForeignKey($data = array())
	{
		$associations	= array_map(
			create_function('$v', 'return $v["foreignKey"];'),
			$this->belongsTo
		);
		$aliases		= array();
		foreach ( $associations as $model => $foreignKey )
		{
			if ( ! array_key_exists($foreignKey, $aliases) )
			{
				$aliases[$foreignKey] = array();
			}
			array_push($aliases[$foreignKey], $model);
		}
		foreach ( $aliases[key($data)] as $model )
		{
			$count	= $this->{$model}->find('count', array(
				'conditions'	=> array("{$model}.{$this->{$model}->primaryKey}" => current($data)),
				'recursive'		=> -1
			));
			if ( $count == 1 )
			{
				return true;
			}
		}
		return false;
	}


	public $apiCode				= null;
	public $apiCodeMessage		= null;

	/**
	 * API - Respuestas
	 */
	public function apiResponse($code = null)
	{
		return ApiResponse::code($code);
	}


	/**
	 * API - Autenticacion
	 */
	public function apiAuth($data = array())
	{
		ApiResponse::init($this);
		/**
		 * Valida al cliente
		 */
		if ( empty($data) || empty($data['email']) || empty($data['secreto']) )
		{
			return ApiResponse::code('AUTH_INCOMPLETO');
			return $this->apiResponse('AUTH_INCOMPLETO');
		}

		$admin		= ClassRegistry::init('Administrador')->find('first', array(
			'fields'		=> array('Administrador.id', 'Administrador.email', 'Administrador.secret_key', 'Administrador.activo'),
			'conditions'	=> array('Administrador.email' => $data['email']),
			'recursive'		=> -1,
			'callbacks'		=> false
		));
		if ( ! $admin )
		{
			return $this->apiResponse('AUTH_CLIENTE_INEXISTENTE');
		}
		if ( $admin['Administrador']['secret_key'] !== $data['secreto'] )
		{
			return $this->apiResponse('AUTH_CLIENTE_CLAVE_ERRONEA');
		}
		if ( ! $admin['Administrador']['activo'] )
		{
			return $this->apiResponse('AUTH_CLIENTE_INACTIVO');
		}

		return true;
	}
}
