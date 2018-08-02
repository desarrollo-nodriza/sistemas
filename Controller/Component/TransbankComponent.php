<?php
App::uses('Component', 'Controller');
App::uses('File', 'Utility');
class TransbankComponent extends Component
{
	/**
	 * Campos enviados via POST por Transbank a la página de cierre
	 * @var array
	 */
	public $camposPost		= array(
		'TBK_ORDEN_COMPRA',		'TBK_TIPO_TRANSACCION',		'TBK_RESPUESTA',
		'TBK_MONTO',			'TBK_CODIGO_AUTORIZACION',	'TBK_FINAL_NUMERO_TARJETA',
		'TBK_FECHA_CONTABLE',	'TBK_FECHA_TRANSACCION',	'TBK_HORA_TRANSACCION',
		'TBK_ID_SESION',		'TBK_ID_TRANSACCION',		'TBK_TIPO_PAGO',
		'TBK_NUMERO_CUOTAS',	'TBK_VCI',					'TBK_MAC'
	);

	/**
	 * Rutas relativas a los CGI
	 * @var array
	 */
	public $cgiPath			= array(
		'pago'					=> '/cgi-bin/tbk_bp_pago.cgi',
		'checkMac'				=> '/cgi-bin/tbk_check_mac.cgi'
	);

	/**
	 * Ruta relativa al directorio de logs y template del archivo log
	 */
	public $macLog			= array(
		'path'					=> '/cgi-bin/log/mac',
		'template'				=> 'tbk_maclog_TR_%d.log'
	);


	/**
	 * Genera el archivo de validacion de firma digital MAC
	 *
	 * @param			int				$compra_id			ID de la OC
	 * @param			array			$campos				POST enviado por Transbank
	 * @return			bool								Resultado de la operación
	 */
	public function logMac($compra_id = null, $campos = array())
	{
		if ( ! $compra_id || empty($campos) )
		{
			return false;
		}

		/**
		 * Crea el archivo de log y la carpeta, si no existen
		 */
		$path			= WWW_ROOT . ltrim($this->macLog['path'], '/') . DS . sprintf($this->macLog['template'], $compra_id);
		$file			= @new File($path, true, 0755);
		if ( ! $file->exists() )
		{
			return false;
		}

		/**
		 * Escribe el log
		 */
		$log			= '';
		foreach ( $this->camposPost as $campo )
		{
			$log			.= sprintf('%s=%s&', $campo, $campos[$campo]);
		}
		if ( ! $file->write($log, 'w+') )
		{
			return false;
		}

		return $path;
	}


	/**
	 * Realiza la validacion de la firma digital MAC
	 *
	 * @param			string			$log				Archivo con el log de la transacción a validar
	 * @return			bool								Resultado de la operación
	 */
	public function validaMac($log = null)
	{
		$cgi		= WWW_ROOT . ltrim($this->cgiPath['checkMac'], '/');
		if ( ! is_file($cgi) )
		{
			return false;
		}

		@exec(sprintf('%s %s', $cgi, $log), $resultado);

		if ( empty($resultado) || empty($resultado[0]) )
		{
			return false;
		}

		return (trim($resultado[0]) === 'CORRECTO');
	}


	/**
	 * Retorna la descripcion del tipo de pago segun su codigo (TBK_TIPO_PAGO)
	 *
	 * @param			string			$codigo				Codigo extraido de TBK_TIPO_PAGO
	 * @return			string								Descripción
	 */
	public static function tipoPago($codigo = '')
	{
		return ($codigo == 'VD' ? 'Débito' : 'Crédito');
	}


	/**
	 * Retorna la descripcion del tipo de cuota segun su codigo (TBK_TIPO_PAGO)
	 *
	 * @param			string			$codigo				Codigo extraido de TBK_TIPO_PAGO
	 * @return			string								Descripción
	 */
	public static function tipoCuota($codigo = '')
	{
		$tipos			= array(
			'VD'			=> 'Venta Débito',
			'VN'			=> 'Sin Cuotas',
			'VC'			=> 'Cuotas normales',
			'SI'			=> 'Sin interés',
			'S2'			=> 'Sin interés',
			'NC'			=> 'Sin interés',
			'CI'			=> 'Cuotas Comercio'
		);

		return (isset($tipos[$codigo]) ? $tipos[$codigo] : '');
	}
}
