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
}
