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



	public function crear( $identificador, $id_oc, $id_adjunto = null, $fecha, $monto, $facturas = array() )
	{	
		
		$pago = array(
			'Pago' => array(
				'identificador'           => $identificador,
				'orden_compra_id'         => $id_oc,
				'orden_compra_adjunto_id' => $id_adjunto,
				'fecha_pago'              => $fecha,
				'monto_pagado'            => (float) $monto
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


		$this->create();
		return $this->saveAll($pago);

	}


	public function relacionar_pago_factura($id)
	{
		$oc = ClassRegistry::init('OrdenCompra')->find('first', array(
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

		if ($total_f =! 1 || $total_p != 1) {
			return false;
		}

		$pago = array(
			'Pago' => array(
				'id' => $oc['Pago'][0]['id']
			),
			'OrdenCompraFactura' => array(
				0 => array(
					'pagada'          => 1,
					'factura_id'      => $oc['OrdenCompraFactura'][0]['id'],
					'monto_facturado' => (float) $oc['OrdenCompraFactura'][0]['monto_facturado'],
					'monto_pagado'    => (float) $oc['Pago'][0]['monto_pagado']
				)
			)
		);

		return $this->saveAll($pago);
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
				)
			)
		));

		if (empty($pago)) {
			return;
		}

		if (empty($pago['Saldo'])) {
			$id_proveedor = ClassRegistry::init('OrdenCompra')->field('proveedor_id', array('id' => $pago['Pago']['orden_compra_id']));

			# Creamos el saldo del pago
			ClassRegistry::init('Saldo')->crear($id_proveedor, $pago['Pago']['orden_compra_id'], null, $id, $pago['Pago']['monto_pagado']);	
		}

		$oc = ClassRegistry::init('OrdenCompra')->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $pago['Pago']['orden_compra_id']
			),
			'contain' => array(
				'OrdenCompraFactura' => array(
					'fields' => array(
						'OrdenCompraFactura.*'
					),
					'conditions' => array(
						'OrdenCompraFactura.pagada' => 0
					)
				),
				'Pago' => array(
					'fields' => array(
						'Pago.id',
						'Pago.monto_pagado',
						'Pago.fecha_pago',
						'Pago.identificador',
						'Pago.adjunto',
						'Pago.pagado'
					),
					'conditions' => array(
						'Pago.pagado' => 1
					)
				)
			)
		));


		if (empty($oc['OrdenCompraFactura'])) 
			return;


		$total_p = array_sum(Hash::extract($oc['Pago'], '{n}.monto_pagado'));
		$total_f = array_sum(Hash::extract($oc['OrdenCompraFactura'], '{n}.monto_facturado'));


		# Marcamos las facturas relacionadas como pagadas
		$facturas = array();


		if ( $total_p >= $total_f && $total_f > 0 ) {

			foreach ($oc['OrdenCompraFactura'] as $if => $f) {
				$facturas[$if]['OrdenCompraFactura']['id']           = $f['id'];
				$facturas[$if]['OrdenCompraFactura']['monto_pagado'] = $f['monto_facturado'];
				$facturas[$if]['OrdenCompraFactura']['pagada']       = 1;

				# Se relacionan las facturas con los pagos
				foreach ($oc['Pago'] as $ip => $p) {
					$res[$f['id']][$p['id']] = ClassRegistry::init('FacturasPago')->save(array(
						'FacturasPago' => array(
							'factura_id' => $f['id'],
							'pago_id' => $p['id'],
						)
					));


				}
			}

			ClassRegistry::init('OrdenCompraFactura')->saveMany($facturas);

		}else{

			foreach ($oc['OrdenCompraFactura'] as $if => $f) {

				# Se relacionan las facturas con los pagos
				foreach ($oc['Pago'] as $ip => $p) {
					$res[$f['id']][$p['id']] = ClassRegistry::init('FacturasPago')->save(array(
						'FacturasPago' => array(
							'factura_id' => $f['id'],
							'pago_id' => $p['id'],
						)
					));
				}
			}

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