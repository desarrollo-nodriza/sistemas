<?php
App::uses('AppModel', 'Model');
class Mensaje extends AppModel
{

	private $origen = array(
		'cliente' => 'Cliente',
		'empleado' => 'Funcionario'
	);

	public function origen($nombre)
	{
		return $this->origen[$nombre];
	}


	/**
	 * BEHAVIORS
	 * Adjunto
	 */
	var $actsAs			= array(
		'Image'		=> array(
			'fields'	=> array(
				'adjunto'	=> array(
					'versions'	=> array(
						array(
							'prefix'	=> 'mini',
							'width'		=> 100,
							'height'	=> 100,
							'crop'		=> true
						),
						array(
							'prefix'	=> 'landscape',
							'width'		=> 300,
							'height'	=> 200,
							'crop'		=> true
						)
					)
				)
			)
		)
	);


	public $belongsTo = array(
		'HiloMensaje' => array(
			'className'				=> 'Mensaje',
			'foreignKey'			=> 'parent_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'Administrador' => array(
			'className'				=> 'Administrador',
			'foreignKey'			=> 'administrador_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'VentaCliente' => array(
			'className'				=> 'VentaCliente',
			'foreignKey'			=> 'venta_cliente_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		)
	);

	public $hasMany = array(
        'RespuestaMensaje' => array(
            'className' => 'Mensaje',
            'foreignKey' => 'parent_id'
        )
    );


	/**
	 * Crea un hilo padre/madre para los mensajes
	 * @param  [type] $cliente       [description]
	 * @param  [type] $administrador [description]
	 * @param  [type] $venta         [description]
	 * @param  [type] $producto      [description]
	 * @return [type]                [description]
	 */
	public function crear_hilo($data)
	{
		$hilo = array(
			'Mensaje' => array(
				'parent_id'                 => null, 
				'venta_cliente_id'          => null,
				'administrador_id'          => null,
				'venta_id'                  => null,
				'venta_detalle_producto_id' => null
			)
		);

		if (isset($data['venta_cliente_id'])) {
			$hilo['Mensaje']['venta_cliente_id'] = $data['venta_cliente_id'];
		}

		if (isset($data['administrador_id'])) {
			$hilo['Mensaje']['administrador_id'] = $data['administrador_id'];
		}

		if (isset($data['venta_id'])) {
			$hilo['Mensaje']['venta_id'] = $data['venta_id'];
		}

		if (isset($data['venta_detalle_producto_id'])) {
			$hilo['Mensaje']['venta_detalle_producto_id'] = $data['venta_detalle_producto_id'];
		}

		$this->save($hilo);
		return $this->id;
	}


	/**
	 * [obtener_hilo_id description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function obtener_hilo_id($data)	
	{
		$qry = array(
			'conditions' => array(
				'Mensaje.parent_id' => null
			),
			'fields' => array(
				'Mensaje.id'
			)
		);	

		if (isset($data['venta_cliente_id'])) {
			$qry = array_replace_recursive($qry, array(
				'conditions' => array(
					'Mensaje.venta_cliente_id' => $data['venta_cliente_id']
				)
			));
		}

		if (isset($data['administrador_id'])) {
			$qry = array_replace_recursive($qry, array(
				'conditions' => array(
					'Mensaje.administrador_id' => $data['administrador_id']
				)
			));
		}

		if (isset($data['venta_id'])) {
			$qry = array_replace_recursive($qry, array(
				'conditions' => array(
					'Mensaje.venta_id' => $data['venta_id']
				)
			));
		}

		if (isset($data['venta_detalle_producto_id'])) {
			$qry = array_replace_recursive($qry, array(
				'conditions' => array(
					'Mensaje.venta_detalle_producto_id' => $data['venta_detalle_producto_id']
				)
			));
		}

		# No hay nada que buscar
		if (empty($qry)) {
			return 0;
		}

		$hilo = $this->find('first', $qry);

		if (empty($hilo)) {
			return 0;
		}

		return $hilo['Mensaje']['id'];

	}

}