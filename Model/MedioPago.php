<?php
App::uses('AppModel', 'Model');
class MedioPago extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

	/**
	 * ASOCIACIONES
	 */
	public $hasMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'medio_pago_id',
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
