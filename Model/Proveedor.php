<?php
App::uses('AppModel', 'Model');
class Proveedor extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';


	public $hasMany = array(
		/*'PrecioEspecificoProveedor' => array(
			'className'				=> 'PrecioEspecificoProveedor',
			'foreignKey'			=> 'proveedor_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),*/
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'proveedor_id',
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
		'Saldo' => array(
			'className'				=> 'Saldo',
			'foreignKey'			=> 'proveedor_id',
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
			'foreignKey'			=> 'proveedor_id',
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
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'joinTable'				=> 'proveedores_venta_detalle_productos',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'venta_detalle_producto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Moneda' => array(
			'className'				=> 'Moneda',
			'joinTable'				=> 'monedas_proveedores',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'moneda_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'width'					=> 'MonedasProveedor',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
		/*'Marca' => array(
			'className'				=> 'Marca',
			'joinTable'				=> 'proveedores_marcas',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'marca_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),*/
		/*'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'joinTable'				=> 'orden_compras_proveedores',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'orden_compra_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'with'					=> 'OrdenComprasProveedor',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)*/
	);


	public function afterFind($results, $primary = false) {


		# Convertimos meta emails en un "Modelo"
		foreach ($results as $key => $val) {

	        if (isset($val['Proveedor']['meta_emails'])) {
	            $results[$key]['Proveedor']['meta_emails'] = json_decode($results[$key]['Proveedor']['meta_emails'], true);
	        }
	    }
	    return $results;
	}
}
