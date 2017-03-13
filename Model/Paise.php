<?php 
App::uses('AppModel', 'Model');

Class Paise extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Paise';
	public $useTable = 'country';
	public $primaryKey = 'id_country';

	/**
	* Config
	*/
	public $displayField	= 'iso_code';

	/**
	 * Asosiaciones
	 * @var array
	 */
	public $hasMany = array(
		'Clientedireccion' => array(
			'className'				=> 'Clientedireccion',
			'foreignKey'			=> 'id_country',
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
		'Region' => array(
			'className'				=> 'Region',
			'foreignKey'			=> 'id_country',
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
			'joinTable'				=> 'country_lang',
			'foreignKey'			=> 'id_country',
			'associationForeignKey'	=> 'id_lang',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'PaisIdioma',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);
	
}