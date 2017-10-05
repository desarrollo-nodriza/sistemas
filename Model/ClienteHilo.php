<?php 
App::uses('AppModel', 'Model');

Class ClienteHilo extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'ClienteHilo';
	public $useTable = 'customer_thread';
	public $primaryKey = 'id_customer_thread';

	/**
	* Config
	*/
	public $displayField	= 'status';

	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'Cliente' => array(
			'className'				=> 'Cliente',
			'foreignKey'			=> 'id_customer',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Orden' => array(
			'className'				=> 'Orden',
			'foreignKey'			=> 'id_order',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Lang' => array(
			'className'				=> 'Lang',
			'foreignKey'			=> 'id_lang',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Productotienda' => array(
			'className'				=> 'Productotienda',
			'foreignKey'			=> 'id_lang',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

	public $hasMany = array(
		'ClienteMensaje' => array(
			'className'				=> 'ClienteMensaje',
			'foreignKey'			=> 'id_customer_thread',
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