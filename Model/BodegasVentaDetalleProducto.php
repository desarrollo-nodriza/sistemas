<?php
App::uses('AppModel', 'Model');
class BodegasVentaDetalleProducto extends AppModel
{
	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);
}
