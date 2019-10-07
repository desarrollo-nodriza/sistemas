<?php
App::uses('AppModel', 'Model');
class Venta extends AppModel
{	

	/**
	 * @var array
	 */
	public $picking_estado = array(
		'no_definido' => array(
			'label' => 'Incompleta',
			'color' => '#B64645'
		),
		'empaquetar' => array(
			'label' => 'Listo para embalar',
			'color' => '#3FBAE4'
		),
		'empaquetando' => array(
			'label' => 'En preparación',
			'color' => '#FEA223'
		),
		'empaquetado' => array(
			'label' => 'Embalaje finalizado',
			'color' => '#95B75D'
		)
	);


	/**
	 * @var array
	 */
	public $picking_estados_lista = array(
		'no_definido' => 'Incompleta',
		'empaquetar'  => 'Listo para embalar',
		'empaquetando' => 'En prepración',
		'empaquetado'  => 'Emabalaje finalizado'
	);

	/**
	 * BEHAVIORS
	 * Foto CI
	 */
	var $actsAs			= array(
		'Image'		=> array(
			'fields'	=> array(
				'ci_receptor'	=> array(
					'versions'	=> array(
						array(
							'prefix'	=> 'mini',
							'width'		=> 100,
							'height'	=> 100,
							'crop'		=> true
						),
						array(
							'prefix'	=> 'landscape',
							'width'		=> 300,
							'height'	=> 200,
							'crop'		=> true
						)
					)
				)
			)
		)
	);

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'Marketplace' => array(
			'className'				=> 'Marketplace',
			'foreignKey'			=> 'marketplace_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Marketplace')
		),
		'VentaEstado' => array(
			'className'				=> 'VentaEstado',
			'foreignKey'			=> 'venta_estado_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'VentaEstado')
		),
		'MedioPago' => array(
			'className'				=> 'MedioPago',
			'foreignKey'			=> 'medio_pago_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'MedioPago')
		),
		'VentaCliente' => array(
			'className'				=> 'VentaCliente',
			'foreignKey'			=> 'venta_cliente_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Cliente')
		),
		'MetodoEnvio' => array(
			'className'				=> 'MetodoEnvio',
			'foreignKey'			=> 'metodo_envio_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'VentaEstado')
		),
	);
	public $hasMany = array(
		'VentaDetalle' => array(
			'className'				=> 'VentaDetalle',
			'foreignKey'			=> 'venta_id',
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
		'VentaMensaje' => array(
			'className'				=> 'VentaMensaje',
			'foreignKey'			=> 'venta_id',
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
		'Dte' => array(
			'className'				=> 'Dte',
			'foreignKey'			=> 'venta_id',
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
		'VentaTransaccion' => array(
			'className'				=> 'VentaTransaccion',
			'foreignKey'			=> 'venta_id',
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
		'WebpayStore' => array(
			'className'				=> 'WebpayStore',
			'foreignKey'			=> 'id_order',
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
		'Manifiesto' => array(
			'className'				=> 'Manifiesto',
			'joinTable'				=> 'manifiestos_ventas',
			'foreignKey'			=> 'venta_id',
			'associationForeignKey'	=> 'manifiesto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ManifiestosVenta',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'joinTable'				=> 'orden_compras_ventas',
			'foreignKey'			=> 'venta_id',
			'associationForeignKey'	=> 'orden_compra_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'OrdenComprasVenta',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Transporte' => array(
			'className'				=> 'Transporte',
			'joinTable'				=> 'transportes_ventas',
			'foreignKey'			=> 'venta_id',
			'associationForeignKey'	=> 'transporte_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);


	public function beforeSave($options = array())
	{	
		$peso_total = (float) 0;

		if (isset($this->data['VentaDetalle'])) {
			foreach ($this->data['VentaDetalle'] as $i => $d) {

				if (!isset($d['VentaDetalle']['cantidad'])) {
					continue;
				}

				$peso_producto = (float) ClassRegistry::init('VentaDetalleProducto')->field('peso', array('id' => $d['VentaDetalle']['venta_detalle_producto_id']));

				$this->data['VentaDetalle'][$i]['VentaDetalle']['peso_bulto'] = round($peso_producto * $d['VentaDetalle']['cantidad'], 2);		

				$peso_total = $peso_total + $this->data['VentaDetalle'][$i]['VentaDetalle']['peso_bulto'];

				$this->data['VentaDetalle'][$i]['VentaDetalle']['cantidad_pendiente_entrega'] = $d['VentaDetalle']['cantidad'];
				$this->data['VentaDetalle'][$i]['VentaDetalle']['cantidad_entregada']         = 0;

				if (isset($d['VentaDetalle']['precio'])) {
					$this->data['VentaDetalle'][$i]['VentaDetalle']['precio_bruto'] = monto_bruto($d['VentaDetalle']['precio']);		
				}
			}
		}

		if ($peso_total > 0) {
			$this->data['Venta']['peso_bulto_total'] = (float) round($peso_total, 2);
		}
	}


	/**
	 * [obtener_venta_por_id description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function obtener_venta_por_id($id)
	{
		return $this->find(
			'first',
			array(
				'conditions' => array(
					'Venta.id' => $id
				),
				'contain' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'Bodega' => array(
								'fields' => array(
									'Bodega.id', 'Bodega..nombre', 'Bodega.activo', 'Bodega.principal', 'Bodega.direccion', 'Bodega.fono'
								)
							),
							'fields' => array(
								'VentaDetalleProducto.id', 'VentaDetalleProducto.id_externo', 'VentaDetalleProducto.nombre', 'VentaDetalleProducto.codigo_proveedor', 'VentaDetalleProducto.cantidad_virtual', 'VentaDetalleProducto.stock_automatico', 'VentaDetalleProducto.ancho', 'VentaDetalleProducto.alto', 'VentaDetalleProducto.largo', 'VentaDetalleProducto.peso'
							)
						),
						'conditions' => array(
							'VentaDetalle.activo' => 1
						),
						'fields' => array(
							'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.cantidad', 'VentaDetalle.venta_id', 'VentaDetalle.completo', 'VentaDetalle.cantidad_pendiente_entrega', 'VentaDetalle.cantidad_reservada', 'VentaDetalle.cantidad_entregada', 'VentaDetalle.confirmado_app', 'VentaDetalle.reservado_virtual'
						)
					),
					'VentaEstado' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.plantilla'
							)
						),
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.venta_estado_categoria_id', 'VentaEstado.permitir_dte', 'VentaEstado.nombre', 'VentaEstado.notificacion_cliente'
						)
					),
					'VentaTransaccion',
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.logo', 'Tienda.direccion', 'Tienda.facturacion_apikey', 'Tienda.emails_bcc', 'Tienda.url', 'Tienda.direccion', 'Tienda.stock_automatico', 'activo_enviame', 'apihost_enviame', 'apikey_enviame', 'company_enviame', 'bodega_enviame', 'meta_ids_enviame', 'peso_enviame', 'volumen_enviame', 'mandrill_apikey'
						)
					),
					'Marketplace' => array(
						'fields' => array(
							'Marketplace.id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id',
							'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
							'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token', 'Marketplace.stock_automatico'
						),
						'MarketplaceTipo' => array(
							'fields' => array(
								'MarketplaceTipo.id', 'MarketplaceTipo.nombre'
							)
						)
					),
					'MedioPago' => array(
						'fields' => array(
							'MedioPago.id', 'MedioPago.nombre'
						)
					),
					'MetodoEnvio' => array(
						'fields' => array(
							'MetodoEnvio.id', 'MetodoEnvio.nombre'
						)
					),
					'VentaCliente' => array(
						'fields' => array(
							'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono', 'VentaCliente.created'
						)
					),
					'Transporte' => array(
						'fields' => array(
							'Transporte.id', 'Transporte.nombre', 'Transporte.url_seguimiento', 'Transporte.tiempo_entrega'
						)
					),
					'Dte' => array(
						'Administrador' => array(
							'fields' => array(
								'Administrador.id', 'Administrador.email'
							)
						),
						'fields' => array(
							'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
							'Dte.total', 'Dte.fecha', 'Dte.estado', 'Dte.venta_id', 'Dte.pdf', 'Dte.invalidado', 'Dte.administrador_id'
						),
						'order' => 'Dte.fecha DESC'
					)
				),
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo', 'Venta.descuento', 'Venta.costo_envio',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.metodo_envio_id', 'Venta.venta_cliente_id', 'Venta.direccion_entrega', 'Venta.comuna_entrega', 'Venta.nombre_receptor',
					'Venta.fono_receptor', 'Venta.picking_estado', 'Venta.prioritario', 'Venta.estado_anterior', 'Venta.picking_email', 'Venta.venta_estado_responsable', 'Venta.chofer_email', 'Venta.fecha_enviado', 'Venta.fecha_entregado', 'Venta.ci_receptor', 'Venta.fecha_transito', 'Venta.etiqueta_envio_externa'
				)
			)
		);
	}



	public function obtener_venta_por_id_tiny($id)
	{
		return $this->find(
			'first',
			array(
				'conditions' => array(
					'Venta.id' => $id
				),
				'contain' => array(
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre'
						)
					),
					'VentaEstado' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
							)
						),
						'fields' => array(
							'VentaEstado.nombre'
						)
					),
					'MetodoEnvio' => array(
						'fields' => array(
							'MetodoEnvio.nombre'
						)
					),
					'VentaCliente' => array(
						'fields' => array(
							'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.telefono', 'VentaCliente.email'
						)
					),
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.nombre'
							)
						),
						'fields' => array(
							'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.cantidad', 'VentaDetalle.cantidad_pendiente_entrega', 'VentaDetalle.cantidad_reservada', 'VentaDetalle.cantidad_entregada', 'VentaDetalle.confirmado_app'
						)
					),
					'Marketplace' => array(
						'fields' => array(
							'Marketplace.nombre', 'Marketplace.marketplace_tipo_id'
						)
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.nombre'
						)
					)
				)
			)
		);
	}


	/**
	 * [obtener_ventas_preparar description]
	 * @param  string  $estado      [description]
	 * @param  integer $limit       [description]
	 * @param  integer $offset      [description]
	 * @param  array   $estados_ids [description]
	 * @return [type]               [description]
	 */
	public function obtener_ventas_preparar($estado = '', $limit = -1, $offset = 0, $estados_ids = array(), $id_venta = 0, $id_metodo_envio = 0, $id_marketplace = 0, $id_tienda = 0)
	{	
		$joins[] = array(
			'table' => 'rp_venta_estados',
			'alias' => 'ventas_estados',
			'type' => 'INNER',
			'conditions' => array(
				'ventas_estados.id = Venta.venta_estado_id',
				"ventas_estados.venta_estado_categoria_id" => $estados_ids,
				"ventas_estados.permitir_retiro_oc"  => 1
			)
		);

		$joins[] = array(
			'table' => 'rp_venta_detalles',
			'alias' => 'venta_detalles',
			'type' => 'INNER',
			'conditions' => array(
				'venta_detalles.venta_id = Venta.id',
				'venta_detalles.cantidad_reservada = venta_detalles.cantidad',
			)
		);

		
		$joins[] = array(
			'table' => 'rp_dtes',
			'alias' => 'dtes',
			'type' => 'INNER',
			'conditions' => array(
				'dtes.venta_id = Venta.id',
				"dtes.tipo_documento" => array(33, 39),
				"dtes.estado = 'dte_real_emitido'",
				"dtes.invalidado = 0"
			)
		);

		$conditions = array('Venta.picking_estado' => $estado);

		if ($id_venta) {
			$conditions = array_replace_recursive($conditions, array(
				'Venta.id' => $id_venta
			));
		}

		if ($id_metodo_envio) {
			$conditions = array_replace_recursive($conditions, array(
				'Venta.metodo_envio_id' => $id_metodo_envio
			));
		}

		if ($id_marketplace) {
			$conditions = array_replace_recursive($conditions, array(
				'Venta.marketplace_id' => $id_marketplace
			));
		}

		if ($id_tienda) {
			$conditions = array_replace_recursive($conditions, array(
				'Venta.tienda_id' => $id_tienda
			));
		}
		
		$ventas =  $this->find('all', array(
			'conditions' => $conditions,
			'joins'  => $joins,
			'limit'  => $limit,
			'offset' => $offset,
			'order'  => array('Venta.prioritario' => 'desc', 'Venta.fecha_venta' => 'asc', 'Venta.modified' => 'desc'),
			'group'  => 'Venta.id',
			'fields' => array(
				'Venta.id'
			),
		));

		return $ventas;
	}

	/**
	 * [obtener_ventas_preparadas description]
	 * @param  string  $estado      [description]
	 * @param  integer $limit       [description]
	 * @param  integer $offset      [description]
	 * @param  array   $estados_ids [description]
	 * @return [type]               [description]
	 */
	public function obtener_ventas_preparadas($estado = '', $limit = 10, $offset = 0, $estados_ids = array())
	{	
		/*$joins[] = array(
			'table' => 'rp_venta_estados',
			'alias' => 'ventas_estados',
			'type' => 'INNER',
			'conditions' => array(
				'ventas_estados.id = Venta.venta_estado_id',
				"ventas_estados.venta_estado_categoria_id" => $estados_ids,
				"ventas_estados.permitir_retiro_oc"  => 1
			)
		);*/

		$joins[] = array(
			'table' => 'rp_venta_detalles',
			'alias' => 'venta_detalles',
			'type' => 'INNER',
			'conditions' => array(
				'venta_detalles.venta_id = Venta.id',
				'venta_detalles.fecha_completado >= DATE_ADD(CURDATE(),INTERVAL -1 DAY)'
			)
		);

		
		$joins[] = array(
			'table' => 'rp_dtes',
			'alias' => 'dtes',
			'type' => 'INNER',
			'conditions' => array(
				'dtes.venta_id = Venta.id',
				"dtes.tipo_documento" => array(33, 39),
				"dtes.estado = 'dte_real_emitido'",
				"dtes.invalidado = 0"
			)
		);

		$ventas =  $this->find('all', array(
			'conditions' => array(
				'Venta.picking_estado' => $estado,
			),
			'joins'  => $joins,
			'limit'  => $limit,
			'offset' => $offset,
			'order'  => array('Venta.prioritario' => 'desc', 'Venta.modified' => 'desc', 'Venta.fecha_venta' => 'asc'),
			'group'  => 'Venta.id',
			'fields' => array(
				'Venta.id', 'Venta.picking_estado', 'Venta.prioritario', 'Venta.fecha_venta'
			),
		));
		
		return $ventas;
	}


	/**
	 * Devolvemos las unidades reservadas o las que ya se sacaron de bodega
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function cancelar_venta($id)
	{	
		$this->id = $id;
		if (!$this->exists()) {
			return false;
		}

		$venta = $this->obtener_venta_por_id($id);
		
		foreach ($venta['VentaDetalle'] as $iv => $detalle) {
			
			$pmp = ClassRegistry::init('Bodega')->obtener_pmp_por_id($detalle['venta_detalle_producto_id']);
			$vDetalle = ClassRegistry::init('VentaDetalle');

			$vDetalle->id = $detalle['id'];

			# Devolver stock a bodega
			if ($detalle['cantidad_entregada'] > 0) {
				ClassRegistry::init('Bodega')->crearEntradaBodega($detalle['venta_detalle_producto_id'], null, $detalle['cantidad_entregada'], $pmp, 'VT');
				$vDetalle->saveField('cantidad_entregada', 0);
				$vDetalle->saveField('cantidad_pendiente_entrega', $detalle['cantidad_entregada']);
				$vDetalle->saveField('completo', 0);
			}

			# Devolver unidades reservadas
			if ($detalle['cantidad_reservada'] > 0) {
				$vDetalle->saveField('cantidad_reservada', 0);
			}

			# Nuevo stock virtual
			if ($detalle['reservado_virtual']) { 
				ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($detalle['venta_detalle_producto_id'], $detalle['cantidad'], 'aumentar');
				$vDetalle->saveField('reservado_virtual', 0);
			}
		}

		$this->saveField('picking_estado', 'no_definido');
		$this->saveField('subestado_oc', 'no_entregado');

		return;

	}

	/**
	 * Reserva las unidades que esten en existencia y cambia el estad de la venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function pagar_venta($id)
	{	
		$this->id = $id;
		if (!$this->exists()) {
			return false;
		}

		$venta = $this->obtener_venta_por_id($id);	

		# solo se procesa si el estado de la venta ha cambiado
		if ($venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior'] ) {
			foreach ($venta['VentaDetalle'] as $ip => $producto) {
				
				ClassRegistry::init('VentaDetalle')->id = $producto['id'];

				if ($producto['cantidad_reservada'] == 0) {
					$reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock($producto['venta_detalle_producto_id'], $producto['cantidad']);
					ClassRegistry::init('VentaDetalle')->saveField('cantidad_reservada', $reservado);
					ClassRegistry::init('VentaDetalle')->saveField('cantidad_pendiente_entrega', $producto['cantidad']);

					$venta['VentaDetalle'][$ip]['cantidad_reservada'] = $reservado;
				}				

				# Nuevo stock virtual
				if (!$producto['reservado_virtual']) { 
					ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($producto['venta_detalle_producto_id'], $producto['cantidad']);
					ClassRegistry::init('VentaDetalle')->saveField('reservado_virtual', 1);
				}

			}
		}

		if (array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_reservada')) == array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad'))) {

			$picking_estado = $this->field('picking_estado');

			if (empty($picking_estado) || $picking_estado == 'no_definido' ) {
				$this->saveField('picking_estado', 'empaquetar');
			}
			$this->saveField('subestado_oc', 'no_entregado');
		}

		return;
	}


	/**
	 * [pagar_manual description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function entregar($id)
	{
		$this->id = $id;
		if (!$this->exists()) {
			return false;
		}

		$venta = $this->obtener_venta_por_id($id);

		$detalles = array();

		# solo se procesa si el estado de la venta ha cambiado
		if ($venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior'] ) {

			foreach ($venta['VentaDetalle'] as $ip => $producto) {

				if ($producto['cantidad_reservada'] > 0){

					# crear salida de productos
					$detalles[$ip]['VentaDetalle']['id']               = $producto['id'];
					$detalles[$ip]['VentaDetalle']['completo']         = ($venta['VentaDetalle'][$ip]['cantidad'] == $producto['cantidad_reservada']) ? 1 : 0;
					$detalles[$ip]['VentaDetalle']['fecha_completado'] = date('Y-m-d H:i:s');

					$detalles[$ip]['VentaDetalle']['cantidad_reservada']         = 0;
					$detalles[$ip]['VentaDetalle']['cantidad_entregada']         = $producto['cantidad_reservada'];
					$detalles[$ip]['VentaDetalle']['cantidad_pendiente_entrega'] = $producto['cantidad'] - $producto['cantidad_reservada'];

					ClassRegistry::init('Bodega')->crearSalidaBodega($producto['venta_detalle_producto_id'], null, $producto['cantidad_reservada'], 'VT');

				}else{
					throw new Exception("No se permite entregar un pedido que no tiene los productos reservados.", 1);
					
					$reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock($producto['venta_detalle_producto_id'], $producto['cantidad']);
					
					$detalles[$ip]['VentaDetalle']['id']                         = $producto['id'];
					$detalles[$ip]['VentaDetalle']['cantidad_reservada']         = $reservado;
					$detalles[$ip]['VentaDetalle']['cantidad_pendiente_entrega'] = $producto['cantidad'];
				
				}

				# Nuevo stock virtual
				if ($producto['reservado_virtual']) { 
					ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($producto['venta_detalle_producto_id'], $producto['cantidad']);
					$detalles[$ip]['VentaDetalle']['reservado_virtual'] = 1;
				}

			}
			
			# Guardamos los cambios
			ClassRegistry::init('VentaDetalle')->saveMany($detalles);

		}

		# Pedido está entregado completo
		$this->saveField('picking_estado', 'empaquetado');			
		$this->saveField('subestado_oc', 'entregado');
		$this->saveField('fecha_completado', date('Y-m-d H:i:s'));		

		return;
	}


	/**
	 * Devuleve las unidades que esten en existencia y cambia el estad de la venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function revertir_venta($id)
	{	
		$this->id = $id;
		if (!$this->exists()) {
			return false;
		}

		$ventaDetalles = ClassRegistry::init('VentaDetalle')->find('all', array(
			'conditions' => array(
				'venta_id' => $id
			)
		));	

		foreach ($ventaDetalles as $ip => $producto) {
			ClassRegistry::init('VentaDetalle')->id = $producto['VentaDetalle']['id'];
			ClassRegistry::init('VentaDetalle')->saveField('cantidad_reservada', 0);
			ClassRegistry::init('VentaDetalle')->saveField('cantidad_pendiente_entrega', $producto['VentaDetalle']['cantidad']);

			# Nuevo stock virtual
			if ($producto['VentaDetalle']['reservado_virtual']) { 
				ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($producto['VentaDetalle']['venta_detalle_producto_id'], $producto['VentaDetalle']['cantidad'], 'aumentar');
				ClassRegistry::init('VentaDetalle')->saveField('reservado_virtual', 0);
			}
		}

		$this->saveField('picking_estado', 'no_definido');
		$this->saveField('subestado_oc', 'no_entregado');

		return;
	}


	/**
	 * Si hay items disponible se reserva el stock y se actualiza el picking_estado de la venta.
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function reservar_stock_producto($id)
	{
		ClassRegistry::init('VentaDetalle')->id = $id;
		if (!ClassRegistry::init('VentaDetalle')->exists()) {
			return 0;
		}

		# Solo se reserva si la cantidad reservada es distinta a la cantidad comprada por el cliente
		if (ClassRegistry::init('VentaDetalle')->field('cantidad_reservada') == ClassRegistry::init('VentaDetalle')->field('cantidad')) {
			return 0;
		}

		$reservar = ClassRegistry::init('VentaDetalle')->field('cantidad') - ClassRegistry::init('VentaDetalle')->field('cantidad_reservada');

		$reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock(ClassRegistry::init('VentaDetalle')->field('venta_detalle_producto_id'), $reservar);
		
		$save = array(
			'VentaDetalle' => array(
				'id' => $id,
				'cantidad_reservada' => $reservado
			)
		);

		if(!ClassRegistry::init('VentaDetalle')->save($save))
			return 0;

		$venta = $this->obtener_venta_por_id(ClassRegistry::init('VentaDetalle')->field('venta_id'));

		if (array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_reservada')) == array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad'))) {
			$this->id = $venta['Venta']['id'];

			$picking_estado = $this->field('picking_estado');

			if (empty($picking_estado) || $picking_estado == 'no_definido' ) {
				$this->saveField('picking_estado', 'empaquetar');
			}
			$this->saveField('subestado_oc', 'no_entregado');
		}

		return $reservado;
	}


	/**
	 * [liberar_reserva_stock_producto description]
	 * @param  [type] $id      [description]
	 * @param  [type] $liberar [description]
	 * @return [type]          [description]
	 */
	public function liberar_reserva_stock_producto($id, $liberar)
	{
		ClassRegistry::init('VentaDetalle')->id = $id;
		if (!ClassRegistry::init('VentaDetalle')->exists()) {
			return 0;
		}

		if ($liberar == 0 || $liberar < 0 || $liberar > ClassRegistry::init('VentaDetalle')->field('cantidad_reservada'))
			return 0;

		$nueva_cantidad = ClassRegistry::init('VentaDetalle')->field('cantidad_reservada') - $liberar;
			
		if(ClassRegistry::init('VentaDetalle')->saveField('cantidad_reservada', $nueva_cantidad)) {

			ClassRegistry::init('VentaDetalle')->saveField('confirmado_app', 0);

			$this->id = ClassRegistry::init('VentaDetalle')->field('venta_id');
			$this->saveField('picking_estado', 'no_definido');
			return $liberar;
		}else{
			return 0;
		}
	}


	/**
	 * [obtener_lista_cantidad_productos_vendidos description]
	 * @param  [type] $id_venta [description]
	 * @return [type]           [description]
	 */
	public function obtener_lista_cantidad_productos_vendidos($id_venta)
	{	
		$vendidos = ClassRegistry::init('VentaDetalle')->find('all', array(
			'conditions' => array(
				'VentaDetalle.venta_id' => $id_venta
			),
			'fields' => array(
				'VentaDetalle.id',
				'VentaDetalle.venta_detalle_producto_id',
				'VentaDetalle.cantidad'
			)
		));

		$indices = array();

		foreach ($vendidos as $iv => $detalle) {
			$indices[$detalle['VentaDetalle']['venta_detalle_producto_id']] = $detalle['VentaDetalle']['venta_detalle_producto_id'];
		}

		$indices = array_unique($indices);
		$items   = array();

		foreach ($indices as $id_producto) {
			$items[$id_producto] = array_sum(Hash::extract($vendidos, '{n}.VentaDetalle[venta_detalle_producto_id='.$id_producto.'].cantidad'));
		}

		return $items;
	}

}
