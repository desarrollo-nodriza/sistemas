<?php 
App::uses('AppModel', 'Model');

Class Carro extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Carro';
	public $useTable = 'cart';
	public $primaryKey = 'id_cart';

	/**
	* Config
	*/
	public $displayField	= 'id_cart';

	public $validate = array(

	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	
	public $hasMany = array(
		'Orden' => array(
			'className'				=> 'Orden',
			'foreignKey'			=> 'id_cart',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'WebpayStore' => array(
			'className'				=> 'WebpayStore',
			'foreignKey'			=> 'id_order',
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