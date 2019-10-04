<?php
App::uses('AppModel', 'Model');
class OrdenCompra extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'id';


	public $estados = array(
		''                  => 'No procesada',
		'iniciado'          => 'En revisión',
		'validado'          => 'En asignación de m. de pago',
		'asignacion_moneda' => 'En revisión proveedor',
		'validado_proveedor'=> 'En proceso de pago',
		'pagado'            => 'Pagado',
		'enviado'           => 'Enviado',
		'incompleto'        => 'Recibido incompleto',
		'pendiente_factura' => 'Factura pendiente',
		'recibido'          => 'Finalizado',
		'cancelada'          => 'Cancelada'
	);


	public $estado_proveedor = array(
		'accept'      => 'Aceptado',
		'modified'    => 'Modificar cantidad',
		'stockout'    => 'Sin stock',
		'price_error' => 'Error de precio'
	);


	/**
	 * BEHAVIORS
	 */
	var $actsAs			= array(
		/**
		 * IMAGE UPLOAD
		 */
		'Image'		=> array(
			'fields'	=> array(
				'adjunto'	=> array(
				)
			)
		)
	);

	public $hasMany = array(
		'ChildOrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'parent_id',
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
		'OrdenCompraPago' => array(
			'className'				=> 'OrdenCompraPago',
			'foreignKey'			=> 'orden_compra_id',
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
		'OrdenCompraFactura' => array(
			'className'				=> 'OrdenCompraFactura',
			'foreignKey'			=> 'orden_compra_id',
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
		'OrdenCompraAdjunto' => array(
			'className'				=> 'OrdenCompraAdjunto',
			'foreignKey'			=> 'orden_compra_id',
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
		'Saldo' => array(
			'className'				=> 'Saldo',
			'foreignKey'			=> 'orden_compra_id',
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
		'Pago' => array(
			'className'				=> 'Pago',
			'foreignKey'			=> 'orden_compra_id',
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


	public $belongsTo = array(
		'Administrador' => array(
			'className'				=> 'Administrador',
			'foreignKey'			=> 'administrador_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'Moneda' => array(
			'className'				=> 'Moneda',
			'foreignKey'			=> 'moneda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'Proveedor' => array(
			'className'				=> 'Proveedor',
			'foreignKey'			=> 'proveedor_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);

	public $hasAndBelongsToMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'joinTable'				=> 'orden_compras_ventas',
			'foreignKey'			=> 'orden_compra_id',
			'associationForeignKey'	=> 'venta_id',
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
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'joinTable'				=> 'orden_compras_venta_detalle_productos',
			'foreignKey'			=> 'orden_compra_id',
			'associationForeignKey'	=> 'venta_detalle_producto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'OrdenComprasVentaDetalleProducto',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		/*'Proveedor' => array(
			'className'				=> 'Proveedor',
			'joinTable'				=> 'orden_compras_proveedores',
			'foreignKey'			=> 'orden_compra_id',
			'associationForeignKey'	=> 'proveedor_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'OrdenComprasProveedor',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)*/
	);


	public function afterFind($results, $primary = false) {

		# Convertimos meta dtes en un "Modelo"
		foreach ($results as $key => $val) {
	        if (isset($val['OrdenCompra']['meta_dtes'])) {
	            $results[$key]['OrdenCompra']['meta_dtes'] = json_decode($results[$key]['OrdenCompra']['meta_dtes'], true);
	        }
	    }
	    return $results;
	}


	public function obtener_metricas()
	{
		$ocs = $this->find('all', array(
			'conditions' => array(
				'OrdenCompra.parent_id !=' => null,
				'OrdenCompra.fecha_recibido !=' => null
			),
			'fields' => array(
				'OrdenCompra.created',
				'OrdenCompra.fecha_enviado',
				'OrdenCompra.fecha_recibido'
			)
		));

		$avg = array();
		foreach ($ocs as $key => $value) {
			$f_creacion  = date_create($value['OrdenCompra']['created']);
			$f_recepcion = date_create($value['OrdenCompra']['fecha_recibido']);
			$f_enviado   = date_create($value['OrdenCompra']['fecha_enviado']);

			$diferencia1 = date_diff($f_creacion, $f_recepcion);
			$diferencia2 = date_diff($f_enviado, $f_recepcion);
			$diferencia3 = date_diff($f_creacion, $f_enviado);

			$avg[$key]['creado_recibido']['dias']   = $diferencia1->days;
			$avg[$key]['creado_recibido']['horas']  = $diferencia1->h;
			$avg[$key]['enviado_recibido']['dias']  = $diferencia2->days;
			$avg[$key]['enviado_recibido']['horas'] = $diferencia2->h;
			$avg[$key]['creado_enviado']['dias']    = $diferencia3->days;
			$avg[$key]['creado_enviado']['horas']   = $diferencia3->h;
		}	

		$promedio_creado_recibido  = (array_sum(Hash::extract($avg, '{n}.creado_recibido.dias')) / count($avg));
		$promedio_enviado_recibido = (array_sum(Hash::extract($avg, '{n}.enviado_recibido.dias')) / count($avg));
		$promedio_creado_enviado   = (array_sum(Hash::extract($avg, '{n}.creado_enviado.dias')) / count($avg));
		
		return array(
			'tiempo_promedio' => array(
				'creado_recibido'  => $promedio_creado_recibido,
				'enviado_recibido' => $promedio_enviado_recibido,
				'creado_enviado'   => $promedio_creado_enviado
			)
		);
	}


	public function es_pago_factura_unico($id)
	{
		$oc = $this->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'OrdenCompraFactura' => array(
					'fields' => array(
						'OrdenCompraFactura.id',
						'OrdenCompraFactura.monto_facturado'
					)
				),
				'Pago' => array(
					'fields' => array(
						'Pago.id',
						'Pago.monto_pagado'
					)
				)
			)
		));

		$total_f = count($oc['OrdenCompraFactura']);
		$total_p = count($oc['Pago']);

		$return  = false;

		if ($total_f == 1 && $total_p == 1) {
			$return = true;
		}

		return $return;

	}



	public function es_pago_agendado($id)
	{
		$oc = $this->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda' => array(
					'fields' => array(
						'Moneda.tipo'
					)
				)
			)
		));
		
		if ($oc['Moneda']['tipo'] == 'agendar') {
			return true;
		}else{
			return false;
		}

	}


	public function crear_oc($id_proveedor, $estado = '', $items = array())
	{

	}

	public function obtener_ventas_por_oc($id_oc, $order = 'ASC')
	{	

		$ocVentas = ClassRegistry::init('OrdenComprasVenta')->find('all', array(
			'conditions' => array(
				'OrdenComprasVenta.orden_compra_id' => $id_oc
			)
		));

		$ventas = ClassRegistry::init('Venta')->find('all', array(
			'conditions' => array(
				'Venta.id' => Hash::extract($ocVentas, '{n}.OrdenComprasVenta.venta_id')
			),
			'contain' => array(
				'VentaDetalle' => array(
					'VentaDetalleProducto'
				)
			),
			'order' => array(
				'Venta.fecha_venta' => $order
			)
		));

		return $ventas;
	}


	public function obtener_ventas_por_productos($id_oc, $id_productos = array())
	{
		$ventas = ClassRegistry::init('Venta')->find('all', array(
			'contain' => array(
				'VentaDetalle' => array(
					'VentaDetalleProducto'
				),
				'Tienda',
				'Marketplace'
			),
			'joins' => array(
			    array(
			    	'table' => 'venta_detalles',
			        'alias' => 'vd',
			        'type' => 'INNER',
			        'conditions' => array(
			            'vd.venta_id = Venta.id',
			            'vd.venta_detalle_producto_id' => $id_productos
			        )
			    ),
			    array(
			    	'table' => 'orden_compras_ventas',
			        'alias' => 'v',
			        'type' => 'INNER',
			        'conditions' => array(
			            'v.venta_id = Venta.id',
			            'v.orden_compra_id' => $id_oc
			        )
			    )
			)
		));

		return $ventas;
		
	}


	public function obtener_descuento_oc($id_oc)
	{	
		$oc = $this->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id_oc
			),
			'contain' => array(
				'Proveedor' => array(
					'Moneda'
				),
			)
		));

		$descuento = 0;

		# Descuentos por método de pago
		if ( Hash::check($oc, 'Proveedor.Moneda.{n}[id=' . $oc['OrdenCompra']['moneda_id'] . ']') )
		{
			$descuento = Hash::extract($oc, 'Proveedor.Moneda.{n}[id=' . $oc['OrdenCompra']['moneda_id'] . '].MonedasProveedor.descuento')[0];
		}

		return $descuento;
	}

	public function obtener_oc_en_proveedor()
	{
		return $this->find('all', array(
			'conditions' => array(
				'OrdenCompra.estado' => 'asignacion_moneda',
				'OrdenCompra.validado_proveedor' => 0
			)
		));
	}

}
