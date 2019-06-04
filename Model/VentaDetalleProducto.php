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


	public function obtener_cantidad_reservada($id)
	{

		$vendidos = ClassRegistry::init('VentaDetalle')->find('all', array(
			'conditions' => array(
				'VentaDetalle.venta_detalle_producto_id' => $id,
				'VentaDetalle.completo' => 0,
				'VentaDetalle.created >' => '2019-05-28 18:00:00' 
			),
			'fields' => array(
				'VentaDetalle.cantidad_pendiente_entrega', 'VentaDetalle.cantidad_entregada', 'VentaDetalle.cantidad', 'VentaDetalle.cantidad_reservada'
			)
		));

		if (empty($vendidos)) {
			return 0;
		}

		$total = 0;

		foreach ($vendidos as $iv => $vendido) {

			if ($vendido['VentaDetalle']['cantidad_reservada'] == 0)
				continue;

			$total = $total + ( $vendido['VentaDetalle']['cantidad_reservada'] - $vendido['VentaDetalle']['cantidad_entregada'] );
		}

		return $total;

	}

	public static function obtener_descuento_por_producto($producto = array(), $indice = false)
	{	
		$respuesta = array();

		if ($indice) {
			$precio_lista = $producto['VentaDetalleProducto']['precio_costo'];	
		}else{
			$precio_lista = $producto['precio_costo'];
		}

		$descuentosMarcaCompuestos  = Hash::extract($producto, 'Marca.PrecioEspecificoMarca.{n}[descuento_compuesto=1]');
		$descuentosMarcaEspecificos = Hash::extract($producto, 'Marca.PrecioEspecificoMarca.{n}[descuento_compuesto=0]');
		$descuentosMarca 			= Hash::extract($producto, 'Marca.PrecioEspecificoMarca.{n}');

		$descuentosProductoCompuestos   = Hash::extract($producto, 'PrecioEspecificoProducto.{n}[descuento_compuesto=1]');
		$descuentosProductosEspecificos = Hash::extract($producto, 'PrecioEspecificoProducto.{n}[descuento_compuesto=0]');
		$descuentosProducto             = Hash::extract($producto, 'PrecioEspecificoProducto.{n}');

		$descuentosCompuestos = Hash::format(array_merge($descuentosMarcaCompuestos, $descuentosProductoCompuestos), array('{n}.descuento'), '%1d');
		$descCompuesto        = 0;

		$respuesta['total_descuento']  = 0;
		$respuesta['nombre_descuento'] = '';
		$respuesta['valor_descuento']  = 0;

		# Descuento marca
		if ( !empty($descuentosMarca) ) {

			if ($descuentosMarca[0]['descuento_compuesto']) {

				$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $producto['Marca']['descuento_base']);
				$respuesta['total_descuento']  = $precio_lista * $descCompuesto;
				$respuesta['nombre_descuento'] = 'Compuestos (%): ' . ($descCompuesto*100);
				$respuesta['valor_descuento'] = $descCompuesto;

			}else{

				if ($producto['Marca']['PrecioEspecificoMarca'][0]['tipo_descuento']) {
					$respuesta['total_descuento'] = $precio_lista * ($producto['Marca']['PrecioEspecificoMarca'][0]['descuento'] / 100); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['Marca']['PrecioEspecificoMarca'][0]['nombre'] . ': % ' . $producto['Marca']['PrecioEspecificoMarca'][0]['descuento'];	
					$respuesta['valor_descuento'] = $producto['Marca']['PrecioEspecificoMarca'][0]['descuento'];

				}else{
					$respuesta['total_descuento'] = $producto['Marca']['PrecioEspecificoMarca'][0]['descuento']; // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['Marca']['PrecioEspecificoMarca'][0]['nombre'] . ': $ ' . CakeNumber::currency($producto['Marca']['PrecioEspecificoMarca'][0]['descuento'] , 'CLP');
					$respuesta['valor_descuento'] = $producto['Marca']['PrecioEspecificoMarca'][0]['descuento'];
				}

			}

		}

		# Descuento producto
		if ( !empty($descuentosProducto) ) {

			if ($descuentosProducto[0]['descuento_compuesto']) {

				if ($descCompuesto > 0) {
					$respuesta['total_descuento']  = $precio_lista * $descCompuesto;	
				}else{

					$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $producto['Marca']['descuento_base']);
					
					$respuesta['total_descuento']  = $precio_lista * $descCompuesto;
				}

				$respuesta['nombre_descuento'] = 'Compuestos (%): ' . ($descCompuesto*100);
				$respuesta['valor_descuento'] = $descCompuesto;

			}else{

				if ($producto['PrecioEspecificoProducto'][0]['tipo_descuento']) {
					$respuesta['total_descuento'] = $precio_lista * ($producto['PrecioEspecificoProducto'][0]['descuento'] / 100); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['PrecioEspecificoProducto'][0]['nombre'] . ': % ' . $producto['PrecioEspecificoProducto'][0]['descuento'];
					$respuesta['valor_descuento'] = $producto['PrecioEspecificoProducto'][0]['descuento'];
				}else{
					$respuesta['total_descuento'] = $producto['PrecioEspecificoProducto'][0]['descuento']; // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['PrecioEspecificoProducto'][0]['nombre'] . ': $ ' . CakeNumber::currency($producto['PrecioEspecificoProducto'][0]['descuento'] , 'CLP');
					$respuesta['valor_descuento'] = $producto['PrecioEspecificoProducto'][0]['descuento'];
				}

			}

		}

		if (empty($descuentosMarca) && empty($descuentosProducto) && isset($producto['Marca']['descuento_base'])) {
			$respuesta['total_descuento'] = $precio_lista * ($producto['Marca']['descuento_base'] / 100); // Primer descuento
			$respuesta['nombre_descuento'] = 'Descuento base marca' . ': % ' . $producto['Marca']['descuento_base'];
			$respuesta['valor_descuento'] = $producto['Marca']['descuento_base'];
		}

		

		return $respuesta;
	}


	public function obtener_precio_costo($id)
	{
		$producto = $this->find('first', array(
			'conditions' => array(
				'VentaDetalleProducto.id' => $id
			),
			'contain' => array(
				'PrecioEspecificoProducto' => array(
					'conditions' => array(
						'OR' => array(
							'PrecioEspecificoProducto.descuento_infinito' => 1,
							'AND' => array(
								array('PrecioEspecificoProducto.fecha_inicio <=' => date('Y-m-d')),
								array('PrecioEspecificoProducto.fecha_termino >=' => date('Y-m-d')),
							)
						)
					),
					'order' => array(
						'PrecioEspecificoProducto.id' => 'DESC'
					)
				),
				'Marca' => array(
					'PrecioEspecificoMarca' => array(
						'conditions' => array(
							'OR' => array(
								'PrecioEspecificoMarca.descuento_infinito' => 1,
								'AND' => array(
									array('PrecioEspecificoMarca.fecha_inicio <=' => date('Y-m-d')),
									array('PrecioEspecificoMarca.fecha_termino >=' => date('Y-m-d')),
								)
							)
						),
						'order' => array(
							'PrecioEspecificoMarca.id' => 'DESC'
						)
					)
				)
			)
		));

		$descuentos = self::obtener_descuento_por_producto($producto, true);

		return $producto['VentaDetalleProducto']['precio_costo'] - $descuentos['total_descuento'];
	}

}
