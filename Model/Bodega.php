<?php
App::uses('AppModel', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class Bodega extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';
	public $useDbConfig     = "default";

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
			'IN' => 'Ingreso por cancelación de la venta',
			'ED' => 'Salida desde venta',
			'NOMBRE' => 'I/O venta'
		),
		'NC' => array(
			'IN' => 'Ingreso por Nota de crédito por concepto de anulación y/o devolución',
			'ED' => 'Salida por Nota de crédito', // No se debería usar,
			'NOMBRE' => 'Nota de crédito'
		),
		'GT' => array(
			'IN' => 'Ingreso por Nota de crédito por concepto de garantia (no computable)',
			'ED' => 'Salida por Nota de crédito', // No se debería usar,
			'NOMBRE' => 'Garantia'
		)
	); 


	/**
	 * ASOCIACIONES
	 */

	public $belongsTo = array(
		'Comuna' => array(
			'className'				=> 'Comuna',
			'foreignKey'			=> 'comuna_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
		),
	);
	
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
		),
		'Rol' => array(
			'className'				=> 'Rol',
			'joinTable'				=> 'bodegas_roles',
			'foreignKey'			=> 'bodega_id',
			'associationForeignKey'	=> 'roles_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'width'					=> 'BodegasRol',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);

	public $hasMany = array(
		'Rol' => array(
			'className'				=> 'Rol',
			'foreignKey'			=> 'bodega_id',
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
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'bodega_id',
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


	public function obtener_bodegas()
	{
		return $this->find('list', array('conditions' => array('Bodega.activo' => 1)));
	}

	public function obtener_bodegas_sucursal()
	{
		
		return $this->find('all');
	}


	/**
	 * [obtener_pmp_por_id description]
	 * @param  [type] $id_producto [description]
	 * @return [type]              [description]
	 */
	public function obtener_pmp_por_id($id_producto = null)
	{
		$pmp = ClassRegistry::init('Pmp')->obtener_pmp($id_producto);
	
		return $pmp;
	} 


	/**
	 * [obtener_pmp_por_producto_bodega description]
	 * @param  [type] $id_producto [description]
	 * @param  [type] $bodega_id   [description]
	 * @return [type]              [description]
	 */
	public function obtener_pmp_por_producto_bodega($id_producto = null, $bodega_id = null)
	{
		$pmp = ClassRegistry::init('Pmp')->obtener_pmp($id_producto, $bodega_id);

		return $pmp;

	} 


	/**
	 * Obtiene el stock de una bodega en especifico
	 * @param  int  $id_producto Id del producto
	 * @param  int  $id_bodega   ID de bodega que se desea consultar
	 * @param  boolean $sin_reserva True: Retorna el total sin descontaar las unidades reservadas, False: Retorna las unieades disponibles reales para vender
	 * @return int 	
	 */
	public function obtenerCantidadProductoBodega($id_producto, $id_bodega = null, $sin_reserva = false)
	{	
		# Bodega principal
		if (empty($id_bodega)) {
			$id_bodega = ClassRegistry::init('Bodega')->find('first', array('conditions' => array('Bodega.principal' => 1), 'limit' => 1, 'fields' => array('Bodega.id')))['Bodega']['id'];
		}

		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.bodega_id' => $id_bodega,
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				'BodegasVentaDetalleProducto.tipo <>' => 'GT'
			)
		));

		$total = 0;

		if (!empty($historico)) {
			// Sumatoria de las cantidades
			$total = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.cantidad'));	
		}

		if ($sin_reserva)
			return $total;

		# Obtenemos la cantidad reservada o vendida no empaquetada
		$reservado = ClassRegistry::init('VentaDetalleProducto')->obtener_cantidad_reservada($id_producto, null, $id_bodega);
		
		if ($total <= $reservado)
			return 0; // No tenemos stock

		if ($total > $reservado)
			$total = ($total - $reservado); // Descontamos la cantidad reservada

		return $total;
	}


	/**
	 * Retorna la cantidad disponibles en las bodegas
	 * @param  int  $id_producto Id del producto
	 * @param  boolean $sin_reserva  True: Retorna el total sin descontaar las unidades reservadas, False: Retorna las unieades disponibles reales para vender
	 * @return int
	 */
	public function obtenerCantidadProductoBodegas($id_producto, $sin_reserva = false)
	{
		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				'BodegasVentaDetalleProducto.tipo <>' => 'GT'
			)
		));
		
		$total = 0;

		if (!empty($historico)) {
			$total = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.cantidad'));		
		}

		if ($sin_reserva)
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
	 * Verifica que un producto pueda hacer movimientos como ajustes en la bodega seleccionada
	 * @param  [type] $id_producto [description]
	 * @param  [type] $bodega_id   [description]
	 * @return [type]              [description]
	 */
	public function permite_ajuste($id_producto, $bodega_id)
	{
		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto, 
				'BodegasVentaDetalleProducto.bodega_id' => $bodega_id,
				'BodegasVentaDetalleProducto.tipo' => array('II', 'MV', 'OC') 
			)
		));

		if (empty($historico)) {
			return false;
		}

		return true;
	}


	/**
	 * Guarda el ingreso de un producto en una bodega
	 * @param  [type] $id_producto  [description]
	 * @param  [type] $bodega_id    [description]
	 * @param  [type] $cantidad     [description]
	 * @param  [type] $precio_costo [description]
	 * @param  [type] $tipo         [description]
	 * @param  [string] $glosa
	 * @return [type]               [description]
	 */
	public function crearEntradaBodega($id_producto, $bodega_id = null, $cantidad, $precio_costo, $tipo, $id_oc = null, $id_venta = null, $glosa = '', $responsable = '')
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
				'valor'                     => round($precio_costo, 2),
				'total'                     => round($precio_costo * $cantidad, 2),
				'fecha'                     => date('Y-m-d H:i:s'),
				'responsable'               => ($responsable) ? $responsable : CakeSession::read('Auth.Administrador.email'),
				'glosa'						=> (empty($glosa)) ? $this->tipoMovimientos[$tipo]['IN'] : $glosa,
				'orden_compra_id'			=> $id_oc,
				'venta_id'     				=> $id_venta
			)
		);

		ClassRegistry::init('BodegasVentaDetalleProducto')->create();
		if (ClassRegistry::init('BodegasVentaDetalleProducto')->save($data)) {
			ClassRegistry::init('Pmp')->registrar_pmp($id_producto, $bodega_id);
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
	 * @param  [string] $glosa
	 * @return [type]              [description]
	 */
	public function crearSalidaBodega($id_producto, $bodega_id = null, $cantidad, $valor = 0, $tipo, $id_oc = null, $id_venta = null, $glosa = '')
	{	
		if ($cantidad <= 0) {
			return false;
		}

		# Bodega principal
		if (empty($bodega_id)) {
			$bodega_id = ClassRegistry::init('Bodega')->find('first', array('conditions' => array('Bodega.principal' => 1), 'limit' => 1, 'fields' => array('Bodega.id')))['Bodega']['id'];
		}

		if ($valor == 0) {
			$valor = $this->obtener_pmp_por_producto_bodega($id_producto, $bodega_id);
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
				'valor'                     => round(-$valor, 2),
				'total'                     => round($valor * -$cantidad, 2),
				'fecha'                     => date('Y-m-d H:i:s'),
				'responsable'               => CakeSession::read('Auth.Administrador.email'),
				'glosa'						=> (empty($glosa)) ? $this->tipoMovimientos[$tipo]['ED'] : $glosa,
				'orden_compra_id'			=> $id_oc,
				'venta_id'     				=> $id_venta
			)
		);

		ClassRegistry::init('BodegasVentaDetalleProducto')->create();
		if (ClassRegistry::init('BodegasVentaDetalleProducto')->save($data)) {
			ClassRegistry::init('Pmp')->registrar_pmp($id_producto, $bodega_id);
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
	public function ajustarInventario($id_producto, $bodega_id, $cantidad, $precio_costo = null, $glosa = '')
	{	

		$enBodega = $this->obtenerCantidadProductoBodega($id_producto, $bodega_id, true);
		
		if (empty($precio_costo) || $precio_costo == 0) {
			$precio_costo = $this->obtener_pmp_por_producto_bodega($id_producto, $bodega_id);			
		}

		if($precio_costo <= 0)
			return false;

		$result = false;

		// Se crea una entrada con la diferencia
		if ($cantidad > $enBodega) {
			$result = $this->crearEntradaBodega($id_producto, $bodega_id, ($cantidad-$enBodega), $precio_costo, 'AJ', null, null, $glosa );
		}
		
		// Se crea una salida del total que hay en bodega
		if ($cantidad == 0 && $cantidad < $enBodega) {
			$result = $this->crearSalidaBodega($id_producto, $bodega_id, $enBodega, $precio_costo, 'AJ', null, null, $glosa );
		}

		// Se crea una salida con la diferencia
		if ($cantidad < $enBodega && $cantidad > 0) {
			$result = $this->crearSalidaBodega($id_producto, $bodega_id, ($enBodega-$cantidad), $precio_costo, 'AJ', null, null, $glosa );
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
		$precio_costo = $this->obtener_pmp_por_producto_bodega($id_producto, $bodega_origen_id);
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

			if (!$this->permite_ajuste($value['id_producto'], $value['bodega_id'])) {
				#$result['errores'][] = 'Item #' . $value['id_producto'] . ' No puede ser ajustado en la bodega seleccionada, ya que la bodega no tiene registros de ingreso.';
				#continue;
				
				

			}
			if (isset($value['glosa'])) {
				$ii = $this->ajustarInventario($value['id_producto'], $value['bodega_id'], $value['cantidad'], $value['precio'], $value['glosa']);
			}else
			{
				$ii = $this->ajustarInventario($value['id_producto'], $value['bodega_id'], $value['cantidad'], $value['precio']);
			}
			

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
	public function calcular_reserva_stock($id, $cantidad, $bodega= null)
	{	
		$enBodega   = $this->obtenerCantidadProductoBodega($id, $bodega);
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

	
	/**
	 * Obtiene el total de unidades movidas para una venta dada
	 * Tambien se puede usar por bodega
	 * 
	 * @param int $id_venta Identificador de la venta
	 * @param int $id_producto Idenitficador del producto
	 * @param int $id_bodega Identificador de bodega
	 * 
	 * @return int
	 */
	public function obtener_total_mv_por_venta($id_venta, $id_producto, $id_bodega = '')
	{	
		$qry = array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_id' => $id_venta,
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				'BodegasVentaDetalleProducto.tipo' => array('VT', 'NC')
			),
			'fields' => array('BodegasVentaDetalleProducto.cantidad')
		);

		if (!empty($id_bodega))
		{
			$qry =  array_replace_recursive($qry, array(
				'conditions' => array(
					'BodegasVentaDetalleProducto.bodega_id' => $id_bodega
				)
			));
		}

		$mvs = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', $qry);

		return array_sum(Hash::extract($mvs, '{n}.BodegasVentaDetalleProducto.cantidad'));

	}

	
	/**
	 * obtener_bodega_principal
	 *
	 * @return void
	 */
	public function obtener_bodega_principal()
	{
		return $this->find('first', array('conditions' => array('Bodega.principal' => 1), 'limit' => 1, 'fields' => array('Bodega.id')));
	}
}
