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
		'en_revision' => array(
			'label' => 'En revisión manual',
			'leyenda' => 'Para que todo vaya bien, estamos revisando manualmente tu pedido.',
			'color' => '#e0694d'
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
	public $canal_venta_manual = array(
		'cotizacion' => 'Cotización',
		'telefono' => 'Teléfono',
		'correo' => 'Correo',
		'facebook' => 'RRSS Facebook',
		'meson' => 'Mesón',
		'chat' => 'Chat (Tawk)',
		'whatsapp' => 'Wahtsapp',
		'contacto' => 'Contacto Web',
		'cambio_por_ndc' => 'Cambio por NDC'
	);


	/**
	 * @var array
	 */
	public $picking_estados_lista = array(
		'no_definido' => 'No preparado',
		'en_revision' => 'En revisión manual',
		'empaquetar'  => 'Listo para embalar',
		'empaquetando' => 'En prepración',
		'empaquetado'  => 'Embalaje finalizado'
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
		'Comuna' => array(
			'className'				=> 'Comuna',
			'foreignKey'			=> 'comuna_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'VentaEstado')
		),
		'Bodega' => array(
			'className'				=> 'Bodega',
			'foreignKey'			=> 'bodega_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
		),
		'CanalVenta' => array(
			'className'				=> 'CanalVenta',
			'foreignKey'			=> 'canal_venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
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
		),
		'EmbalajeWarehouse' => array(
			'className'				=> 'EmbalajeWarehouse',
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
		'TransportesVenta' => array(
			'className'				=> 'TransportesVenta',
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
		'HistorialEmbalaje' => array(
			'className'				=> 'HistorialEmbalaje',
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
		),
		'VentaEstado2' => array(
			'className'				=> 'VentaEstado',
			'joinTable'				=> 'estados_ventas',
			'foreignKey'			=> 'venta_id',
			'associationForeignKey'	=> 'venta_estado_id',
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


				# Si la venta ya tiene agregado el item se descarta
				if (!isset($d['VentaDetalle']['id']) && isset($this->data['Venta']['id']))
				{
					$existe = ClassRegistry::init('VentaDetalle')->find('first', array(
						'conditions' => array(
							'VentaDetalle.venta_id' => $this->data['Venta']['id'],
							'VentaDetalle.venta_detalle_producto_id' => $d['VentaDetalle']['venta_detalle_producto_id']
						)
					));

					# si ya est
					if ($existe)
					{
						unset($this->data['VentaDetalle'][$i]);
						continue;
					}
				}

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

				if ($this->data['Venta']['total'] < 0)
					$this->data['Venta']['total'] = (float) 0;

			}	
			
		}

		# Se actualiza peso del bulto
		if ($peso_total > 0) {
			$this->data['Venta']['peso_bulto_total'] = (float) round($peso_total, 2);
		}
		
		return true;
	}


	public function afterSave($created, $options = array())
	{	
		
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
					
					'HistorialEmbalaje',
					'VentaDetalle' => array(
						'HistorialEmbalaje',
						'EmbalajeProductoWarehouse' => array(
							'EmbalajeWarehouse'
						),
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.id', 'VentaDetalleProducto.id_externo', 'VentaDetalleProducto.nombre', 'VentaDetalleProducto.codigo_proveedor', 'VentaDetalleProducto.cantidad_virtual', 'VentaDetalleProducto.stock_automatico', 'VentaDetalleProducto.ancho', 'VentaDetalleProducto.alto', 'VentaDetalleProducto.largo', 'VentaDetalleProducto.peso',
							)
						),
						'Atributo' => array(
							'AtributoGrupo'
						),
						'VentaDetallesReserva'=>[
							'fields'=>['VentaDetallesReserva.cantidad_reservada',"VentaDetallesReserva.venta_detalle_id","VentaDetallesReserva.venta_detalle_producto_id","VentaDetallesReserva.embalaje_id","VentaDetallesReserva.bodega_id"]
						],
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
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.plantilla', 'VentaEstadoCategoria.reserva_stock'
							)
						),
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.venta_estado_categoria_id', 'VentaEstado.permitir_dte', 'VentaEstado.nombre', 'VentaEstado.notificacion_cliente'
						)
					),
					'VentaTransaccion',
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre', 'Tienda.rut', 'Tienda.fono', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.logo', 'Tienda.direccion', 'Tienda.facturacion_apikey', 'Tienda.emails_bcc', 'Tienda.url', 'Tienda.direccion', 'Tienda.stock_automatico', 'Tienda.activo_enviame', 'Tienda.apihost_enviame', 'Tienda.apikey_enviame', 'Tienda.company_enviame', 'Tienda.bodega_enviame', 'Tienda.meta_ids_enviame', 'Tienda.peso_enviame', 'Tienda.volumen_enviame', 'Tienda.mandrill_apikey', 'Tienda.starken_rut', 'Tienda.starken_clave'
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
							'MetodoEnvio.*'
						),
						'Bodega' => [
							'Comuna',
							'fields' => [
								'Bodega.nombre',
								'Bodega.nombre_contacto',
								'Bodega.fono',
								'Bodega.direccion',
								'Bodega.comuna_id'
							],
						],
						'BodegasMetodoEnvio'
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
					'VentaEstado2' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.plantilla'
							)
						),
						'fields' => array(
							'VentaEstado2.id', 'VentaEstado2.venta_estado_categoria_id', 'VentaEstado2.nombre'
						),
						'order' => array('EstadosVenta.fecha' => 'DESC')
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
						'order' => array('Dte.fecha' => 'DESC')
					),
					'Administrador' => array(
						'fields' => array(
							'Administrador.email', 'Administrador.nombre'
						)
					),
					'OrdenCompra' => array(
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.id'
							)
						),
						'fields' => array(
							'OrdenCompra.id',
							'OrdenCompra.estado',
							'OrdenCompra.fecha_validado_proveedor'
						)
					),
					'EmbalajeWarehouse' => array(
						'HistorialEmbalaje',
						'Bodega' => array(
							'fields' => array(
								'Bodega.id',
								'Bodega.nombre'
							)
						),
						'EmbalajeProductoWarehouse' => array(
							'VentaDetalleProducto' => array(
								'fields' => array(
									'VentaDetalleProducto.id',
									'VentaDetalleProducto.nombre',
									'VentaDetalleProducto.alto',
									'VentaDetalleProducto.ancho',
									'VentaDetalleProducto.largo',
									'VentaDetalleProducto.peso'
								)
							),
						)
					),
					'Comuna' => [
						'fields' => [
							'Comuna.district_id_blue_express',
							'Comuna.state_id_blue_express'
						]
					],
					'CanalVenta' => array(
						'fields' => array(
							'CanalVenta.id',
							'CanalVenta.nombre'
						)
					),
					'Bodega' => array(
						'fields' => array(
							'Bodega.nombre',
							'Bodega.direccion',
							'Bodega.horario_atencion'
						)
					)
				),
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo', 'Venta.descuento', 'Venta.costo_envio',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.metodo_envio_id', 'Venta.venta_cliente_id', 'Venta.direccion_entrega', 'Venta.numero_entrega', 'Venta.otro_entrega', 'Venta.comuna_entrega', 'Venta.ciudad_entrega', 'Venta.nombre_receptor', 'Venta.rut_receptor',
					'Venta.fono_receptor', 'Venta.picking_estado', 'Venta.prioritario', 'Venta.estado_anterior', 'Venta.picking_email', 'Venta.venta_estado_responsable', 'Venta.chofer_email', 'Venta.fecha_enviado', 'Venta.fecha_entregado', 'Venta.ci_receptor', 'Venta.fecha_transito', 'Venta.etiqueta_envio_externa', 
					'Venta.venta_manual', 'Venta.administrador_id', 'Venta.nota_interna', 'Venta.paquete_generado', 'Venta.comuna_id', 'Venta.picking_motivo_revision', 'Venta.origen_venta_manual' ,'Venta.referencia_despacho' ,'Venta.bodega_id', 'Venta.canal_venta_id'
				)
			)
		);
	}


	/**
	 * [obtener_ventas_por_oc description]
	 * @param  [type] $id_oc [description]
	 * @return [type]        [description]
	 */
	// ! Se mueve metodo a controllador OrdenComprasController 
	// ! No usar este metodo desde acá, sino desde el controllador
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
					),
					'order' => array('Venta.fecha_venta' => 'ASC')
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
					'Transporte' => array(
						'fields' => array(
							'Transporte.nombre'
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


	public function obtener_venta_ot($id)
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
							'Tienda.id', 'Tienda.nombre', 'Tienda.rut'
						)
					),
					'MetodoEnvio' => array(
						'fields' => array(
							'MetodoEnvio.nombre', 'MetodoEnvio.dependencia', 'MetodoEnvio.rut_api_rest', 'MetodoEnvio.clave_api_rest', 'MetodoEnvio.rut_empresa_emisor', 'MetodoEnvio.rut_usuario_emisor', 'MetodoEnvio.clave_usuario_emisor', 'MetodoEnvio.tipo_entrega', 'MetodoEnvio.tipo_pago', 'MetodoEnvio.numero_cuenta_corriente', 'MetodoEnvio.dv_numero_cuenta_corriente', 'MetodoEnvio.centro_costo_cuenta_corriente', 'MetodoEnvio.tipo_servicio', 'MetodoEnvio.generar_ot'
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
	// ! Metodo obsoleto y en desuzo
	public function obtener_ventas_preparar($estado = '', $limit = -1, $offset = 0, $estados_ids = array(), $id_venta = 0, $id_metodo_envio = 0, $id_marketplace = 0, $id_tienda = 0, $comuna = '')
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

		if ($comuna) {
			$conditions = array_replace_recursive($conditions, array(
				'Venta.comuna_entrega' => $comuna
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

			$bodega_id = ClassRegistry::init('Bodega')->find('first', array(
				'conditions' => array(
					'Bodega.principal' => 1
				), 
				'limit' => 1, 
				'fields' => array(
					'Bodega.id'
				)
			))['Bodega']['id'];
			
			$pmp 	  = ClassRegistry::init('Bodega')->obtener_pmp_por_producto_bodega($detalle['venta_detalle_producto_id'], $bodega_id);
			$vDetalle = ClassRegistry::init('VentaDetalle');

			$vDetalle->id = $detalle['id'];

			$vDetalle->saveField('cantidad_entregada', 0);
			$vDetalle->saveField('cantidad_en_espera', 0);
			$vDetalle->saveField('fecha_llegada_en_espera', '');
			$vDetalle->saveField('cantidad_pendiente_entrega', ($detalle['cantidad'] - $detalle['cantidad_anulada']) );
			$vDetalle->saveField('completo', 0);

			# Devolver stock a bodega
			# En teoria la unica forma de devolver a stock es por medio de una Nota de credito
			if ($detalle['cantidad_entregada'] > 0) 
			{	
				#ClassRegistry::init('Bodega')->crearEntradaBodega($detalle['venta_detalle_producto_id'], null, $detalle['cantidad_entregada'], $pmp, 'VT', null, $id);
			}
			
			// TODO Quitamos las reservas
			ClassRegistry::init('VentaDetallesReserva')->updateAll(['VentaDetallesReserva.cantidad_reservada'=> 0 ], ['VentaDetallesReserva.venta_detalle_id'=> $detalle['id']]);
			
			# Nuevo stock virtual
			if ($detalle['reservado_virtual']) 
			{ 
				#ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($detalle['venta_detalle_producto_id'], ($detalle['cantidad'] - $detalle['cantidad_anulada']), 'aumentar');
				$vDetalle->saveField('reservado_virtual', 0);
			}
		}

		$this->cambiar_estado_picking($id, 'no_definido');
		
		# Preparamos los embalajes
		// ClassRegistry::init('EmbalajeWarehouse')->procesar_embalajes($id);

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

		$log = array();

		$venta = $this->obtener_venta_por_id($id);	

		$log[] = array(
			'Log' => array(
				'administrador' => 'Inicia pagar venta ' . $id,
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($venta)
			)
		);
		
		if ($venta['VentaEstado']['VentaEstadoCategoria']['reserva_stock'] == false) {

			$log[] = array(
				'Log' => array(
					'administrador' => "No se han realizado reservas debido a la categoría ({$venta['VentaEstado']['venta_estado_categoria_id']}) del estado ({$venta['VentaEstado']['id']}) de la venta {$id}" ,
					'modulo'        => 'Ventas',
					'modulo_accion' => json_encode($venta)
				)
			);

			# Guardamos el log
			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			
			return;
		}
		$VentaDetallesReserva = [];
		foreach ($venta['VentaDetalle'] as $ip => $producto) 
		{
			
			ClassRegistry::init('VentaDetalle')->id = $producto['id'];

			// $cantidad_entregada = 0;

			// # Obtenemos los movimientos del productos en esta venta
			// $cantidad_mv = ClassRegistry::init('Bodega')->obtener_total_mv_por_venta($id, $producto['venta_detalle_producto_id']);
			
			// # tiene salida
			// if ($cantidad_mv < 0)
			// {
			// 	$cantidad_entregada = ($cantidad_mv * -1);
			// }

			// # Guardamos las cantidades entregadas
			// ClassRegistry::init('VentaDetalle')->saveField('cantidad_entregada', $cantidad_entregada);
			// $venta['VentaDetalle'][$ip]['cantidad_entregada'] = $cantidad_entregada;

			# Calculamos las unidades que se deben reservar
			$cantidad_vendida   = ($producto['cantidad'] - $producto['cantidad_anulada'] - $producto['cantidad_en_espera']);
			$cantidad_reservar  = $cantidad_vendida - $producto['cantidad_entregada'];
			
			$log[] = array(
				'Log' => array(
					'administrador' => 'Reservar producto detalle ' . $producto['id'],
					'modulo' => 'Ventas',
					'modulo_accion' => 'Cantidad a reservar:' . $cantidad_reservar
				)
			);

			$suma_cant_reservada_y_por_reservar = 0;
			# Reservamos
			if ( $cantidad_reservar > 0 ) 
			{

				$cantidad_reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock($producto['venta_detalle_producto_id'],  $cantidad_reservar, $venta['Venta']['bodega_id']);
				
				$suma_cant_reservada_y_por_reservar = $suma_cant_reservada_y_por_reservar + $cantidad_reservado;

				if ($cantidad_reservado > 0) {
					
					$VentaDetallesReserva [] = [
						'VentaDetallesReserva' => 
						[
							'venta_detalle_id'	       	=> $producto['id'],
							'bodega_id'			       	=> $venta['Venta']['bodega_id'],
							'cantidad_reservada'       	=> $cantidad_reservado,
							'venta_detalle_producto_id' => $producto['venta_detalle_producto_id'],
							
						]
					];
				}
				
				// TODO Si aun no se termina de reservar el total y el metodo de envio permite buscar reserva en otras bodegas se procede a consultar stock en dichas
				if ($venta['MetodoEnvio']['permitir_reservar_stock_otra_bodega'] && $suma_cant_reservada_y_por_reservar < $cantidad_vendida  ) {
			
					foreach ($venta['MetodoEnvio']['BodegasMetodoEnvio'] as $value) {
		
						// TODO Como ya se hizo una reserva se trata de reservar lo que falta
						$cantidad_reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock($producto['venta_detalle_producto_id'], ($cantidad_vendida - $suma_cant_reservada_y_por_reservar ), $value['bodega_id']);
						
						// TODO Si se encuentra stock en otras bodegas no se continua buscando 
						if ($cantidad_reservado > 0 ) {

							$suma_cant_reservada_y_por_reservar = $suma_cant_reservada_y_por_reservar + $cantidad_reservado;
							$bodega_a_reservar 	  				= $value['bodega_id'];
							$VentaDetallesReserva [] 			= [
								'VentaDetallesReserva' => 
								[
									'venta_detalle_id'	       	=> $producto['id'],
									'bodega_id'			       	=> $bodega_a_reservar,
									'cantidad_reservada'       	=> $cantidad_reservado,
									'venta_detalle_producto_id' => $producto['venta_detalle_producto_id'],
									
								]
							];
							// TODO Si ya reserve el total se termina de recorrer bodegas
							if ($suma_cant_reservada_y_por_reservar == $cantidad_vendida) {
								break;
							}
							
						}
					}
				}

				$log[] = array(
					'Log' => array(
						'administrador' => 'Reservar producto detalle ' . $producto['id'],
						'modulo' 		=> 'Ventas',
						'modulo_accion' => 'Cantidad disponible para reservar:' . $cantidad_reservado
					)
				);

				ClassRegistry::init('VentaDetalle')->saveField('cantidad_pendiente_entrega', $suma_cant_reservada_y_por_reservar);
				
			}				

			# Nuevo stock virtual
			if (!$producto['reservado_virtual']) 
			{ 
				$cant = $producto['cantidad'] - $producto['cantidad_anulada'];
				ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($producto['venta_detalle_producto_id'], $cant);
				ClassRegistry::init('VentaDetalle')->saveField('reservado_virtual', $cant);
			}

		}

		if ($VentaDetallesReserva) {
			ClassRegistry::init('VentaDetallesReserva')->create();
			ClassRegistry::init('VentaDetallesReserva')->saveMany($VentaDetallesReserva);
		}

		

		$log[] = array(
			'Log' => array(
				'administrador' => 'Finaliza pagar venta ' . $id,
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($venta)
			)
		);

		# Guardamos el log
		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);
		
		// ! Calculamos a partir de la nueva tabla para reservar stock
		# Calculamos el total de unidades reservadas de la venta
		$cant_reservada_sum = array_sum(Hash::extract($VentaDetallesReserva, '{n}.VentaDetallesReserva.cantidad_reservada'));
		$cant_anulada_sum 	= array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_anulada'));
		$cant_en_espera_sum = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_en_espera'));
		$cant_entregada_sum = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_entregada'));
		$cant_vendida_sum   = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')) - $cant_anulada_sum - $cant_en_espera_sum - $cant_entregada_sum;

		$picking_estado = $this->field('picking_estado');

		# Pasamos a picking
		if ( $cant_reservada_sum == $cant_vendida_sum && $cant_vendida_sum > 0) 
		{
			if (empty($picking_estado) || $picking_estado == 'no_definido' ) 
			{	
				$this->cambiar_estado_picking($id, 'empaquetar');
			}

			$this->saveField('subestado_oc', 'no_entregado');
		}
		elseif ($picking_estado == 'no_definido')
		{
			$this->cambiar_estado_picking($id, 'no_definido'); // Creamos embalaje
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

		if (!$this->exists()) 
		{
			return false;
		}

		$log = [];

		$venta = $this->obtener_venta_por_id($id);
		
		$log[] = array(
			'Log' => array(
				'administrador' => 'Inicia entregar venta ' . $id,
				'modulo' 		=> 'Ventas',
				'modulo_accion' => json_encode($venta)
			)
		);

		$HuboCambios = false;
		$embalajes 	 = Hash::extract($venta['EmbalajeWarehouse'], "{n}[estado=finalizado]");
		$fecha       = date('Y-m-d H:i:s');

		foreach ($embalajes as $embalaje) 
		{	
			foreach ($embalaje['EmbalajeProductoWarehouse'] as $producto) {

				$detalle = ClassRegistry::init('VentaDetalle')->find('first',
					[
						'fields'=>[ 
							'VentaDetalle.id',
							'VentaDetalle.completo',
							'VentaDetalle.cantidad',
							'VentaDetalle.cantidad_entregada',
							'VentaDetalle.cantidad_pendiente_entrega',
							'VentaDetalle.cantidad_anulada',
							'VentaDetalle.reservado_virtual',
						],
						'conditions' => ['VentaDetalle.id'=> $producto['detalle_id']]
					]
				);

				$VentaDetallesReserva = ClassRegistry::init('VentaDetallesReserva')->find('first',[
					'fields'=>
					[ 
						'VentaDetallesReserva.id',
						'VentaDetallesReserva.venta_detalle_id',
						'VentaDetallesReserva.venta_detalle_producto_id',
						'VentaDetallesReserva.cantidad_reservada',
						'VentaDetallesReserva.bodega_id',
						'VentaDetallesReserva.embalaje_id',
					],
					'conditions' => [
						"VentaDetallesReserva.venta_detalle_id" 			=> $producto['detalle_id'] ,
						"VentaDetallesReserva.venta_detalle_producto_id" 	=> $producto['producto_id'] ,
						"VentaDetallesReserva.bodega_id" 					=> $producto['EmbalajeWarehouse']['bodega_id'] ,
						"VentaDetallesReserva.cantidad_reservada"			=> $producto['cantidad_embalada'] ,
						"VentaDetallesReserva.embalaje_id"					=> null
					]
				]);

				// ! Si no retorna la VentaDetallesReserva es porque ya fue procesada y no hay que quitar la reserva
				if (is_null($VentaDetallesReserva['VentaDetallesReserva']['id']) ) {
					continue;
				}

				$log[] = array(
					'Log' => array(
						'administrador' => 'Se quita reserva usada en embalaje ' .$embalaje['id'],
						'modulo' 		=> 'Ventas',
						'modulo_accion' => json_encode(
							[
								'embalaje' 				=> $embalaje,
								'detalle'  				=> $detalle,
								'VentaDetallesReserva' 	=> $VentaDetallesReserva
							]
						)
					)
				);

				$cantidad_pendiente_entrega = ( $detalle['VentaDetalle']['cantidad_pendiente_entrega'] - $producto['cantidad_embalada'] );

				$actualizarDetalle = [ 
					'VentaDetalle' => [
						'id' 						 => $producto['detalle_id'],
						'completo'					 => ($cantidad_pendiente_entrega == 0),
						'fecha_completado'			 => ($cantidad_pendiente_entrega == 0) ? $fecha : null,
						'cantidad_entregada'		 => ($detalle['VentaDetalle']['cantidad_entregada'] + $producto['cantidad_embalada']),
						'cantidad_pendiente_entrega' => $cantidad_pendiente_entrega,
					]
				];
				
				$VentaDetallesReserva['VentaDetallesReserva']['cantidad_reservada'] = $VentaDetallesReserva['VentaDetallesReserva']['cantidad_reservada'] - $producto['cantidad_embalada'];
				$VentaDetallesReserva['VentaDetallesReserva']['embalaje_id'] 		= $embalaje['id'];

				$log[] = array(
					'Log' => array(
						'administrador' => 'Se quita reserva usada en embalaje ' .$embalaje['id'],
						'modulo' 		=> 'Ventas',
						'modulo_accion' => json_encode(
							[
								'Despues de quitar reserva' 	=> $VentaDetallesReserva
							]
						)
					)
				);
				# Nuevo stock virtual
				if ($detalle['VentaDetalle']['reservado_virtual']) 
				{ 
					ClassRegistry::init('VentaDetalleProducto')->actualizar_stock_virtual($producto['producto_id'], $producto['cantidad_embalada']  );
				}

				# Se saca el producto de la bodega
				ClassRegistry::init('Bodega')->crearSalidaBodega($producto['producto_id'],  $producto['EmbalajeWarehouse']['bodega_id'] , $producto['cantidad_embalada'], null, 'VT', null, $id);
				ClassRegistry::init('VentaDetallesReserva')->saveAll($VentaDetallesReserva);
				ClassRegistry::init('VentaDetalle')->saveAll($actualizarDetalle);
				$HuboCambios = true;
				

			}
		
		}

		if($HuboCambios){

			$this->cambiar_estado_picking($id, 'empaquetado');
			$this->saveField('subestado_oc', 'entregado');
			$this->saveField('fecha_entregado',$fecha);		
		}

		# Guardamos el log
		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

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
			),
		));	
		foreach ($ventaDetalles as $ip => $producto) {
			ClassRegistry::init('VentaDetalle')->id = $producto['VentaDetalle']['id'];

			// TODO Quitamos las reservas
			ClassRegistry::init('VentaDetallesReserva')->updateAll(['VentaDetallesReserva.cantidad_reservada'=> 0 ], ['VentaDetallesReserva.venta_detalle_id'=> $producto['VentaDetalle']['id']]);

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
		$log = array();
		
		$ventaDetalle     = ClassRegistry::init('VentaDetalle')->find('first', array(
			'conditions' => array(
				'VentaDetalle.id' => $id
			),
			'fields' => array(
				'VentaDetalle.id',
				'VentaDetalle.venta_id',
				'VentaDetalle.venta_detalle_producto_id',
				'VentaDetalle.cantidad',
				'VentaDetalle.cantidad_anulada',
				'VentaDetalle.cantidad_entregada',
				'VentaDetalle.cantidad_en_espera',
				'VentaDetalle.fecha_llegada_en_espera'
			),
			'contain' => [
				'VentaDetallesReserva'=>[
					'fields'=>[ 
							'VentaDetallesReserva.venta_detalle_id',
							'VentaDetallesReserva.venta_detalle_producto_id',
							'VentaDetallesReserva.cantidad_reservada',
							'VentaDetallesReserva.bodega_id',
						]
					]
				]
		));

		$log[] = array(
			'Log' => array(
				'administrador' => 'Producto reservar inicia ' . $id,
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($ventaDetalle)
			)
		);
		
		if (empty($ventaDetalle)) 
		{
			return 0;
		}

		# Venta del detalle
		$venta = $this->find('first', array(
			'conditions' => array(
				'Venta.id' => $ventaDetalle['VentaDetalle']['venta_id']
			),
			'contain' => array(
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.cantidad',
						'VentaDetalle.cantidad_anulada',
						'VentaDetalle.cantidad_entregada',
						'VentaDetalle.cantidad_en_espera',
					),
					'VentaDetallesReserva'=>[
						'fields'=>[ 
								'VentaDetallesReserva.venta_detalle_id',
								'VentaDetallesReserva.venta_detalle_producto_id',
								'VentaDetallesReserva.cantidad_reservada',
								'VentaDetallesReserva.bodega_id',
							]
						]
					),
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.id','VentaEstadoCategoria.reserva_stock'
						)
					),
					'fields' => array(
						'VentaEstado.id'
					)
				),
				'MetodoEnvio' => [
					'fields' =>[ 'MetodoEnvio.permitir_reservar_stock_otra_bodega'],
					'BodegasMetodoEnvio'
					]
			),
			'fields' => array(
				'Venta.id',
				'Venta.picking_estado',
				'Venta.venta_estado_id',
				'Venta.bodega_id'
			)
		));

		if ($venta['VentaEstado']['VentaEstadoCategoria']['reserva_stock'] == false) {

			$log[] = array(
				'Log' => array(
					'administrador' => "No se han realizado reservas debido a la categoría ({$venta['VentaEstado']['venta_estado_categoria_id']}) del estado ({$venta['VentaEstado']['id']}) de la venta {$ventaDetalle['VentaDetalle']['venta_id']}" ,
					'modulo'        => 'Ventas',
					'modulo_accion' => json_encode($venta)
				)
			);

			# Guardamos el log
			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			return 0;
		
		}
		
		$cant_reservada = array_sum( Hash::extract($ventaDetalle['VentaDetallesReserva'], "{n}.cantidad_reservada"));
		$cant_vendida   = $ventaDetalle['VentaDetalle']['cantidad'] - $ventaDetalle['VentaDetalle']['cantidad_anulada'];
		$cant_entregada = $ventaDetalle['VentaDetalle']['cantidad_entregada'];
		$cant_en_espera = $ventaDetalle['VentaDetalle']['cantidad_en_espera'];
		$fecha_llegada  = $ventaDetalle['VentaDetalle']['fecha_llegada_en_espera'];

		if ($cant_vendida == $cant_entregada) {
			return 0;
		}

		$suma_cant_reservada_y_por_reservar = $cant_reservada;
		# Solo se reserva si la cantidad reservada es distinta a la cantidad comprada por el cliente
		
		if ($cant_reservada != $cant_vendida) 
		{
			
			$reservar 		      = $cant_vendida - $cant_reservada - $cant_entregada;
			$bodega_a_reservar    = $venta['Venta']['bodega_id'];
			$reservado 		      = ClassRegistry::init('Bodega')->calcular_reserva_stock($ventaDetalle['VentaDetalle']['venta_detalle_producto_id'], $reservar, $bodega_a_reservar);
			$VentaDetallesReserva = [];
			
			if ($reservado > 0) {

				$suma_cant_reservada_y_por_reservar = $suma_cant_reservada_y_por_reservar + $reservado;
				$VentaDetallesReserva [] = [
					'VentaDetallesReserva' => 
					[
						'venta_detalle_id'	       	=> $ventaDetalle['VentaDetalle']['id'],
						'bodega_id'			       	=> $bodega_a_reservar,
						'cantidad_reservada'       	=> $reservado,
						'venta_detalle_producto_id' => $ventaDetalle['VentaDetalle']['venta_detalle_producto_id'],
						
					]
				];
			}
			// TODO Si aun no se termina de reservar el total y el metodo de envio permite buscar reserva en otras bodegas se procede a consultar stock en dichas
			if ($venta['MetodoEnvio']['permitir_reservar_stock_otra_bodega'] && $suma_cant_reservada_y_por_reservar < $reservar ) {
			
				foreach ($venta['MetodoEnvio']['BodegasMetodoEnvio'] as $value) {
	
					$reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock($ventaDetalle['VentaDetalle']['venta_detalle_producto_id'], ($reservar - $suma_cant_reservada_y_por_reservar ), $value['bodega_id']);
					// TODO Si se encuentra stock en otras bodegas no se continua buscando 
					if ($reservado > 0 ) {
						$suma_cant_reservada_y_por_reservar = $suma_cant_reservada_y_por_reservar + $reservado;
						$bodega_a_reservar 	  				= $value['bodega_id'];
						$VentaDetallesReserva [] 			= [
							'VentaDetallesReserva' => 
							[
								'venta_detalle_id'	       	=> $ventaDetalle['VentaDetalle']['id'],
								'bodega_id'			       	=> $bodega_a_reservar,
								'cantidad_reservada'       	=> $reservado,
								'venta_detalle_producto_id' => $ventaDetalle['VentaDetalle']['venta_detalle_producto_id'],
								
							]
						];
						// TODO Si ya reserve el total se termina de recorrer bodegas
						if ($suma_cant_reservada_y_por_reservar == $cant_vendida) {
							break;
						}
						
					}
				}
			}
			$cant_vendida = $cant_vendida - $cant_entregada;

			// * Si hay para reservar se crean registros de reserva
			if ($VentaDetallesReserva) {
				ClassRegistry::init('VentaDetallesReserva')->create();
				ClassRegistry::init('VentaDetallesReserva')->saveMany($VentaDetallesReserva);
			}

			// ! La reserva ya no se hace desde el VentaDetalle
			// $ventaDetalle['VentaDetalle']['cantidad_reservada'] = $reservado;
			$diff = $cant_vendida - $reservado;
			
			if ($diff >= $cant_en_espera) 
			{
				$ventaDetalle['VentaDetalle']['cantidad_en_espera'] = $cant_en_espera;
			}
			elseif ($diff == 0) 
			{
				$ventaDetalle['VentaDetalle']['cantidad_en_espera'] = 0;
				$ventaDetalle['VentaDetalle']['fecha_llegada_en_espera'] = '';		
			}
			else 
			{
				$ventaDetalle['VentaDetalle']['cantidad_en_espera'] = $cant_en_espera - $diff;	
			}

			if (empty($fecha_llegada)) 
			{
				unset($ventaDetalle['VentaDetalle']['cantidad_en_espera']);
			}

			# El agendamiento se elimina si esta todo reservado
			if ($cant_reservada == $cant_vendida)
			{
				$ventaDetalle['VentaDetalle']['cantidad_en_espera'] = 0;
				$ventaDetalle['VentaDetalle']['fecha_llegada_en_espera'] = '';
			}
			$log[] = array(
				'Log' => array(
					'administrador' => 'Producto reservar finaliza ' . $id,
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode($ventaDetalle)
				)
			);

			# Guardamos el log
			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			
			if(!ClassRegistry::init('VentaDetalle')->save($ventaDetalle))
				return 0;
		}

		$venta = $this->find('first', array(
			'conditions' => array(
				'Venta.id' => $ventaDetalle['VentaDetalle']['venta_id']
			),
			'contain' => array(
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.cantidad',
						'VentaDetalle.cantidad_anulada',
						'VentaDetalle.cantidad_entregada',
						'VentaDetalle.cantidad_en_espera',
					),
				'VentaDetallesReserva'=>[
					'fields'=>[ 
							'VentaDetallesReserva.venta_detalle_id',
							'VentaDetallesReserva.venta_detalle_producto_id',
							'VentaDetallesReserva.cantidad_reservada',
							'VentaDetallesReserva.bodega_id',
						]
					]
				),
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.id',
						'VentaEstado.venta_estado_categoria_id'
					),
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.venta'
						)
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.picking_estado',
				'Venta.venta_estado_id'
			)
		));

		$total_cantidad  = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_anulada')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_entregada')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_en_espera'));
		$total_reservado = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.VentaDetallesReserva.{n}.cantidad_reservada'));
		
		# Debe ser una venta de tipo venta para pasar a picking
		if ( $total_reservado == $total_cantidad && $total_reservado > 0 && $venta['VentaEstado']['VentaEstadoCategoria']['venta']) 
		{	
			# Condiciones para pasar a picking
			if (empty($venta['Venta']['picking_estado']) || $venta['Venta']['picking_estado'] == 'no_definido' || $venta['Venta']['picking_estado'] == 'en_revision' || $venta['Venta']['picking_estado'] == 'empaquetado' ) 
			{
				# Pasa a picking
				$this->cambiar_estado_picking($venta['Venta']['id'], 'empaquetar');
			}
		}
		else
		{
			# Se cambia a no preparado
			$this->cambiar_estado_picking($venta['Venta']['id'], 'no_definido');
		}
		
		# Preparamos los embalajes
		// ! Ya no se usara metodo desde modelo, solo desde componente
		// ! Se debera instanciar el metodo "procesar_embalajes" posterior al del metodo "reservar_stock_producto" 
		// ClassRegistry::init('EmbalajeWarehouse')->procesar_embalajes($venta['Venta']['id']);

		return $suma_cant_reservada_y_por_reservar;
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

				$save = array_replace_recursive($save, array(
					'Venta' => array(
						'picking_email' => '', 
						'picking_fecha_embalar' => '', 
						'picking_fecha_inicio' => '', 
						'picking_fecha_temrino' => ''
					)
				));

				
				break;
			
			case 'empaquetar':

				$save = array_replace_recursive($save, array(
					'Venta' => array(
						'picking_email' => '', 
						'picking_fecha_embalar' => date('Y-m-d H:i:s'), 
						'picking_fecha_inicio' => '', 
						'picking_fecha_temrino' => '', 
						'paquete_generado' => 0
					)
				));
				
				break;

			case 'empaquetando':

				# emails es obligatorio en empaquetando
				if (empty($picking_email))
					return false;
				
				$save = array_replace_recursive($save, array(
					'Venta' => array(
						'picking_fecha_inicio' => date('Y-m-d H:i:s')
					)
				));

				break;
			case 'empaquetado':

				$save = array_replace_recursive($save, array(
					'Venta' => array(
						'paquete_generado' => 0, 
						'picking_fecha_termino' => date('Y-m-d H:i:s')
					)
				));

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
	public function liberar_reserva_stock_producto($venta_detalle_reserva_id)
	{

		ClassRegistry::init('VentaDetallesReserva')->id = $venta_detalle_reserva_id;

		if (!ClassRegistry::init('VentaDetallesReserva')->exists()) {
			return 0;
		}

		// TODO Se indica venta_detalle_id para encontrar venta y cantidad a liberar
		ClassRegistry::init('VentaDetalle')->id = ClassRegistry::init('VentaDetallesReserva')->field('venta_detalle_id');

		$liberar = ClassRegistry::init('VentaDetallesReserva')->field('cantidad_reservada');

		// ! Se elimina reserva
		ClassRegistry::init('VentaDetallesReserva')->updateAll(['VentaDetallesReserva.cantidad_reservada' => 0],['VentaDetallesReserva.id' => $venta_detalle_reserva_id]);

		ClassRegistry::init('VentaDetalle')->saveField('confirmado_app', 0);

		$this->id = ClassRegistry::init('VentaDetalle')->field('venta_id');
		
		$estado_actual = $this->field('picking_estado');

		if (in_array($estado_actual, array('empaquetar', 'empaquetando', 'empaquetado')))
		{
			$this->cambiar_estado_picking($this->id, 'no_definido');
		}
		
		// ! Ya no usaremos el metodo desde el modelo, si no desde el componente
		// TODO Ahora se está instanciando el metodo del componente desde el metodo admin_liberar_stock_reservado, Controllador Ventas
		# Preparamos los embalajes
		// ClassRegistry::init('EmbalajeWarehouse')->procesar_embalajes($this->id);

		return $liberar;
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

		$ventasAgendamiento =  ClassRegistry::init('VentaDetalle')->find('all', array(
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
					'table' => 'rp_orden_compras_venta_detalle_productos',
					'alias' => 'oc_productos',
					'type' => 'INNER',
					'conditions' => array(
						'oc_productos.orden_compra_id = OrdenComprasVenta.orden_compra_id',
						'oc_productos.estado_proveedor' => array('stockout', 'price_error', 'modified')
					)
				),
				array(
					'table' => 'rp_venta_detalles',
					'alias' => 'vd',
					'type' => 'INNER',
					'conditions' => array(
						'vd.venta_id = OrdenComprasVenta.venta_id',
						'vd.venta_detalle_producto_id = oc_productos.venta_detalle_producto_id',
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

		$ventasRevisionPicking = $this->find('all', array(
			'conditions' => array(
				'Venta.picking_estado' => 'en_revision'
			),
			'fields' => array('Venta.id')
		));

		$ids_1 = Hash::extract($ventasAgendamiento, '{n}.VentaDetalle.venta_id');
		$ids_2 = Hash::extract($ventasStockout, '{n}.OrdenComprasVenta.venta_id');
		$ids_3 = Hash::extract($ventasRevisionPicking, '{n}.Venta.id');
		$ids   = array_unique(array_merge($ids_1, $ids_2, $ids_3));


		return $ids;
	}


	/**
	 * Obtiene las ventas desde hace un mes que no esten con la opción de picking activa
	 * @return arr
	 */
	public function obtener_ventas_sin_reserva()
	{
		$filter = array(
			'joins' => array(
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'estado',
					'type' => 'INNER',
					'conditions' => array(
						'estado.id = Venta.venta_estado_id',
					)
				),
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'estado_categoria',
					'type' => 'INNER',
					'conditions' => array(
						'estado_categoria.id = estado.venta_estado_categoria_id',
						'estado_categoria.venta = 1',
						'estado_categoria.reserva_stock = 1',
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
				'Venta.fecha_venta >=' => date("Y-m-d H:i:s",strtotime(date('Y-m-d')."-1 month")),
				'Venta.picking_estado' => 'no_definido'
			),
			'order' => array('Venta.fecha_venta' => 'ASC'),
			'fields' => array(
				'Venta.id'
			)
		);

		return $this->find('all', $filter);

	}


	/**
	 * Ventas pagadas retrasadas no notificadas
	 * @return array
	 */
	public function obtener_ventas_retrasadas_no_notificadas($dias_retraso = 3, $dias_limite = 50)
	{	
		$fecha_actual  = date('Y-m-d');
		$fecha_retraso = date('Y-m-d 23:59:59', strtotime(sprintf('%s - %d days', $fecha_actual, $dias_retraso)));
		$fecha_limite  = date('Y-m-d 00:00:00', strtotime(sprintf('%s - %d days', $fecha_actual, $dias_limite)));
		
		$qry = array(
			'conditions' => array(
				'Venta.fecha_venta BETWEEN ? AND ?' => array($fecha_limite, $fecha_retraso),
				'Venta.picking_estado' => array('no_definido', 'empaquetar', 'en_revision'),
				'Venta.marketplace_id' => null,
				'OR' => array(
					array('Venta.notificado_retraso_cliente' => 0),
					array('Venta.notificado_retraso_cliente' => ''),
					array('Venta.notificado_retraso_cliente' => null)
				)
			),
			'joins' => array(
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'venta_estados',
					'type' => 'INNER',
					'conditions' => array(
						'venta_estados.id = Venta.venta_estado_id'
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
			'contain' => array(
				'Tienda' => array(
					'fields' => array(
						'Tienda.id',
						'Tienda.nombre',
						'Tienda.url',
						'Tienda.direccion',
						'Tienda.mandrill_apikey'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre',
						'VentaCliente.apellido',
						'VentaCliente.email'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.id_externo',
				'Venta.referencia',
				'Venta.picking_estado',
				'Venta.notificado_retraso_cliente',
				'Venta.tienda_id',
				'Venta.venta_estado_id'
			),
			'order' => array('Venta.fecha_venta' => 'ASC')
		);
	
		return $this->find('all', $qry);
	}


	/**
	 * Ventas pagadas retrasadas notificadas
	 * @return array
	 */
	public function obtener_ventas_retrasadas($dias_retraso = 3, $dias_limite = 50)
	{	
		$fecha_actual  = date('Y-m-d');
		$fecha_retraso = date('Y-m-d 23:59:59', strtotime(sprintf('%s - %d days', $fecha_actual, $dias_retraso)));
		$fecha_limite  = date('Y-m-d 00:00:00', strtotime(sprintf('%s - %d days', $fecha_actual, $dias_limite)));
		
		$qry = array(
			'conditions' => array(
				'Venta.fecha_venta BETWEEN ? AND ?' => array($fecha_limite, $fecha_retraso),
				'Venta.picking_estado' => array('no_definido', 'en_revision')
			),
			'joins' => array(
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'venta_estados',
					'type' => 'INNER',
					'conditions' => array(
						'venta_estados.id = Venta.venta_estado_id'
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
			'contain' => array(
				'Tienda' => array(
					'fields' => array(
						'Tienda.id',
						'Tienda.nombre',
						'Tienda.url',
						'Tienda.direccion',
						'Tienda.mandrill_apikey'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre',
						'VentaCliente.apellido',
						'VentaCliente.email'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.nombre'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.id_externo',
				'Venta.referencia',
				'Venta.picking_estado',
				'Venta.notificado_retraso_cliente',
				'Venta.tienda_id',
				'Venta.venta_estado_id',
				'Venta.fecha_venta'
			),
			'order' => array('Venta.fecha_venta' => 'ASC')
		);
	
		return $this->find('all', $qry);
	}


	/**
	 * Obtiene las ventas que necesitan actualizarse según sus metodos de envio
	 * 
	 * @return $array Ventas
	 */
	public function obtener_ventas_con_envios()
	{
		return $this->find('all', array(
			'joins' => array(
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
						'VentaEstadoCategoria.venta' => 1,
						'VentaEstadoCategoria.cancelado' => 0,
						'VentaEstadoCategoria.rechazo' => 0,
						'VentaEstadoCategoria.final' => 0
					)
				),
				array(
					'table' => 'rp_transportes_ventas',
					'alias' => 'TransporteVenta',
					'type' => 'INNER',
					'conditions' => array(
						'TransporteVenta.venta_id = Venta.id'
					)
				),
				array(
					'table' => 'rp_envio_historicos',
					'alias' => 'EnvioHistorico',
					'type' => 'INNER',
					'conditions' => array(
						'EnvioHistorico.transporte_venta_id = TransporteVenta.id',
						'EnvioHistorico.notificado' => 0
					)
				)
			),
			'contain' => array(
				'Transporte'
			),	
			'fields' => array(
				'Venta.id', 'Venta.venta_estado_id'
			),
			'group' => array('Venta.id')
		));
	}


	public function obtener_ventas_con_envios_sin_historico()
	{
		return $this->find('all', array(
			'joins' => array(
				array(
					'table' => 'rp_metodo_envios',
					'alias' => 'MetodoEnvio',
					'type' => 'INNER',
					'conditions' => array(
						'MetodoEnvio.id = Venta.metodo_envio_id',
						'MetodoEnvio.dependencia' => 'boosmap',
						'MetodoEnvio.generar_ot' => 1
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
						'VentaEstadoCategoria.venta' => 1,
						'VentaEstadoCategoria.cancelado' => 0,
						'VentaEstadoCategoria.rechazo' => 0,
						'VentaEstadoCategoria.final' => 0
					)
				),
				array(
					'table' => 'rp_transportes_ventas',
					'alias' => 'TransporteVenta',
					'type' => 'INNER',
					'conditions' => array(
						'TransporteVenta.venta_id = Venta.id',
						'TransporteVenta.cod_seguimiento !='  => ''
					)
				),
				array(
					'table' => 'rp_transportes',
					'alias' => 't',
					'type' => 'INNER',
					'conditions' => array(
						't.id = TransporteVenta.transporte_id',
						't.codigo'  => 'BOOSMAP-WS'
					)
				),
				array(
					'table' => 'rp_envio_historicos',
					'alias' => 'EnvioHistorico',
					'type' => 'LEFT',
					'conditions' => array(
						'EnvioHistorico.transporte_venta_id = TransporteVenta.id',
					)
				)
			),
			'conditions' => array(
				'EnvioHistorico.id' => NULL,
				'Venta.fecha_venta >' => '2021-01-01 00:00:00'
			),
			'contain' => array(
				'Transporte'
			),	
			'fields' => array(
				'Venta.id', 'Venta.fecha_venta', 'Venta.venta_estado_id'
			),
			'group' => array('Venta.id')
		));
	}

	public function metodo_envio_id($id)
	{	
		$this->id = $id;
		return $this->field('metodo_envio_id');
	}

	public function bodega_id($id)
	{	
		$this->id = $id;
		return $this->field('bodega_id');
	}
}
