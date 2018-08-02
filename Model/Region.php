<?php 
App::uses('AppModel', 'Model');

Class Region extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Region';
	public $useTable = 'state';
	public $primaryKey = 'id_state';

	/**
	* Config
	*/
	public $displayField	= 'name';

	/**
	 * Asosiaciones
	 * @var array
	 */
	public $hasMany = array(
		'Clientedireccion' => array(
			'className'				=> 'Clientedireccion',
			'foreignKey'			=> 'id_state',
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

	public $belongsTo = array(
		'Paise' => array(
			'className'				=> 'Paise',
			'foreignKey'			=> 'id_country',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
}