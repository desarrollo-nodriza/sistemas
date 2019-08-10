<?php
App::uses('AppModel', 'Model');
class OrdenCompraPago extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'identificador';

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
		'Moneda' => array(
			'className'				=> 'Moneda',
			'foreignKey'			=> 'moneda_id',
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
			'foreignKey'			=> 'orden_compra_pago_id',
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
			'joinTable'				=> 'orden_compra_facturas_pagos',
			'foreignKey'			=> 'orden_compra_pago_id',
			'associationForeignKey'	=> 'orden_compra_factura_id',
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
		)
	);


	/**
	 * Pagos que deben agendarse los cuales su OC ya tiene facturas relacionada
	 * @return array
	 */
	public function pagos_pendiente_dte()
	{

		return $this->find('all', array(
			'conditions' => array(
				'OR' => array(
					'OrdenCompraPago.fecha_pago'    => null,
					'OrdenCompraPago.identificador' => null,
					'OrdenCompraPago.cuenta'        => null
				)
			),
			'joins' => array(
				array('table' => 'monedas',
			        'alias' => 'Moneda',
			        'type' => 'INNER',
			        'conditions' => array(
			            'Moneda.id = OrdenCompraPago.moneda_id',
			            'Moneda.tipo = "esperar"'
			        )
			    ),
			    array('table' => 'orden_compras',
			        'alias' => 'OrdenCompra',
			        'type' => 'INNER',
			        'conditions' => array(
			            'OrdenCompra.id = OrdenCompraPago.orden_compra_id'
			        )
			    ),
			    array('table' => 'orden_compra_facturas',
			        'alias' => 'OrdenCompraFactura',
			        'type' => 'INNER',
			        'conditions' => array(
			            'OrdenCompraFactura.orden_compra_id = OrdenCompra.id'
			        )
			    )
			),
			'fields' => array(
				'OrdenCompraPago.id',
				'OrdenCompra.id',
				'OrdenCompra.email_finanza',
				'OrdenCompraFactura.*',
			)
		));

	}
}
