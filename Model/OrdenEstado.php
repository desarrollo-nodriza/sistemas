<?php 
App::uses('AppModel', 'Model');

Class OrdenEstado extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'OrdenEstado';
	public $useTable = 'order_state';
	public $primaryKey = 'id_order_state';

	/**
	* Config
	*/
	public $displayField	= 'id_order_state';

	public $validate = array(

	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	
	public $hasMany = array(
		'Orden' => array(
			'className'				=> 'Orden',
			'foreignKey'			=> 'current_state',
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

	public $hasAndBelongsToMany = array(
		'Lang' => array(
			'className'				=> 'Lang',
			'joinTable'				=> 'order_state_lang',
			'foreignKey'			=> 'id_order_state',
			'associationForeignKey'	=> 'id_lang',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'OrdenEstadoIdioma',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);
}