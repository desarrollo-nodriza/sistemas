<?php 
App::uses('AppModel', 'Model');

Class Saldo extends AppModel {

	/**
	* Config
	*/
	public $displayField	= 'id';

	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'Proveedor' => array(
			'className'				=> 'Proveedor',
			'foreignKey'			=> 'proveedor_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'orden_compra_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'OrdenCompraPago' => array(
			'className'				=> 'OrdenCompraPago',
			'foreignKey'			=> 'orden_compra_pago_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Pago' => array(
			'className'				=> 'Pago',
			'foreignKey'			=> 'pago_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'OrdenCompraFactura' => array(
			'className'				=> 'OrdenCompraFactura',
			'foreignKey'			=> 'orden_compra_factura_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);



	public function crear($id_proveedor, $id_oc = null, $id_factura = null, $id_pago = null, $saldo)
	{
		if ($saldo <= 0) {
			return false;
		}
		
		$this->create();

		return $this->save(
			array(
				'Saldo' => array(
					'proveedor_id'            => $id_proveedor,
					'orden_compra_id'         => $id_oc,
					'orden_compra_factura_id' => $id_factura,
					'pago_id'                 => $id_pago,
					'saldo'                   => (float) $saldo
				)
			)
		);
	}


	public function obtener_saldo_proveedor($id_proveedor)
	{
		return $this->find('all', array(
			'conditions' => array(
				'proveedor_id' => $id_proveedor
			)
		));
	}


	public function obtener_saldo_total_proveedor($id_proveedor)
	{
		$saldos = $this->obtener_saldo_proveedor($id_proveedor);

		return array_sum(Hash::extract($saldos, '{n}.Saldo.saldo'));

	}


	public function descontar($id_proveedor, $id_oc = null, $id_factura = null, $id_pago = null, $monto)
	{
		$this->create();

		$descontar = array(
			'Saldo' => array(
				'proveedor_id'            => $id_proveedor,
				'orden_compra_id'         => $id_oc,
				'orden_compra_factura_id' => $id_factura,
				'pago_id'                 => $id_pago,
				'saldo'                   =>  - (float) $monto
			)
		);


		return $this->save($descontar);
		
	}
}