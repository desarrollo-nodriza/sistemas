<?php 
App::uses('AppModel', 'Model');

Class Empleado extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Empleado';
	public $useTable = 'employee';
	public $primaryKey = 'id_employee';

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
		'ClienteMensaje' => array(
			'className'				=> 'ClienteMensaje',
			'foreignKey'			=> 'id_employee',
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