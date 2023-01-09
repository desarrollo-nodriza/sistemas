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
		),
		'VentaDetallesReserva' => array(
			'className'				=> 'VentaDetallesReserva',
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
		
	}

	public function afterSave($created, $options = array())
	{
		$item = $this->find('first', array(
			'conditions' => array(
				'id' => $this->data['VentaDetalleProducto']['id']
			)
		));

		$pWarehouse = ClassRegistry::init('ProductoWarehouse')->find('first', array(
			'conditions' => array(
				'id' => $this->data['VentaDetalleProducto']['id']
			)
		));

		$productoWarehouse = array(
			'ProductoWarehouse' => array(
				'id' => $item['VentaDetalleProducto']['id'],
				'marca_id' => $item['VentaDetalleProducto']['marca_id'],
				'nombre' => $item['VentaDetalleProducto']['nombre'],
				'nombre_corto' => strtolower(Inflector::slug($item['VentaDetalleProducto']['nombre'], '-')),
				'cantidad_virtual' => $item['VentaDetalleProducto']['cantidad_virtual'],
				'sku' => $item['VentaDetalleProducto']['codigo_proveedor'],
				'peso' => $item['VentaDetalleProducto']['peso'],
				'alto' => $item['VentaDetalleProducto']['alto'],
				'ancho' => $item['VentaDetalleProducto']['ancho'],
				'largo' => $item['VentaDetalleProducto']['largo'],
				'qr_sec' => $item['VentaDetalleProducto']['qr_sec'],
				'activo' => $item['VentaDetalleProducto']['activo'],
				'cod_barra' => null,
				'fecha_creacion' => $item['VentaDetalleProducto']['created'],
				'ultima_modifacion' => $item['VentaDetalleProducto']['modified']
			)
		);

		# Existe el producto en warehouse
		if ($pWarehouse)
		{	
			# Tiene codigo de barra
			if (!empty($pWarehouse['ProductoWarehouse']['cod_barra']))
			{
				$productoWarehouse['ProductoWarehouse']['cod_barra'] = $pWarehouse['ProductoWarehouse']['cod_barra'];
			}
		}

		# En caso de modificar alguno de estos valores se actualiza o crea
		foreach ($this->data['VentaDetalleProducto'] as $index => $val)
		{	
			if ($index == 'cod_barra')
			{
				$productoWarehouse['ProductoWarehouse']['cod_barra'] = ($val) ? $val : null;
			}

			if ($index == 'permitir_ingreso_sin_barra')
			{
				$productoWarehouse['ProductoWarehouse']['permitir_ingreso_sin_barra'] = $val;
			}
		}
	
		# Guardamos
		if (!empty($productoWarehouse))
		{	
			ClassRegistry::init('ProductoWarehouse')->save($productoWarehouse);
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
	public function obtener_cantidad_reservada($id, $id_venta = null, $id_bodega = null)
	{

		// ! Se cambia la forma de leer las reservas ya que al mandar la bodega esta no siempre conside con la bodega de la venta
		$venta_detalle_ids = [];

		if($id_venta){
			$venta_detalle_ids = ClassRegistry::init('VentaDetalle')->find('all',
			[
				"conditions" => ['VentaDetalle.venta_id' => $id_venta],
				"fields" 	 => ['VentaDetalle.id'],
			]);
		}
		
		$conditions = [
			'VentaDetallesReserva.venta_detalle_producto_id' => $id,
			'VentaDetallesReserva.venta_detalle_id'		 	 => Hash::extract($venta_detalle_ids, '{n}.VentaDetalle.id'),
			'VentaDetallesReserva.bodega_id' 				 => $id_bodega
		];
	
		$vendidos = ClassRegistry::init('VentaDetallesReserva')->find('all', [
			'fields' 	 => ['VentaDetallesReserva.venta_detalle_producto_id','VentaDetallesReserva.bodega_id','cantidad_reservada_total'],
			'conditions' => array_filter($conditions),
			'group'  	 => ['VentaDetallesReserva.venta_detalle_producto_id']
		]);

		// ! Se retornan las reservas desde VentaDetallesReserva y no desde VentaDetalle
		return $vendidos[0]['VentaDetallesReserva']['cantidad_reservada_total'] ?? 0;

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
	 * Retorna un arreglo con los ids de productos con stock disponible para vender.
	 *
	 * @return array
	 */
	public function obtener_productos_con_stock_disponible($bodega_id = null)
	{	

		$qry = array(
			'fields'     => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id',
				'SUM(BodegasVentaDetalleProducto.cantidad) as stock'
			),
			'conditions' => array(
				'BodegasVentaDetalleProducto.tipo !=' => 'GT'
			),
			'group'      => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id'
			),
			'having' 	 => array(
				'SUM(BodegasVentaDetalleProducto.cantidad) > 0'
			),
			'order' => array(
				'stock' => 'asc'
			)
		);

		if (!empty($bodega_id))
		{
			$qry = array_replace_recursive($qry, array(
				'conditions' => array(
					'BodegasVentaDetalleProducto.bodega_id' => $bodega_id
				)
			));

			$qry['fields'][] = 'BodegasVentaDetalleProducto.bodega_id';
		}
		
		$ids_con_stock_fisico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', $qry);

		// $qry2 = array(
		// 	'fields'     => array(
		// 		'VentaDetalle.venta_detalle_producto_id',
		// 		'SUM(VentaDetalle.cantidad_reservada) as reservado'
		// 	),
		// 	'contain' => ['VentaDetallesReserva']
		// 	,
		// 	'joins'      => array(
		// 		array(
		// 			'table' => 'rp_ventas',
		// 			'alias' => 'Venta',
		// 			'type' => 'INNER',
		// 			'conditions' => array(
		// 				'Venta.id = VentaDetalle.venta_id'
		// 			)
		// 		),
		// 		array(
		// 			'table' => 'rp_venta_estados',
		// 			'alias' => 'VentaEstado',
		// 			'type' => 'INNER',
		// 			'conditions' => array(
		// 				'VentaEstado.id = Venta.venta_estado_id'
		// 			)
		// 		),
		// 		array(
		// 			'table' => 'rp_venta_estado_categorias',
		// 			'alias' => 'VentaEstadoCategoria',
		// 			'type' => 'INNER',
		// 			'conditions' => array(
		// 				'VentaEstadoCategoria.id = VentaEstado.venta_estado_categoria_id',
		// 				'VentaEstadoCategoria.reserva_stock = 1'
		// 			)
		// 		)
		// 	),
		// 	
		// 	
		// 	
		// 	'having' => array('SUM(VentaDetalle.cantidad_reservada) > 0'),
		// 	'order'      => array('reservado' => 'asc'),
		// 	'group'      => array('VentaDetalle.venta_detalle_producto_id')
		// );

		// # Agregamos la bodega al calculo de reservas
		// if (!empty($bodega_id))
		// {
		// 	$qry2 = array_replace_recursive($qry2, array(
		// 		'joins' => array(
		// 			array(
		// 				'table' => 'rp_ventas',
		// 				'alias' => 'Venta',
		// 				'type' => 'INNER',
		// 				'conditions' => array(
		// 					'Venta.bodega_id' => $bodega_id
		// 				)
		// 			)
		// 		)
		// 	));

		// 	$qry2['fields'][] = 'Venta.bodega_id';
		// }

		// $ids_con_reserva = ClassRegistry::init('VentaDetalle')->find('all', $qry2);
		$conditions = [
			'VentaDetallesReserva.venta_detalle_producto_id' => Hash::extract($ids_con_stock_fisico, '{n}.BodegasVentaDetalleProducto.venta_detalle_producto_id'),
			'VentaDetallesReserva.bodega_id' => $bodega_id
		];
	
		$ids_con_reserva = ClassRegistry::init('VentaDetallesReserva')->find('all', [
			'fields' 	 => ['VentaDetallesReserva.venta_detalle_producto_id','VentaDetallesReserva.bodega_id','cantidad_reservada_total'],
			'conditions' => array_filter($conditions),
			'having' 	 => ['SUM(VentaDetallesReserva.cantidad_reservada) > 0'],
			'group'  	 => ['VentaDetallesReserva.venta_detalle_producto_id']
		]);
		
		# Preparamos ids para usarlos en la actualización
		$id_stock_disponible = array();
		foreach ($ids_con_stock_fisico as $ids => $s) 
		{	
			$id_stock_disponible[$ids]['id'] = $s['BodegasVentaDetalleProducto']['venta_detalle_producto_id'];
			$id_stock_disponible[$ids]['stock_disponible'] = $s[0]['stock'];
			$id_stock_disponible[$ids]['stock_fisico'] = $s[0]['stock'];
			$id_stock_disponible[$ids]['stock_reservado'] = 0;

			if (!empty($bodega_id))
			{
				$id_stock_disponible[$ids]['bodega_id'] = $s['BodegasVentaDetalleProducto']['bodega_id'];
			}

			$producto_reservado  = array_sum(Hash::extract($ids_con_reserva, "{n}.VentaDetallesReserva[venta_detalle_producto_id={$s['BodegasVentaDetalleProducto']['venta_detalle_producto_id']}].cantidad_reservada_total") );
			$id_stock_disponible[$ids]['stock_disponible'] = ($producto_reservado <= $s[0]['stock'] ) ? $s[0]['stock'] - $producto_reservado : 0;
			$id_stock_disponible[$ids]['stock_reservado'] = $producto_reservado;
			

			// foreach ($ids_con_reserva as $idr => $r) 
			// {	
			// 	if ($s['BodegasVentaDetalleProducto']['venta_detalle_producto_id'] ==  $r['VentaDetalle']['venta_detalle_producto_id'])
			// 	{	
			// 		# Descontamos las unidades reservadas
			// 		$id_stock_disponible[$ids]['stock_disponible'] = ($r[0]['reservado'] <= $s[0]['stock'] ) ? $s[0]['stock'] - $r[0]['reservado'] : 0;
			// 		$id_stock_disponible[$ids]['stock_reservado'] = $r[0]['reservado'];
			// 	}
			// }

			# Quitamos los stock disponibles iguales a 0
			if ($id_stock_disponible[$ids]['stock_disponible'] == 0)
			{
				unset($id_stock_disponible[$ids]);
			}
		}

		return $id_stock_disponible;
	}

	
	/**
	 * obtener_productos_con_stock_disponible_por_bodega
	 *
	 * @return void
	 */
	public function obtener_productos_con_stock_disponible_por_bodega()
	{
		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('activo' => 1)));

		$respuesta =  array();
		foreach ($bodegas as $id => $bodega) 
		{	
			$productos = $this->obtener_productos_con_stock_disponible($id);

			$respuesta[] = array(
				'bodega_id' => $id,
				'bodega_nombre' => $bodega,
				'productos' => $productos
			);
		}

		return $respuesta;
	}


	/**
	 * Obtiene todos los productos que tienen stock disponible por bodegas
	 * 
	 * @return array
	 */
	public function obtener_productos_con_stock_disponible_por_bodegas_v2()
	{
		# Obtenemos todos los productos con stock fisico por bodegas
		$stock_disponible = classRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'fields' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id',
				'BodegasVentaDetalleProducto.bodega_id',
				'SUM(BodegasVentaDetalleProducto.cantidad) as stock_fisico',
				'SUM(BodegasVentaDetalleProducto.cantidad) as stock_disponible'
			),
			'group' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id',
				'BodegasVentaDetalleProducto.bodega_id'
			),
			'conditions' => ['BodegasVentaDetalleProducto.tipo <>' => 'GT'],
			'having' => array(
				'SUM(BodegasVentaDetalleProducto.cantidad) > 0'
			)
		));
		
		if (empty($stock_disponible))
			return array();

		# Separamos los ids
		$id_productos_stock_disponible = array_unique(Hash::extract($stock_disponible, '{n}.BodegasVentaDetalleProducto.venta_detalle_producto_id'));

		# Obtenemos las reservas de los productos con stock fisico por bodegas
		$reservas = classRegistry::init('VentaDetallesReserva')->find('all', array(
			'conditions' => array(
				'VentaDetallesReserva.venta_detalle_producto_id IN' => $id_productos_stock_disponible
			),
			'fields' => array(
				'VentaDetallesReserva.venta_detalle_producto_id',
				'VentaDetallesReserva.bodega_id',
				'SUM(VentaDetallesReserva.cantidad_reservada) as cantidad_reservada'
			),
			'group' => array(
				'VentaDetallesReserva.venta_detalle_producto_id',
				'VentaDetallesReserva.bodega_id'
			),
			'having' => array(
				'SUM(VentaDetallesReserva.cantidad_reservada) > 0'
			)
		));

		# Descontamos las unidades reservadas
		array_walk($stock_disponible, function(&$p) use($reservas) 
		{	
			$p[0]['stock_reservado'] = 0;
			
			foreach ($reservas as $r)
			{	
				if ($r['VentaDetallesReserva']['venta_detalle_producto_id'] == $p['BodegasVentaDetalleProducto']['venta_detalle_producto_id'] 
					&& $r['VentaDetallesReserva']['bodega_id'] == $p['BodegasVentaDetalleProducto']['bodega_id'])
				{
					$p[0]['stock_disponible'] = (int) $p[0]['stock_disponible'] - $r[0]['cantidad_reservada'];
					$p[0]['stock_reservado'] = $r[0]['cantidad_reservada'];
				}
			}

			return $p;

		});
		
		# Separamos los ids
		$id_productos_stock_disponible = array_unique(Hash::extract($stock_disponible, '{n}.BodegasVentaDetalleProducto.venta_detalle_producto_id'));
		
		# Inofrmación de los productos con stock disponible
		$productos = $this->find('all', array(
			'conditions' => array(
				'VentaDetalleProducto.id IN' => $id_productos_stock_disponible 
			)
		));
		
		# Obtenemos las bodegas involucradas una sola vez para reutilizarlas para cada producto
		$bodegas = ClassRegistry::init('Bodega')->find('all', array(
			'conditions' => array(
				'Bodega.id IN' => array_unique(Hash::extract($stock_disponible, '{n}.BodegasVentaDetalleProducto.bodega_id'))
			)
		));

		# Asignamos los stock según bodegas
		$productos = array_map(function($p) use ($bodegas, $stock_disponible)
		{
			foreach($stock_disponible as $sd)
			{	
				if ($sd['BodegasVentaDetalleProducto']['venta_detalle_producto_id'] == $p['VentaDetalleProducto']['id'])
				{	
					$b = Hash::extract($bodegas, '{n}.Bodega[id='.$sd['BodegasVentaDetalleProducto']['bodega_id'].']');

					// * Las bodegas que no tienen stock disponible no se muestran
					 if ($sd[0]['stock_disponible'] < 1 ) 
					 	continue;	

					$p['Disponibilidad']['Bodega'][] = array(
						'venta_detalle_producto_id' => $sd['BodegasVentaDetalleProducto']['venta_detalle_producto_id'],
						'bodega_id' 				=> $sd['BodegasVentaDetalleProducto']['bodega_id'],
						'stock_disponible' 			=> $sd[0]['stock_disponible'],
						'stock_fisico' 				=> $sd[0]['stock_fisico'],
						'stock_reservado' 			=> $sd[0]['stock_reservado'],
						'detalle_bodega' 			=> $b[0]
					);
				}
			}	
			
			# Arreglo general de disponibilidad
			$p['Disponibilidad']['General'] = array(
				'stock_fisico' 		=> array_sum(Hash::extract($p, 'Disponibilidad.Bodega.{n}.stock_fisico')),
				'stock_reservado' 	=> array_sum(Hash::extract($p, 'Disponibilidad.Bodega.{n}.stock_reservado')),
				'stock_disponible' 	=> array_sum(Hash::extract($p, 'Disponibilidad.Bodega.{n}.stock_disponible')),
			);

			# Se agrega información general del producto para uso personalizado
			$p['VentaDetalleProducto']['disponibilidad'] = array(
				'stock_fisico' 		=> $p['Disponibilidad']['General']['stock_fisico'],
				'stock_reservado' 	=> $p['Disponibilidad']['General']['stock_reservado'],
				'stock_disponible' 	=> $p['Disponibilidad']['General']['stock_disponible'],
			);

			return $p;

		}, $productos);

		// * Se Muestran solo los que tienen stock general
		$productos = array_filter($productos, function($v,$k)
		{
			return $v['Disponibilidad']['General']['stock_disponible'] > 0;
		}, ARRAY_FILTER_USE_BOTH);


		return $productos;
	}

	public function disponibilidad_por_bodega()
	{
		return ClassRegistry::init('VentaDetalleProducto')->query("
		SELECT p.id producto_id,
			p.nombre,
			p.peso,
			concat(b.nombre, ' (', b.direccion, ')')                    nombre_bodega,
			(SUM(`BodegasVentaDetalleProducto`.`cantidad`) - ifnull((select SUM(reserva.cantidad_reservada)
																	 from rp_venta_detalles_reservas reserva
																	 where reserva.venta_detalle_producto_id =
																		   `BodegasVentaDetalleProducto`.`venta_detalle_producto_id`
																	   and reserva.bodega_id = `BodegasVentaDetalleProducto`.`bodega_id`
																	 group by reserva.venta_detalle_producto_id),
																	0)) disponibilidad
		 FROM `rp_bodegas_venta_detalle_productos` AS `BodegasVentaDetalleProducto`
				  inner join rp_bodegas b on b.id = bodega_id
				  inner join rp_venta_detalle_productos p on p.id = venta_detalle_producto_id
		 WHERE `BodegasVentaDetalleProducto`.`tipo` <> 'GT'
		 GROUP BY `BodegasVentaDetalleProducto`.`venta_detalle_producto_id`, `BodegasVentaDetalleProducto`.`bodega_id`
		 HAVING disponibilidad > 0
 		");
	}
}
