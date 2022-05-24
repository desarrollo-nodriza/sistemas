<?php
App::uses('AppModel', 'Model');
class TransportesVenta extends AppModel
{
	public $hasMany = array(
		'EnvioHistorico' => array(
			'className'				=> 'EnvioHistorico',
			'foreignKey'			=> 'transporte_venta_id',
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
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'Transporte' => array(
			'className'				=> 'Transporte',
			'foreignKey'			=> 'transporte_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'EmbalajeWarehouse' => array(
			'className'				=> 'EmbalajeWarehouse',
			'foreignKey'			=> 'embalaje_id',
			'dependent'				=> false,
			'conditions'			=> '',
		),
	);
}
