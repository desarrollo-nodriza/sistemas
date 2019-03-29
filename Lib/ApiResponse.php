<?php
class ApiResponse
{

	private static $clase							= null;


	public static function code_Exception($codeIndex = null)
	{
		$errorMap		= array(
			//0 OK
			'MALFORMED_DATA'             => array('code' => 10, 'message' => 'Datos malformados'),
			'BLANK_FIELD'                => array('code' => 11, 'message' => 'Campos en blanco'),
			'AUTH_INCOMPLETO'            => array('code' => 12, 'message' => 'Email o clave incompleta'),
			'AUTH_CLIENTE_INACTIVO'      => array('code' => 13, 'message' => 'Cuenta inactiva'),
			'AUTH_CLIENTE_INEXISTENTE' 	 => array('code' => 14, 'message' => 'Cuenta no existe'),
			'AUTH_CLIENTE_CLAVE_ERRONEA' => array('code' => 14, 'message' => 'Clave incorrecta'),
			'EMPTY_FORM'                 => array('code' => 15, 'message' => 'Formulario sin campos'),
			'UNKNOWN_FIELD'              => array('code' => 16, 'message' => 'Campo no existe'),
			'REQUIRED_FIELD'             => array('code' => 17, 'message' => 'Campo requerido'),
			'UNKNOWN_ERROR'              => array('code' => 18, 'message' => 'Error desconocido')
		);

		if ( ! $codeIndex || empty($errorMap[$codeIndex]) )
		{
			$codeIndex		= 'UNKNOWN_ERROR';
		}

		return	array('code' => $errorMap[$codeIndex]['code'], 'message' => $errorMap[$codeIndex]['message']);

	}

	public static function init($class = null)
	{
		if ( $class )
		{
			self::$clase	= $class;
		}
	}

	public static function code($code = null)
	{
		if ( ! $code )
		{
			return false;
		}

		if ( self::$clase )
		{	
			$exception                   = self::code_Exception($code);

			self::$clase->apiCode        = $exception['code'];
			self::$clase->apiCodeMessage = $exception['message'];
		}

		return false;
	}
}
