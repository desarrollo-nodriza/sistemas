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
		),
		'Mensaje' => array(
			'className'				=> 'Mensaje',
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
		'Pmp' => array(
			'className'				=> 'Pmp',
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
		'EmbalajeProductoWarehouse' => array(
			'className'				=> 'EmbalajeProductoWarehouse',
			'foreignKey'			=> 'producto_id',
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
		),
		'Prospecto' => array(
			'className'				=> 'Prospecto',
			'joinTable'				=> 'productos_prospectos',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'associationForeignKey'	=> 'prospecto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ProductosProspecto',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Cotizacion' => array(
			'className'				=> 'Cotizacion',
			'joinTable'				=> 'cotizaciones_productos',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'associationForeignKey'	=> 'cotizacion_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'CotizacionesProducto',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);


		
	/**
	 * beforeSave
	 *
	 * @param  mixed $options
	 * @return void
	 */
	public function beforeSave($options = array())
	{
		# Guardamos en el otro modelo espejo
		$campos = array_keys(ClassRegistry::init('ProductoWarehouse')->schema());
		
		$productoWarehouse = array();

		foreach ($campos as $col)
		{	
			if (isset($this->data['VentaDetalleProducto'][$col]))
			{
				$productoWarehouse = array_replace_recursive($productoWarehouse, array(
					$col => $this->data['VentaDetalleProducto'][$col]
				));
			}
		}

		# Guardamos
		if (!empty($productoWarehouse))
		{	
			/*ClassRegistry::init('ProductoWarehouse')->save(
				array(
					'ProductoWarehouse' => $productoWarehouse
				)
			);*/
		}

	}

	
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		
		if (empty($conditions))
		{
			return $this->find('count');
		}
		else
		{
			return $this->find('count', array(
				'conditions' => $conditions
			));
		}
		
	}


	public function obtener_producto_por_id($id = null)
	{
		return $this->find('first', array(
			'conditions' => array(
				'VentaDetalleProducto.id' => $id
			),
			'contain' => array(
				'PrecioEspecificoProducto' => array(
					'conditions' => array(
						'PrecioEspecificoProducto.activo' => 1,
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
							'PrecioEspecificoMarca.activo' => 1,
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
	}


	/**
	 * [obtener_cantidad_reservada description]
	 * @param  [type] $id       [description]
	 * @param  [type] $id_venta [description]
	 * @return [type]           [description]
	 */
	public function obtener_cantidad_reservada($id, $id_venta = null)
	{

		$qry = array(
			'conditions' => array(
				'VentaDetalle.venta_detalle_producto_id' => $id,
				'VentaDetalle.completo' => 0,
				'VentaDetalle.created >' => '2019-05-28 18:00:00' 
			),
			'joins'      => array(
				array(
					'table' => 'rp_ventas',
					'alias' => 'Venta',
					'type' => 'INNER',
					'conditions' => array(
						'Venta.id = VentaDetalle.venta_id'
					)
				),
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'VentaEstado',
					'type' => 'INNER',
					'conditions' => array(
						'VentaEstado.id = Venta.venta_estado_id'
					)
				),
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'VentaEstadoCategoria',
					'type' => 'INNER',
					'conditions' => array(
						'VentaEstadoCategoria.id = VentaEstado.venta_estado_categoria_id',
						'VentaEstadoCategoria.venta = 1'
					)
				)
			),
			'fields' => array(
				'VentaDetalle.cantidad_pendiente_entrega', 'VentaDetalle.cantidad_entregada', 'VentaDetalle.cantidad', 'VentaDetalle.cantidad_reservada', 'VentaDetalle.venta_id', 'VentaDetalle.id'
			)
		);

		if (!empty($id_venta)) {
			$qry = array_replace_recursive($qry, array('conditions' => array(
				'VentaDetalle.venta_id' => $id_venta
			)));
		}
		
		$vendidos = ClassRegistry::init('VentaDetalle')->find('all', $qry);
		
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

	/**
	 * [obtener_descuento_por_producto description]
	 * @param  array   $producto [description]
	 * @param  boolean $indice   [description]
	 * @return [type]            [description]
	 */
	public static function obtener_descuento_por_producto($producto = array(), $indice = false)
	{	
		$respuesta = array();

		if ($indice) {
			$precio_lista = $producto['VentaDetalleProducto']['precio_costo'];	
		}else{
			$precio_lista = $producto['precio_costo'];
		}

		$descuentosMarcaCompuestos  = Hash::extract($producto, 'Marca.PrecioEspecificoMarca.{n}[descuento_compuesto=1]');
		$descuentosMarcaCompuestos  = Hash::extract($descuentosMarcaCompuestos, '{n}[activo=1]');
		$descuentosMarcaEspecificos = Hash::extract($producto, 'Marca.PrecioEspecificoMarca.{n}[descuento_compuesto=0]');
		$descuentosMarcaEspecificos = Hash::extract($descuentosMarcaEspecificos, '{n}[activo=1]');
		$descuentosMarca 			= Hash::extract($producto, 'Marca.PrecioEspecificoMarca.{n}[activo=1]');

		$descuentosProductoCompuestos   = Hash::extract($producto, 'PrecioEspecificoProducto.{n}[descuento_compuesto=1]');
		$descuentosProductoCompuestos   = Hash::extract($descuentosProductoCompuestos, '{n}[activo=1]');
		$descuentosProductosEspecificos = Hash::extract($producto, 'PrecioEspecificoProducto.{n}[descuento_compuesto=0]');
		$descuentosProductosEspecificos = Hash::extract($descuentosProductosEspecificos, '{n}[activo=1]');
		$descuentosProducto             = Hash::extract($producto, 'PrecioEspecificoProducto.{n}[activo=1]');
		
		$descuentosCompuestos = Hash::format(array_merge($descuentosMarcaCompuestos, $descuentosProductoCompuestos), array('{n}.descuento'), '%1d');
		$descCompuesto        = 0;

		$respuesta['total_descuento']  = 0;
		$respuesta['nombre_descuento'] = '';
		$respuesta['valor_descuento']  = 0;

		# Descuento marca
		if ( !empty($descuentosMarca) ) {

			if ($descuentosMarca[0]['descuento_compuesto']) {

				$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $producto['Marca']['descuento_base']);
				$respuesta['total_descuento']  = round($precio_lista * $descCompuesto);
				$respuesta['nombre_descuento'] = 'Compuestos (%): ' . ($descCompuesto*100);
				$respuesta['valor_descuento'] = $descCompuesto;

			}else{

				if ($producto['Marca']['PrecioEspecificoMarca'][0]['tipo_descuento']) {
					$respuesta['total_descuento'] = round($precio_lista * ($producto['Marca']['PrecioEspecificoMarca'][0]['descuento'] / 100)); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['Marca']['PrecioEspecificoMarca'][0]['nombre'] . ': % ' . $producto['Marca']['PrecioEspecificoMarca'][0]['descuento'];	
					$respuesta['valor_descuento'] = $producto['Marca']['PrecioEspecificoMarca'][0]['descuento'];

				}else{
					$respuesta['total_descuento'] = round($producto['Marca']['PrecioEspecificoMarca'][0]['descuento']); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['Marca']['PrecioEspecificoMarca'][0]['nombre'] . ': $ ' . CakeNumber::currency($producto['Marca']['PrecioEspecificoMarca'][0]['descuento'] , 'CLP');
					$respuesta['valor_descuento'] = $producto['Marca']['PrecioEspecificoMarca'][0]['descuento'];
				}

			}

		}
	
		# Descuento producto
		if ( !empty($descuentosProducto) ) {

			if ($descuentosProducto[0]['descuento_compuesto']) {
				
				if ($descCompuesto > 0) {
					$respuesta['total_descuento']  = round($precio_lista * $descCompuesto);	
				}else{
					
					$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $producto['Marca']['descuento_base']);
					
					$respuesta['total_descuento']  = round($precio_lista * $descCompuesto);
				}

				$respuesta['nombre_descuento'] = 'Compuestos (%): ' . ($descCompuesto*100);
				$respuesta['valor_descuento'] = $descCompuesto;

			}else{

				if ($producto['PrecioEspecificoProducto'][0]['tipo_descuento']) {
					$respuesta['total_descuento'] = round($precio_lista * ($producto['PrecioEspecificoProducto'][0]['descuento'] / 100)); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['PrecioEspecificoProducto'][0]['nombre'] . ': % ' . $producto['PrecioEspecificoProducto'][0]['descuento'];
					$respuesta['valor_descuento'] = $producto['PrecioEspecificoProducto'][0]['descuento'];
				}else{
					$respuesta['total_descuento'] = round($producto['PrecioEspecificoProducto'][0]['descuento']); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['PrecioEspecificoProducto'][0]['nombre'] . ': ' . CakeNumber::currency($producto['PrecioEspecificoProducto'][0]['descuento'] , 'CLP');
					$respuesta['valor_descuento'] = $producto['PrecioEspecificoProducto'][0]['descuento'];
				}

			}

		}

		if (empty($descuentosMarca) && empty($descuentosProducto) && isset($producto['Marca']['descuento_base'])) {
			$respuesta['total_descuento'] = round($precio_lista * ($producto['Marca']['descuento_base'] / 100)); // Primer descuento
			$respuesta['nombre_descuento'] = 'Descuento base marca' . ': % ' . $producto['Marca']['descuento_base'];
			$respuesta['valor_descuento'] = $producto['Marca']['descuento_base'];
		}

		

		return $respuesta;
	}


	/**
	 * [obtener_descuento_por_producto_id description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function obtener_descuento_por_producto_id($id)
	{
		$producto = $this->find('first', array(
			'conditions' => array(
				'VentaDetalleProducto.id' => $id
			),
			'contain' => array(
				'Marca' => array(
					'PrecioEspecificoMarca'
				),
				'PrecioEspecificoProducto'
			)
		));

		if (empty($producto))
			return array();

		return self::obtener_descuento_por_producto($producto, true);
	}


	/**
	 * [obtener_precio_costo description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function obtener_precio_costo($id)
	{
		$producto = $this->find('first', array(
			'conditions' => array(
				'VentaDetalleProducto.id' => $id
			),
			'contain' => array(
				'PrecioEspecificoProducto' => array(
					'conditions' => array(
						'PrecioEspecificoProducto.activo' => 1,
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
							'PrecioEspecificoMarca.activo' => 1,
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


	/**
	 * [obtener_codigo_unico description]
	 * @param  string $prefx [description]
	 * @return [type]      [description]
	 */
	public function obtener_codigo_unico($prefx = '')
	{
		return uniqid($prefx, true);
	}


	/**
	 * Modifica las cnatidades vituales de un item
	 * @param  [type] $id    [description]
	 * @param  [type] $stock [description]
	 * @param  string $tipo  descontar|aumentar
	 * @return bool
	 */
	public function actualizar_stock_virtual($id, $stock, $tipo = 'descontar')
	{		
		$this->id = $id;
		if ($tipo == 'descontar') {
			$nwStock = ($this->field('cantidad_virtual') - $stock);
		}else if ($tipo == 'aumentar') {
			$nwStock = ($this->field('cantidad_virtual') + $stock);
		}else{
			return false;
		}

		if ($nwStock < 0) {
			$nwStock = 0;
		}

		return $this->saveField('cantidad_virtual', $nwStock);
	}


	public function obtener_cantidad_vendida($id)
	{
		$ventas = ClassRegistry::init('VentaDetalle')->find('all', array(
			'conditions' => array(
				'VentaDetalle.venta_detalle_producto_id' => $id
			),
			'fields' => array(
				'VentaDetalle.cantidad_entregada'
			)
		));	

		return array_sum(Hash::extract($ventas, '{n}.VentaDetalle.cantidad_entregada'));
	}


	public function obtener_tiempo_entrega($id)
	{	
		$producto = $this->find('first', array(
			'conditions' => array(
				'VentaDetalleProducto.id' => $id
			),
			'contain' => array(
				'OrdenCompra' => array(
					'conditions' => array(
						'OrdenCompra.estado' => 'recibido'
					)
				),
				'VentaDetalle',
				'Marca'
			)
		));

		$ventas = ClassRegistry::init('Venta')->find('all', array(
			'conditions' => array(
				'Venta.id' => array_unique(Hash::extract($producto['VentaDetalle'], '{n}.venta_id')),
				'Venta.fecha_entregado !=' => '' 
			)
		));


		$avg = array();
		foreach ($ventas as $key => $value) {
			$f_creacion  = date_create($value['Venta']['fecha_venta']);
			$f_recepcion = date_create($value['Venta']['fecha_entregado']);

			$diferencia1 = date_diff($f_creacion, $f_recepcion);

			$avg[$key]['creado_recibido']['dias']   = $diferencia1->days;
			$avg[$key]['creado_recibido']['horas']  = $diferencia1->h;
		}
		
		if (count($avg) > 0) {

			$promedio_creado_recibido  = (array_sum(Hash::extract($avg, '{n}.creado_recibido.dias')) / count($avg));
			
			# Si el tiempo de entrega calcuado es mayor al tiempo de la marca se mantiene el de la marca.
			if(!empty($producto['Marca']['tiempo_entrega_maximo']) && $promedio_creado_recibido > $producto['Marca']['tiempo_entrega_maximo']){
				$promedio_creado_recibido = $producto['Marca']['tiempo_entrega_maximo'];
			}
		
		}else{
			$promedio_creado_recibido = $producto['Marca']['tiempo_entrega_maximo'];
		}

		return ceil($promedio_creado_recibido);

	}


	/**
	 * Vrifica que el producto que se intenta preparar esté en la bodega principal
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function permitir_preparacion($id, $cantidad)
	{	

		$disponible_en_bodega_principal = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($id, null, true);
		
		if ( $disponible_en_bodega_principal >= $cantidad) {
			return true;
		}

		return false;
	}
}
