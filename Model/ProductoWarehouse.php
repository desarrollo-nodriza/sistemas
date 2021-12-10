<?php
App::uses('AppModel', 'Model');

class ProductoWarehouse extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $useDbConfig = 'warehouse';
	public $useTable = 'productos';
	public $displayField	= 'nombre';

	
	/**
	 * beforeSave
	 *
	 * @param  mixed $options
	 * @return void
	 */
	public function beforeSave($options = array())
	{
		if (isset($this->data['ProductoWarehouse']['nombre']))
		{
			$this->data['ProductoWarehouse']['nombre_corto'] = Inflector::slug($this->data['ProductoWarehouse']['nombre'], '-');
		}

		return true;
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


		
	/**
	 * sincronizar
	 *
	 * @param  mixed $data
	 * @return void
	 */
	public function sincronizar($data)
	{
		
	}


	public function obtener_producto_por_id($id = null)
	{
		return $this->find('first', array(
			'conditions' => array(
				'ProductoWarehouse.id' => $id
			)
		));
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


	
	/**
	 * obtener_tiempo_entrega
	 * 
	 * Calcula el tiempo de entrega aproximado del producto
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function obtener_tiempo_entrega($id)
	{	
		$producto = $this->find('first', array(
			'conditions' => array(
				'Producto.id' => $id
			),
			'contain' => array(
				'OrdenCompra' => array(
					'conditions' => array(
						'OrdenCompra.estado' => 'recibido'
					)
				),
				'VentaDetalle',
				'ProveedorWarehouse'
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
		
        # Tomamos el proveedor con el menor tiempo de despacho para realizar el cálculo
        $minimo_entrega = sort(Hash::extract($producto['ProveedorWarehouse'], '{n}.tiempo_entrega_maximo'));

		if (count($avg) > 0) {

			$promedio_creado_recibido  = (array_sum(Hash::extract($avg, '{n}.creado_recibido.dias')) / count($avg));

			# Si el tiempo de entrega calcuado es mayor al tiempo de la marca se mantiene el de la marca.
			if(!empty($minimo_entrega) && $promedio_creado_recibido > $minimo_entrega){
				$promedio_creado_recibido = $minimo_entrega;
			}
		
		}else{
			$promedio_creado_recibido = $minimo_entrega;
		}

		return ceil($promedio_creado_recibido);

	}


	/**
	 * [obtener_descuento_por_producto description]
	 * @param  array   $producto [description]
	 * @param  boolean $indice   [description]
	 * @return [type]            [description]
	 */
	public static function obtener_descuento_por_producto($producto = array())
	{	
		$respuesta = array();

		# Separamos los descuentos compuestos de marcas
		$descuentosMarcaCompuestos  = Hash::extract($producto, 'MarcaWarehouse.PrecioEspecificoMarcaWarehouse.{n}[categoria_descuento=compuesto]');

		# Todos los descuentos de marcas
		$descuentosMarca = Hash::extract($producto, 'MarcaWarehouse.PrecioEspecificoMarcaWarehouse.{n}');

		# Separamos los desucnetos compuestos de producto
		$descuentosProductoCompuestos   = Hash::extract($producto, 'PrecioEspecificoProductoWarehouse.{n}[categoria_descuento=compuesto]');

		# Todos los descuentos de producto
		$descuentosProducto = Hash::extract($producto, 'PrecioEspecificoProductoWarehouse.{n}');
		
		# Fusionamos todos los descuentos compuestos
		$descuentosCompuestos = Hash::format(array_merge($descuentosMarcaCompuestos, $descuentosProductoCompuestos), array('{n}.descuento'), '%1d');
		$descCompuesto        = 0;

		$respuesta['total_descuento']  = 0;
		$respuesta['nombre_descuento'] = '';
		$respuesta['valor_descuento']  = 0;

		# Descuento marca
		if ( !empty($descuentosMarca) ) 
		{
			# Existe descuento compuesto de marca
			if (!empty($descuentosMarcaCompuestos)) 
			{	
				# Calculamos en segun descuento base de la marca
				$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $producto['MarcaWarehouse']['descuento_base']);

				$respuesta['total_descuento']  = round($producto['ProveedorWarehouse'][0]['ProveedoresProducto']['precio_lista'] * $descCompuesto);
				$respuesta['nombre_descuento'] = 'Compuestos (%): ' . ($descCompuesto*100);
				$respuesta['valor_descuento'] = $descCompuesto;

			}
			else
			{
				# Descuento no compuesto de tipo $
				if ($producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['tipo_descuento'] == '$') 
				{	
					# Se retorna el descuento más reciente
					$respuesta['total_descuento'] = round($producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['descuento']); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['nombre'] . ': $ ' . CakeNumber::currency($producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['descuento'] , 'CLP');
					$respuesta['valor_descuento'] = $producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['descuento'];
				}
				else
				{	
					# Descuento de tipo %	
					# Se retorna el descuento más reciente
					$respuesta['total_descuento'] = obtener_descuento_monto($producto['ProveedorWarehouse'][0]['ProveedoresProducto']['precio_lista'], $producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['descuento']); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['nombre'] . ': % ' . $producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['descuento'];	
					$respuesta['valor_descuento'] = $producto['MarcaWarehouse']['PrecioEspecificoMarcaWarehouse'][0]['descuento'];
				}

			}

		}
		
		# Descuento producto
		if ( !empty($descuentosProducto) ) 
		{
			# Descuentos compuestos
			if (!empty($descuentosProductoCompuestos)) 
			{
				# Ya viene calculado el descuento compuesto en conjunto con las marcas
				if ($descCompuesto > 0) 
				{
					$respuesta['total_descuento']  = round($producto['ProveedorWarehouse'][0]['ProveedoresProducto']['precio_lista'] * $descCompuesto);	
				}
				else
				{
					# Calculamos el descuento compuesto de los productos
					$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $producto['MarcaWarehouse']['descuento_base']);
					
					$respuesta['total_descuento']  = round($producto['ProveedorWarehouse'][0]['ProveedoresProducto']['precio_lista'] * $descCompuesto);

				}

				$respuesta['nombre_descuento'] = 'Compuestos (%): ' . ($descCompuesto*100);
				$respuesta['valor_descuento'] = $descCompuesto;

			}
			else
			{	
				# Descuento no compuesto de tipo $
				if ($producto['PrecioEspecificoProductoWarehouse'][0]['tipo_descuento'] == '$') 
				{	
					# Se retorna el descuento más reciente
					$respuesta['total_descuento'] = round($producto['PrecioEspecificoProductoWarehouse'][0]['descuento']); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['PrecioEspecificoProductoWarehouse'][0]['nombre'] . ': ' . CakeNumber::currency($producto['PrecioEspecificoProductoWarehouse'][0]['descuento'] , 'CLP');
					$respuesta['valor_descuento'] = $producto['PrecioEspecificoProductoWarehouse'][0]['descuento'];
				}
				else
				{	
					# Descuento de tipo %
					# Se retorna el descuento más reciente
					$respuesta['total_descuento'] = obtener_descuento_monto($producto['ProveedorWarehouse'][0]['ProveedoresProducto']['precio_lista'], $producto['PrecioEspecificoProductoWarehouse'][0]['descuento']); // Primer descuento
					$respuesta['nombre_descuento'] = 'Descuento ' . $producto['PrecioEspecificoProductoWarehouse'][0]['nombre'] . ': % ' . $producto['PrecioEspecificoProductoWarehouse'][0]['descuento'];
					$respuesta['valor_descuento'] = $producto['PrecioEspecificoProductoWarehouse'][0]['descuento'];
				}

			}

		}

		# si no existen descuentos de marca ni producto, se retorna el descuento base de la marca si corresponde
		if (empty($descuentosMarca) && empty($descuentosProducto) && isset($producto['MarcaWarehouse']['descuento_base'])) 
		{	
			# Se retorna el descuento más reciente
			$respuesta['total_descuento'] = obtener_descuento_monto($producto['ProveedorWarehouse'][0]['ProveedoresProducto']['precio_lista'], $producto['MarcaWarehouse']['descuento_base']); // Primer descuento
			$respuesta['nombre_descuento'] = 'Descuento base marca' . ': % ' . $producto['MarcaWarehouse']['descuento_base'];
			$respuesta['valor_descuento'] = $producto['MarcaWarehouse']['descuento_base'];
		}

		return $respuesta;
	
	}


		
	/**
	 * obtener_descuento_por_producto_id
	 *
	 * @param  mixed $producto_id
	 * @param  mixed $proveedor_id
	 * @return void
	 */
	public function obtener_descuento_por_producto_id($producto_id, $proveedor_id)
	{
		$producto = $this->find('first', array(
			'conditions' => array(
				'ProductoWarehouse.id' => $producto_id
			),
			'contain' => array(
				'MarcaWarehouse' => array(
					'PrecioEspecificoMarcaWarehouse' => array(
						'ProveedorWarehouse',
						'conditions' => array(
							'PrecioEspecificoMarcaWarehouse.estado' => 1,
							'PrecioEspecificoMarcaWarehouse.proveedor_id' => $proveedor_id,
							'OR' => array(
								array(
									array('PrecioEspecificoMarcaWarehouse.fecha_inicio <=' => date('Y-m-d')),
									array('PrecioEspecificoMarcaWarehouse.fecha_final >=' => date('Y-m-d'))
								),
								array(
									'PrecioEspecificoMarcaWarehouse.infinito' => 1
								)
							)
						),
						'order' => array(
							'PrecioEspecificoMarcaWarehouse.id' => 'DESC'
						)
					)
				),
				'PrecioEspecificoProductoWarehouse' => array(
					'ProveedorWarehouse',
					'conditions' => array(
						'PrecioEspecificoProductoWarehouse.estado' => 1,
						'PrecioEspecificoProductoWarehouse.proveedor_id' => $proveedor_id,
						'OR' => array(
							array(
								array('PrecioEspecificoProductoWarehouse.fecha_inicio <=' => date('Y-m-d')),
								array('PrecioEspecificoProductoWarehouse.fecha_final >=' => date('Y-m-d'))
							),
							array(
								'PrecioEspecificoProductoWarehouse.infinito' => 1
							)
						)
					),
					'order' => array(
						'PrecioEspecificoProductoWarehouse.id' => 'DESC'
					)
				),
				'ProveedorWarehouse'
			),
			'joins' => array(
				array(
					'table' => 'proveedores_productos',
					'alias' => 'pp',
					'type' => 'INNER',
					'conditions' => array(
						'pp.producto_id' => $producto_id,
						'pp.proveedor_id' => $proveedor_id
					)
				),
			)
		));

		if (empty($producto))
			return array();

		return self::obtener_descuento_por_producto($producto);
	}
}
