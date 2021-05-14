<?php
App::uses('AppModel', 'Model');
class OrdenComprasVentaDetalleProducto extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	
	/**
	 * Set Cake config DB
	 */
	public $primaryKey = 'id';

	public $belongsTo = array(
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Venta')
		),
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'orden_compra_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'OrdenCompra')
		)
	);


	public function obtener_cantidad_confirmada_proveedor($id_producto, $id_oc)
	{
		$ocDetalles = $this->find('first', array(
			'conditions' => array(
				'OrdenComprasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				'OrdenComprasVentaDetalleProducto.orden_compra_id' => $id_oc
			)
		));

		return $ocDetalles['OrdenComprasVentaDetalleProducto']['cantidad'];

	}


	public function obtener_productos_por_oc($id_oc)
	{
		
	}

}