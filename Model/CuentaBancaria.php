<?php
App::uses('AppModel', 'Model');

class CuentaBancaria extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'alias';

	public $hasMany = array(
		'Pago' => array(
			'className'				=> 'Pago',
			'foreignKey'			=> 'cuenta_bancaria_id',
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