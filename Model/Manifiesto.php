<?php
App::uses('AppModel', 'Model');
class Manifiesto extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'created';


	public $tipo_productos = array(
		'Dia habil siguiente' => 'Dia habil siguiente'
	);

	public $tamano_productos = array(
		'Paqueteria Moto'      => 'Paqueteria Moto',
		'Paqueteria Camioneta' => 'Paqueteria Camioneta'
	);

	public $tipo_retornos = array(
		'Sin retorno' => 'Sin retorno',
		'Con retorno' => 'Con retorno'
	);


	public $tramos = array(
		'Tramo 0 (moto)' => array(
			'min' => 0,
			'max' => 3
		),
		'Tramo 1 3.1kg - 10kg' => array(
			'min' => 3.1,
			'max' => 10
		),
		'Tramo 2 10.1kg - 15kg' => array(
			'min' => 10.1,
			'max' => 15
		),
		'Tramo 3 15.1kg - 25kg' => array(
			'min' => 15.1,
			'max' => 25
		),
		'Tramo 4 + 25kg' => array(
			'min' => 25,
			'max' => 1000
		)
	);

	/**
	 * BEHAVIORS
	 */
	var $actsAs			= array(
		/**
		 * IMAGE UPLOAD
		 */
		/*
		'Image'		=> array(
			'fields'	=> array(
				'imagen'	=> array(
					'versions'	=> array(
						array(
							'prefix'	=> 'mini',
							'width'		=> 100,
							'height'	=> 100,
							'crop'		=> true
						)
					)
				)
			)
		)
		*/
	);

	/**
	 * VALIDACIONES
	 */
	public $validate = array(
		'impreso' => array(
			'boolean' => array(
				'rule'			=> array('boolean'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'entregado' => array(
			'boolean' => array(
				'rule'			=> array('boolean'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		)
	);


	public $belongsTo = array(
		'Transporte' => array(
			'className'				=> 'Transporte',
			'foreignKey'			=> 'transporte_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'Administrador' => array(
			'className'				=> 'Administrador',
			'foreignKey'			=> 'administrador_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'Comuna' => array(
			'className'				=> 'Comuna',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);

	public $hasAndBelongsToMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'joinTable'				=> 'manifiestos_ventas',
			'foreignKey'			=> 'manifiesto_id',
			'associationForeignKey'	=> 'venta_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ManifiestosVenta',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		/*'Orden' => array(
			'className'				=> 'Orden',
			'joinTable'				=> 'manifiestos_ventas',
			'foreignKey'			=> 'manifiesto_id',
			'associationForeignKey'	=> 'id_order',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ManifiestosVenta',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)*/

	);



	public function obtener_tramo_por_peso($peso = 0)
	{
		foreach ($this->tramos as $tramo => $valores) {
			if ($peso >= $valores['min'] && $peso <= $valores['max']) {
				return $tramo;
			}
		}
	}
}
