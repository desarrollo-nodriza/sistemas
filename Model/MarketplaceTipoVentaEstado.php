<?php
App::uses('AppModel', 'Model');
class TiendaVentaEstado extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';
	
	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'MarketplaceTipo' => array(
			'className'				=> 'MarketplaceTipo',
			'foreignKey'			=> 'marketplace_tipo_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'MarketplaceTipo')
		),
		'VentaEstado' => array(
			'className'				=> 'VentaEstado',
			'foreignKey'			=> 'venta_estado_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'VentaEstado')
		)
	);
}
