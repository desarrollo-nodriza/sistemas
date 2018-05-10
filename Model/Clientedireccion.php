<?php 
App::uses('AppModel', 'Model');

Class Clientedireccion extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Clientedireccion';
	public $useTable = 'address';
	public $primaryKey = 'id_address';

	/**
	* Config
	*/
	public $displayField	= 'alias';

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
		'Paise' => array(
			'className'				=> 'Paise',
			'foreignKey'			=> 'id_country',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Region' => array(
			'className'				=> 'Region',
			'foreignKey'			=> 'id_state',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

}