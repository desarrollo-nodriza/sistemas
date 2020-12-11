<?php
App::uses('AppModel', 'Model');
class OrdenCompraFactura extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'folio';

	public $belongsTo = array(
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'orden_compra_id',
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

	public $hasMany = array(
		'Saldo' => array(
			'className'				=> 'Saldo',
			'foreignKey'			=> 'orden_compra_factura_id',
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
		'OrdenCompraPago' => array(
			'className'				=> 'OrdenCompraPago',
			'joinTable'				=> 'orden_compra_facturas_pagos',
			'foreignKey'			=> 'orden_compra_factura_id',
			'associationForeignKey'	=> 'orden_compra_pago_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'with'					=> 'OrdenCompraFacturasPago',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Pago' => array(
			'className'				=> 'Pago',
			'joinTable'				=> 'facturas_pagos',
			'foreignKey'			=> 'factura_id',
			'associationForeignKey'	=> 'pago_id',
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


	public function crear($factura = array())
	{	
		if($this->save($factura)){
			$id = $this->id;
			$this->clear();
			return $id;
		}else{
			return 0;
		}
	}


	/**
	 * 
	 * @param  [type] $id_oc [description]
	 * @return [type]        [description]
	 */
	public function relacionar_pago_inmediato($id_oc)
	{
		$oc = ClassRegistry::init('OrdenCompra')->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id_oc
			),
			'contain' => array(
				'OrdenCompraFactura' => array(
					'fields' => array(
						'OrdenCompraFactura.id',
						'OrdenCompraFactura.monto_facturado',
						'OrdenCompraFactura.monto_pagado',
						'OrdenCompraFactura.folio'
					)
				),
				'OrdenCompraPago' => array(
					'fields' => array(
						'OrdenCompraPago.*'
					)
				)
			)
		));

		$toSave =  array();

		foreach ($oc['OrdenCompraFactura'] as $iocf => $ocf) {

			if (count($oc['OrdenCompraPago']) > 1) {
				# No crea la relación se debe hacer manualmente (Notificar)
				return false;
			}
			
			if ($ocf['monto_pagado'] == $ocf['monto_facturado'])
				continue;

			foreach ($oc['OrdenCompraPago'] as $iocp => $ocp) {

				if ($ocp['fecha_pago'] > date('Y-m-d'))
					continue;
				
				if ($ocp['monto_real_pagado'] < $ocf['monto_facturado']) 
					continue;

				$toSave[$iocf]['OrdenCompraFactura'] = array(
					'id'                   => $ocf['id'],
					'monto_pagado'		   => $ocp['monto_pagado'],
					'pagada'			   => 1
				);

				$toSave[$iocf]['OrdenCompraPago'][$iocp] = array(
					'orden_compra_pago_id' => $ocp['id'],
					'monto_pagado'         => $ocf['monto_facturado'],
				);
			}

		}
		
		if (!empty($toSave)) {
			return $this->saveMany($toSave);
		}

		return false;
	}


	/**
	 * Retorna el siguiente id al ultimo registrado
	 * @return int
	 */
	public function obtener_siguiente_id()
	{
		$ultimo = $this->find('first', array(
			'order' => array('created' => 'desc'),
			'fields' => array('id')
		));

		if (empty($ultimo)) {
			return 1;
		}else{
			return (int) $ultimo['OrdenCompraFactura']['id'] + 1;
		}
	}

	/**
	 * Obtiene la factura dado el folio, tipo de dte y prpoveedor
	 * @param int $invoice FOLIO del DTE
	 * @param int $supplier_id ID del proveedor
	 * @param int $type Tipo DTE, 33 o 61
	 */
	public function find_by_invoice($invoice, $supplier_id, $type = 33)
	{
		return $this->find('first', array(
			'conditions' => array(
				'OrdenCompraFactura.folio' => $invoice,
				'OrdenCompraFactura.proveedor_id' => $supplier_id,
				'OrdenCompraFactura.tipo_documento' => $type
			)
		));
	}
}
