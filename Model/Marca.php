<?php
App::uses('AppModel', 'Model');
class Marca extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';


	public $hasMany = array(
		'PrecioEspecificoMarca' => array(
			'className'				=> 'PrecioEspecificoMarca',
			'foreignKey'			=> 'marca_id',
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
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'foreignKey'			=> 'marca_id',
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
		/*'Proveedor' => array(
			'className'				=> 'Proveedor',
			'joinTable'				=> 'marcas_proveedores',
			'foreignKey'			=> 'marca_id',
			'associationForeignKey'	=> 'proveedor_id',
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
}
