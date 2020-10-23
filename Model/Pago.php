<?php 
App::uses('AppModel', 'Model');

Class Pago extends AppModel {

	/**
	* Config
	*/
	public $displayField	= 'id';

	
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

	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'orden_compra_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Proveedor' => array(
			'className'				=> 'Proveedor',
			'foreignKey'			=> 'proveedor_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'OrdenCompraAdjunto' => array(
			'className'				=> 'OrdenCompraAdjunto',
			'foreignKey'			=> 'orden_compra_adjunto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'CuentaBancaria' => array(
			'className'				=> 'CuentaBancaria',
			'foreignKey'			=> 'cuenta_bancaria_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Moneda' => array(
			'className'				=> 'Moneda',
			'foreignKey'			=> 'moneda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
	
	public $hasMany = array(
		'Saldo' => array(
			'className'				=> 'Saldo',
			'foreignKey'			=> 'pago_id',
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
		'OrdenCompraFactura' => array(
			'className'				=> 'OrdenCompraFactura',
			'joinTable'				=> 'facturas_pagos',
			'foreignKey'			=> 'pago_id',
			'associationForeignKey'	=> 'factura_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'with'					=> 'FacturasPago',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);


	public function afterSave($created, $options = array())
	{	
		if (isset($this->data['Pago']['pagado']) && $this->data['Pago']['pagado']) {
			$this->finalizar($this->data['Pago']['id']);
		}
	}



	public function crear( $identificador, $id_oc = null, $id_adjunto = null, $fecha, $monto, $facturas = array(), $moneda_id = null, $proveedor_id = null )
	{	
		
		$pago = array(
			'Pago' => array(
				'identificador'           => $identificador,
				'orden_compra_id'         => $id_oc,
				'orden_compra_adjunto_id' => $id_adjunto,
				'fecha_pago'              => $fecha,
				'monto_pagado'            => $monto
			)

		);

		# si el pago es al día se marca como pago pagado
		if (!empty($fecha) && $fecha <= date('Y-m-d')) {
			$pago['Pago']['pagado'] = 1;
		}

		if (!empty($facturas)) {
			$pago = array_replace_recursive($pago, array(
				'OrdenCompraFactura' => $facturas
			));
		}

		if (!empty($moneda_id)) {
			$pago = array_replace_recursive($pago, array(
				'Pago' =>  array(
					'moneda_id' => $moneda_id
				)
			));
		}

		if (!empty($proveedor_id)) {
			$pago = array_replace_recursive($pago, array(
				'Pago' =>  array(
					'proveedor_id' => $proveedor_id
				)
			));
		}


		$this->create();

		if ($this->saveAll($pago)) {
			return $this->id;
		}

		return 0;

	}


	/**
	 * Crea la relación entre un pago único y una factura única
	 * @param  [type] $pago_id         [description]
	 * @param  [type] $factura_id      [description]
	 * @param  [type] $monto_facturado [description]
	 * @param  [type] $monto_pagado    [description]
	 * @return [type]                  [description]
	 */
	public function relacionar_pago_factura($pago_id, $factura_id, $monto_facturado, $monto_pagado)
	{
		
		$pago = array(
			'FacturasPago' => array(
				'factura_id'      => $factura_id,
				'pago_id'         => $pago_id,
				'monto_facturado' => $monto_facturado,
				'monto_pagado'    => $monto_pagado
			)
		);	

		return ClassRegistry::init('FacturasPago')->save($pago);
	}


	public function revertir ($id)
	{
		$pago = $this->find('first', array(
			'conditions' => array(
				'Pago.id' => $id
			),
			'fields' => array(
				'Pago.pagado'
			)
		));

		$pago['Pago']['pagado'] = 0;

		return $this->save($pago);
	}


	public function finalizar($id)
	{
		$pago = $this->find('first', array(
			'conditions' => array(
				'Pago.id' => $id
			),
			'fields' => array(
				'Pago.orden_compra_id',
				'Pago.monto_pagado'
			),
			'contain' => array(
				'Saldo' => array(
					'fields' => array(
						'Saldo.id'
					)
				),
				'OrdenCompra' => array(
					'fields' => array(
						'OrdenCompra.proveedor_id'
					)
				),
				'OrdenCompraFactura'
			)
		));
		
		if (empty($pago)) {
			return;
		}
		
		if (empty($pago['Saldo'])) {

			if (!empty($pago['Pago']['orden_compra_id'])) {
				$id_proveedor = $pago['OrdenCompra']['proveedor_id'];
			}else{
				$id_proveedor = Hash::extract($pago['OrdenCompraFactura'], '{n}.proveedor_id')[0];
			}

			# Creamos el saldo del pago
			ClassRegistry::init('Saldo')->crear($id_proveedor, $pago['Pago']['orden_compra_id'], null, $id, $pago['Pago']['monto_pagado']);	
		}		
		
		# Marcamos las facturas relacionadas como pagadas
		$facturas = array();

		$monto_pago   = 0;
		$monto_pagado = 0;

		# Validamos y relacionamos los pagos y las facturas
		foreach ($pago['OrdenCompraFactura'] as $if => $f) {

			$pagosRelFactura = ClassRegistry::init('OrdenCompraFactura')->find('first', array(
				'conditions' => array(
					'OrdenCompraFactura.id' => $f['id']
				),
				'contain' => array(
					'Pago' => array(
						'conditions' => array(
							'Pago.pagado' => 1
						)
					)
				)
			));
			
			$total_a_pagar = $f['monto_facturado'] - $f['monto_pagado'];
			
			if ($total_a_pagar < 0) {
				$monto_pagado = $monto_pagado + $f['monto_pagado'];
				continue;
			}
			
			$monto_pago  = array_sum(Hash::extract($pagosRelFactura, 'Pago.{n}.monto_pagado')) - $f['monto_pagado'] - $monto_pagado;
			
			$facturas[$if]['OrdenCompraFactura']['id']           = $f['id'];
			# Pagamos la factura y descontamos del monto pagado
			if ( $total_a_pagar <= $monto_pago ) {

				$facturas[$if]['OrdenCompraFactura']['pagada'] = 1;
				$facturas[$if]['OrdenCompraFactura']['monto_pagado'] = $f['monto_facturado'];
				
				$monto_pagado = $monto_pagado + $total_a_pagar + $f['monto_pagado'];

			}else{
				$facturas[$if]['OrdenCompraFactura']['monto_pagado'] = $monto_pago + $f['monto_pagado'];
				$monto_pagado = $monto_pagado + $monto_pago + $f['monto_pagado'];
			}
		}
		
		#guardamos las facturas
		if (!empty($facturas)) {
			ClassRegistry::init('OrdenCompraFactura')->saveMany($facturas);	
		}

		return;

	}


	/**
	 * Pagos que deben agendarse los cuales su OC ya tiene facturas relacionada
	 * @return array
	 */
	public function pagos_pendiente_dte()
	{

		return $this->find('all', array(
			'conditions' => array(
				'OR' => array(
					'Pago.fecha_pago'         => null,
					'Pago.identificador'      => null,
					'Pago.cuenta_bancaria_id' => null,
					'Pago.monto_pagado' 	  => null
				)
			),
			'joins' => array(
			    array('table' => 'orden_compras',
			        'alias' => 'OrdenCompra',
			        'type' => 'INNER',
			        'conditions' => array(
			            'OrdenCompra.id = Pago.orden_compra_id'
			        )
			    ),
			    array('table' => 'orden_compra_facturas',
			        'alias' => 'OrdenCompraFactura',
			        'type' => 'INNER',
			        'conditions' => array(
			            'OrdenCompraFactura.orden_compra_id = OrdenCompra.id'
			        )
			    ),
			    array('table' => 'monedas',
			        'alias' => 'Moneda',
			        'type' => 'INNER',
			        'conditions' => array(
			            'Moneda.id = OrdenCompra.moneda_id',
			            'OR' => array(
			            	'Moneda.tipo = "esperar"',
			            	'Moneda.tipo = "agendar"'
			            )
			        )
			    )
			),
			'fields' => array(
				'Pago.id',
				'OrdenCompra.id',
				'OrdenCompra.email_finanza',
				'OrdenCompraFactura.*',
			)
		));

	}
}