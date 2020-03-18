<?php
App::uses('AppModel', 'Model');
class Venta extends AppModel
{	

	/**
	 * @var array
	 */
	public $picking_estado = array(
		'no_definido' => array(
			'label' => 'No preparado',
			'leyenda' => 'Estamos recolectando el/los productos de tu pedido',
			'color' => '#B64645'
		),
		'empaquetar' => array(
			'label' => 'Listo para embalar',
			'leyenda' => 'Tus productos ya se encuentran en nuestra bodega para ser preparados',
			'color' => '#3FBAE4'
		),
		'empaquetando' => array(
			'label' => 'En preparación',
			'leyenda' => 'Estamos preparando tu pedido',
			'color' => '#FEA223'
		),
		'empaquetado' => array(
			'label' => 'Embalaje finalizado',
			'leyenda' => 'Todos los productos estan embalados',
			'color' => '#95B75D'
		)
	);


	/**
	 * @var array
	 */
	public $picking_estados_lista = array(
		'no_definido' => 'No preparado',
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
		'Administrador' => array(
			'className'				=> 'Administrador',
			'foreignKey'			=> 'administrador_id',
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
		),
		'Mensaje' => array(
			'className'				=> 'Mensaje',
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
		$total_venta = (float) 0;
		$descuento   = (!isset($this->data['Venta']['descuento'])) ? 0 : $this->data['Venta']['descuento'];
		$costo_envio = (!isset($this->data['Venta']['costo_envio'])) ? 0 : $this->data['Venta']['costo_envio'];

		if (isset($this->data['VentaDetalle'])) {

			foreach ($this->data['VentaDetalle'] as $i => $d) {

				if (!isset($d['VentaDetalle']['venta_detalle_producto_id']))
					continue;

				# Obtenemos el peso del producto
				$peso_producto = (float) ClassRegistry::init('VentaDetalleProducto')->field('peso', array('id' => $d['VentaDetalle']['venta_detalle_producto_id']));

				# El peso se multiplica pr la cantidad de itemes de la venta
				if (isset($d['VentaDetalle']['cantidad_anulada'])) {
					$peso_total    = $peso_total + round($peso_producto * ($d['VentaDetalle']['cantidad'] - $d['VentaDetalle']['cantidad_anulada']), 2);
				}else{
					$peso_total    = $peso_total + round($peso_producto * $d['VentaDetalle']['cantidad'], 2);
				}

				# Sumatoria del total de la venta
				$total_venta   = $total_venta + round($d['VentaDetalle']['total_bruto']);
			}
			
			# si viene dado el campo total bruto de los items se calcula el total en base a ello, de lo contrario se mantiene el total
			if (Hash::check($this->data['VentaDetalle'], '{n}.VentaDetalle.total_bruto')) {
				$this->data['Venta']['total'] = $total_venta - $descuento + $costo_envio;	
			}	
			
		}

		# Se actualiza peso del bulto
		if ($peso_total > 0) {
			$this->data['Venta']['peso_bulto_total'] = (float) round($peso_total, 2);
		}
		
		return true;
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
								'VentaDetalleProducto.id', 'VentaDetalleProducto.id_externo', 'VentaDetalleProducto.nombre', 'VentaDetalleProducto.codigo_proveedor', 'VentaDetalleProducto.cantidad_virtual', 'VentaDetalleProducto.stock_automatico', 'VentaDetalleProducto.ancho', 'VentaDetalleProducto.alto', 'VentaDetalleProducto.largo', 'VentaDetalleProducto.peso',
							)
						),
						'conditions' => array(
							'VentaDetalle.activo' => 1
						),
						'fields' => array(
							'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.precio_bruto', 'VentaDetalle.cantidad', 'VentaDetalle.venta_id', 'VentaDetalle.completo', 'VentaDetalle.cantidad_pendiente_entrega', 'VentaDetalle.cantidad_reservada', 'VentaDetalle.cantidad_entregada', 'VentaDetalle.confirmado_app', 'VentaDetalle.reservado_virtual', 'VentaDetalle.cantidad_anulada', 'VentaDetalle.monto_anulado', 'VentaDetalle.dte', 'VentaDetalle.total_neto', 'VentaDetalle.total_bruto', 'VentaDetalle.cantidad_en_espera', 'VentaDetalle.fecha_llegada_en_espera'
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
							'MetodoEnvio.id', 'MetodoEnvio.nombre', 'MetodoEnvio.retiro_local'
						)
					),
					'Mensaje' => array(
						'conditions' => array(
							'Mensaje.parent_id' => null,
						),
						'RespuestaMensaje' => array(
							'fields' => array(
								'RespuestaMensaje.id', 'RespuestaMensaje.parent_id', 'RespuestaMensaje.venta_cliente_id', 'RespuestaMensaje.administrador_id', 'RespuestaMensaje.venta_id', 'RespuestaMensaje.venta_detalle_producto_id', 'RespuestaMensaje.mensaje', 'RespuestaMensaje.adjunto', 'RespuestaMensaje.created', 'RespuestaMensaje.autor', 'RespuestaMensaje.origen'
							)
						),
						'Administrador' => array(
							'fields' => array(
								'Administrador.nombre',
								'Administrador.email'
							)
						),
						'VentaCliente' => array(
							'fields' => array(
								'VentaCliente.nombre',
								'VentaCliente.apellido',
								'VentaCliente.email'
							)
						),
						'fields' => array(
							'Mensaje.id', 'Mensaje.parent_id', 'Mensaje.venta_cliente_id', 'Mensaje.administrador_id', 'Mensaje.venta_id', 'Mensaje.venta_detalle_producto_id', 'Mensaje.mensaje', 'Mensaje.adjunto', 'Mensaje.created', 'Mensaje.autor', 'Mensaje.origen'
						),
						'order' => array('Mensaje.created' => 'DESC')
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
					),
					'Administrador' => array(
						'fields' => array(
							'Administrador.email', 'Administrador.nombre'
						)
					)
				),
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo', 'Venta.descuento', 'Venta.costo_envio',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.metodo_envio_id', 'Venta.venta_cliente_id', 'Venta.direccion_entrega', 'Venta.comuna_entrega', 'Venta.nombre_receptor',
					'Venta.fono_receptor', 'Venta.picking_estado', 'Venta.prioritario', 'Venta.estado_anterior', 'Venta.picking_email', 'Venta.venta_estado_responsable', 'Venta.chofer_email', 'Venta.fecha_enviado', 'Venta.fecha_entregado', 'Venta.ci_receptor', 'Venta.fecha_transito', 'Venta.etiqueta_envio_externa', 'Venta.venta_manual', 'Venta.administrador_id', 'Venta.nota_interna'
				)
			)
		);
	}


	/**
	 * [obtener_ventas_por_oc description]
	 * @param  [type] $id_oc [description]
	 * @return [type]        [description]
	 */
	public function reservar_stock_por_oc($id_oc)
	{
		$ocVentas = ClassRegistry::init('OrdenCompra')->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id_oc
			),
			'contain' => array(
				'Venta' => array(
					'VentaDetalle' => array(
						'fields' => array(
							'VentaDetalle.id'
						)
					),
					'fields' => array(
						'Venta.id'
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id'
			)
		));

		if (empty($ocVentas['Venta'])) {
			return;
		}
		
		foreach ($ocVentas['Venta'] as $iv => $venta) {
			foreach ($venta['VentaDetalle'] as $id => $d) {
				$reservado = $this->reservar_stock_producto($d['id']);
			}
		}

		return;
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
							'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.cantidad', 'VentaDetalle.cantidad_anulada', 'VentaDetalle.cantidad_pendiente_entrega', 'VentaDetalle.cantidad_reservada', 'VentaDetalle.cantidad_entregada', 'VentaDetalle.confirmado_app', 'VentaDetalle.cantidad_en_espera'
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
				'venta_detalles.venta_id = Venta.id'
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
			'contain' => array(
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.cantidad',
						'VentaDetalle.cantidad_reservada',
						'VentaDetalle.cantidad_anulada',
						'VentaDetalle.cantidad_en_espera',
						'VentaDetalle.cantidad_entregada'
					)
				)
			),
			'limit'  => $limit,
			'offset' => $offset,
			'order'  => array('Venta.prioritario' => 'desc', 'Venta.fecha_venta' => 'asc', 'Venta.modified' => 'desc'),
			'group'  => 'Venta.id',
			'fields' => array(
				'Venta.id'
			),
		));

		# Quitamos las ventas que no tengan sus itemes correcto
		foreach ($ventas as $iv => $v) {
			
			$cant_reservada = array_sum(Hash::extract($v['VentaDetalle'], '{n}.cantidad_reservada'));
			$cant_cant = array_sum(Hash::extract($v['VentaDetalle'], '{n}.cantidad')) - array_sum(Hash::extract($v['VentaDetalle'], '{n}.cantidad_anulada')) - array_sum(Hash::extract($v['VentaDetalle'], '{n}.cantidad_entregada')) - array_sum(Hash::extract($v['VentaDetalle'], '{n}.cantidad_en_espera'));

			if ($cant_reservada != $cant_cant || $cant_reservada == 0) {
				unset($ventas[$iv]);
			}

		}

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

		$joins[] = array(
			'table' => 'rp_venta_detalles',
			'alias' => 'venta_detalles',
			'type' => 'INNER',
			'conditions' => array(
				'venta_detalles.venta_id = Venta.id'
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
				ClassRegistry::init('Bodega')->crearEntradaBodega($detalle['venta_detalle_producto_id'], null, $detalle['cantidad_entregada'], $pmp, 'VT', null, $id);
				$vDetalle->saveField('cantidad_entregada', 0);
				$vDetalle->saveField('cantidad_anulada', 0);
				$vDetalle->saveField('cantidad_pendiente_entrega', $detalle['cantidad_entregada']);
				$vDetalle->saveField('completo', 0);
			}

			# Devolver unidades reservadas
			if ($detalle['cantidad_reservada'] > 0) {
				$vDetalle->saveField('cantidad_reservada', 0);
			}

			# Nuevo stock virtual
			if ($detalle['reservado_virtual']) { 
				ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($detalle['venta_detalle_producto_id'], ($detalle['cantidad'] - $detalle['cantidad_anulada']), 'aumentar');
				$vDetalle->saveField('reservado_virtual', 0);
			}
		}

		$this->cambiar_estado_picking($id, 'no_definido');
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
		#if ($venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior'] ) {
			foreach ($venta['VentaDetalle'] as $ip => $producto) {
				
				ClassRegistry::init('VentaDetalle')->id = $producto['id'];
				if ($producto['cantidad_reservada'] == 0 && $producto['cantidad_entregada'] < $producto['cantidad'] ) {
					$reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock($producto['venta_detalle_producto_id'], ($producto['cantidad'] - $producto['cantidad_anulada'] - $producto['cantidad_en_espera'] - $producto['cantidad_entregada']) );
					ClassRegistry::init('VentaDetalle')->saveField('cantidad_reservada', $reservado);
					ClassRegistry::init('VentaDetalle')->saveField('cantidad_pendiente_entrega', $producto['cantidad']);

					$venta['VentaDetalle'][$ip]['cantidad_reservada'] = $reservado;
				}				

				# Nuevo stock virtual
				if (!$producto['reservado_virtual']) { 
					$cant = $producto['cantidad'] - $producto['cantidad_anulada'];
					ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($producto['venta_detalle_producto_id'], $cant);
					ClassRegistry::init('VentaDetalle')->saveField('reservado_virtual', 1);
				}

			}
		#}
		
		$cant_reservada_sum = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_reservada'));
		$cant_vendida_sum   = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_anulada')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_en_espera')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_entregada'));

		if ( $cant_reservada_sum == $cant_vendida_sum ) {

			$picking_estado = $this->field('picking_estado');
			
			if (empty($picking_estado) || $picking_estado == 'no_definido' ) {
				$this->cambiar_estado_picking($id, 'empaquetar');
				#$this->saveField('picking_estado', 'empaquetar');
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
					$detalles[$ip]['VentaDetalle']['completo']         = ( ($venta['VentaDetalle'][$ip]['cantidad'] - $venta['VentaDetalle'][$ip]['cantidad_anulada']) == $producto['cantidad_reservada']) ? 1 : 0;
					$detalles[$ip]['VentaDetalle']['fecha_completado'] = date('Y-m-d H:i:s');

					$detalles[$ip]['VentaDetalle']['cantidad_reservada']         = 0;
					$detalles[$ip]['VentaDetalle']['cantidad_entregada']         = $producto['cantidad_reservada'];
					$detalles[$ip]['VentaDetalle']['cantidad_pendiente_entrega'] = ($producto['cantidad'] - $producto['cantidad_anulada']) - $producto['cantidad_reservada'];

					ClassRegistry::init('Bodega')->crearSalidaBodega($producto['venta_detalle_producto_id'], null, $producto['cantidad_reservada'], null, 'VT', null, $id);

				}else if ($producto['cantidad_entregada'] == ($producto['cantidad'] - $producto['cantidad_anulada'])) {

					$detalles[$ip]['VentaDetalle'] = $producto;

					continue;

				}else{
					throw new Exception("No se permite entregar un pedido que no tiene los productos reservados.", 1);
					
					$reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock($producto['venta_detalle_producto_id'], $producto['cantidad']);
					
					$detalles[$ip]['VentaDetalle']['id']                         = $producto['id'];
					$detalles[$ip]['VentaDetalle']['cantidad_reservada']         = $reservado;
					$detalles[$ip]['VentaDetalle']['cantidad_pendiente_entrega'] = $producto['cantidad'] - $producto['cantidad_anulada'];
				
				}

				# Nuevo stock virtual
				if ($producto['reservado_virtual']) { 
					ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($producto['venta_detalle_producto_id'], ($producto['cantidad'] - $producto['cantidad_anulada']) );
					$detalles[$ip]['VentaDetalle']['reservado_virtual'] = 1;
				}

			}
			
			# Guardamos los cambios
			ClassRegistry::init('VentaDetalle')->saveMany($detalles);

		}

		# Pedido está entregado completo
		$this->cambiar_estado_picking($id, 'empaquetado');
					
		$this->saveField('subestado_oc', 'entregado');
		$this->saveField('fecha_entregado', date('Y-m-d H:i:s'));		

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

		$this->cambiar_estado_picking($id, 'no_definido');
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

		$cant_reservada = ClassRegistry::init('VentaDetalle')->field('cantidad_reservada');
		$cant_cant      = ClassRegistry::init('VentaDetalle')->field('cantidad') - ClassRegistry::init('VentaDetalle')->field('cantidad_anulada');
		$cant_entregada = ClassRegistry::init('VentaDetalle')->field('cantidad_entregada');
		$cant_en_espera = ClassRegistry::init('VentaDetalle')->field('cantidad_en_espera');
		$fecha_llegada  = ClassRegistry::init('VentaDetalle')->field('fecha_llegada_en_espera');

		if ($cant_cant == $cant_entregada) {
			return 0;
		}

		$reservar = $cant_cant - $cant_reservada - $cant_entregada;
		
		$disponible = ClassRegistry::init('Bodega')->calcular_reserva_stock(ClassRegistry::init('VentaDetalle')->field('venta_detalle_producto_id'), $reservar);
		$reservado  = $cant_reservada + $disponible;

		# Solo se reserva si la cantidad reservada es distinta a la cantidad comprada por el cliente
		if ($cant_reservada != $cant_cant ) {
			
			$cant_cant = $cant_cant - $cant_entregada;

			$save = array(
				'VentaDetalle' => array(
					'id' => $id,
					'cantidad_reservada' => $reservado
				)
			);

			if ($cant_en_espera == $reservado && $reservado != $cant_cant) {
				$save['VentaDetalle']['cantidad_en_espera'] = $cant_cant - $reservado;
			}
			else if ($reservado == 0) {
				$save['VentaDetalle']['cantidad_en_espera'] = $cant_en_espera;	
			}
			else if ($cant_en_espera < $reservado && $reservado != $cant_cant){
				$save['VentaDetalle']['cantidad_en_espera'] = $cant_cant - $reservado;
			}
			else if ($cant_en_espera > $reservado && $reservado != $cant_cant){
				$save['VentaDetalle']['cantidad_en_espera'] = $cant_en_espera - $reservado;
			}
			else if ($reservado == $cant_cant && $reservado == $cant_cant) {
				$save['VentaDetalle']['cantidad_en_espera'] = 0;
			}

			if (empty($fecha_llegada)) {
				unset($save['VentaDetalle']['cantidad_en_espera']);
			}
			
			if(!ClassRegistry::init('VentaDetalle')->save($save))
				return 0;
		}

		$venta = $this->obtener_venta_por_id(ClassRegistry::init('VentaDetalle')->field('venta_id'));
		
		$total_cantidad = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_anulada')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_entregada')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_en_espera'));
		$total_reservado = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_reservada'));

		if ( $total_reservado == $total_cantidad && $total_reservado > 0) {
			$this->id = $venta['Venta']['id'];

			$picking_estado = $this->field('picking_estado');

			if (empty($picking_estado) || $picking_estado == 'no_definido' || $picking_estado == 'empaquetado' ) {
				$this->saveField('picking_estado', 'empaquetar');
			}
			$this->saveField('subestado_oc', 'no_entregado');
		}

		return $reservado;
	}


	/**
	 * Cambiar estado picking de una venta y registra las fechas de cambios
	 * @param  int 		$id             ID de la venta
	 * @param  string   $picking_estado 'no_definido', 'empaquetar', 'empaquetando', 'empaquetado'
	 * @param  string   $picking_email  Email de quien empaqueta (obligatorio para estado empaquetando)
	 * @return bool
	 */
	public function cambiar_estado_picking($id, $picking_estado, $picking_email = '')
	{
		$save = array(
			'Venta' => array(
				'id' => $id,
				'picking_estado' => $picking_estado
			)
		);

		if (!empty($picking_email)) {
			$save = array_replace_recursive($save, array('Venta' => array('picking_email' => $picking_email) ));
		}

		switch ($picking_estado) {
			case 'no_definido':
				$save = array_replace_recursive($save, array('Venta' => array('picking_email' => '', 'picking_fecha_inicio' => '', 'picking_fecha_temrino' => '')));
				break;
			
			case 'empaquetar':
				$save = array_replace_recursive($save, array('Venta' => array('picking_email' => '', 'picking_fecha_inicio' => '', 'picking_fecha_temrino' => '')));
				break;

			case 'empaquetando':

				# emails es obligatorio en empaquetando
				if (empty($picking_email))
					return false;
				
				$save = array_replace_recursive($save, array('Venta' => array('picking_fecha_inicio' => date('Y-m-d H:i:s'))));
				break;
			case 'empaquetado':
				$save = array_replace_recursive($save, array('Venta' => array('picking_fecha_termino' => date('Y-m-d H:i:s'))));
				break;
		}

		return $this->save($save, array('callbacks' => false));

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


	public function generar_referencia()
	{
		$ref = strtoupper(bin2hex(openssl_random_pseudo_bytes(3)));

		while (!self::referencia_disponible($ref)) {
			$ref = bin2hex(openssl_random_pseudo_bytes(3));
		}

		return $ref;
	}

	private function referencia_disponible($ref)
	{
		$venta = $this->find('count', array(
			'conditions' => array(
				'Venta.referencia' => $ref
			)
		));

		if ($venta > 1) {
			return false;
		}

		return true;
	}


	public function obtener_ventas_productos_retraso_ids()
	{	

		$ventasRestrasos =  ClassRegistry::init('VentaDetalle')->find('all', array(
			'joins' => array(
				array(
					'table' => 'rp_ventas',
					'alias' => 'ventas',
					'type' => 'INNER',
					'conditions' => array(
						'ventas.id = VentaDetalle.venta_id'
					)
				),
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'venta_estados',
					'type' => 'INNER',
					'conditions' => array(
						'venta_estados.id = ventas.venta_estado_id'
					)
				),
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'venta_estados_cat',
					'type' => 'INNER',
					'conditions' => array(
						'venta_estados_cat.id = venta_estados.venta_estado_categoria_id',
						'venta_estados_cat.venta = 1',
						'venta_estados_cat.envio = 0',
						'venta_estados_cat.final = 0'
					)
				)
			),
			'conditions' => array(
				'VentaDetalle.cantidad_en_espera >' => 0
			),
			'fields' => array(
				'VentaDetalle.venta_id'
			)
		));

		$ventasStockout = ClassRegistry::init('OrdenComprasVenta')->find('all', array(
			'joins' => array(
				array(
					'table' => 'rp_orden_compras',
					'alias' => 'oc',
					'type' => 'INNER',
					'conditions' => array(
						'oc.id = OrdenComprasVenta.orden_compra_id'
					)
				),
				array(
					'table' => 'rp_orden_compras',
					'alias' => 'oc_2',
					'type' => 'INNER',
					'conditions' => array(
						'oc_2.parent_id = oc.id'
					)
				),
				array(
					'table' => 'rp_orden_compras_venta_detalle_productos',
					'alias' => 'oc_productos',
					'type' => 'INNER',
					'conditions' => array(
						'oc_productos.orden_compra_id = oc_2.id',
						'oc_productos.estado_proveedor != "accept"'
					)
				),
				array(
					'table' => 'rp_venta_detalles',
					'alias' => 'vd',
					'type' => 'INNER',
					'conditions' => array(
						'vd.venta_id = OrdenComprasVenta.venta_id',
						'vd.cantidad_anulada < vd.cantidad'
					)
				),
				array(
					'table' => 'rp_ventas',
					'alias' => 'ventas',
					'type' => 'INNER',
					'conditions' => array(
						'ventas.id = vd.venta_id'
					)
				),
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'venta_estados',
					'type' => 'INNER',
					'conditions' => array(
						'venta_estados.id = ventas.venta_estado_id'
					)
				),
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'venta_estados_cat',
					'type' => 'INNER',
					'conditions' => array(
						'venta_estados_cat.id = venta_estados.venta_estado_categoria_id',
						'venta_estados_cat.venta = 1',
						'venta_estados_cat.envio = 0',
						'venta_estados_cat.final = 0'
					)
				)
			),
			'fields' => array('OrdenComprasVenta.venta_id')
		));

		$ids_1 = Hash::extract($ventasRestrasos, '{n}.VentaDetalle.venta_id');
		$ids_2 = Hash::extract($ventasStockout, '{n}.OrdenComprasVenta.venta_id');
		$ids   = array_unique(array_merge($ids_1, $ids_2));


		return $ids;
	}



	public function obtener_ventas_sin_reserva()
	{
		$filter = array(
			'joins' => array(
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'estado',
					'type' => 'INNER',
					'conditions' => array(
						'estado.id = Venta.venta_estado_id'
					)
				),
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'estado_categoria',
					'type' => 'INNER',
					'conditions' => array(
						'estado_categoria.id = estado.venta_estado_categoria_id',
						'estado_categoria.venta = 1',
						'estado_categoria.final = 0'
					)
				)
			),
			'contain' => array(
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.id'
					)
				)
			),
			'conditions' => array(
				'Venta.fecha_venta >=' => date("Y-m-d H:i:s",strtotime(date('Y-m-d')."-1 month"))
			),
			'order' => array('Venta.fecha_venta' => 'ASC'),
			'fields' => array(
				'Venta.id'
			)
		);

		return $this->find('all', $filter);

	}
}
