<?php
App::uses('AppModel', 'Model');
class VentaMensaje extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';
	
	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Venta')
		)
	);
	public $hasMany = array(
		'ChildVentaMensaje' => array(
			'className'				=> 'VentaMensaje',
			'foreignKey'			=> 'parent_id',
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
