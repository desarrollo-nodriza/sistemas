<?php 
App::uses('AppModel', 'Model');

Class EmbalajeProductoWarehouse extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $useDbConfig = 'warehouse';
	public $useTable = 'embalaje_productos';
	public $displayField	= 'id';


	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'EmbalajeWarehouse' => array(
			'className'				=> 'EmbalajeWarehouse',
			'foreignKey'			=> 'embalaje_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
        ),
        'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'foreignKey'			=> 'producto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'ProductoWarehouse' => array(
			'className'				=> 'ProductoWarehouse',
			'foreignKey'			=> 'producto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'VentaDetalle' => array(
			'className'				=> 'VentaDetalle',
			'foreignKey'			=> 'detalle_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);

}