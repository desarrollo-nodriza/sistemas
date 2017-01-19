<?php
App::uses('Component', 'Controller');
class CarroComponent extends Component
{
	/**
	 * Componentes a utilizar por el carro
	 * @var array
	 */
	public $components			= array('Session');

	/**
	 * Contenedor principal de todos los datos del carro
	 * @var string
	 */
	public $contenedor			= 'Carro';

	/**
	 * Contenedor principal de los catalogos de productos
	 * @var string
	 */
	public $catalogos			= 'Catalogos';

	/**
	 * Catalogo por defecto
	 * @var string
	 */
	public $default				= 'catalogo';

	/**
	 * Key contenedora del precio del producto
	 * @var string|null
	 */
	public $precio				= 'Producto.precio';

	/**
	 * Key contenedora del peso del producto
	 * @var string|null
	 */
	public $peso				= 'Producto.peso';

	/**
	 * Cantidad minima del producto
	 * @var int
	 */
	public $minimo				= 0;


	/**
	 * Agrega un producto al carro
	 *
	 * @param			int				$id					ID del producto
	 * @param			int				$cantidad			Cantidad del producto
	 * @param			array			$data				Datos del producto
	 * @param			array			$meta				Metadatos del producto
	 * @return			array								Resumen del carro post operacion
	 */
	public function agregar($id = null, $cantidad = 1, $catalogo = null, $data = array(), $meta = array())
	{
		if ( ! $id || ( $catalogo && ! is_string($catalogo) ) )
		{
			return false;
		}
		if ( ! $catalogo )
		{
			$catalogo		= $this->default;
		}
		$this->normalizarCantidad($cantidad);

		/**
		 * Actualizar
		 */
		if ( $this->Session->check(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, $catalogo, $id)) )
		{
			$actual		= $this->Session->read(sprintf('%s.%s.%s.Productos.%d.Meta.Cantidad', $this->contenedor, $this->catalogos, $catalogo, $id));
			return $this->actualizar($id, ($actual + $cantidad), $catalogo, $data, $meta);
		}

		/**
		 * Datos a agregar
		 */
		$info			= array(
			'Data'			=> $data,
			'Meta'			=> array_replace($meta, array(
				'Catalogo'			=> $catalogo,
				'Cantidad'			=> $cantidad,
				'Precio'			=> null,
				'Subtotal'			=> null,
				'Peso'				=> null
			))
		);

		/**
		 * Actualiza la informacion de precio si es necesario
		 */
		if ( $this->precio )
		{
			$precio			= (int) Hash::extract($data, $this->precio)[0];
			$info			= array_replace_recursive($info, array(
				'Meta'			=> array(
					'Precio'		=> $precio,
					'Subtotal'		=> ($precio * $cantidad)
				)
			));
		}

		/**
		 * Actualiza la informacion de peso si es necesario
		 */
		if ( $this->peso )
		{
			$peso			= (int) Hash::extract($data, $this->peso)[0];
			$info			= array_replace_recursive($info, array(
				'Meta'			=> array(
					'Peso'			=> $peso
				)
			));
		}

