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

	public function recalcular_total_producto($id_detalle){

		$detalle = $this->find('first', array(
			'conditions' => array(
				'VentaDetalle.id' => $id_detalle
			)
		));

		$detalle['VentaDetalle']['precio_bruto'] = monto_bruto($detalle['VentaDetalle']['precio']);
		$detalle['VentaDetalle']['total_neto']   = ($detalle['VentaDetalle']['precio'] * ($detalle['VentaDetalle']['cantidad'] - $detalle['VentaDetalle']['cantidad_anulada']));
		$detalle['VentaDetalle']['total_bruto']  = monto_bruto($detalle['VentaDetalle']['total_neto']);

		return $this->save($detalle);

	}
}
