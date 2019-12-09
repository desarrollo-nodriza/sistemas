<?php
App::uses('AppModel', 'Model');
class Prospecto extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';


	private static $estados = array(
		'creado' => 'Creado',
		'cotizacion' => 'Paso a cotización',
		'esperando_informacion' => 'Esperando información'
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
		'nombre' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'descripcion' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
	);

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'EstadoProspecto' => array(
			'className'				=> 'EstadoProspecto',
			'foreignKey'			=> 'estado_prospecto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'EstadoProspecto')
		),
		'Moneda' => array(
			'className'				=> 'Moneda',
			'foreignKey'			=> 'moneda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Moneda')
		),
		'Origen' => array(
			'className'				=> 'Origen',
			'foreignKey'			=> 'origen_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Origen')
		),
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'ValidezFecha')
		),
		'Transporte' => array(
			'className'				=> 'Transporte',
			'foreignKey'			=> 'transporte_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'ValidezFecha')
		),
		'VentaCliente' => array(
			'className'				=> 'VentaCliente',
			'foreignKey'			=> 'venta_cliente_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'ValidezFecha')
		),
		'Direccion' => array(
			'className'				=> 'Direccion',
			'foreignKey'			=> 'direccion_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'ValidezFecha')
		)
	);
	public $hasMany = array(
		'Cotizacion' => array(
			'className'				=> 'Cotizacion',
			'foreignKey'			=> 'prospecto_id',
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

	public $hasAndBelongsToMany = array(
		'Productotienda' => array(
			'className'				=> 'Productotienda',
			'joinTable'				=> 'productotiendas_prospectos',
			'foreignKey'			=> 'prospecto_id',
			'associationForeignKey'	=> 'id_product',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ProductotiendaProspecto',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'joinTable'				=> 'productos_prospectos',
			'foreignKey'			=> 'prospecto_id',
			'associationForeignKey'	=> 'venta_detalle_producto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ProductosProspecto',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);

	public function beforeSave( $options = array() )
	{	

	}

	public function obtener_estados($nombre = '')
	{
		if (empty($nombre)) {
			return self::$estados;
		}

		return self::$estados[$nombre];
	}
}