		/**
		 * Agrega el producto al carro
		 */
		$this->Session->write(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, $catalogo, $id), $info);

		/**
		 * Recalcula los subtotales y entrega la informacion de la operacion
		 */
		$this->recalcular();
		return array(
			'Producto'		=> $info['Meta'],
			'Catalogo'		=> $this->Session->read(sprintf('%s.%s.%s.Meta', $this->contenedor, $this->catalogos, $catalogo)),
			'Carro'			=> $this->estado()
		);
	}


	/**
	 * Actualiza un producto del carro
	 *
	 * @param			int				$id					ID del producto
	 * @param			int				$cantidad			Cantidad del producto
	 * @param			array			$data				Datos del producto
	 * @return			boolean								Resultado de la operacion
	 */
	public function actualizar($id = null, $cantidad = 1, $catalogo = null, $data = array(), $meta = array())
	{
		if ( ! $id )
		{
			return false;
		}
		if ( ! $catalogo )
		{
			$catalogo		= $this->default;
		}
		$this->normalizarCantidad($cantidad);

		/**
		 * Agregar
		 */
		if ( ! $this->Session->check(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, $catalogo, $id)) )
		{
			return $this->agregar($id, $cantidad, $catalogo, $data, $meta);
		}

		/**
		 * Actualizar
		 */
		else
		{
			$meta				= $this->Session->read(sprintf('%s.%s.%s.Productos.%d.Meta', $this->contenedor, $this->catalogos, $catalogo, $id));
			$meta['Cantidad']	= $cantidad;
			if ( $meta['Precio'] )
			{
				$meta['Subtotal']		= ($meta['Precio'] * $cantidad);
			}
			$this->Session->write(sprintf('%s.%s.%s.Productos.%d.Meta', $this->contenedor, $this->catalogos, $catalogo, $id), $meta);
		}

		/**
		 * Recalcula los subtotales y entrega la informacion de la operacion
		 */
		$this->recalcular();
		return array(
			'Producto'		=> $meta,
			'Catalogo'		=> $this->Session->read(sprintf('%s.%s.%s.Meta', $this->contenedor, $this->catalogos, $catalogo)),
			'Carro'			=> $this->estado()
		);
	}


	/**
	 * Elimina un producto del carro
	 *
	 * @param			int				$id					ID del producto
	 * @return			array								Resumen del carro post operacion
	 */
	public function eliminar($id = null, $catalogo = null)
	{
		if ( ! $id )
		{
			return false;
		}

		/**
		 * Elimina el producto de un catalogo especifico
		 */
		if ( $catalogo )
		{
			$this->Session->delete(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, $catalogo, $id));
		}

		/**
		 * Elimina el producto de todos los catalogos
		 */
		else
		{
			$catalogos		= $this->Session->read(sprintf('%s.%s', $this->contenedor, $this->catalogos));
			foreach ( $catalogos as $catalogo => $data )
			{
				if ( empty($data['Productos']) )
				{
					foreach ( $data as $subcatalogo => $subdata )
					{
						$this->Session->delete(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, sprintf('%s.%s', $catalogo, $subcatalogo), $id));
					}
				}
				else
				{
					$this->Session->delete(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, $catalogo, $id));

					/**
					 * Elimina el catalogo si ya no tiene productos
					 */
					$productos		= $this->Session->read(sprintf('%s.%s.%s.Productos', $this->contenedor, $this->catalogos, $catalogo));
					if ( empty($productos) )
					{
						$this->Session->delete(sprintf('%s.%s.%s', $this->contenedor, $this->catalogos, $catalogo));
					}
				}
			}
		}

		/**
		 * Recalcula los subtotales y entrega la informacion de la operacion
		 */
		$this->recalcular();
		if ( $catalogo )
		{
			return array(
				'Catalogo'		=> $this->Session->read(sprintf('%s.%s.%s.Meta', $this->contenedor, $this->catalogos, $catalogo)),
				'Carro'			=> $this->estado()
			);
		}

		return array(
			'Carro'			=> $this->estado()
		);
	}


	/**
	 * Devuelve el estado actual del carro, sin productos
	 *
	 * @return			array
	 */
	public function estado()
	{
		return $this->Session->read(sprintf('%s.Meta', $this->contenedor));
	}

	public function catalogos()
	{
		$catalogos		= $this->Session->read(sprintf('%s.%s', $this->contenedor, $this->catalogos));
		$nombres		= array();

		foreach ( $catalogos as $catalogo => $data )
		{
			$nombres[]		= $catalogo;
		}

		return $nombres;
	}

	public function eliminarCatalogo($catalogo = null)
	{
		if ( ! $catalogo )
		{
			return false;
		}

		$this->Session->delete(sprintf('%s.%s.%s', $this->contenedor, $this->catalogos, $catalogo));
		$this->recalcular();
		return array(
			'Carro'			=> $this->estado()
		);
	}


	/**
	 * Retorna la lista de productos agregados al carro
	 *
	 * @param			string|null			$catalogo		Nombre del catalogo, si es null busca todos los catalogos
	 * @return			array								Arreglo con los datos de productos
	 */
	public function productos($catalogo = null)
	{
		/**
		 * Obtiene los productos de un catalogo en especial
		 */
		if ( $catalogo )
		{
			if ( $this->Session->check(sprintf('%s.%s.%s', $this->contenedor, $this->catalogos, $catalogo)) )
			{
				return array($catalogo => $this->Session->read(sprintf('%s.%s.%s', $this->contenedor, $this->catalogos, $catalogo)));
			}
			return null;
		}

		/**
		 * Obtiene los productos de todos los catalogos
		 */
		return $this->Session->read(sprintf('%s.%s', $this->contenedor, $this->catalogos));
	}


	/**
	 * Retorna la informacion de un producto dado en el catalogo espeficiado.
	 * Si no se especifica catalogo, lo busca en todos los catalogos
	 *
	 * @param			string|null			$catalogo		Nombre del catalogo, si es null busca todos los catalogos
	 * @return			array|null							Arreglo con los datos de productos
	 */
	public function producto($id = null, $catalogo = null)
	{
		if ( ! $id )
		{
			return false;
		}

		/**
		 * Obtiene el producto de un catalogo en especifico
		 */
		if ( $catalogo )
		{
			return $this->Session->read(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, $catalogo, $id));
		}

		/**
		 * Busca el producto en todos los catalogos
		 */
		$catalogos		= $this->Session->read(sprintf('%s.%s', $this->contenedor, $this->catalogos));
		if ( $catalogos )
		{
			foreach ( $catalogos as $catalogo => $data )
			{
				if ( empty($data['Productos']) )
				{
					foreach ( $data as $subcatalogo )
					{
						if ( ! empty($subcatalogo['Productos'][$id]) )
						{
							return $this->Session->read(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, $catalogo, $id));
						}
					}
				}
				else
				{
					if ( ! empty($data['Productos'][$id]) )
					{
						return $this->Session->read(sprintf('%s.%s.%s.Productos.%d', $this->contenedor, $this->catalogos, $catalogo, $id));
					}
				}
			}
		}

		return null;
	}


	/**
	 * Vacia el carro
	 *
	 * @return			bool								Resultado de la operacion
	 */
	public function vaciar()
	{
		return $this->Session->write(sprintf('%s', $this->contenedor), array(
			'Meta'			=> array(
				'Cantidad'		=> 0,
				'Subtotal'		=> null,
				'Peso'			=> null,
			)
		));
	}


	/**
	 * Destruye los datos del carro
	 *
	 * @return			bool								Resultado de la operacion
	 */
	public function destruir()
	{
		return $this->Session->delete(sprintf('%s', $this->contenedor));
	}


	/**
	 * Calcula los subtotales por catalogo o subcatalogo
	 *
	 * @param			array				$catalogo		Productos del catalogo
	 * @param			array				$metaCatalogo	Metadatos del catalogo
	 * @param			array				$meta			Metadatos del carro general
	 * @return			array								Retorna los metadatos del catalogo
	 */
	private function recalcularCatalogo($productos, $metaCatalogo, &$meta)
	{
		foreach ( $productos['Productos'] as $producto )
		{
			$metaCatalogo['Cantidad']		+= $producto['Meta']['Cantidad'];
			$meta['Cantidad']				+= $producto['Meta']['Cantidad'];
			if ( $producto['Meta']['Subtotal'] )
			{
				$metaCatalogo['Subtotal']		+= $producto['Meta']['Subtotal'];
				$meta['Subtotal']				+= $producto['Meta']['Subtotal'];
			}
			if ( $producto['Meta']['Peso'] )
			{
				$metaCatalogo['Peso']			+= $producto['Meta']['Cantidad'] * $producto['Meta']['Peso'];
				$meta['Peso']					+= $producto['Meta']['Cantidad'] * $producto['Meta']['Peso'];
			}
		}

		return $metaCatalogo;
	}


	/**
	 * Recalcula el subtotal por catalogo y total final, incluyendo total de productos
	 *
	 * @return			void
	 */
	private function recalcular()
	{
		$catalogos			= $this->Session->read(sprintf('%s.%s', $this->contenedor, $this->catalogos));
		$metaBase			= $metaCatalogo = $meta = array(
			'Cantidad'			=> 0,
			'Subtotal'			=> null,
			'Peso'				=> null
		);

		foreach ( $catalogos as $catalogo => $data )
		{
			/**
			 * Detecta si existe un subcatalogo
			 */
			if ( empty($data['Productos']) )
			{
				foreach ( $data as $subcatalogo => $subdata)
				{
					$this->Session->write(
						sprintf('%s.%s.%s.Meta', $this->contenedor, $this->catalogos, sprintf('%s.%s', $catalogo, $subcatalogo)),
						$this->recalcularCatalogo($subdata, $metaBase, $meta)
					);
				}
			}

			else
			{
				$this->Session->write(
					sprintf('%s.%s.%s.Meta', $this->contenedor, $this->catalogos, $catalogo),
					$this->recalcularCatalogo($data, $metaBase, $meta)
				);
			}
		}
		$this->Session->write(sprintf('%s.Meta', $this->contenedor), $meta);
	}


	/**
	 * Normaliza la cantidad minima para agregar/actualizar un producto
	 *
	 * @return			void
	 */
	private function normalizarCantidad(&$cantidad = 1)
	{
		$cantidad		= (int) abs($cantidad);
		$cantidad		= ($cantidad < $this->minimo ? $this->minimo : $cantidad);
	}
}
