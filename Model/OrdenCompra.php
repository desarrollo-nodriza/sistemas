<?php
App::uses('AppModel', 'Model');
class OrdenCompra extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'created';

	/**
	 * BEHAVIORS
	 */
	var $actsAs			= array(
		/**
		 * IMAGE UPLOAD
		 */
		'Image'		=> array(
			'fields'	=> array(
				'adjunto'	=> array(
				)
			)
		)
	);

	public $hasMany = array(
		'ChildOrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'parent_id',
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


	public $belongsTo = array(
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
		'Moneda' => array(
			'className'				=> 'Moneda',
			'foreignKey'			=> 'moneda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'Proveedor' => array(
			'className'				=> 'Proveedor',
			'foreignKey'			=> 'proveedor_id',
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
			'joinTable'				=> 'orden_compras_ventas',
			'foreignKey'			=> 'orden_compra_id',
			'associationForeignKey'	=> 'venta_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'OrdenComprasVenta',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'joinTable'				=> 'orden_compras_venta_detalle_productos',
			'foreignKey'			=> 'orden_compra_id',
			'associationForeignKey'	=> 'venta_detalle_producto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'OrdenComprasVentaDetalleProducto',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		/*'Proveedor' => array(
			'className'				=> 'Proveedor',
			'joinTable'				=> 'orden_compras_proveedores',
			'foreignKey'			=> 'orden_compra_id',
			'associationForeignKey'	=> 'proveedor_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'OrdenComprasProveedor',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)*/
	);


	public function afterFind($results, $primary = false) {


		# Convertimos meta dtes en un "Modelo"
		foreach ($results as $key => $val) {

	        if (isset($val['OrdenCompra']['meta_dtes'])) {
	            $results[$key]['OrdenCompra']['meta_dtes'] = json_decode($results[$key]['OrdenCompra']['meta_dtes'], true);
	        }
	    }
	    return $results;
	}
}
