<?php 
App::uses('AppModel', 'Model');

Class Fabricante extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Fabricante';
	public $useTable = 'manufacturer';
	public $primaryKey = 'id_manufacturer';


	public $hasMany = array(
		'Productotienda' => array(
			'className'				=> 'Productotienda',
			'foreignKey'			=> 'id_manufacturer',
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
		'Socio' => array(
			'className'				=> 'Socio',
			'joinTable'				=> 'fabricantes_socios',
			'foreignKey'			=> 'id_manufacturer',
			'associationForeignKey'	=> 'socio_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'FabricantesSocio',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);

}