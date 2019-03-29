<?php
App::uses('AppModel', 'Model');
class VentaDetalle extends AppModel
{
	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Venta')
		),
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'VentaDetalleProducto')
		)
	);
}
