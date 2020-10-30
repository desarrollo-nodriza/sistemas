<?php
App::uses('AppModel', 'Model');
class VentaCliente extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';


	private static $tipo_cliente = array(
		'persona' => 'Persona natural',
		'empresa' => 'Empresa'
	);


	/**
	 * ASOCIACIONES
	 */
	public $hasMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_cliente_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'Direccion' => array(
			'className'				=> 'Direccion',
			'foreignKey'			=> 'venta_cliente_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'Prospecto' => array(
			'className'				=> 'Prospecto',
			'foreignKey'			=> 'venta_cliente_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'Mensaje' => array(
			'className'				=> 'Mensaje',
			'foreignKey'			=> 'venta_cliente_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'Token' => array(
			'className'				=> 'Token',
			'foreignKey'			=> 'venta_cliente_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'Contacto' => array(
			'className'				=> 'Contacto',
			'foreignKey'			=> 'venta_cliente_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		)
	);

	/**
	 * Callback
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public function beforeSave($options = array())
	{	
		# Si el viene con rut, lo formateamos
		if (isset($this->data['VentaCliente']['rut'])) {

			$nw_rut = str_replace('.', '', $this->data['VentaCliente']['rut']);
			$nw_rut = str_replace('-', '', $nw_rut);
			
			$dv = substr($nw_rut, -1);
			$rut = substr($nw_rut, 0, -1);
		
			$this->data['VentaCliente']['rut'] = $rut . '-' . $dv;

		}

		return true;
	}


	function obtener_tipo_cliente()
	{
		return self::$tipo_cliente;
	}


	public function crear_token($cliente_id, $tienda_id = '', $duracion = 48)
	{	
		$expira = new DateTime(date('Y-m-d H:i:s'));
		$expira->modify(sprintf('+%d hours', $duracion));

		$token_acceso = ClassRegistry::init('Token')->generar_token(24);

		$token['Token'] = array(
			'venta_cliente_id' => $cliente_id,
			'token'            => $token_acceso,
			'expires'          => $expira->format('Y-m-d H:i:s')
		);

		if (!empty($tienda_id)) {
			$token['Token']['tienda_id'] = $tienda_id;
		}

		ClassRegistry::init('Token')->create();
		if (ClassRegistry::init('Token')->save($token)) {
			
			return array(
				'expires_token' => $expira->format('Y-m-d H:i:s'),
				'token'         => $token_acceso
			);

		}else{

			$log = array(
				'Log' => array(
					'administrador' => 'Crear Token Cliente: ' . $cliente_id,
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode(ClassRegistry::init('Token')->validationErrors)
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
}
