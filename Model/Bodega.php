<?php
App::uses('AppModel', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class Bodega extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

	public $tipoMovimientos = array(
		'MV' => array(
			'IN' => 'Movimiento de bodega: Ingresar', 
			'ED' => 'Movimiento de bodega: Salida',
			'NOMBRE' => 'Movimiento entre bodegas'
		),
		'AJ' => array(
			'IN' => 'Ajuste de inventario: Ingresar', 
			'ED' => 'Ajuste de inventario: Salida',
			'NOMBRE' => 'Ajuste de inventario'
		),
		'II' => array(
			'IN' => 'Inventario inicial: Ingresar',
			'ED' => 'Inventario inicial: Salida', // Jamás se debería usar
			'NOMBRE' => 'Inventario inicial'
		),
		'OC' => array(
			'IN' => 'Ingreso normal por OC',
			'ED' => 'Salida normal',
			'NOMBRE' => 'I/O normal'
		),
		'VT' => array(
			'IN' => 'Ingreso por cancelación o devolución',
			'ED' => 'Salida desde venta',
			'NOMBRE' => 'I/O venta'
		)
	); 


	/**
	 * ASOCIACIONES
	 */
	public $hasAndBelongsToMany = array(
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'joinTable'				=> 'bodegas_venta_detalle_productos',
			'foreignKey'			=> 'bodega_id',
			'associationForeignKey'	=> 'venta_detalle_producto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'width'					=> 'BodegasVentaDetalleProducto',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);


	public function beforeSave($options = array())
	{
		# Bodega principal única
		if (!isset($this->data['Bodega']['principal'])) {
			return true;
		}

		# Quitamos bodegas principales
		if ($this->data['Bodega']['principal']) {
			$bodegas_p = $this->find('list', array(
				'conditions' => array(
					'Bodega.principal' => 1
				)
			));

			if (empty($bodegas_p)) {
				return true;
			}

			$dataToSave = array();
			foreach ($bodegas_p as $id => $nombre) {
				$dataToSave[] = array(
					'Bodega' => array(
						'id' => $id,
						'principal' => 0
					)
				);
			}

			$this->saveMany($dataToSave, array('callbacks' => false));
		}

		return true;
	}


	public function obtener_pmp_por_id($id_producto = null)
	{
		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				#'BodegasVentaDetalleProducto.tipo' => array('OC', 'II')
			)
		));

		$pmp = ClassRegistry::init('VentaDetalleProducto')->obtener_precio_costo($id_producto);
			
		if (!empty($historico)) {
			// Sumatoria de las cantidades
			$inCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].cantidad'));
			$edCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));

			$inTotal  = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].total'));
			$outTotal = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].total'));

			$Q = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.cantidad'));
			$PQ = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.total'));
		
			if ($Q != 0) {
				$pmp = $PQ / $Q;	
			}
		}

		return $pmp;

	} 


	public function obtener_pmp_por_producto_bodega($id_producto = null, $bodega_id = null)
	{
		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				'BodegasVentaDetalleProducto.bodega_id' => $bodega_id,
				#'BodegasVentaDetalleProducto.tipo' => array('OC', 'II')
			)
		));

		$pmp = ClassRegistry::init('VentaDetalleProducto')->obtener_precio_costo($id_producto);

		if (!empty($historico)) {
			// Sumatoria de las cantidades
			$inCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].cantidad'));
			$edCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));

			$inTotal  = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].total'));
			$outTotal = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].total'));

			$Q = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.cantidad'));
			$PQ = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.total'));
		
			if ($Q != 0) {
				$pmp = $PQ / $Q;	
			}
		}

		return $pmp;

	} 


	public function obtenerCantidadProductoBodega($id_producto, $id_bodega = null, $real = false)
	{	
		# Bodega principal
		if (empty($id_bodega)) {
			$id_bodega = ClassRegistry::init('Bodega')->find('first', array('conditions' => array('Bodega.principal' => 1), 'limit' => 1, 'fields' => array('Bodega.id')))['Bodega']['id'];
		}

		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.bodega_id' => $id_bodega,
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto
			)
		));

		$total = 0;

		if (!empty($historico)) {
			// Sumatoria de las cantidades
			$inCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].cantidad'));
			$edCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));

			$total = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.cantidad'));	
		}

		if ($real)
			return $total;

		# Obtenemos la cantidad reservada o vendida no empaquetada
		$reservado = ClassRegistry::init('VentaDetalleProducto')->obtener_cantidad_reservada($id_producto);

		if ($total <= $reservado)
			return 0; // No tenemos stock

		if ($total > $reservado)
			$total = ($total - $reservado); // Descontamos la cantidad reservada

		return $total;
	}


	public function obtenerCantidadProductoBodegas($id_producto, $real = false)
	{
		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto
			)
		));

		$total = 0;

		if (!empty($historico)) {
			// Sumatoria de las cantidades
			$inCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].cantidad'));
			$edCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));

			$total = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.cantidad'));		
		}

		if ($real)
			return $total;

		# Obtenemos la cantidad reservada o vendida no empaquetada
		$reservado = ClassRegistry::init('VentaDetalleProducto')->obtener_cantidad_reservada($id_producto);

		if ($total <= $reservado)
			return 0; // No tenemos stock

		if ($total > $reservado)
			$total = ($total - $reservado); // Descontamos la cantidad reservada

		return $total;
	}


	public function obtener_pmp_por_sku($sku = null)
	{
		$historico = $this->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.sku' => $sku
			)
		));

		// Sumatoria de las cantidades
		$inCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].cantidad'));
	}


	/**
	 * Guarda el ingreso de un producto en una bodega
	 * @param  [type] $id_producto  [description]
	 * @param  [type] $bodega_id    [description]
	 * @param  [type] $cantidad     [description]
	 * @param  [type] $precio_costo [description]
	 * @param  [type] $tipo         [description]
	 * @return [type]               [description]
	 */
	public function crearEntradaBodega($id_producto, $bodega_id = null, $cantidad, $precio_costo, $tipo, $id_oc = null, $id_venta = null)
	{	
		if ($cantidad <= 0) {
			return false;
		}

		# Bodega principal
		if (empty($bodega_id)) {
			$bodega_id = ClassRegistry::init('Bodega')->find('first', array('conditions' => array('Bodega.principal' => 1), 'limit' => 1, 'fields' => array('Bodega.id')))['Bodega']['id'];
		}

		$data = array(
			'BodegasVentaDetalleProducto' => array(
				'bodega_id'                 => $bodega_id,
				'venta_detalle_producto_id' => $id_producto,
				'bodega'					=> $this->field('nombre', array('id' => $bodega_id) ),
				'sku'                       => ClassRegistry::init('VentaDetalleProducto')->field('codigo_proveedor', array('id' => $id_producto) ),
				'cantidad'                  => $cantidad,
				'io'                        => 'IN',
				'tipo' 						=> $tipo,
				'valor'                     => $precio_costo,
				'total'                     => $precio_costo * $cantidad,
				'fecha'                     => date('Y-m-d H:i:s'),
				'responsable'               => CakeSession::read('Auth.Administrador.email'),
				'glosa'						=> $this->tipoMovimientos[$tipo]['IN'],
				'orden_compra_id'			=> $id_oc,
				'venta_id'     				=> $id_venta
			)
		);

		ClassRegistry::init('BodegasVentaDetalleProducto')->create();
		if (ClassRegistry::init('BodegasVentaDetalleProducto')->save($data)) {
			return true;
		}

		return false;
	}


	/**
	 * Guarda la salida de un producto de una bodega
	 * @param  [type] $id_producto [description]
	 * @param  [type] $bodega_id   [description]
	 * @param  [type] $cantidad    [description]
	 * @param  [type] $tipo        [description]
	 * @return [type]              [description]
	 */
	public function crearSalidaBodega($id_producto, $bodega_id = null, $cantidad, $valor = 0, $tipo, $id_oc = null, $id_venta = null)
	{	
		if ($cantidad <= 0) {
			return false;
		}
		
		if ($valor == 0) {
			$valor = $this->obtener_pmp_por_id($id_producto);
		}

		# Bodega principal
		if (empty($bodega_id)) {
			$bodega_id = ClassRegistry::init('Bodega')->find('first', array('conditions' => array('Bodega.principal' => 1), 'limit' => 1, 'fields' => array('Bodega.id')))['Bodega']['id'];
		}
		
		$data = array(
			'BodegasVentaDetalleProducto' => array(
				'bodega_id'                 => $bodega_id,
				'venta_detalle_producto_id' => $id_producto,
				'bodega'					=> $this->field('nombre', array('id' => $bodega_id) ),
				'sku'                       => ClassRegistry::init('VentaDetalleProducto')->field('codigo_proveedor', array('id' => $id_producto) ),
				'cantidad'                  => -$cantidad,
				'io'                        => 'ED',
				'tipo'						=> $tipo,
				'valor'                     => -$valor,
				'total'                     => $valor * -$cantidad,
				'fecha'                     => date('Y-m-d H:i:s'),
				'responsable'               => CakeSession::read('Auth.Administrador.email'),
				'glosa'						=> $this->tipoMovimientos[$tipo]['ED'],
				'orden_compra_id'			=> $id_oc,
				'venta_id'     				=> $id_venta
			)
		);

		ClassRegistry::init('BodegasVentaDetalleProducto')->create();
		if (ClassRegistry::init('BodegasVentaDetalleProducto')->save($data)) {
			return true;
		}

		return false;
	}


	/**
	 * Crea entrasa y salidas segun corresponda la cantidad ingresada v/s la cantidad que exista en bodega
	 * @param  [type] $id_producto [description]
	 * @param  [type] $bodega_id   [description]
	 * @param  [type] $cantidad    [description]
	 * @return [type]              [description]
	 */
	public function ajustarInventario($id_producto, $bodega_id, $cantidad, $precio_costo = null)
	{	

		if (empty($precio_costo) || $precio_costo == 0) {
			$precio_costo = $this->obtener_pmp_por_id($id_producto);			
		}

		$enBodega = $this->obtenerCantidadProductoBodega($id_producto, $bodega_id, true);

		$result = false;

		// Se crea una entrada con la diferencia
		if ($cantidad > $enBodega) {
			$result = $this->crearEntradaBodega($id_producto, $bodega_id, ($cantidad-$enBodega), $precio_costo, 'AJ' );
		}
		
		// Se crea una salida del total que hay en bodega
		if ($cantidad == 0 && $cantidad < $enBodega) {
			$result = $this->crearSalidaBodega($id_producto, $bodega_id, $enBodega, $precio_costo, 'AJ' );
		}

		// Se crea una salida con la diferencia
		if ($cantidad < $enBodega && $cantidad > 0) {
			$result = $this->crearSalidaBodega($id_producto, $bodega_id, ($enBodega-$cantidad), $precio_costo, 'AJ' );
		}

		if ($cantidad == 0 && $enBodega == 0) {
			$result = false;
		}

		return $result;
	}


	/**
	 * Crea una salida de la bodega actual y una entrada a la nueva bodega
	 * @param  int 		$id_producto       	Identificadro del producto
	 * @param  int 		$bodega_origen_id  	Identificador de la bodega de origen	
	 * @param  int 		$bodega_destino_id 	Identificador de la bodega de destino
	 * @param  int 		$cantidad          	Cantidad a mover
	 * @return bool                    	
	 */
	public function moverProductoBodega($id_producto, $bodega_origen_id, $bodega_destino_id, $cantidad)
	{

		// Crear la salida de la bodega actual
		$result1 = $this->crearSalidaBodega($id_producto, $bodega_origen_id, $cantidad, 0, 'MV');

		// Crear la entrada en la bodega nueva
		$precio_costo = $this->obtener_pmp_por_id($id_producto);
		$result2 	  = $this->crearEntradaBodega($id_producto, $bodega_destino_id, $cantidad, $precio_costo, 'MV');

		if ($result1 && $result2) {
			return true;
		}else{
			return false;
		}

	}


	/**
	 * [cargaInicialBodega description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function cargaInicialBodega($data = array())
	{	

		if (empty($data)) {
			throw new Exception("Empty data", 310);
		}

		$result = array();

		foreach ($data as $key => $value) {
			
			if (!isset($value['id_producto'])
				|| !isset($value['bodega_id'])
				|| !isset($value['cantidad'])
				|| !isset($value['precio_costo'])
				) {
				$result['errores'][] = __('Existen items sin los datos correspondientes, verifique los campos e intente nuevamente.');
				continue;
			}


			if (!ClassRegistry::init('VentaDetalleProducto')->exists($value['id_producto'])) {
				$result['errores'][] = __('Item id #'.$value['id_producto'].' no existe en los registros, verifique los campos e intente nuevamente.');
				continue;
			}

			# Verificamos que el item no tenga ya un ingreso inicial
			$existeIi = ClassRegistry::init('BodegasVentaDetalleProducto')->find('first', array('conditions' => array('venta_detalle_producto_id' => $value['id_producto'], 'bodega_id' => $value['bodega_id'], 'io' => 'II')));

			if (!empty($existeIi)) {
				$result['errores'][] = sprintf('Item %d ya ha sido tiene un ingreso inicial. Para modificarlo debe ajustarlo.', $value['id_producto']);
				continue;
			}

			$ii = $this->crearEntradaBodega($value['id_producto'], $value['bodega_id'], $value['cantidad'], $value['precio_costo'], 'II');

			if ($ii) {
				$result['procesados'] = (isset($result['procesados'])) ? $result['procesados']+1 : 1;
			}else{
				$result['errores'][] = sprintf('Item %d no pudo ser ingresado, verifique los campos e intente nuevamente.', $value['id_producto']);
			}

		}

		return $result;

	}



	/**
	 * [ajustarInventarioMasivo description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function ajustarInventarioMasivo($data = array())
	{	

		if (empty($data)) {
			throw new Exception("Empty data", 310);
		}

		$result = array();

		foreach ($data as $key => $value) {
			
			if (!isset($value['id_producto'])
				|| !isset($value['bodega_id'])
				|| !isset($value['cantidad'])
				) {
				$result['errores'][] = __('Existen items sin los datos correspondientes, verifique los campos e intente nuevamente.');
				continue;
			}


			if (!ClassRegistry::init('VentaDetalleProducto')->exists($value['id_producto'])) {
				$result['errores'][] = __('Item id #'.$value['id_producto'].' no existe en los registros, verifique los campos e intente nuevamente.');
				continue;
			}


			$ii = $this->ajustarInventario($value['id_producto'], $value['bodega_id'], $value['cantidad'], $value['precio']);

			if ($ii) {
				$result['procesados'] = (isset($result['procesados'])) ? $result['procesados']+1 : 1;
			}else{
				$result['errores'][] = sprintf('Item %d no pudo ser ajustado, o no es necesario ajustarlo.', $value['id_producto']);
			}

		}

		return $result;

	}


	/**
	 * [moverProductoBodegaMasivo description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function moverProductoBodegaMasivo($data = array())
	{	

		if (empty($data)) {
			throw new Exception("Empty data", 310);
		}

		$result = array();

		foreach ($data as $key => $value) {
			
			if (!isset($value['id_producto'])
				|| !isset($value['bodega_id_origen'])
				|| !isset($value['bodega_id_destino'])
				|| !isset($value['cantidad'])
				) {
				$result['errores'][] = __('Existen items sin los datos correspondientes, verifique los campos e intente nuevamente.');
				continue;
			}


			if (!ClassRegistry::init('VentaDetalleProducto')->exists($value['id_producto'])) {
				$result['errores'][] = __('Item id #'.$value['id_producto'].' no existe en los registros, verifique los campos e intente nuevamente.');
				continue;
			}


			$ii = $this->moverProductoBodega($value['id_producto'], $value['bodega_id_origen'], $value['bodega_id_destino'], $value['cantidad']);

			if ($ii) {
				$result['procesados'] = (isset($result['procesados'])) ? $result['procesados']+1 : 1;
			}else{
				$result['errores'][] = sprintf('Item %d no pudo ser movido, verifique los campos e intente nuevamente.', $value['id_producto']);
			}

		}

		return $result;

	}


	/**
	 * Crea un salida de productos tomando la cantidad desde la bodega
	 * @param  [type] $id               [description]
	 * @param  [type] $cantidad         [description]
	 * @param  string $bodega_origen_id [description]
	 * @return bool                   [description]
	 */
	public function calcular_reserva_stock($id, $cantidad)
	{	
		$enBodega   = $this->obtenerCantidadProductoBodegas($id);
		$nwcantidad = 0;

		# Se toman todos los items de bodega
		if ($enBodega <= $cantidad) {
			$nwcantidad = $enBodega;
		}

		# se descuentan los item necesarios
		if ($enBodega > $cantidad) {
			$nwcantidad = $cantidad;
		}

		return $nwcantidad;

	}


	/**
	 * Busca el precio con el cual se compró por ultima vez el producto
	 * @param  [type] $id_venta    [description]
	 * @param  [type] $id_producto [description]
	 * @return [type]              [description]
	 */
	public function ultimo_precio_compra($id_producto)
	{
		$mv = ClassRegistry::init('BodegasVentaDetalleProducto')->find('first', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				'BodegasVentaDetalleProducto.io' => 'IN',
				'BodegasVentaDetalleProducto.tipo' => 'OC'
			),
			'order' => array('BodegasVentaDetalleProducto.fecha' => 'DESC')
		));

		$costo = ClassRegistry::init('VentaDetalleProducto')->obtener_precio_costo($id_producto);

		if (!empty($mv)) {
			$costo = $mv['BodegasVentaDetalleProducto']['valor'];
		}

		return $costo;
	}
}
