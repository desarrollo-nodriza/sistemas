<?php
App::uses('AppModel', 'Model');
class VentaDetalleProducto extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'Marca' => array(
			'className'				=> 'Marca',
			'foreignKey'			=> 'marca_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);

	public $hasMany = array(
		'VentaDetalle' => array(
			'className'				=> 'VentaDetalle',
			'foreignKey'			=> 'venta_detalle_producto_id',
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
		'PrecioEspecificoProducto' => array(
			'className'				=> 'PrecioEspecificoProducto',
			'foreignKey'			=> 'venta_detalle_producto_id',
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
		'BodegasVentaDetalleProducto' => array(
			'className'				=> 'BodegasVentaDetalleProducto',
			'foreignKey'			=> 'venta_detalle_producto_id',
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
		'Bodega' => array(
			'className'				=> 'Bodega',
			'joinTable'				=> 'bodegas_venta_detalle_productos',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'associationForeignKey'	=> 'bodega_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'width'					=> 'BodegasVentaDetalleProducto',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Proveedor' => array(
			'className'				=> 'Proveedor',
			'joinTable'				=> 'proveedores_venta_detalle_productos',
			'foreignKey'			=> 'venta_detalle_producto_id',
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
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'joinTable'				=> 'orden_compras_venta_detalle_productos',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'associationForeignKey'	=> 'orden_compra_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'width'					=> 'OrdenComprasVentaDetalleProducto',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);


	public function beforeValidate($options = array()) {

		/*if (count(Hash::extract($this->data, 'Bodega.{n}.io')) > 0 ) {

			foreach ($this->data['Bodega'] as $ib => $b) {

				# sumar a bodega
				if ( strtoupper($b['io']) === 'IN' ) {

					$sku = (isset($this->data['VentaDetalleProducto']['codigo_proveedor'])) ? $this->data['VentaDetalleProducto']['codigo_proveedor'] : ClassRegistry::init('VentaDetalleProducto')->field('codigo_proveedor', array('id' => $this->data['VentaDetalleProducto']['id']));

					if (!isset($b['valor'])) {
						$valor = (isset( $this->data['VentaDetalleProducto']['precio_costo'])) ?  $this->data['VentaDetalleProducto']['precio_costo'] : ClassRegistry::init('VentaDetalleProducto')->field('precio_costo', array('id' => $this->data['VentaDetalleProducto']['id']));	
					}else{
						$valor = $b['valor'];
					}

					# armamos la entrada
					$this->data['Bodega'][$ib] = array_replace_recursive($this->data['Bodega'][$ib], array(
						'bodega' => ClassRegistry::init('Bodega')->field('nombre', array('id' => $b['bodega_id'])),
						'sku' => $sku,
						'cantidad' => $b['cantidad'],
						'valor' => (float) $valor, // Precio costo
						'total' => (float) $valor * $b['cantidad'],
						'fecha' => (isset($b['fecha'])) ? $b['fecha'] : date('Y-m-d H:i:s')
					));
				}

				# Quitar de bodega
				if ( strtoupper($b['io']) === 'ED') {

					$sku = (isset($this->data['VentaDetalleProducto']['codigo_proveedor'])) ? $this->data['VentaDetalleProducto']['codigo_proveedor'] : ClassRegistry::init('VentaDetalleProducto')->field('codigo_proveedor', array('id' => $this->data['VentaDetalleProducto']['id']));
					
					if (!isset($b['valor'])) {
						$valor = ClassRegistry::init('Bodega')->obtener_pmp_por_id($this->data['VentaDetalleProducto']['id']);
					}else{
						$valor = $b['valor'];
					}
					# armamos la salida
					$this->data['Bodega'][$ib] = array_replace_recursive($this->data['Bodega'][$ib], array(
						'bodega' => ClassRegistry::init('Bodega')->field('nombre', array('id' => $b['bodega_id'])),
						'sku' => $sku,
						'cantidad' => $b['cantidad'],
						'valor' => (float) $valor, // Precio costo
						'total' => (float) $valor * $b['cantidad'],
						'fecha' => (isset($b['fecha'])) ? $b['fecha'] : date('Y-m-d H:i:s')
					));

				}
			}
		}
		#prx($this->data);*/
		return true;
	}


	

}
