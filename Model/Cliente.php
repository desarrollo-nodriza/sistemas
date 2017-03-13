<?php 
App::uses('AppModel', 'Model');

Class Cliente extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Cliente';
	public $useTable = 'customer';
	public $primaryKey = 'id_customer';

	/**
	* Config
	*/
	public $displayField	= 'firstname';

	public $validate = array(

	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	public $hasMany = array(
		'Clientedireccion' => array(
			'className'				=> 'Clientedireccion',
			'foreignKey'			=> 'id_customer',
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