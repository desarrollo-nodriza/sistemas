<?php
App::uses('AppModel', 'Model');
class Venta extends AppModel
{
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
		)
	);


	public function beforeSave($options = array())
	{
		if (isset($this->data['VentaDetalle'])) {
			foreach ($this->data['VentaDetalle'] as $i => $d) {

				if (!isset($d['VentaDetalle']['cantidad'])) {
					continue;
				}

				$this->data['VentaDetalle'][$i]['VentaDetalle']['cantidad_pendiente_entrega'] = $d['VentaDetalle']['cantidad'];
				$this->data['VentaDetalle'][$i]['VentaDetalle']['cantidad_entregada']         = 0;

				if (isset($d['VentaDetalle']['precio'])) {
					$this->data['VentaDetalle'][$i]['VentaDetalle']['precio_bruto'] = monto_bruto($d['VentaDetalle']['precio']);		
				}
			}
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
							'fields' => array(
								'VentaDetalleProducto.id', 'VentaDetalleProducto.nombre'
							)
						),
						'conditions' => array(
							'VentaDetalle.activo' => 1
						),
						'fields' => array(
							'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.cantidad', 'VentaDetalle.venta_id'
						)
					),
					'VentaEstado' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
							)
						),
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.venta_estado_categoria_id'
						)
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.logo', 'Tienda.direccion', 'Tienda.facturacion_apikey'
						)
					),
					'Marketplace' => array(
						'fields' => array(
							'Marketplace.id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id',
							'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
							'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token'
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
					'Dte' => array(
						'fields' => array(
							'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
							'Dte.total', 'Dte.fecha', 'Dte.estado', 'Dte.venta_id', 'Dte.pdf', 'Dte.invalidado'
						),
						'order' => 'Dte.fecha DESC'
					)
				),
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo', 'Venta.descuento', 'Venta.costo_envio',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id', 'Venta.direccion_entrega', 'Venta.comuna_entrega', 'Venta.nombre_receptor',
					'Venta.fono_receptor'
				)
			)
		);
	}
}
